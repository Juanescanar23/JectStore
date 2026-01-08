<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('landlord')->table('licenses', function (Blueprint $table) {
            $table->foreignId('plan_id')->nullable()->after('account_id')->constrained('plans')->nullOnDelete();
            $table->decimal('price_usd', 12, 2)->default(0)->after('currency');
            $table->unsignedInteger('contract_months')->default(0)->after('price_usd');
        });

        DB::connection('landlord')
            ->table('licenses')
            ->where(function ($query) {
                $query->whereNull('price_usd')->orWhere('price_usd', 0);
            })
            ->update(['price_usd' => DB::raw('amount')]);

        DB::connection('landlord')
            ->table('licenses')
            ->where(function ($query) {
                $query->whereNull('contract_months')->orWhere('contract_months', 0);
            })
            ->update([
                'contract_months' => DB::raw('GREATEST(1, TIMESTAMPDIFF(MONTH, starts_at, expires_at))'),
            ]);
    }

    public function down(): void
    {
        Schema::connection('landlord')->table('licenses', function (Blueprint $table) {
            $table->dropConstrainedForeignId('plan_id');
            $table->dropColumn(['price_usd', 'contract_months']);
        });
    }
};
