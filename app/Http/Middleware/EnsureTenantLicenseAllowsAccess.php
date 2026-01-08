<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Licensing\LicenseAccessPolicy;
use App\Models\Landlord\License;
use App\Models\Landlord\LicenseBilling;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;

final class EnsureTenantLicenseAllowsAccess
{
    public function handle(Request $request, Closure $next)
    {
        $tenant = tenancy()->tenant;

        if (! $tenant) {
            abort(404);
        }

        // Si el tenant ya esta suspendido por nosotros, no seguir
        if (! empty($tenant->suspended_at) || ! empty($tenant->deactivated_at)) {
            abort(402, 'Tienda suspendida.');
        }

        if (empty($tenant->license_id)) {
            abort(402, 'Tienda sin licencia asignada.');
        }

        $license = License::query()->find($tenant->license_id);
        if (! $license) {
            abort(402, 'Licencia no encontrada.');
        }

        $billing = LicenseBilling::query()
            ->where('license_id', $license->id)
            ->where('provider', 'dlocalgo')
            ->first();

        $state = LicenseAccessPolicy::evaluate($license, $billing, CarbonImmutable::now());

        if (in_array($state, ['suspended', 'expired', 'cancelled'], true)) {
            abort(402, 'Licencia suspendida o vencida.');
        }

        return $next($request);
    }
}
