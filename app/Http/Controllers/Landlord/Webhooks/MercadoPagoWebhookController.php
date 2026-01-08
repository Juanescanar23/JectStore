<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landlord\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessBillingEventJob;
use App\Models\Landlord\AccountProviderConfig;
use App\Models\Landlord\BillingEvent;
use App\Services\Billing\MercadoPago\MercadoPagoWebhookVerifier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

final class MercadoPagoWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $raw = $request->getContent();

        // MP puede enviar data.id en body o query; topic/type segun integracion
        $eventType = (string) ($request->input('type') ?? $request->query('topic') ?? $request->query('type') ?? 'unknown');

        $resourceId = (string) (
            $request->input('data.id')
            ?? $request->input('data')['id'] ?? null
            ?? $request->query('data.id')
            ?? $request->query('id')
            ?? ''
        );

        $accountId = (int) ($request->query('account_id') ?? $request->input('account_id') ?? 0);
        $tenantId = (string) ($request->query('tenant_id') ?? $request->input('tenant_id') ?? '');
        $tenantId = $tenantId !== '' ? $tenantId : null;

        if ($resourceId === '') {
            return response('missing resource id', 400);
        }

        $xSignature = (string) $request->header('x-signature', '');
        $xRequestId = (string) $request->header('x-request-id', '');

        // Para verificar firma necesitamos webhook_secret del account (reseller)
        if ($accountId <= 0) {
            return response('missing account_id', 400);
        }

        $cfg = AccountProviderConfig::query()
            ->where('account_id', $accountId)
            ->where('provider', 'mercadopago')
            ->first();

        if (! $cfg) {
            return response('account mp not configured', 400);
        }

        $secret = $cfg->getWebhookSecretPlain();
        if (! $secret) {
            return response('webhook secret not configured', 400);
        }

        if ($xSignature === '' || $xRequestId === '') {
            return response('missing signature headers', 401);
        }

        if (! MercadoPagoWebhookVerifier::verify($xSignature, $xRequestId, $resourceId, $secret)) {
            return response('invalid signature', 401);
        }

        // Guardar evento idempotente
        try {
            $evt = DB::connection('landlord')->transaction(function () use ($raw, $xSignature, $xRequestId, $eventType, $resourceId, $accountId, $tenantId) {
                return BillingEvent::query()->create([
                    'provider' => 'mercadopago',
                    'event_type' => $this->normalizeEventType($eventType),
                    'provider_event_id' => $resourceId,
                    'account_id' => $accountId,
                    'tenant_id' => $tenantId,
                    'payload_raw' => $raw,
                    'payload_hash' => hash('sha256', $raw),
                    'signature_header' => 'x-signature=' . $xSignature . '; x-request-id=' . $xRequestId,
                    'status' => 'pending',
                ]);
            });
        } catch (\Throwable $e) {
            return response('ok', 200); // duplicate
        }

        ProcessBillingEventJob::dispatch($evt->id);

        return response('ok', 200);
    }

    private function normalizeEventType(string $t): string
    {
        $t = strtolower(trim($t));

        // En la practica: payment / merchant_order / preapproval (suscripciones)
        if (str_contains($t, 'preapproval')) {
            return 'preapproval';
        }
        if (str_contains($t, 'merchant_order')) {
            return 'merchant_order';
        }
        if (str_contains($t, 'payment')) {
            return 'payment';
        }

        return 'unknown';
    }
}
