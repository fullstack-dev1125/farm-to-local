<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // users (phone, home_zip, role, no password) is created in 0001_01_01_000000_create_users_table.
        Schema::create('login_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('channel', 10);       // sms|email
            $table->string('destination');       // E.164 phone or email
            $table->string('code_hash');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->timestamp('expires_at');
            $table->timestamp('consumed_at')->nullable();
            $table->timestamps();
            $table->index(['destination', 'expires_at']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('login_codes');
    }
};
