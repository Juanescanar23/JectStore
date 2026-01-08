<?php

declare(strict_types=1);

namespace App\Services\Billing;

use App\Models\Landlord\License;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;

final class LicenseStatusService
{
    public function setActive(License $license): void
    {
        $this->updateStatus($license, 'active');
        $this->unsuspendTenants($license);
    }

    public function setGrace(License $license): void
    {
        $this->updateStatus($license, 'grace');
        $this->unsuspendTenants($license);
    }

    public function setSuspended(License $license, CarbonImmutable $now): void
    {
        $this->updateStatus($license, 'suspended');
        $this->suspendTenants($license, $now);
    }

    public function setExpired(License $license, CarbonImmutable $now): void
    {
        $this->updateStatus($license, 'expired');
        $this->suspendTenants($license, $now);
    }

    private function updateStatus(License $license, string $status): void
    {
        if ($license->status === $status) {
            return;
        }

        $license->status = $status;
        $license->save();
    }

    private function suspendTenants(License $license, CarbonImmutable $now): void
    {
        DB::connection('landlord')
            ->table('tenants')
            ->where('license_id', $license->id)
            ->whereNull('suspended_at')
            ->update(['suspended_at' => $now->toDateTimeString()]);
    }

    private function unsuspendTenants(License $license): void
    {
        DB::connection('landlord')
            ->table('tenants')
            ->where('license_id', $license->id)
            ->whereNotNull('suspended_at')
            ->update(['suspended_at' => null]);
    }
}
