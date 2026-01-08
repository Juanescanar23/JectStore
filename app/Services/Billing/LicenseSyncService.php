<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Models\Landlord\License;
use App\Models\Landlord\LicenseBilling;
use Carbon\CarbonImmutable;

final class LicenseSyncService
{
    public function __construct(
        private readonly LicenseStatusService $statusService
    ) {}

    public function syncAll(): int
    {
        $count = 0;

        License::query()->chunkById(200, function ($licenses) use (&$count) {
            foreach ($licenses as $license) {
                $this->syncLicense($license);
                $count++;
            }
        });

        return $count;
    }

    public function syncLicense(License $license): void
    {
        $now = CarbonImmutable::now();

        if ($license->expires_at && CarbonImmutable::parse($license->expires_at)->lessThanOrEqualTo($now)) {
            $this->statusService->setExpired($license, $now);
            $this->updateBillingStatus($license, 'expired');
            return;
        }

        $billing = LicenseBilling::query()
            ->where('license_id', $license->id)
            ->where('provider', 'dlocalgo')
            ->first();

        if (! $billing || ! $billing->current_period_end) {
            return;
        }

        $periodEnd = CarbonImmutable::parse($billing->current_period_end);

        if ($now->lessThanOrEqualTo($periodEnd)) {
            $this->statusService->setActive($license);
            $this->updateBillingStatus($license, 'active');
            return;
        }

        $graceDays = (int) ($billing->grace_days ?? 5);
        $graceEnd = $periodEnd->addDays($graceDays);

        if ($now->lessThanOrEqualTo($graceEnd)) {
            $this->statusService->setGrace($license);
            $this->updateBillingStatus($license, 'past_due');
            return;
        }

        $this->statusService->setSuspended($license, $now);
        $this->updateBillingStatus($license, 'suspended');
    }

    private function updateBillingStatus(License $license, string $status): void
    {
        LicenseBilling::query()
            ->where('license_id', $license->id)
            ->where('provider', 'dlocalgo')
            ->update([
                'status' => $status,
                'updated_at' => now(),
            ]);
    }
}
