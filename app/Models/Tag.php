<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Practice, certification and "what kind" tags (docs/11). A pending tag is visible only on the suggesting
 * seller's page and never in filters (CLAUDE.md rule 13) — use approved() for anything buyer-facing.
 */
class Tag extends Model
{
    use HasFactory;

    public $timestamps = false;

    /** status, merged_into_id and suggested_by_seller_id are set by the staff review flow, not forms. */
    protected $fillable = ['slug', 'name', 'group', 'requires_document'];

    protected function casts(): array
    {
        return [
            'requires_document' => 'boolean',
            'use_count_cache' => 'integer',
        ];
    }

    public function sellers(): BelongsToMany
    {
        return $this->belongsToMany(Seller::class);
    }

    public function mergedInto(): BelongsTo
    {
        return $this->belongsTo(self::class, 'merged_into_id');
    }

    public function suggestedBy(): BelongsTo
    {
        return $this->belongsTo(Seller::class, 'suggested_by_seller_id');
    }

    public function scopeApproved(Builder $query): void
    {
        $query->where('status', 'approved');
    }
}
