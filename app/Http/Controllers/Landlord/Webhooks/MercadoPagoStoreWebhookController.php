<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landlord\Webhooks;

use App\Http\Controllers\Controller;
use App\Models\Tenant\PaymentProviderConfig;
use App\Services\Billing\MercadoPago\MercadoPagoClient;
use App\Services\Billing\MercadoPago\MercadoPagoWebhookVerifier;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stancl\Tenancy\Database\Models\Tenant;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;

final class MercadoPagoStoreWebhookController extends Controller
{
    public function __construct(
        private readonly OrderRepository $orderRepository,
        private readonly InvoiceRepository $invoiceRepository
    ) {
    }

    public function __invoke(Request $request, string $tenantId): Response
    {
        $tenant = Tenant::query()->find($tenantId);

        if (! $tenant) {
            return response('tenant not found', 404);
        }

        tenancy()->initialize($tenant);

        try {
            $raw = $request->getContent();
            $eventType = (string) ($request->input('type') ?? $request->query('topic') ?? $request->query('type') ?? 'unknown');

            $resourceId = (string) (
                $request->input('data.id')
                ?? $request->input('data')['id'] ?? null
                ?? $request->query('data.id')
                ?? $request->query('id')
                ?? ''
            );

            if ($resourceId === '') {
                return response('missing resource id', 400);
            }

            $xSignature = (string) $request->header('x-signature', '');
            $xRequestId = (string) $request->header('x-request-id', '');

            if ($xSignature === '' || $xRequestId === '') {
                return response('missing signature headers', 401);
            }

            $config = PaymentProviderConfig::query()
                ->where('provider', 'mercadopago')
                ->first();

            if (! $config) {
                return response('mercadopago not configured', 400);
            }

            $secret = $config->getWebhookSecretPlain();
            if (! $secret) {
                return response('webhook secret not configured', 400);
            }

            if (! MercadoPagoWebhookVerifier::verify($xSignature, $xRequestId, $resourceId, $secret)) {
                return response('invalid signature', 401);
            }

            if (strtolower($eventType) !== 'payment') {
                return response('ok', 200);
            }

            $client = new MercadoPagoClient($config->getAccessTokenPlain());
            $payment = $client->getPayment($resourceId);

            $status = strtolower((string) ($payment['status'] ?? ''));
            if (! in_array($status, ['approved', 'authorized'], true)) {
                return response('ok', 200);
            }

            $orderId = (int) ($payment['external_reference'] ?? 0);
            if (! $orderId) {
                $orderId = (int) ($request->query('order_id') ?? $request->input('order_id') ?? 0);
            }

            if (! $orderId) {
                return response('missing order id', 400);
            }

            /** @var Order|null $order */
            $order = $this->orderRepository->find($orderId);

            if (! $order) {
                return response('order not found', 404);
            }

            if ($order->payment && $order->payment->method !== 'mercadopago') {
                return response('ok', 200);
            }

            if ($order->status === Order::STATUS_PROCESSING || $order->status === Order::STATUS_COMPLETED) {
                return response('ok', 200);
            }

            $paymentModel = $order->payment;
            if ($paymentModel) {
                $additional = $paymentModel->additional ?? [];
                $additional['mp_payment_id'] = $resourceId;
                $additional['mp_payment_status'] = $status;
                $paymentModel->additional = $additional;
                $paymentModel->save();
            }

            if (! $order->canInvoice()) {
                return response('ok', 200);
            }

            $items = [];
            foreach ($order->items as $item) {
                $qty = (int) $item->qty_to_invoice;
                if ($qty > 0) {
                    $items[$item->id] = $qty;
                }
            }

            if (! $items) {
                return response('ok', 200);
            }

            $this->invoiceRepository->create([
                'order_id' => $order->id,
                'invoice' => [
                    'items' => $items,
                ],
            ], 'paid', 'processing');

            return response('ok', 200);
        } finally {
            tenancy()->end();
        }
    }
}
