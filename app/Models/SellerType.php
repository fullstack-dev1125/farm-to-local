<?php

namespace App\Models;

use App\Enums\SellerType as SellerTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One row per type a seller checked (seller_types, composite key seller_id + type).
 * The enum App\Enums\SellerType holds the labels and colours; this model is the pivot-like row.
 * Rows are inserted or deleted, never updated in place (Eloquent has no composite-key updates).
 */
class SellerType extends Model
{
    use HasFactory;

    protected $table = 'seller_types';

    protected $primaryKey = null;

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['seller_id', 'type', 'is_primary'];

    protected function casts(): array
    {
        return [
            'type' => SellerTypeEnum::class,
            'is_primary' => 'boolean',
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(Seller::class);
    }
}
