<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Laravel's default users table exists; adapt it for passwordless, phone-first auth.
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 20)->nullable()->unique()->after('name');
            $table->string('email')->nullable()->change();
            $table->string('password')->nullable()->change();
            $table->timestamp('phone_verified_at')->nullable()->after('email_verified_at');
            $table->string('home_zip', 10)->nullable()->after('phone_verified_at');
            $table->string('role', 20)->default('buyer')->after('home_zip'); // buyer|seller|market_manager|admin
        });

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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'phone_verified_at', 'home_zip', 'role']);
        });
    }
};
