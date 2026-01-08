<?php

declare(strict_types=1);

namespace App\Services\Billing\MercadoPago;

final class MercadoPagoWebhookVerifier
{
    /**
     * Mercado Pago signature: header x-signature contains ts and v1; manifest commonly:
     * "id:{id};request-id:{x-request-id};ts:{ts};" and HMAC_SHA256(secret, manifest) == v1
     * (referencias publicas consistentes) :contentReference[oaicite:17]{index=17}
     */
    public static function verify(string $xSignature, string $xRequestId, string $resourceId, string $secret): bool
    {
        $ts = null;
        $v1 = null;

        foreach (explode(',', $xSignature) as $part) {
            $part = trim($part);
            [$k, $val] = array_pad(explode('=', $part, 2), 2, null);
            if (! $k || $val === null) {
                continue;
            }
            $k = trim($k);
            $val = trim($val);

            if ($k === 'ts') {
                $ts = $val;
            } elseif ($k === 'v1') {
                $v1 = $val;
            }
        }

        if (! $ts || ! $v1) {
            return false;
        }

        $manifest = sprintf('id:%s;request-id:%s;ts:%s;', $resourceId, $xRequestId, $ts);
        $expected = hash_hmac('sha256', $manifest, $secret);

        return hash_equals($expected, $v1);
    }
}
