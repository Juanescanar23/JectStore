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

final class PortalBillingPageTest extends TestCase
{
    use RefreshLandlordDatabase;

    public function test_portal_billing_page_renders(): void
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

        $user = LandlordUser::query()->create([
            'account_id' => $account->id,
            'name' => 'Owner Demo',
            'email' => 'owner@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'account_owner',
            'status' => 'active',
        ]);

        $resp = $this->actingAs($user, 'landlord')->get('/portal/billing');

        $resp->assertStatus(200);
        $resp->assertSee('Standard 100');
        $resp->assertSee('ACTIVE');
    }
}
