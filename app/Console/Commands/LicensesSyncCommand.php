<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Domain\Licensing\LicenseAccessPolicy;
use App\Models\Landlord\License;
use App\Models\Landlord\LicenseBilling;
use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Stancl\Tenancy\Database\Models\Tenant;

final class LicensesSyncCommand extends Command
{
    protected $signature = 'licenses:sync';
    protected $description = 'Sync license/billing status, apply grace period, and suspend/unsuspend tenants.';

    public function handle(): int
    {
        $now = CarbonImmutable::now();

        $licenses = License::query()->get();

        foreach ($licenses as $license) {
            $billing = LicenseBilling::query()
                ->where('license_id', $license->id)
                ->where('provider', 'dlocalgo')
                ->first();

            $state = LicenseAccessPolicy::evaluate($license, $billing, $now);

            DB::connection('landlord')->transaction(function () use ($license, $billing, $state, $now) {
                // Estado licencia (usar tus enums existentes)
                if (in_array($state, ['expired', 'suspended', 'cancelled'], true)) {
                    $license->status = $state;
                    $license->save();

                    // Suspender todas las tiendas de esa licencia
                    Tenant::query()
                        ->where('license_id', $license->id)
                        ->update(['suspended_at' => $now->toDateTimeString()]);

                    if ($billing) {
                        $billing->status = $state === 'cancelled' ? 'canceled' : $state;
                        $billing->save();
                    }
                    return;
                }

                // active o grace
                $license->status = $state;
                $license->save();

                if ($billing) {
                    $billing->status = $state;
                    $billing->save();
                }

                // Si esta active/grace, levantamos suspension (si la suspension fue por license)
                Tenant::query()
                    ->where('license_id', $license->id)
                    ->update(['suspended_at' => null]);
            });
        }

        $this->info('licenses:sync done');
        return self::SUCCESS;
    }
}
