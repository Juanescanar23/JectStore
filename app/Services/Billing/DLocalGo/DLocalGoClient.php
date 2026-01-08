<?php

declare(strict_types=1);

namespace App\Services\Billing\DLocalGo;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

final class DLocalGoClient
{
    private readonly string $baseUrl;
    private readonly string $apiKey;
    private readonly string $secretKey;
    private readonly int $timeoutSeconds;

    public function __construct(
        ?string $baseUrl = null,
        ?string $apiKey = null,
        ?string $secretKey = null,
        int $timeoutSeconds = 30
    ) {
        $this->baseUrl = $baseUrl ?? (string) config('billing.dlocalgo.base_url');
        $this->apiKey = $apiKey ?? (string) config('billing.dlocalgo.api_key');
        $this->secretKey = $secretKey ?? (string) config('billing.dlocalgo.secret_key');
        $this->timeoutSeconds = $timeoutSeconds;
    }

    private function http(): PendingRequest
    {
        // Docs: Authorization = Bearer [api key:secret key] :contentReference[oaicite:13]{index=13}
        return Http::baseUrl($this->baseUrl)
            ->timeout($this->timeoutSeconds)
            ->asJson()
            ->withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey . ':' . $this->secretKey,
                'Content-Type' => 'application/json',
            ]);
    }

    public function createPlan(array $payload): array
    {
        $resp = $this->http()->post('/v1/subscription/plan', $payload);

        if (! $resp->successful()) {
            throw new RuntimeException('dLocalGo create plan failed: ' . $resp->status() . ' ' . $resp->body());
        }

        return $resp->json();
    }

    public function createSubscriptionPlan(array $payload): array
    {
        return $this->createPlan($payload);
    }

    public function retrievePayment(string $paymentId): array
    {
        // GET https://api.dlocalgo.com/v1/payments/:payment_id :contentReference[oaicite:14]{index=14}
        $resp = $this->http()->get('/v1/payments/' . urlencode($paymentId));

        if (! $resp->successful()) {
            throw new RuntimeException('dLocalGo retrieve payment failed: ' . $resp->status() . ' ' . $resp->body());
        }

        return $resp->json();
    }
}
