<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('markets', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 140)->unique();
            $table->string('name', 140);
            $table->string('address_line')->nullable();
            $table->string('city', 100)->nullable();
            $table->char('state', 2)->index();
            $table->string('zip', 10)->nullable()->index();
            $table->decimal('lat', 9, 6);   // USDA rows without coordinates fall back to their ZIP centroid
            $table->decimal('lng', 9, 6);
            $table->json('days')->nullable();      // ["Sat"] or ["Sat","Sun"]
            $table->string('hours', 80)->nullable();
            $table->string('season', 80)->nullable();
            $table->boolean('snap')->default(false);
            $table->string('website')->nullable();
            $table->string('operator', 140)->nullable();
            $table->string('source', 20)->default('manual'); // usda|manual
            $table->string('source_id', 40)->nullable();
            $table->foreignId('manager_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('claimed_at')->nullable();
            $table->unsignedInteger('vendor_count_cache')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
        // Generated from lat/lng so it can never drift (bulk imports included). SPATIAL indexes need NOT NULL,
        // hence lat/lng are NOT NULL. POINT(lng, lat) + ST_SRID(…, 4326) = WGS84, as in docs/02 "Geo query".
        DB::statement('ALTER TABLE markets ADD COLUMN location POINT GENERATED ALWAYS AS (ST_SRID(POINT(lng, lat), 4326)) STORED SRID 4326 NOT NULL');
        DB::statement('ALTER TABLE markets ADD SPATIAL INDEX markets_location_spatial (location)');

        Schema::create('market_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->foreignId('market_id')->constrained()->cascadeOnDelete();
            $table->string('day', 10)->nullable();
            $table->string('hours', 40)->nullable();
            $table->unique(['seller_id', 'market_id', 'day']);
        });

        Schema::create('market_vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('market_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name', 140)->nullable();          // when not (yet) a seller
            $table->string('category_hint', 60)->nullable();
            $table->timestamps();
        });

        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->string('doc_type', 30); // ag_registration|cottage_permit|market_letter|business_license|nursery_license|custom_exempt|contractor_license|operation_photo|vouch
            $table->string('permit_number', 60)->nullable();
            $table->timestamp('submitted_at');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('decision', 12)->default('pending'); // pending|approved|denied|more_info
            $table->text('notes')->nullable();
            $table->foreignId('voucher_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['decision', 'submitted_at']);
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->string('reporter_contact')->nullable();
            $table->foreignId('seller_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('conversation_id')->nullable();
            $table->string('reason', 20); // reselling|identity|health_claim|unsafe|harassment|other
            $table->text('details')->nullable();
            $table->string('status', 12)->default('open'); // open|reviewing|closed
            $table->string('outcome', 40)->nullable();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('neighbor_shares', function (Blueprint $table) {
            $table->id();
            $table->string('phone', 20)->index();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 120);
            $table->string('category', 12)->default('fruit'); // fruit|vegetables|herbs|eggs|cuttings|other
            $table->string('description', 500)->nullable();
            $table->decimal('lat', 9, 6);   // SNAPPED to block level before storing
            $table->decimal('lng', 9, 6);
            $table->string('block_label', 80)->nullable(); // "Hyde Park"
            $table->string('zip', 10)->index();
            $table->string('pickup_mode', 14)->default('message_first'); // message_first|curbside|come_pick
            $table->string('price_mode', 4)->default('free');            // free|pwyw
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->index();
            $table->boolean('extended_once')->default(false);
            $table->string('status', 10)->default('active'); // active|expired|removed
            $table->timestamps();
        });
        // Generated from lat/lng so it can never drift (bulk imports included). SPATIAL indexes need NOT NULL,
        // hence lat/lng are NOT NULL. POINT(lng, lat) + ST_SRID(…, 4326) = WGS84, as in docs/02 "Geo query".
        DB::statement('ALTER TABLE neighbor_shares ADD COLUMN location POINT GENERATED ALWAYS AS (ST_SRID(POINT(lng, lat), 4326)) STORED SRID 4326 NOT NULL');
        DB::statement('ALTER TABLE neighbor_shares ADD SPATIAL INDEX shares_location_spatial (location)');
    }

    public function down(): void
    {
        foreach (['neighbor_shares', 'reports', 'verifications', 'market_vendors', 'market_attendances', 'markets'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
