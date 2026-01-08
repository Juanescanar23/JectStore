<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('landlord')->create('verified_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();

            // Dominio raíz del cliente (sin subdominio): cliente.com / cliente.test
            $table->string('root_domain')->unique();

            // Host del portal: portal.cliente.com / portal.cliente.test
            $table->string('portal_host')->unique();

            $table->enum('status', ['pending', 'verified', 'active', 'suspended'])->default('pending');
            $table->string('verification_token')->nullable();
            $table->dateTime('verified_at')->nullable();

            $table->timestamps();

            $table->index(['account_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('verified_domains');
    }
};
