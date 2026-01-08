<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant\Checkout;

use App\Models\Tenant\PaymentProviderConfig;
use App\Services\Billing\MercadoPago\MercadoPagoClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Webkul\Checkout\Facades\Cart;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Transformers\OrderResource;

final class MercadoPagoController extends Controller
{
    public function __construct(
        private readonly OrderRepository $orderRepository
    ) {
    }

    public function redirect(): RedirectResponse
    {
        $cart = Cart::getCart();

        if (! $cart || ! $cart->payment || $cart->payment->method !== 'mercadopago') {
            return redirect()->route('shop.checkout.cart.index');
        }

        $config = PaymentProviderConfig::query()
            ->where('provider', 'mercadopago')
            ->first();

        if (! $config) {
            return redirect()
                ->route('shop.checkout.cart.index')
                ->with('error', 'Mercado Pago no está configurado.');
        }

        $order = $this->orderRepository->create((new OrderResource($cart))->jsonSerialize());

        try {
            $client = new MercadoPagoClient($config->getAccessTokenPlain());
            $preference = $client->createPreference($this->buildPreferencePayload($cart, $order->id));
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('shop.checkout.cart.index')
                ->with('error', 'No se pudo iniciar el pago con Mercado Pago.');
        }

        $payment = $order->payment;
        $additional = $payment->additional ?? [];
        $additional['mp_preference_id'] = $preference['id'] ?? null;
        $additional['mp_init_point'] = $preference['init_point'] ?? null;
        $additional['mp_sandbox_init_point'] = $preference['sandbox_init_point'] ?? null;
        $payment->additional = $additional;
        $payment->save();

        Cart::deActivateCart();

        session()->flash('order_id', $order->id);

        $redirectUrl = $preference['init_point'] ?? $preference['sandbox_init_point'] ?? null;

        if (! $redirectUrl) {
            return redirect()->route('shop.checkout.onepage.success');
        }

        return redirect()->away($redirectUrl);
    }

    private function buildPreferencePayload($cart, int $orderId): array
    {
        $items = $cart->items->map(function ($item) use ($cart) {
            return [
                'title' => $item->name,
                'quantity' => (int) $item->quantity,
                'unit_price' => (float) $item->price,
                'currency_id' => $cart->cart_currency_code,
            ];
        })->values()->all();

        $notificationUrl = $this->notificationUrl($orderId);

        return [
            'items' => $items,
            'notification_url' => $notificationUrl,
            'external_reference' => (string) $orderId,
            'metadata' => [
                'order_id' => $orderId,
                'tenant_id' => tenancy()->tenant?->id,
            ],
            'back_urls' => [
                'success' => route('shop.checkout.onepage.success'),
                'failure' => route('shop.checkout.cart.index'),
                'pending' => route('shop.checkout.cart.index'),
            ],
            'auto_return' => 'approved',
        ];
    }

    private function notificationUrl(int $orderId): string
    {
        $base = rtrim((string) config('app.url'), '/');
        $tenantId = tenancy()->tenant?->id ?? '';

        return $base . '/webhooks/mercadopago/' . $tenantId . '?order_id=' . $orderId;
    }
}
