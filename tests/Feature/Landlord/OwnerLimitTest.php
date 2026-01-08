<?php

declare(strict_types=1);

namespace Tests\Feature\Landlord;

use App\Models\Landlord\Account;
use App\Models\Landlord\LandlordUser;
use App\Models\Landlord\License;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\RefreshLandlordDatabase;
use Tests\TestCase;

final class OwnerLimitTest extends TestCase
{
    use RefreshLandlordDatabase;

    public function test_owner_limit_is_enforced(): void
    {
        $account = Account::query()->create([
            'name' => 'Cliente Demo',
            'status' => 'active',
        ]);

        $startsAt = CarbonImmutable::now()->startOfDay();
        License::query()->create([
            'account_id' => $account->id,
            'plan_code' => 'standard_100',
            'plan_name' => 'Standard 100',
            'max_tenants' => 100,
            'amount' => 99.99,
            'price_usd' => 99.99,
            'currency' => 'USD',
            'contract_months' => 12,
            'starts_at' => $startsAt,
            'expires_at' => $startsAt->addMonthsNoOverflow(12),
            'status' => 'active',
            'grace_days' => 5,
        ]);

        $admin = LandlordUser::query()->create([
            'account_id' => $account->id,
            'name' => 'Superadmin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'superadmin',
            'status' => 'active',
        ]);

        for ($i = 1; $i <= 3; $i++) {
            LandlordUser::query()->create([
                'account_id' => $account->id,
                'name' => 'Owner ' . $i,
                'email' => 'owner' . $i . '@example.com',
                'password' => Hash::make('Secret123!'),
                'role' => 'account_owner',
                'status' => 'active',
            ]);
        }

        $resp = $this->actingAs($admin, 'landlord')->post('/admin/accounts/' . $account->id . '/users', [
            'name' => 'Owner 4',
            'email' => 'owner4@example.com',
            'password' => 'Secret123!',
            'role' => 'account_owner',
        ]);

        $resp->assertSessionHasErrors(['role']);
        $this->assertSame(3, LandlordUser::query()
            ->where('account_id', $account->id)
            ->where('role', 'account_owner')
            ->count());
    }
}
