<?php

declare(strict_types=1);

namespace App\Domain\Licensing;

use App\Domain\Billing\BillingCycle;
use App\Models\Landlord\License;
use App\Models\Landlord\LicenseBilling;
use Carbon\CarbonImmutable;

final class LicenseAccessPolicy
{
    /**
     * Evaluate current access state for a license and its billing record.
     * Returns: active, grace, suspended, expired, cancelled.
     */
    public static function evaluate(License $license, ?LicenseBilling $billing, CarbonImmutable $now): string
    {
        $status = strtolower((string) $license->status);
        if (in_array($status, ['suspended', 'expired', 'cancelled', 'canceled'], true)) {
            return $status === 'canceled' ? 'cancelled' : $status;
        }

        if ($license->expires_at && CarbonImmutable::parse($license->expires_at)->lessThanOrEqualTo($now)) {
            return 'expired';
        }

        if (! $billing || ! $billing->current_period_end) {
            return $status ?: 'active';
        }

        $dueDate = CarbonImmutable::parse($billing->current_period_end)->startOfDay();
        $graceDays = (int) ($billing->grace_days ?? $license->grace_days ?? 5);
        $graceEndsAt = BillingCycle::graceEndsAt($dueDate, $graceDays);

        if ($now->greaterThan($graceEndsAt)) {
            return 'suspended';
        }

        if ($now->greaterThan($dueDate)) {
            return 'grace';
        }

        return 'active';
    }
}
