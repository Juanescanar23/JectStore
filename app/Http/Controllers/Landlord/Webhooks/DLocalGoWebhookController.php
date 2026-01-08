<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landlord\Webhooks;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessBillingEventJob;
use App\Models\Landlord\BillingEvent;
use App\Services\Billing\DLocalGo\DLocalGoWebhookVerifier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

final class DLocalGoWebhookController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $raw = $request->getContent();
        $auth = (string) $request->header('Authorization', '');

        $apiKey = (string) config('billing.dlocalgo.api_key');
        $secret = (string) config('billing.dlocalgo.secret_key');

        if (! DLocalGoWebhookVerifier::verify($auth, $raw, $apiKey, $secret)) {
            return response('invalid signature', 401);
        }

        $paymentId = (string) $request->input('payment_id', '');
        $licenseId = (int) ($request->input('license_id', 0) ?: $request->query('license_id', 0));

        if ($paymentId === '') {
            return response('missing payment_id', 400);
        }

        $event = null;

        try {
            $event = DB::connection('landlord')->transaction(function () use ($paymentId, $licenseId, $raw, $auth) {
                // Idempotencia dura por unique index
                $evt = BillingEvent::query()->create([
                    'provider' => 'dlocalgo',
                    'event_type' => 'payment_notification',
                    'provider_event_id' => $paymentId,
                    'license_id' => $licenseId ?: null,
                    'payload_raw' => $raw,
                    'payload_hash' => hash('sha256', $raw),
                    'signature_header' => $auth,
                    'status' => 'pending',
                ]);
                return $evt;
            });
        } catch (\Throwable $e) {
            // Duplicate = reintento => 200 OK
            return response('ok', 200);
        }

        ProcessBillingEventJob::dispatch($event->id);

        return response('ok', 200);
    }
}
