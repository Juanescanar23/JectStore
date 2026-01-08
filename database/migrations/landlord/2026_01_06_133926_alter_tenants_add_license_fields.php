<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('landlord')->table('tenants', function (Blueprint $table) {
            // Relación comercial
            $table->foreignId('account_id')->nullable()->after('id')->constrained('accounts')->nullOnDelete();
            $table->foreignId('license_id')->nullable()->after('account_id')->constrained('licenses')->nullOnDelete();

            // Estado operativo del tenant
            $table->dateTime('suspended_at')->nullable()->after('data')->index();
            $table->dateTime('deactivated_at')->nullable()->after('suspended_at')->index();

            // Identificador amigable (para armar subdominio): tienda1, ferreteria-popayan, etc.
            $table->string('store_slug')->nullable()->after('deactivated_at');

            $table->index(['license_id', 'deactivated_at']);
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->table('tenants', function (Blueprint $table) {
            $table->dropIndex(['license_id', 'deactivated_at']);

            $table->dropConstrainedForeignId('license_id');
            $table->dropConstrainedForeignId('account_id');

            $table->dropColumn(['suspended_at', 'deactivated_at', 'store_slug']);
        });
    }
};
