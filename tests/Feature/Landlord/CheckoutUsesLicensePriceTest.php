<?php

declare(strict_types=1);

namespace Tests\Feature\Landlord;

use App\Models\Landlord\Account;
use App\Models\Landlord\License;
use App\Services\Billing\DLocalGo\LicenseBillingService;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\RefreshLandlordDatabase;
use Tests\TestCase;

final class CheckoutUsesLicensePriceTest extends TestCase
{
    use RefreshLandlordDatabase;

    public function test_checkout_payload_uses_license_price(): void
    {
        Http::fake([
            '*' => Http::response([
                'id' => 123,
                'plan_token' => 'token',
                'subscribe_url' => 'https://pay.example.test/sub',
            ], 200),
        ]);

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
            'amount' => 12.34,
            'price_usd' => 12.34,
            'currency' => 'USD',
            'contract_months' => 12,
            'starts_at' => $startsAt,
            'expires_at' => $startsAt->addMonthsNoOverflow(12),
            'status' => 'active',
            'grace_days' => 5,
        ]);

        $service = app(LicenseBillingService::class);
        $service->ensureDLocalPlanForLicense($license);

        Http::assertSent(function ($request) use ($license) {
            $payload = $request->data();

            return str_contains($request->url(), '/v1/subscription/plan')
                && isset($payload['amount'], $payload['currency'], $payload['max_periods'])
                && (float) $payload['amount'] === (float) $license->price_usd
                && strtoupper((string) $payload['currency']) === 'USD'
                && (int) $payload['max_periods'] === (int) $license->contract_months;
        });
    }
}
