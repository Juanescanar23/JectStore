<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('landlord')->create('account_provider_configs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id');
            $table->string('provider', 50)->default('mercadopago'); // mercadopago
            $table->text('access_token'); // encrypted
            $table->text('public_key')->nullable(); // encrypted/nullable
            $table->text('webhook_secret')->nullable(); // encrypted
            $table->string('country', 2)->default('AR');
            $table->string('currency', 3)->default('ARS');
            $table->unsignedTinyInteger('grace_days')->default(5);
            $table->timestamps();

            $table->unique(['account_id']);
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('account_provider_configs');
    }
};
