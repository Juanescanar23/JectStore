<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Landlord\StoreSubscription;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class StoreSubscriptionsSyncCommand extends Command
{
    protected $signature = 'store-subscriptions:sync';
    protected $description = 'Sync store subscription status and apply grace suspension per tenant.';

    public function handle(): int
    {
        $now = CarbonImmutable::now();

        $subscriptions = StoreSubscription::query()
            ->where('provider', 'mercadopago')
            ->get();

        foreach ($subscriptions as $subscription) {
            $periodEnd = $subscription->current_period_end
                ? CarbonImmutable::parse($subscription->current_period_end)
                : null;

            $status = strtolower((string) $subscription->status);

            if (in_array($status, ['canceled', 'cancelled', 'expired'], true)) {
                $this->applyStoreSuspension($subscription->tenant_id, $now);
                continue;
            }

            if (! $periodEnd) {
                continue;
            }

            $graceDays = (int) ($subscription->grace_days ?? 5);
            $graceEnd = $periodEnd->addDays($graceDays);

            if (in_array($status, ['active', 'past_due'], true)) {
                if ($now->greaterThan($graceEnd)) {
                    $subscription->status = 'suspended';
                    $subscription->save();
                    $this->applyStoreSuspension($subscription->tenant_id, $now);
                    continue;
                }

                if ($now->greaterThan($periodEnd)) {
                    $subscription->status = 'past_due';
                    $subscription->save();
                    $this->clearStoreSuspension($subscription->tenant_id);
                    continue;
                }

                if ($status === 'active') {
                    $this->clearStoreSuspension($subscription->tenant_id);
                }

                continue;
            }

            if ($status === 'suspended') {
                $this->applyStoreSuspension($subscription->tenant_id, $now);
            }
        }

        $this->info('store-subscriptions:sync done');

        return self::SUCCESS;
    }

    private function applyStoreSuspension(string $tenantId, CarbonImmutable $now): void
    {
        DB::connection('landlord')
            ->table('tenants')
            ->where('id', $tenantId)
            ->whereNull('store_suspended_at')
            ->update(['store_suspended_at' => $now->toDateTimeString()]);
    }

    private function clearStoreSuspension(string $tenantId): void
    {
        DB::connection('landlord')
            ->table('tenants')
            ->where('id', $tenantId)
            ->whereNotNull('store_suspended_at')
            ->update(['store_suspended_at' => null]);
    }
}
