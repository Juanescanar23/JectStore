<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Landlord\License;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class EnsureLandlordLicenseIsActive
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::guard('landlord')->user();
        if (! $user) {
            return $next($request);
        }

        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return $next($request);
        }

        if (! $user->account_id) {
            return $next($request);
        }

        $license = License::query()
            ->where('account_id', $user->account_id)
            ->orderByDesc('starts_at')
            ->first();

        if (! $license) {
            return $next($request);
        }

        if (in_array($license->status, ['suspended', 'expired'], true)) {
            if ($request->routeIs('billing.*') || $request->is('billing*')) {
                return $next($request);
            }

            abort(403);
        }

        return $next($request);
    }
}
