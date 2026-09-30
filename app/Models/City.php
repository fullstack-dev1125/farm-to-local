<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** A city page centre (/{state}/{city}); slugs are unique per state. */
class City extends Model
{
    use HasFactory;

    /** published is flipped by the launch flow (docs/08), so it is not mass-assignable. */
    protected $fillable = ['slug', 'name', 'state', 'lat', 'lng', 'radius_mi'];

    protected function casts(): array
    {
        return [
            'lat' => 'decimal:6',
            'lng' => 'decimal:6',
            'radius_mi' => 'integer',
            'published' => 'boolean',
            'seller_count_cache' => 'integer',
        ];
    }

    public function stateModel(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state', 'code');
    }
}
