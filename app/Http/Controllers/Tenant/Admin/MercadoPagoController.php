<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant\PaymentProviderConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

final class MercadoPagoController extends Controller
{
    public function index()
    {
        $config = PaymentProviderConfig::query()
            ->where('provider', 'mercadopago')
            ->first();

        return view('admin.payments.mercadopago', [
            'config' => $config,
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'access_token' => ['required', 'string'],
            'public_key' => ['required', 'string'],
            'webhook_secret' => ['nullable', 'string'],
        ]);

        PaymentProviderConfig::query()->updateOrCreate(
            [
                'provider' => 'mercadopago',
            ],
            [
                'access_token' => Crypt::encryptString($data['access_token']),
                'public_key' => Crypt::encryptString($data['public_key']),
                'webhook_secret' => $data['webhook_secret'] ? Crypt::encryptString($data['webhook_secret']) : null,
            ]
        );

        return redirect()
            ->back()
            ->with('ok', 'Mercado Pago conectado.');
    }
}
