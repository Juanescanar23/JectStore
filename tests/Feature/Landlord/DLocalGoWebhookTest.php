<?php

declare(strict_types=1);

namespace Tests\Feature\Landlord;

use App\Models\Landlord\BillingEvent;
use Illuminate\Support\Facades\Config;
use Tests\Concerns\RefreshLandlordDatabase;
use Tests\TestCase;

final class DLocalGoWebhookTest extends TestCase
{
    use RefreshLandlordDatabase;

    public function test_rejects_invalid_signature(): void
    {
        Config::set('billing.dlocalgo.api_key', 'k');
        Config::set('billing.dlocalgo.secret_key', 's');

        $payloadArr = ['payment_id' => 'DP-1'];
        $payload = json_encode($payloadArr, JSON_UNESCAPED_SLASHES);
        $headers = [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'V2-HMAC-SHA256, Signature: invalid',
        ];

        $resp = $this->call('POST', '/webhooks/dlocalgo', [], [], [], $headers, $payload);

        $resp->assertStatus(401);
    }

    public function test_accepts_and_is_idempotent(): void
    {
        Config::set('billing.dlocalgo.api_key', 'k');
        Config::set('billing.dlocalgo.secret_key', 's');

        $payloadArr = ['payment_id' => 'DP-123', 'license_id' => 1];
        $payload = json_encode($payloadArr, JSON_UNESCAPED_SLASHES);

        $sig = hash_hmac('sha256', 'k' . $payload, 's');

        $headers = [
            'CONTENT_TYPE' => 'application/json',
            'HTTP_AUTHORIZATION' => 'V2-HMAC-SHA256, Signature: ' . $sig,
        ];

        $resp = $this->call('POST', '/webhooks/dlocalgo', [], [], [], $headers, $payload);
        $resp->assertStatus(200);

        // Reintento
        $resp2 = $this->call('POST', '/webhooks/dlocalgo', [], [], [], $headers, $payload);
        $resp2->assertStatus(200);

        $this->assertSame(1, BillingEvent::query()
            ->where('provider', 'dlocalgo')
            ->where('provider_event_id', 'DP-123')
            ->count());
    }
}
