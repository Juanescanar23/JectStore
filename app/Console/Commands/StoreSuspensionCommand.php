<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Stancl\Tenancy\Database\Models\Domain;
use Stancl\Tenancy\Database\Models\Tenant;

final class StoreSuspensionCommand extends Command
{
    protected $signature = 'tenant:suspension:store {tenant} {action}';
    protected $description = 'Suspend or unsuspend a tenant store without touching license suspension.';

    public function handle(): int
    {
        $tenantInput = (string) $this->argument('tenant');
        $action = strtolower((string) $this->argument('action'));

        if (! in_array($action, ['on', 'off'], true)) {
            $this->error('Action must be on or off.');
            return self::FAILURE;
        }

        $tenant = $this->resolveTenant($tenantInput);
        if (! $tenant) {
            $this->error('Tenant not found: ' . $tenantInput);
            return self::FAILURE;
        }

        $originalStoreSuspendedAt = $tenant->store_suspended_at;

        if ($action === 'on') {
            if (! $tenant->store_suspended_at) {
                $tenant->store_suspended_at = CarbonImmutable::now()->toDateTimeString();
                $tenant->save();
            }
        } else {
            if ($tenant->store_suspended_at) {
                $tenant->store_suspended_at = null;
                $tenant->save();
            }
        }

        $domains = Domain::query()
            ->where('tenant_id', $tenant->id)
            ->pluck('domain')
            ->all();

        $this->info('Tenant: ' . $tenant->id);
        $this->line('Domains: ' . ($domains ? implode(', ', $domains) : '-'));
        $this->line('license_suspended_at: ' . ($tenant->license_suspended_at ?: '-'));
        $this->line('store_suspended_at: ' . ($tenant->store_suspended_at ?: '-'));

        if ($action === 'on' && $originalStoreSuspendedAt) {
            $this->line('store_suspended_at unchanged (already set).');
        }

        return self::SUCCESS;
    }

    private function resolveTenant(string $tenantInput): ?Tenant
    {
        $tenant = Tenant::query()->find($tenantInput);
        if ($tenant) {
            return $tenant;
        }

        $domain = Domain::query()->where('domain', $tenantInput)->first();
        if (! $domain) {
            return null;
        }

        return Tenant::query()->find($domain->tenant_id);
    }
}
