<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('neighbor_share_id')->nullable()->constrained('neighbor_shares')->nullOnDelete();
            $table->string('kind', 20)->default('message'); // message|order_inquiry|booking_request|wholesale_inquiry
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->json('meta')->nullable(); // booking: service, head_count, zip, when
            $table->timestamp('last_message_at')->nullable()->index();
            $table->unsignedSmallInteger('seller_unread_count')->default(0);
            $table->unsignedSmallInteger('buyer_unread_count')->default(0);
            $table->timestamps();
            $table->index(['seller_id', 'last_message_at']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sender_user_id')->nullable()->constrained('users')->nullOnDelete(); // null = system
            $table->text('body');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        Schema::create('statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->char('period', 7); // YYYY-MM
            $table->unsignedInteger('total_fee_cents')->default(0);
            $table->string('stripe_invoice_id', 60)->nullable();
            $table->string('status', 8)->default('draft'); // draft|sent|paid|void
            $table->timestamps();
            $table->unique(['seller_id', 'period']);
        });

        Schema::create('lead_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('seller_id')->constrained()->cascadeOnDelete();
            $table->foreignId('conversation_id')->nullable()->constrained()->nullOnDelete();
            $table->string('kind', 20); // coupon_redemption|tracked_click|lead|booking
            $table->unsignedInteger('amount_cents')->nullable();
            $table->unsignedInteger('fee_cents')->default(0);
            $table->timestamp('occurred_at')->index();
            $table->foreignId('statement_id')->nullable()->constrained()->nullOnDelete();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['seller_id', 'occurred_at']);
        });

        Schema::create('sponsors', function (Blueprint $table) {
            $table->id();
            $table->string('name', 120);
            $table->string('description', 200)->nullable();
            $table->string('url')->nullable();
            $table->string('placement', 20); // ships_hub|home_strip|region|email|supply_shelf
            $table->string('region', 40)->nullable();
            $table->string('shelf', 40)->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->boolean('active')->default(true);
            $table->unsignedTinyInteger('sort')->default(0);
            $table->timestamps();
        });

        Schema::create('supply_shelves', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 40)->unique();
            $table->string('name', 80);
            $table->string('subtitle', 120)->nullable();
            $table->unsignedTinyInteger('sort')->default(0);
            $table->foreignId('sponsor_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 140)->unique();
            $table->string('title', 200);
            $table->string('type', 20); // buying_guide|in_season|seller_playbook|neighbor_share|laws
            $table->string('excerpt', 300)->nullable();
            $table->longText('body')->nullable(); // markdown
            $table->char('state', 2)->nullable();
            $table->string('read_time', 20)->nullable();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->string('key', 40)->primary(); // about|how_it_works|privacy|terms|seller_rules
            $table->string('title', 120);
            $table->longText('body')->nullable();  // markdown
            $table->timestamps();
        });

        Schema::table('reports', function (Blueprint $table) {
            $table->foreign('conversation_id')->references('id')->on('conversations')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('reports', fn (Blueprint $t) => $t->dropForeign(['conversation_id']));
        foreach (['pages', 'posts', 'supply_shelves', 'sponsors', 'lead_events', 'statements', 'messages', 'conversations'] as $t) {
            Schema::dropIfExists($t);
        }
    }
};
