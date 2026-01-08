<?php

declare(strict_types=1);

namespace Tests\Feature\Landlord;

use App\Models\Landlord\Account;
use App\Models\Landlord\LandlordUser;
use App\Models\Landlord\License;
use App\Models\Landlord\Plan;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\RefreshLandlordDatabase;
use Tests\TestCase;

final class LicenseUsesPlanSnapshotTest extends TestCase
{
    use RefreshLandlordDatabase;

    public function test_license_copies_plan_snapshot(): void
    {
        $admin = LandlordUser::query()->create([
            'name' => 'Superadmin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'superadmin',
            'status' => 'active',
        ]);

        $account = Account::query()->create([
            'name' => 'Cliente Demo',
            'status' => 'active',
        ]);

        $plan = Plan::query()->create([
            'name' => 'Standard 100',
            'code' => 'standard_100',
            'price_usd' => 99.99,
            'contract_months' => 12,
            'grace_days' => 5,
            'max_tenants' => 100,
            'is_active' => true,
        ]);

        $startsAt = CarbonImmutable::parse('2026-01-10 08:00:00');

        $resp = $this->actingAs($admin, 'landlord')->post('/admin/accounts/' . $account->id . '/licenses', [
            'plan_id' => $plan->id,
            'starts_at' => $startsAt->toDateTimeString(),
        ]);

        $resp->assertSessionHasNoErrors();

        $license = License::query()->where('account_id', $account->id)->firstOrFail();

        $this->assertSame($plan->id, $license->plan_id);
        $this->assertSame($plan->code, $license->plan_code);
        $this->assertSame($plan->name, $license->plan_name);
        $this->assertSame((float) $plan->price_usd, (float) $license->price_usd);
        $this->assertSame($plan->max_tenants, $license->max_tenants);
        $this->assertSame($plan->grace_days, $license->grace_days);
        $this->assertSame($plan->contract_months, $license->contract_months);
        $this->assertSame('USD', $license->currency);

        $expectedExpiry = $startsAt->addMonthsNoOverflow($plan->contract_months)->toDateTimeString();
        $this->assertSame($expectedExpiry, $license->expires_at);
    }
}
