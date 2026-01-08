<?php

declare(strict_types=1);

namespace App\Services\Portal;

use App\Domain\Billing\BillingCycle;
use App\Domain\Licensing\LicenseAccessPolicy;
use App\Models\Landlord\Account;
use App\Models\Landlord\BillingEvent;
use App\Models\Landlord\LandlordUser;
use App\Models\Landlord\License;
use App\Models\Landlord\LicenseBilling;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Database\Models\Tenant;

final class PortalBillingSummaryService
{
    private const OWNERS_LIMIT = 3;

    public function buildForUser(LandlordUser $user): BillingSummaryDTO
    {
        $account = $user->account_id ? Account::query()->find($user->account_id) : null;
        $license = $account
            ? License::query()
                ->with('plan')
                ->where('account_id', $account->id)
                ->orderByDesc('expires_at')
                ->first()
            : null;
        $billing = $license
            ? LicenseBilling::query()->where('license_id', $license->id)->where('provider', 'dlocalgo')->first()
            : null;

        $now = CarbonImmutable::now();
        $accessState = $license ? LicenseAccessPolicy::evaluate($license, $billing, $now) : 'inactive';
        $displayStatus = $this->formatStatus($accessState);
        $statusMessage = $this->statusMessage($accessState, $license, $billing, $now);

        $anchorDay = null;
        if ($billing && $billing->day_of_month) {
            $anchorDay = (int) $billing->day_of_month;
        } elseif ($license && $license->starts_at) {
            $anchorDay = (int) CarbonImmutable::parse($license->starts_at)->day;
        }

        $periodStart = $billing && $billing->current_period_start
            ? CarbonImmutable::parse($billing->current_period_start)->startOfDay()
            : null;
        $periodEnd = $billing && $billing->current_period_end
            ? CarbonImmutable::parse($billing->current_period_end)->startOfDay()
            : null;

        if (! $periodEnd && $anchorDay) {
            $periodEnd = BillingCycle::nextDueDate($now, $anchorDay);
        }

        if (! $periodStart && $periodEnd) {
            $periodStart = $periodEnd->subMonthNoOverflow()->startOfDay();
        }

        $graceDaysRaw = $billing?->grace_days ?? $license?->grace_days;
        $graceDays = $graceDaysRaw === null ? null : (int) $graceDaysRaw;
        $graceEndsAt = ($periodEnd && $graceDays !== null)
            ? BillingCycle::graceEndsAt($periodEnd, $graceDays)
            : null;
        $daysRemaining = $graceEndsAt ? max(0, $now->diffInDays($graceEndsAt, false)) : null;
        $nextDueDate = $periodEnd;

        $tenantsCount = 0;
        if ($license) {
            $tenantsCount = Tenant::query()->where('license_id', $license->id)->count();
            if ($tenantsCount === 0 && $license->account_id) {
                $tenantsCount = Tenant::query()->where('account_id', $license->account_id)->count();
            }
        }

        $domains = $account ? $this->fetchDomains($account->id) : collect();
        $verifiedDomainsCount = $domains
            ->filter(fn (array $domain) => in_array($domain['status'], ['verified', 'active'], true))
            ->count();

        $owners = $account
            ? LandlordUser::query()
                ->where('account_id', $account->id)
                ->where('role', 'account_owner')
                ->limit(self::OWNERS_LIMIT)
                ->get(['id', 'name', 'email', 'role', 'status'])
            : collect();
        $ownersCount = $owners->count();

        $events = $license
            ? BillingEvent::query()
                ->where('provider', 'dlocalgo')
                ->where('license_id', $license->id)
                ->orderByDesc('id')
                ->limit(10)
                ->get(['id', 'event_type', 'status', 'provider_event_id', 'created_at', 'processed_at', 'error_message'])
            : collect();

        $billingStatus = $billing?->status ?? $this->fallbackBillingStatus($accessState);
        $billingSummary = $license ? [
            'status' => $billingStatus,
            'current_period_start' => $periodStart,
            'current_period_end' => $periodEnd,
            'subscribe_url' => $billing?->subscribe_url,
            'day_of_month' => $anchorDay,
            'cycles_paid' => $billing?->cycles_paid,
            'max_periods' => $billing?->max_periods ?? $license->contract_months,
            'grace_days' => $billing?->grace_days ?? $license->grace_days,
            'last_paid_at' => $billing?->last_paid_at,
            'last_payment_id' => $billing?->last_payment_id,
        ] : null;

        return new BillingSummaryDTO([
            'account' => $account ? [
                'id' => $account->id,
                'name' => $account->name,
                'status' => $account->status,
                'billing_email' => $account->billing_email,
            ] : null,
            'license' => $license ? [
                'id' => $license->id,
                'plan' => $license->plan ? [
                    'id' => $license->plan->id,
                    'code' => $license->plan->code,
                    'name' => $license->plan->name,
                ] : null,
                'plan_code' => $license->plan_code,
                'plan_name' => $license->plan_name,
                'max_tenants' => $license->max_tenants,
                'price_usd' => $license->price_usd,
                'currency' => $license->currency,
                'contract_months' => $license->contract_months,
                'starts_at' => $license->starts_at,
                'expires_at' => $license->expires_at,
                'status' => $license->status,
                'grace_days' => $license->grace_days,
                'notes' => $license->notes,
            ] : null,
            'billing' => $billingSummary,
            'access_state' => $accessState,
            'global' => [
                'status' => $displayStatus,
                'message' => $statusMessage,
            ],
            'cycle' => [
                'period_start' => $periodStart,
                'period_end' => $periodEnd,
                'grace_ends_at' => $graceEndsAt,
                'grace_days' => $graceDays,
                'days_remaining' => $daysRemaining,
                'next_due_date' => $nextDueDate,
            ],
            'contract' => [
                'months_remaining' => $this->monthsRemaining($license, $now),
            ],
            'usage' => [
                'tenants_count' => $tenantsCount,
                'max_tenants' => $license?->max_tenants,
            ],
            'domains' => [
                'count' => $verifiedDomainsCount,
                'items' => $domains,
            ],
            'owners' => $owners,
            'owners_count' => $ownersCount,
            'owners_limit' => self::OWNERS_LIMIT,
            'events' => $events,
            'now' => $now,
        ]);
    }

