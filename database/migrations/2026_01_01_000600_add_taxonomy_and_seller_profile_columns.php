<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Columns docs/02 lists that the hand-written migrations lacked:
 * - tags: group / status / merge + suggestion tracking (docs/02 "tags", docs/11, CLAUDE.md rule 13)
 * - sellers: "Added Sep 28" profile columns (awards is rendered on result rows per docs/06)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            // practice|certification|produce|meat|eggs|seafood|bakery|pantry|wellness|plants|prepared|homestead|services
            $table->string('group', 20)->default('practice')->after('name')->index();
            $table->string('status', 10)->default('approved')->after('requires_document')->index(); // approved|pending|merged
            $table->foreignId('merged_into_id')->nullable()->after('status')->constrained('tags')->nullOnDelete();
            $table->foreignId('suggested_by_seller_id')->nullable()->after('merged_into_id')->constrained('sellers')->nullOnDelete();
            $table->unsignedInteger('use_count_cache')->default(0)->after('suggested_by_seller_id');
        });

        Schema::table('sellers', function (Blueprint $table) {
            $table->json('awards')->nullable()->after('practices');   // ["Texas Beef Council 2025", …]
            $table->json('social')->nullable()->after('facebook');    // instagram, facebook, youtube, … URLs
            $table->string('booking_notes')->nullable()->after('booking_window');
            $table->string('promo_text', 160)->nullable()->after('booking_notes');
            $table->string('promo_url')->nullable()->after('promo_text');
        });
    }

    public function down(): void
    {
        Schema::table('sellers', function (Blueprint $table) {
            $table->dropColumn(['awards', 'social', 'booking_notes', 'promo_text', 'promo_url']);
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropForeign(['merged_into_id']);
            $table->dropForeign(['suggested_by_seller_id']);
            $table->dropIndex(['group']);
            $table->dropIndex(['status']);
            $table->dropColumn(['group', 'status', 'merged_into_id', 'suggested_by_seller_id', 'use_count_cache']);
        });
    }
};
