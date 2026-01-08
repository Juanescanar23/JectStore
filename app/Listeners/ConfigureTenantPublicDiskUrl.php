<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Events\TenancyBootstrapped;

class ConfigureTenantPublicDiskUrl
{
    public function handle(TenancyBootstrapped $event): void
    {
        $baseUrl = rtrim(url('/storage'), '/');

        config([
            'filesystems.disks.public.url' => $baseUrl,
        ]);

        Storage::forgetDisk('public');
    }
}
