<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('landlord')->table('license_billings', function (Blueprint $table) {
            $table->unsignedInteger('cycles_paid')->default(0)->after('max_periods');
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->table('license_billings', function (Blueprint $table) {
            $table->dropColumn('cycles_paid');
        });
    }
};
