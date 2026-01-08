<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::connection('landlord')->create('landlord_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')->nullable()->constrained('accounts')->nullOnDelete();

            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');

            $table->enum('role', ['superadmin', 'account_owner', 'account_manager'])->default('account_owner');
            $table->enum('status', ['active', 'suspended'])->default('active');

            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index(['account_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::connection('landlord')->dropIfExists('landlord_users');
    }
};
