<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Tenant;
use Stancl\Tenancy\Tenancy;

class CreateTenant extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:create
        {domain : Full domain, e.g. demo.jectstore.test}
        {--name= : Tenant name}
        {--email= : Contact email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea un tenant (emprendedor) con BD propia y dominio';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $domain = $this->argument('domain');
        $name   = $this->option('name') ?? Str::before($domain, '.');
        $email  = $this->option('email') ?? null;

        $id = Str::slug($name);

        if (Tenant::find($id)) {
            $this->error("El tenant {$id} ya existe. Borra primero en landlord (tenants/domains) o usa otro id.");

            return self::FAILURE;
        }

        $this->info("Creando tenant {$id} ({$domain})");

        $tenant = Tenant::create([
            'id'   => $id,
            'data' => [
                'name'  => $name,
                'email' => $email,
            ],
        ]);

        $tenant->domains()->create([
            'domain' => $domain,
        ]);

        // Genera y persiste nombre de BD para el tenant.
        $tenant->database()->makeCredentials();

        $dbName   = $tenant->database()->getName();
        $charset  = config('database.connections.landlord.charset');
        $collation= config('database.connections.landlord.collation');

        // Crea la base de datos del tenant.
        DB::connection('landlord')->statement("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET `{$charset}` COLLATE `{$collation}`");

        // Inicializa el contexto tenant y corre migraciones completas de Bagisto.
        tenancy()->initialize($tenant);

        Artisan::call('migrate', [
            '--database' => 'tenant',
            '--force'    => true,
        ]);

        tenancy()->end();

        $this->info("Tenant {$id} creado. Dominio: {$domain}");
        $this->info("BD tenant: {$dbName}");

        return self::SUCCESS;
    }
}
