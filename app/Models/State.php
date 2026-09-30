<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** US state, keyed by its 2-letter code; URLs use the slug (/texas). */
class State extends Model
{
    use HasFactory;

    protected $primaryKey = 'code';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['code', 'name', 'slug', 'launch'];

    protected function casts(): array
    {
        return [
            'launch' => 'boolean',
            'seller_count_cache' => 'integer',
            'market_count_cache' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'state', 'code');
    }

    public function sellers(): HasMany
    {
        return $this->hasMany(Seller::class, 'state', 'code');
    }
}
