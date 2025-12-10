<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ensure COP keeps two decimal places and proper separators.
     */
    public function up(): void
    {
        DB::table('currencies')
            ->where('code', 'COP')
            ->update([
                'decimal'            => 0,
                'decimal_separator'  => DB::raw("COALESCE(decimal_separator, '.')"),
                'group_separator'    => DB::raw("COALESCE(group_separator, ',')"),
                'currency_position'  => DB::raw("COALESCE(currency_position, 'left')"),
            ]);
    }

    /**
     * No rollback action; leave explicit values intact.
     */
    public function down(): void
    {
        //
    }
};
