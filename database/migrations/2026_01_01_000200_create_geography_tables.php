<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zips', function (Blueprint $table) {
            $table->string('zip', 5)->primary();
            $table->decimal('lat', 9, 6);
            $table->decimal('lng', 9, 6);
            $table->string('city', 100)->nullable();
            $table->char('state', 2)->index();
            $table->string('county', 100)->nullable();
        });

        Schema::create('states', function (Blueprint $table) {
            $table->char('code', 2)->primary();
            $table->string('name', 40);
            $table->string('slug', 40)->unique();
            $table->boolean('launch')->default(false);
            $table->unsignedInteger('seller_count_cache')->default(0);
            $table->unsignedInteger('market_count_cache')->default(0);
            $table->timestamps();
        });

        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 80);
            $table->string('name', 80);
            $table->char('state', 2);
            $table->decimal('lat', 9, 6);
            $table->decimal('lng', 9, 6);
            $table->unsignedSmallInteger('radius_mi')->default(50);
            $table->boolean('published')->default(false);
            $table->unsignedInteger('seller_count_cache')->default(0);
            $table->timestamps();
            $table->unique(['state', 'slug']);
            $table->foreign('state')->references('code')->on('states');
        });

        Schema::create('counties_mehko', function (Blueprint $table) {
            $table->id();
            $table->char('state', 2)->default('CA');
            $table->string('county', 80);
            $table->string('status', 20); // permanent|pilot|pending|introduced|verify
            $table->string('since', 40)->nullable();
            $table->unsignedInteger('permits')->nullable();
            $table->text('notes')->nullable();
            $table->string('source')->nullable();
            $table->timestamps();
            $table->unique(['state', 'county']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counties_mehko');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('states');
        Schema::dropIfExists('zips');
    }
};
