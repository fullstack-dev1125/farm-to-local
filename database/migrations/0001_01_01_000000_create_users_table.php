<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // Passwordless, phone-first (CLAUDE.md rule 7). No password, remember token or reset tokens.
            $table->string('name')->nullable();                 // first SMS login creates the user before a name exists
            $table->string('phone', 20)->nullable()->unique();  // E.164
            $table->string('email')->nullable()->unique();      // optional: statements + weekly email only
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->string('home_zip', 10)->nullable();
            $table->string('role', 20)->default('buyer');       // buyer|seller|market_manager|admin
            $table->timestamps();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
