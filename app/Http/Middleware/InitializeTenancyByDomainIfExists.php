<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Contracts\TenantCouldNotBeIdentifiedException;
use Stancl\Tenancy\Resolvers\DomainTenantResolver;
use Stancl\Tenancy\Tenancy;

class InitializeTenancyByDomainIfExists
{
    public function __construct(
        private readonly Tenancy $tenancy,
        private readonly DomainTenantResolver $resolver
    ) {
    }

    public function handle(Request $request, Closure $next)
    {
        try {
            $tenant = $this->resolver->resolve($request->getHost());
            $this->tenancy->initialize($tenant);
        } catch (TenantCouldNotBeIdentifiedException $e) {
            // No tenant for this host; continue in central context.
        }

        return $next($request);
    }
}
