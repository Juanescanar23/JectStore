<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Landlord\License;
use Closure;
use Illuminate\Http\Request;

final class EnsureTenantLicenseIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $tenant = tenant();
        if (! $tenant) {
            return $next($request);
        }

        if (! empty($tenant->suspended_at)) {
            abort(403);
        }

        $licenseId = $tenant->license_id ?? null;
        if (! $licenseId) {
            return $next($request);
        }

        $license = License::query()->find($licenseId);
        if ($license && in_array($license->status, ['suspended', 'expired'], true)) {
            abort(403);
        }

        return $next($request);
    }
}
