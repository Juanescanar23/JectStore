<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventCentralDomainShopAccess
{
    public function handle(Request $request, Closure $next)
    {
        $host = $request->getHost();
        $centralDomains = (array) config('tenancy.central_domains', []);

        if (in_array($host, $centralDomains, true)) {
            $path = ltrim($request->path(), '/');
            $allowedPrefixes = [
                '',
                '__landlord_ping',
                'admin',
                'login',
                'logout',
                'portal',
                'billing',
                'webhooks',
                'up',
            ];

            foreach ($allowedPrefixes as $prefix) {
                if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                    return $next($request);
                }
            }

            return redirect('/admin');
        }

        return $next($request);
    }
}
