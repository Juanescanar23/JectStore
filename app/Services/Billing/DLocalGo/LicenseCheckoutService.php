<?php

declare(strict_types=1);

namespace App\Services\Billing\DLocalGo;

use App\Models\Landlord\License;
use App\Models\Landlord\LicenseBilling;

final class LicenseCheckoutService
{
    public function __construct(
        private readonly LicenseBillingService $billingService
    ) {}

    public function ensureCheckoutForLicense(License $license): LicenseBilling
    {
        return $this->billingService->ensureDLocalPlanForLicense($license);
    }
}
