<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Domain\Licensing\LicenseAccessPolicy;
use App\Models\Landlord\License;
use App\Models\Landlord\LicenseBilling;
use Carbon\CarbonImmutable;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class EnsurePortalLicenseAllowsAccess
{
    /**
     * Permite SIEMPRE rutas de billing para pagar.
     * Bloquea el resto si license esta suspended/expired/cancelled.
     */
    public function handle(Request $request, Closure $next)
    {
        $path = ltrim($request->path(), '/');

        // Dejar pasar todo lo relacionado a pagar/ver estado
        if ($request->routeIs('billing.*') || str_starts_with($path, 'billing') || str_starts_with($path, 'portal/billing')) {
            return $next($request);
        }

        $user = Auth::guard('landlord')->user();
        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return $next($request);
        }

        if (! $user || ! $user->account_id) {
            abort(403);
        }

        $license = License::query()
            ->where('account_id', $user->account_id)
            ->orderByDesc('expires_at')
            ->first();

        if (! $license) {
            abort(403);
        }

        $billing = LicenseBilling::query()
            ->where('license_id', $license->id)
            ->where('provider', 'dlocalgo')
            ->first();

        $state = LicenseAccessPolicy::evaluate($license, $billing, CarbonImmutable::now());

        if (in_array($state, ['suspended', 'expired', 'cancelled'], true)) {
            abort(402, 'Licencia suspendida o vencida. Dirigete a Billing para pagar/renovar.');
        }

        return $next($request);
    }
}
