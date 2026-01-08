<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_provider_configs', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 50)->default('mercadopago');
            $table->text('public_key'); // encrypted
            $table->text('access_token'); // encrypted
            $table->text('webhook_secret')->nullable(); // encrypted
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_provider_configs');
    }
};
