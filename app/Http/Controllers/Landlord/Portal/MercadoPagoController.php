<?php

declare(strict_types=1);

namespace App\Http\Controllers\Landlord\Portal;

use App\Http\Controllers\Controller;
use App\Models\Landlord\Account;
use App\Models\Landlord\AccountProviderConfig;
use App\Models\Landlord\License;
use App\Models\Landlord\StoreSubscription;
use App\Services\Billing\MercadoPago\StoreSubscriptionService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant;

final class MercadoPagoController extends Controller
{
    public function index()
    {
        $user = Auth::guard('landlord')->user();
        if (! $user || ! $user->account_id) {
            abort(403);
        }

        $account = Account::query()->findOrFail($user->account_id);

        $config = AccountProviderConfig::query()
            ->where('account_id', $account->id)
            ->where('provider', 'mercadopago')
            ->first();

        $tenants = Tenant::query()
            ->where('account_id', $account->id)
            ->orderByDesc('created_at')
            ->get();

        $tenantIds = $tenants->pluck('id')->all();
        $domainsByTenant = $this->domainsByTenant($tenantIds);
        $subscriptions = StoreSubscription::query()
            ->where('account_id', $account->id)
            ->whereIn('tenant_id', $tenantIds)
            ->get()
            ->keyBy('tenant_id');

        $defaultAmount = $this->resolveDefaultAmount($account);
        $currency = strtoupper($config->currency ?? 'ARS');

        $stores = $tenants->map(function (Tenant $tenant) use ($domainsByTenant, $subscriptions, $defaultAmount, $currency) {
            $domains = $domainsByTenant->get($tenant->id, collect())->implode(', ');
            $subscription = $subscriptions->get($tenant->id);

            return [
                'id' => $tenant->id,
                'name' => $tenant->store_slug ?: $tenant->id,
                'domains' => $domains ?: '-',
                'license_id' => $tenant->license_id,
                'created_at' => $tenant->created_at,
                'subscription' => $subscription,
                'default_amount' => $subscription?->amount ?: $defaultAmount,
                'currency' => $subscription?->currency ?: $currency,
                'checkout_url' => $subscription?->meta['init_point'] ?? null,
            ];
        });

        return view('portal.mercadopago', [
            'account' => $account,
            'config' => $config,
            'stores' => $stores,
            'currency' => $currency,
            'defaultAmount' => $defaultAmount,
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::guard('landlord')->user();
        if (! $user || ! $user->account_id) {
            abort(403);
        }

        $data = $request->validate([
            'access_token' => ['required', 'string'],
            'webhook_secret' => ['required', 'string'],
            'public_key' => ['nullable', 'string'],
            'country' => ['nullable', 'string', 'size:2'],
            'currency' => ['nullable', 'string', 'size:3'],
            'grace_days' => ['nullable', 'integer', 'min:0', 'max:30'],
        ]);

        AccountProviderConfig::query()->updateOrCreate(
            [
                'account_id' => $user->account_id,
                'provider' => 'mercadopago',
            ],
            [
                'access_token' => Crypt::encryptString($data['access_token']),
                'public_key' => $data['public_key'] ? Crypt::encryptString($data['public_key']) : null,
                'webhook_secret' => Crypt::encryptString($data['webhook_secret']),
                'country' => strtoupper($data['country'] ?? 'AR'),
                'currency' => strtoupper($data['currency'] ?? 'ARS'),
                'grace_days' => $data['grace_days'] ?? 5,
            ]
        );

        return redirect()
            ->back()
            ->with('ok', 'Mercado Pago conectado.');
    }

    public function subscribe(Request $request, string $tenantId)
    {
        $user = Auth::guard('landlord')->user();
        if (! $user || ! $user->account_id) {
            abort(403);
        }

        $data = $request->validate([
            'payer_email' => ['nullable', 'email'],
            'amount' => ['nullable', 'numeric', 'min:1'],
        ]);

        $config = AccountProviderConfig::query()
            ->where('account_id', $user->account_id)
            ->where('provider', 'mercadopago')
            ->first();

        if (! $config) {
            return redirect()
                ->back()
                ->withErrors(['mercadopago' => 'Mercado Pago no esta conectado.']);
        }

        $tenant = Tenant::query()
            ->where('id', $tenantId)
            ->where('account_id', $user->account_id)
            ->firstOrFail();

        $amount = $data['amount'] ?? $this->resolveDefaultAmount(Account::query()->findOrFail($user->account_id));
        if (! $amount || $amount <= 0) {
            return redirect()
                ->back()
                ->withErrors(['amount' => 'Define un monto mensual valido para la tienda.']);
        }

        $payerEmail = $data['payer_email'] ?? $user->email;
        if (! $payerEmail) {
            return redirect()
                ->back()
                ->withErrors(['payer_email' => 'Ingresa el email del pagador.']);
        }

        $currency = strtoupper($config->currency ?? 'ARS');

        $service = new StoreSubscriptionService($config);
        $service->createForTenant($tenant, (float) $amount, $currency, $payerEmail);

        return redirect()
            ->back()
            ->with('ok', 'Suscripcion creada para la tienda.');
    }

    private function resolveDefaultAmount(Account $account): ?float
    {
        $lastSubscription = StoreSubscription::query()
            ->where('account_id', $account->id)
            ->orderByDesc('id')
            ->first();

        if ($lastSubscription && $lastSubscription->amount > 0) {
            return (float) $lastSubscription->amount;
        }

        $license = License::query()
            ->where('account_id', $account->id)
            ->orderByDesc('expires_at')
            ->first();

        if (! $license || $license->price_usd <= 0 || ! $license->max_tenants) {
            return null;
        }

        return round(((float) $license->price_usd) / max(1, (int) $license->max_tenants), 2);
    }

    private function domainsByTenant(array $tenantIds): Collection
    {
        if (! $tenantIds) {
            return collect();
        }

        return Domain::query()
            ->whereIn('tenant_id', $tenantIds)
            ->get(['tenant_id', 'domain'])
            ->groupBy('tenant_id')
            ->map(function (Collection $items) {
                return $items->pluck('domain');
            });
    }
}
