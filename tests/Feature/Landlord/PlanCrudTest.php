<?php

declare(strict_types=1);

namespace Tests\Feature\Landlord;

use App\Models\Landlord\LandlordUser;
use App\Models\Landlord\Plan;
use Illuminate\Support\Facades\Hash;
use Tests\Concerns\RefreshLandlordDatabase;
use Tests\TestCase;

final class PlanCrudTest extends TestCase
{
    use RefreshLandlordDatabase;

    public function test_superadmin_can_create_and_update_plan(): void
    {
        $admin = LandlordUser::query()->create([
            'name' => 'Superadmin',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('Secret123!'),
            'role' => 'superadmin',
            'status' => 'active',
        ]);

        $create = $this->actingAs($admin, 'landlord')->post('/admin/plans', [
            'name' => 'Standard 100',
            'code' => 'standard_100',
            'price_usd' => 99.99,
            'contract_months' => 12,
            'grace_days' => 5,
            'max_tenants' => 100,
            'features' => json_encode([
                'gateways_allowed' => ['mercadopago_checkout'],
            ]),
            'is_active' => 1,
        ]);

        $create->assertSessionHasNoErrors();

        $plan = Plan::query()->where('code', 'standard_100')->first();
        $this->assertNotNull($plan);
        $this->assertSame('Standard 100', $plan->name);
        $this->assertSame(99.99, (float) $plan->price_usd);

        $update = $this->actingAs($admin, 'landlord')->put('/admin/plans/' . $plan->id, [
            'name' => 'Standard 100 Plus',
            'code' => 'standard_100',
            'price_usd' => 129.50,
            'contract_months' => 12,
            'grace_days' => 7,
            'max_tenants' => 120,
            'features' => json_encode([
                'gateways_allowed' => ['mercadopago_checkout', 'mercadopago_subscriptions'],
            ]),
            'is_active' => 1,
        ]);

        $update->assertSessionHasNoErrors();

        $plan->refresh();
        $this->assertSame('Standard 100 Plus', $plan->name);
        $this->assertSame(129.50, (float) $plan->price_usd);
        $this->assertSame(120, $plan->max_tenants);
    }
}
