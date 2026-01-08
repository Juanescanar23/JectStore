<?php

declare(strict_types=1);

namespace App\Services\Billing\DLocalGo;

use App\Domain\Billing\BillingCycle;
use App\Models\Landlord\License;
use App\Models\Landlord\LicenseBilling;
use App\Services\Billing\LicenseStatusService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

final class LicenseBillingService
{
    public function __construct(
        private readonly DLocalGoClient $client,
        private readonly LicenseStatusService $statusService,
    ) {}

    public function ensureDLocalPlanForLicense(License $license): LicenseBilling
    {
        return DB::connection('landlord')->transaction(function () use ($license) {
            $billing = LicenseBilling::query()
                ->where('license_id', $license->id)
                ->where('provider', 'dlocalgo')
                ->first();

            if ($billing && $billing->plan_token) {
                return $billing;
            }

            $startsAt = $license->starts_at ? CarbonImmutable::parse($license->starts_at) : CarbonImmutable::now();
            $dayOfMonth = (int) $startsAt->day;
            $notificationUrl = rtrim((string) config('app.url'), '/') . '/webhooks/dlocalgo?license_id=' . $license->getKey();

            $defaultAmount = (float) config('billing.dlocalgo.default_amount', 0.0);
            $amount = isset($license->amount) ? (float) $license->amount : $defaultAmount;
            if ($amount <= 0) {
                $amount = $defaultAmount;
            }

            $currency = trim((string) ($license->currency ?? ''));
            if ($currency === '') {
                $currency = (string) config('billing.dlocalgo.default_currency', 'USD');
            }
            $currency = strtoupper($currency);

            $country = (string) config('billing.dlocalgo.default_country', '');

            $payload = [
                'name' => 'JectStore License #' . $license->id,
                'description' => 'License billing for account ' . $license->account_id,
                'frequency_type' => 'MONTHLY',
                'day_of_month' => $dayOfMonth,
                'max_periods' => 12,
                'notification_url' => $notificationUrl,
                'currency' => $currency,
                'amount' => $amount,
            ];

            if ($country !== '') {
                $payload['country'] = $country;
            }

            $plan = $this->client->createPlan($payload);

            $billing = $billing ?: new LicenseBilling([
                'license_id' => $license->id,
                'provider' => 'dlocalgo',
            ]);

            $billing->plan_id = isset($plan['id']) ? (int) $plan['id'] : null;
            $billing->plan_token = $plan['plan_token'] ?? null;
            $billing->subscribe_url = $plan['subscribe_url'] ?? null;
            $billing->day_of_month = $dayOfMonth;
            $billing->max_periods = 12;
            $billing->status = $billing->status ?: 'past_due';
            if (! $billing->current_period_start || ! $billing->current_period_end) {
                $periodStart = $license->starts_at ? CarbonImmutable::parse($license->starts_at) : CarbonImmutable::now();
                $billing->current_period_start = $periodStart->toDateTimeString();
                $billing->current_period_end = $periodStart->addMonthNoOverflow()->toDateTimeString();
            }
            $billing->save();

            return $billing;
        });
    }

    public function applyPaidPaymentToLicense(License $license, array $payment): void
    {
        $this->recordPaid($license, $payment);
    }

    public function recordPaid(License $license, array $payment): void
    {
        DB::connection('landlord')->transaction(function () use ($license, $payment) {
            $paidAt = $this->paymentDate($payment) ?? CarbonImmutable::now();

            if ($license->expires_at && CarbonImmutable::parse($license->expires_at)->lessThanOrEqualTo($paidAt)) {
                $this->statusService->setExpired($license, $paidAt);
                return;
            }

            $billing = LicenseBilling::query()
                ->where('license_id', $license->id)
                ->where('provider', 'dlocalgo')
                ->first();

            $billing = $billing ?: new LicenseBilling([
                'license_id' => $license->id,
                'provider' => 'dlocalgo',
                'max_periods' => 12,
                'grace_days' => $license->grace_days ?: 5,
            ]);

            $anchorDay = $billing->day_of_month
                ?: ($license->starts_at ? CarbonImmutable::parse($license->starts_at)->day : (int) $paidAt->day);

            $billing->day_of_month = $billing->day_of_month ?: $anchorDay;
            $billing->cycles_paid = (int) $billing->cycles_paid + 1;
            $billing->last_paid_at = $paidAt->toDateTimeString();
            $billing->current_period_start = $paidAt->startOfDay()->toDateTimeString();
            $billing->current_period_end = BillingCycle::nextDueDate($paidAt, (int) $anchorDay)->toDateTimeString();
            $billing->status = 'active';
            $billing->last_payment_id = isset($payment['id']) ? (string) $payment['id'] : $billing->last_payment_id;
            $billing->save();

            $this->statusService->setActive($license);
        });
    }

    private function periodStartFromAnchor(CarbonImmutable $now, int $anchorDay): CarbonImmutable
    {
        $day = min($anchorDay, $now->daysInMonth);
        $candidate = $now->setDay($day);

        if ($candidate->greaterThan($now)) {
            $candidate = $candidate->subMonthNoOverflow();
        }

        return $candidate;
    }

    private function paymentDate(array $payment): ?CarbonImmutable
    {
        $raw = $payment['approved_date']
            ?? $payment['created_date']
            ?? $payment['created_at']
            ?? null;

        if (! $raw) {
            return null;
        }

        try {
            return CarbonImmutable::parse($raw);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function evaluatePastDueOrSuspend(License $license): void
    {
        $billing = LicenseBilling::query()
            ->where('license_id', $license->id)
            ->where('provider', 'dlocalgo')
            ->first();

        if (! $billing || ! $billing->current_period_end) {
            return;
        }

        $now = CarbonImmutable::now();
        $periodEnd = CarbonImmutable::parse($billing->current_period_end);

        if ($now->lessThanOrEqualTo($periodEnd)) {
            return;
        }

        $graceDays = (int) ($billing->grace_days ?? 5);
        $graceEnd = $periodEnd->addDays($graceDays);

        if ($now->greaterThan($graceEnd)) {
            $billing->status = 'suspended';
            $billing->save();
            $this->statusService->setSuspended($license, $now);
            return;
        }

        $billing->status = 'past_due';
        $billing->save();
        $this->statusService->setGrace($license);
    }

    public function mapDlocalPaymentStatus(string $dlocalStatus): string
    {
        // dLocal statuses: PENDING/PAID/REJECTED/CANCELLED/EXPIRED :contentReference[oaicite:16]{index=16}
        return match (strtoupper($dlocalStatus)) {
            'PAID' => 'active',
            'PENDING' => 'past_due',
            'REJECTED' => 'past_due',
            'CANCELLED' => 'canceled',
            'EXPIRED' => 'expired',
            default => 'past_due',
        };
    }
}
