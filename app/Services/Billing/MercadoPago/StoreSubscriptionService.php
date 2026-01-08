<?php

declare(strict_types=1);

namespace App\Services\Billing\MercadoPago;

use App\Domain\Billing\BillingCycle;
use App\Models\Landlord\AccountProviderConfig;
use App\Models\Landlord\StoreSubscription;
use Carbon\CarbonImmutable;
use RuntimeException;
use Stancl\Tenancy\Database\Models\Tenant;

final class StoreSubscriptionService
{
    private MercadoPagoClient $client;

    public function __construct(
        private readonly AccountProviderConfig $config
    ) {
        $accessToken = $this->config->getAccessTokenPlain();
        if (! $accessToken) {
            throw new RuntimeException('Mercado Pago access token not configured.');
        }

        $this->client = new MercadoPagoClient($accessToken);
    }

    public function createForTenant(Tenant $tenant, float $amount, string $currency, string $payerEmail): StoreSubscription
    {
        $amount = round(max(0.0, $amount), 2);
        if ($amount <= 0) {
            throw new RuntimeException('Subscription amount must be greater than 0.');
        }

        $currency = strtoupper(trim($currency));
        if ($currency === '') {
            $currency = 'ARS';
        }

        $payload = [
            'reason' => 'Suscripcion tienda ' . ($tenant->store_slug ?: $tenant->id),
            'external_reference' => 'tenant:' . $tenant->id,
            'payer_email' => $payerEmail,
            'back_url' => $this->portalBackUrl(),
            'notification_url' => $this->notificationUrl($tenant->id),
            'auto_recurring' => [
                'frequency' => 1,
                'frequency_type' => 'months',
                'transaction_amount' => $amount,
                'currency_id' => $currency,
            ],
        ];

        $preapproval = $this->client->createPreapproval($payload);

        return $this->syncFromPreapproval($tenant->id, $preapproval, [
            'amount' => $amount,
            'currency' => $currency,
        ]);
    }

    public function fetchAndSync(string $preapprovalId, ?string $tenantId = null): StoreSubscription
    {
        $preapproval = $this->client->getPreapproval($preapprovalId);

        return $this->syncFromPreapproval($tenantId, $preapproval);
    }

    public function syncFromPreapproval(?string $tenantId, array $preapproval, array $overrides = []): StoreSubscription
    {
        $tenantId = $tenantId ?: $this->extractTenantId($preapproval['external_reference'] ?? null);
        if (! $tenantId) {
            throw new RuntimeException('Missing tenant id for subscription sync.');
        }

        $status = $this->mapMpStatus((string) ($preapproval['status'] ?? ''));
        $amount = $overrides['amount']
            ?? ($preapproval['auto_recurring']['transaction_amount'] ?? $preapproval['transaction_amount'] ?? 0);
        $currency = $overrides['currency']
            ?? ($preapproval['auto_recurring']['currency_id'] ?? $preapproval['currency_id'] ?? 'ARS');

        $startDate = $this->parseDate($preapproval['auto_recurring']['start_date'] ?? $preapproval['start_date'] ?? null)
            ?? CarbonImmutable::now();
        $nextPayment = $this->parseDate($preapproval['auto_recurring']['next_payment_date'] ?? $preapproval['next_payment_date'] ?? null);
        $periodEnd = $nextPayment ?: BillingCycle::nextDueDate($startDate, (int) $startDate->day);
        $periodStart = $startDate->startOfDay();

        $subscription = StoreSubscription::query()->firstOrNew([
            'account_id' => $this->config->account_id,
            'tenant_id' => $tenantId,
            'provider' => 'mercadopago',
        ]);

        $subscription->provider_subscription_id = $preapproval['id'] ?? $subscription->provider_subscription_id;
        $subscription->status = $status;
        $subscription->amount = (float) $amount;
        $subscription->currency = strtoupper((string) $currency);
        $subscription->grace_days = $this->config->grace_days ?? $subscription->grace_days ?? 5;
        $subscription->current_period_start = $periodStart->toDateTimeString();
        $subscription->current_period_end = $periodEnd->toDateTimeString();
        $subscription->last_paid_at = $this->parseDate($preapproval['last_payment_date'] ?? null)?->toDateTimeString();
        $subscription->meta = $preapproval;
        $subscription->save();

        return $subscription;
    }

    private function mapMpStatus(string $status): string
    {
        $status = strtolower(trim($status));

        return match ($status) {
            'authorized' => 'active',
            'paused', 'pending' => 'past_due',
            'cancelled', 'canceled' => 'canceled',
            'expired' => 'expired',
            default => 'past_due',
        };
    }

    private function extractTenantId(?string $externalReference): ?string
    {
        if (! $externalReference) {
            return null;
        }

        if (str_starts_with($externalReference, 'tenant:')) {
            return trim(substr($externalReference, strlen('tenant:')));
        }

        return null;
    }

    private function parseDate(?string $value): ?CarbonImmutable
    {
        if (! $value) {
            return null;
        }

        try {
            return CarbonImmutable::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function notificationUrl(string $tenantId): string
    {
        return rtrim((string) config('app.url'), '/')
            . '/webhooks/mercadopago?account_id=' . $this->config->account_id
            . '&tenant_id=' . $tenantId;
    }

    private function portalBackUrl(): string
    {
        return rtrim((string) config('app.url'), '/') . '/portal/payments/mercadopago';
    }
}
