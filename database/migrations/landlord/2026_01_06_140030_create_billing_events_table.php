<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('landlord')->create('billing_events', function (Blueprint $table) {
            $table->id();
            $table->string('provider', 50);
            $table->string('event_type', 50)->nullable();
            $table->string('provider_event_id', 191);
            $table->unsignedBigInteger('account_id')->nullable();
            $table->string('tenant_id', 36)->nullable();
            $table->unsignedBigInteger('license_id')->nullable();
            $table->longText('payload_raw');
            $table->string('payload_hash', 64);
            $table->text('signature_header')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->string('status', 20)->default('pending');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->unique(['provider', 'provider_event_id']);
            $table->index(['license_id']);
            $table->index(['account_id']);
            $table->index(['tenant_id']);
            $table->index(['processed_at']);
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('billing_events');
    }
};
