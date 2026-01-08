<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('landlord')->create('license_billings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('license_id');
            $table->string('provider', 50)->default('dlocalgo'); // dlocalgo
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->string('plan_token', 255)->nullable();
            $table->string('subscribe_url', 255)->nullable();
            $table->unsignedTinyInteger('day_of_month')->nullable();
            $table->unsignedInteger('max_periods')->default(12);

            $table->string('status', 20)->default('past_due'); // active|past_due|suspended|canceled|expired
            $table->dateTime('current_period_start')->nullable();
            $table->dateTime('current_period_end')->nullable();
            $table->unsignedTinyInteger('grace_days')->default(5);
            $table->string('last_payment_id', 255)->nullable();
            $table->dateTime('last_paid_at')->nullable();

            $table->json('meta')->nullable();

            $table->timestamps();

            $table->unique(['license_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('license_billings');
    }
};
