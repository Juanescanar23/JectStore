<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureLandlordRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::guard('landlord')->user();

        if (! $user || ! $user->isActive()) {
            abort(403);
        }

        if (! empty($roles) && ! in_array($user->role, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
