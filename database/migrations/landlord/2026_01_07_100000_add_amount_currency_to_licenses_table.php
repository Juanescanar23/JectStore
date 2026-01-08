<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('landlord')->table('licenses', function (Blueprint $table) {
            $table->decimal('amount', 12, 2)->default(0)->after('max_tenants');
            $table->string('currency', 3)->default('USD')->after('amount');
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->table('licenses', function (Blueprint $table) {
            $table->dropColumn(['amount', 'currency']);
        });
    }
};
