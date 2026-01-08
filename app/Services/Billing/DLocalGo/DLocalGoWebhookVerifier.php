<?php

declare(strict_types=1);

namespace App\Services\Billing\DLocalGo;

final class DLocalGoWebhookVerifier
{
    public static function verify(string $authorizationHeader, string $rawBody, string $apiKey, string $secretKey): bool
    {
        // Docs: Authorization: "V2-HMAC-SHA256, Signature: <HMAC>" and HMAC_SHA256(apiKey + payload, secretKey) :contentReference[oaicite:15]{index=15}
        if (! str_starts_with($authorizationHeader, 'V2-HMAC-SHA256')) {
            return false;
        }

        $signature = null;
        $parts = explode(',', $authorizationHeader);
        foreach ($parts as $part) {
            $part = trim($part);
            if (str_starts_with($part, 'Signature:')) {
                $signature = trim(str_replace('Signature:', '', $part));
                break;
            }
        }

        if (! $signature) {
            return false;
        }

        $expected = hash_hmac('sha256', $apiKey . $rawBody, $secretKey);

        return hash_equals($expected, $signature);
    }
}
