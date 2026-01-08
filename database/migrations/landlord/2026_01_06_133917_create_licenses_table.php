<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('landlord')->create('licenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();

            $table->string('plan_code');                 // ej: standard_100
            $table->string('plan_name')->nullable();     // texto opcional
            $table->unsignedInteger('max_tenants')->default(100);

            $table->dateTime('starts_at');
            $table->dateTime('expires_at');

            $table->enum('status', ['active', 'grace', 'suspended', 'expired', 'cancelled'])->default('active');
            $table->unsignedInteger('grace_days')->default(0);

            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['account_id', 'status']);
            $table->index(['expires_at']);
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('licenses');
    }
};
