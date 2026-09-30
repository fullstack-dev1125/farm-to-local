<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/** The 12 fixed categories (docs/01). */
class Category extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['slug', 'name', 'icon', 'sort'];

    protected function casts(): array
    {
        return ['sort' => 'integer'];
    }

    public function sellers(): BelongsToMany
    {
        return $this->belongsToMany(Seller::class);
    }
}
