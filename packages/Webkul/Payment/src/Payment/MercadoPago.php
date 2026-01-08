<?php

namespace Webkul\Payment\Payment;

use App\Models\Tenant\PaymentProviderConfig;

class MercadoPago extends Payment
{
    /**
     * Payment method code.
     *
     * @var string
     */
    protected $code = 'mercadopago';

    /**
     * Get redirect url.
     *
     * @return string
     */
    public function getRedirectUrl()
    {
        return route('shop.mercadopago.redirect');
    }

    /**
     * Is available.
     *
     * @return bool
     */
    public function isAvailable()
    {
        $config = PaymentProviderConfig::query()
            ->where('provider', 'mercadopago')
            ->first();

        if (! $config || empty($config->access_token) || empty($config->public_key)) {
            return false;
        }

        return true;
    }

    /**
     * Get payment method title.
     *
     * @return string
     */
    public function getTitle()
    {
        return 'Mercado Pago';
    }

    /**
     * Get payment method description.
     *
     * @return string
     */
    public function getDescription()
    {
        return 'Paga con Mercado Pago';
    }

    /**
     * Get payment method sort order.
     *
     * @return int
     */
    public function getSortOrder()
    {
        return 3;
    }
}
