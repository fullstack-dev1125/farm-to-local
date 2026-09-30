<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug', 120)->unique();
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('instagram', 80)->nullable();
            $table->string('facebook')->nullable();
            $table->string('order_url')->nullable();

            $table->string('address_line')->nullable();
            $table->string('city', 100)->nullable();
            $table->char('state', 2)->index();
            $table->string('zip', 10)->nullable()->index();
            $table->string('county', 100)->nullable();
            $table->decimal('lat', 9, 6)->nullable();
            $table->decimal('lng', 9, 6)->nullable();
            $table->boolean('show_exact_address')->default(false);

            $table->string('status', 20)->default('listed')->index(); // seeded|listed|verified|suspended
            $table->unsignedTinyInteger('verification_level')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->string('plan', 10)->default('free')->index();     // free|pro|partner
            $table->unsignedSmallInteger('partner_rate_bps')->nullable(); // 500–1000
            $table->unsignedInteger('lead_fee_cents')->nullable();
            $table->string('coupon_code', 20)->default('FTL10');
            $table->timestamp('claimed_at')->nullable();
            $table->string('source', 60)->nullable();

            $table->unsignedSmallInteger('founded_year')->nullable();
            $table->string('owners')->nullable();
            $table->string('processor')->nullable();
            $table->json('practices')->nullable();
            $table->string('permit_status', 12)->default('none'); // none|permitted|no_permit
            $table->string('permit_number', 60)->nullable();
            $table->string('permit_type', 60)->nullable();

            $table->boolean('delivers')->default(false);
            $table->unsignedSmallInteger('delivery_radius_mi')->nullable();
            $table->unsignedInteger('delivery_fee_cents')->nullable();
            $table->unsignedInteger('delivery_min_cents')->nullable();
            $table->string('delivery_days', 80)->nullable();
            $table->boolean('ships')->default(false);
            $table->string('ships_scope', 12)->default('none'); // none|states|nationwide
            $table->json('ships_states')->nullable();
            $table->unsignedInteger('ship_fee_cents')->nullable();
            $table->string('ship_days', 80)->nullable();
            $table->boolean('csa')->default(false);
            $table->string('csa_notes')->nullable();
            $table->string('csa_url')->nullable();
            $table->boolean('wholesale')->default(false);
            $table->string('wholesale_notes')->nullable();
            $table->unsignedSmallInteger('service_radius_mi')->nullable();
            $table->string('service_days', 80)->nullable();
            $table->string('booking_window', 80)->nullable();

            $table->timestamp('products_updated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        // Spatial column for radius search (MySQL 8). SRID 4326 = WGS84.
        DB::statement('ALTER TABLE sellers ADD COLUMN location POINT SRID 4326 NULL');
        DB::statement('ALTER TABLE sellers ADD SPATIAL INDEX sellers_location_spatial (location)');

        Schema::create('seller_types', function (Blueprint $table) {
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->string('type', 30); // farm|fisherman|home_maker|bakery|home_cook|nursery|food_hub|service|market
            $table->boolean('is_primary')->default(false);
            $table->primary(['seller_id', 'type']);
        });

        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 40)->unique();
            $table->string('name', 60);
            $table->string('icon', 40)->nullable();
            $table->unsignedTinyInteger('sort')->default(0);
        });

        Schema::create('category_seller', function (Blueprint $table) {
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->primary(['category_id', 'seller_id']);
        });

        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 40)->unique();
            $table->string('name', 60);
            $table->boolean('requires_document')->default(false);
        });

        Schema::create('seller_tag', function (Blueprint $table) {
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['seller_id', 'tag_id']);
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 10)->default('product'); // product|service
            $table->string('name', 120);
            $table->string('unit', 80)->nullable();
            $table->unsignedInteger('price_cents')->nullable();
            $table->string('price_basis', 10)->default('fixed'); // fixed|per_unit|from|ask
            $table->string('price_text', 80)->nullable();          // e.g. "$175 + $1.10/lb hanging"
            $table->string('status', 12)->default('in_stock');    // in_stock|low|sold_out|reserve|preorder|seasonal
            $table->string('status_note', 40)->nullable();
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('seller_hours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->string('kind', 10)->default('stand'); // stand|pickup
            $table->json('days')->nullable();
            $table->string('opens', 10)->nullable();
            $table->string('closes', 10)->nullable();
            $table->string('notes')->nullable();
            $table->json('payment_methods')->nullable();
            $table->timestamps();
        });

        Schema::create('gear_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->string('name', 120);
            $table->string('canonical_key', 120)->index();
            $table->string('why', 240)->nullable();
            $table->string('url')->nullable();
            $table->string('link_mode', 6)->default('site'); // site|own
            $table->string('affiliate_code', 80)->nullable();
            $table->unsignedTinyInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('gear_clicks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gear_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('ip_hash', 64)->nullable();
            $table->timestamp('created_at');
        });

        Schema::create('attestations', function (Blueprint $table) {
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->string('key', 30); // producer|meat_processing|cottage_food|no_health_claims|prepared_food|license
            $table->timestamp('accepted_at');
            $table->string('ip_hash', 64)->nullable();
            $table->primary(['seller_id', 'key']);
        });

        Schema::create('saved_sellers', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->primary(['user_id', 'seller_id']);
        });

        Schema::create('email_preferences', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->cascadeOnDelete();
            $table->char('state', 2)->nullable();
            $table->json('categories')->nullable();
            $table->boolean('weekly')->default(true);
            $table->boolean('shares_near_me')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['email_preferences', 'saved_sellers', 'attestations', 'gear_clicks', 'gear_items', 'seller_hours',
                  'products', 'seller_tag', 'tags', 'category_seller', 'categories', 'seller_types', 'sellers'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
