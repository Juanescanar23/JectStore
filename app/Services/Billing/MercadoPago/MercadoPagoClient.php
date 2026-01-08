<?php

declare(strict_types=1);

namespace App\Services\Billing\MercadoPago;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class MercadoPagoClient
{
    public function __construct(
        private readonly string $accessToken,
        private readonly string $baseUrl = 'https://api.mercadopago.com',
        private readonly int $timeoutSeconds = 30
    ) {}

    private function http(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)
            ->timeout($this->timeoutSeconds)
            ->asJson()
            ->withToken($this->accessToken);
    }

    public function getPayment(string $id): array
    {
        // Official reference: GET https://api.mercadopago.com/v1/payments/{id} :contentReference[oaicite:18]{index=18}
        $resp = $this->http()->get('/v1/payments/' . urlencode($id));
        if (! $resp->successful()) {
            throw new RuntimeException('MP getPayment failed: ' . $resp->status() . ' ' . $resp->body());
        }
        return $resp->json();
    }

    public function createPreapproval(array $payload): array
    {
        // Reference: POST https://api.mercadopago.com/preapproval :contentReference[oaicite:19]{index=19}
        $resp = $this->http()->post('/preapproval', $payload);
        if (! $resp->successful()) {
            throw new RuntimeException('MP createPreapproval failed: ' . $resp->status() . ' ' . $resp->body());
        }
        return $resp->json();
    }

    public function getPreapproval(string $id): array
    {
        // Reference: GET https://api.mercadopago.com/preapproval/{id} :contentReference[oaicite:20]{index=20}
        $resp = $this->http()->get('/preapproval/' . urlencode($id));
        if (! $resp->successful()) {
            throw new RuntimeException('MP getPreapproval failed: ' . $resp->status() . ' ' . $resp->body());
        }
        return $resp->json();
    }

    public function createPreference(array $payload): array
    {
        // Preference endpoint (Checkout Pro) es estandar en MP; en MVP lo usamos via API.
        $resp = $this->http()->post('/checkout/preferences', $payload);
        if (! $resp->successful()) {
            throw new RuntimeException('MP createPreference failed: ' . $resp->status() . ' ' . $resp->body());
        }
        return $resp->json();
    }
}
