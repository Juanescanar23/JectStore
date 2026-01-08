<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('landlord')->table('tenants', function (Blueprint $table) {
            $table->dateTime('license_suspended_at')->nullable()->after('suspended_at')->index();
            $table->dateTime('store_suspended_at')->nullable()->after('license_suspended_at')->index();
        });

        DB::connection('landlord')
            ->table('tenants')
            ->whereNotNull('suspended_at')
            ->update(['license_suspended_at' => DB::raw('suspended_at')]);
    }

    public function down(): void
    {
        Schema::connection('landlord')->table('tenants', function (Blueprint $table) {
            $table->dropColumn(['license_suspended_at', 'store_suspended_at']);
        });
    }
};