    private function formatStatus(string $state): string
    {
        $state = strtolower($state);
        if ($state === 'cancelled') {
            return 'CANCELED';
        }
        if ($state === 'inactive') {
            return 'INACTIVE';
        }
        return strtoupper($state);
    }

    private function fallbackBillingStatus(string $state): string
    {
        $state = strtolower($state);

        return match ($state) {
            'grace' => 'past_due',
            'active' => 'active',
            'suspended' => 'suspended',
            'expired' => 'expired',
            'cancelled', 'canceled' => 'canceled',
            default => 'pending',
        };
    }

    private function statusMessage(string $state, ?License $license, ?LicenseBilling $billing, CarbonImmutable $now): string
    {
        $state = strtolower($state);

        return match ($state) {
            'active' => 'Licencia activa. Todo funciona con normalidad.',
            'grace' => 'Estas en periodo de gracia. Se requiere pago para evitar suspension.',
            'suspended' => 'Licencia suspendida. Paga para reactivar el servicio.',
            'expired' => $license && $license->expires_at
                ? 'Contrato finalizado el ' . CarbonImmutable::parse($license->expires_at)->format('Y-m-d') . '.'
                : 'Contrato finalizado.',
            'cancelled', 'canceled' => 'Licencia cancelada. Contacta soporte para reactivacion.',
            default => 'Estado pendiente de validacion.',
        };
    }

    private function monthsRemaining(?License $license, CarbonImmutable $now): ?int
    {
        if (! $license || ! $license->expires_at) {
            return null;
        }

        $expiresAt = CarbonImmutable::parse($license->expires_at);
        $months = $now->diffInMonths($expiresAt, false);

        return max(0, $months);
    }

    private function fetchDomains(int $accountId): Collection
    {
        return DB::connection('landlord')
            ->table('verified_domains')
            ->where('account_id', $accountId)
            ->orderByDesc('id')
            ->limit(10)
            ->get(['id', 'root_domain', 'portal_host', 'status', 'verified_at'])
            ->map(function ($domain) {
                return [
                    'id' => $domain->id,
                    'root_domain' => $domain->root_domain,
                    'portal_host' => $domain->portal_host,
                    'status' => $domain->status,
                    'verified_at' => $domain->verified_at,
                ];
            });
    }
}
