<?php

declare(strict_types=1);

namespace Tests\Feature\Landlord;

use App\Models\Landlord\Account;
use App\Models\Landlord\LandlordUser;
use App\Models\Landlord\License;
use App\Models\Landlord\LicenseBilling;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\RefreshLandlordDatabase;
use Tests\TestCase;

final class BillingCheckoutButtonTest extends TestCase
{
    use RefreshLandlordDatabase;

    public function test_shows_pay_now_when_subscribe_url_exists(): void
    {
        [$account, $user, $license] = $this->seedAccountWithOwner();

        LicenseBilling::query()->create([
            'license_id' => $license->id,
            'provider' => 'dlocalgo',
            'subscribe_url' => 'https://pay.example.test/sub',
            'status' => 'active',
            'day_of_month' => 5,
            'max_periods' => 12,
            'grace_days' => 5,
            'current_period_start' => CarbonImmutable::now()->startOfDay(),
            'current_period_end' => CarbonImmutable::now()->addMonthNoOverflow()->startOfDay(),
        ]);

        $resp = $this->actingAs($user, 'landlord')->get('/portal/billing');

        $resp->assertStatus(200);
        $resp->assertSee('Pagar ahora');
        $resp->assertDontSee('Generar link de pago');
    }

    public function test_shows_generate_link_when_subscribe_url_missing(): void
    {
        [$account, $user] = $this->seedAccountWithOwner();

        $resp = $this->actingAs($user, 'landlord')->get('/portal/billing');

        $resp->assertStatus(200);
        $resp->assertSee('Generar link de pago');
    }

    private function seedAccountWithOwner(): array
    {
        $account = Account::query()->create([
            'name' => 'Cliente Demo',
            'status' => 'active',
        ]);

        $startsAt = CarbonImmutable::now()->startOfDay();
        $license = License::query()->create([
            'account_id' => $account->id,
            'plan_code' => 'standard_100',
            'plan_name' => 'Standard 100',
            'max_tenants' => 100,
            'amount' => 99.99,
            'currency' => 'USD',
            'starts_at' => $startsAt,
            'expires_at' => $startsAt->addMonthsNoOverflow(12),
            'status' => 'active',
            'grace_days' => 5,
        ]);

        $user = LandlordUser::query()->create([
            'account_id' => $account->id,
            'name' => 'Owner Demo',
            'email' => 'owner' . uniqid('', true) . '@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'account_owner',
            'status' => 'active',
        ]);

        return [$account, $user, $license];
    }
}
