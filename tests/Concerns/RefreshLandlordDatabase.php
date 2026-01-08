<?php

declare(strict_types=1);

namespace Tests\Concerns;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;

trait RefreshLandlordDatabase
{
    use RefreshDatabase;

    protected static bool $landlordMigrated = false;
    protected array $connectionsToTransact = ['landlord'];

    protected function refreshTestDatabase()
    {
        if (! RefreshDatabaseState::$migrated) {
            $this->artisan('migrate:fresh', $this->migrateFreshUsing());
            RefreshDatabaseState::$migrated = true;
        }

        if (! static::$landlordMigrated) {
            $this->artisan('migrate', [
                '--database' => 'landlord',
                '--path' => 'database/migrations/landlord',
                '--force' => true,
            ]);
            static::$landlordMigrated = true;
        }

        $this->app[Kernel::class]->setArtisan(null);

        $this->beginDatabaseTransaction();
    }
}
