<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('landlord')->create('store_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->string('tenant_id', 36);

            $table->string('provider', 50)->default('mercadopago'); // mercadopago
            $table->string('provider_subscription_id', 255)->nullable(); // preapproval id
            $table->string('status', 20)->default('past_due');

            $table->dateTime('current_period_start')->nullable();
            $table->dateTime('current_period_end')->nullable();

            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 3)->default('ARS');
            $table->unsignedTinyInteger('grace_days')->default(5);
            $table->dateTime('last_paid_at')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();

            $table->unique(['provider', 'provider_subscription_id']);
            $table->index(['account_id', 'tenant_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('store_subscriptions');
    }
};
