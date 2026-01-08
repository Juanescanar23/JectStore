<?php

declare(strict_types=1);

namespace Tests\Feature\Landlord;

use App\Models\Landlord\Account;
use App\Models\Landlord\AccountProviderConfig;
use App\Models\Landlord\BillingEvent;
use Illuminate\Support\Facades\Crypt;
use Tests\Concerns\RefreshLandlordDatabase;
use Tests\TestCase;

final class MercadoPagoWebhookTest extends TestCase
{
    use RefreshLandlordDatabase;

    public function test_rejects_when_signature_missing(): void
    {
        $account = Account::query()->create(['name' => 'A', 'status' => 'active']);

        AccountProviderConfig::query()->create([
            'account_id' => $account->id,
            'provider' => 'mercadopago',
            'access_token' => Crypt::encryptString('AT'),
            'webhook_secret' => Crypt::encryptString('SECRET'),
        ]);

        $resp = $this->post('/webhooks/mercadopago?account_id=' . $account->id, ['data' => ['id' => '123'], 'type' => 'payment']);
        $resp->assertStatus(401);
    }

    public function test_accepts_valid_signature_and_is_idempotent(): void
    {
        $account = Account::query()->create(['name' => 'A', 'status' => 'active']);

        AccountProviderConfig::query()->create([
            'account_id' => $account->id,
            'provider' => 'mercadopago',
            'access_token' => Crypt::encryptString('AT'),
            'webhook_secret' => Crypt::encryptString('SECRET'),
        ]);

        $resourceId = '999';
        $requestId = 'req-1';
        $ts = '1700000000';
        $manifest = "id:{$resourceId};request-id:{$requestId};ts:{$ts};";
        $v1 = hash_hmac('sha256', $manifest, 'SECRET');
        $xSignature = "ts={$ts},v1={$v1}";

        $headers = [
            'x-signature' => $xSignature,
            'x-request-id' => $requestId,
        ];

        $resp1 = $this->withHeaders($headers)->post('/webhooks/mercadopago?account_id=' . $account->id, [
            'data' => ['id' => $resourceId],
            'type' => 'payment',
        ]);

        $resp1->assertStatus(200);

        $resp2 = $this->withHeaders($headers)->post('/webhooks/mercadopago?account_id=' . $account->id, [
            'data' => ['id' => $resourceId],
            'type' => 'payment',
        ]);

        $resp2->assertStatus(200);

        $this->assertSame(1, BillingEvent::query()
            ->where('provider', 'mercadopago')
            ->where('provider_event_id', $resourceId)
            ->where('event_type', 'payment')
            ->count());
    }
}
