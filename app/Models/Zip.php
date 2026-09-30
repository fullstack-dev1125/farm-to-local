<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/** ZIP centroid (ZCTA gazetteer). Used to geocode search input and seller ZIPs. */
class Zip extends Model
{
    use HasFactory;

    protected $primaryKey = 'zip';

    protected $keyType = 'string';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['zip', 'lat', 'lng', 'city', 'state', 'county'];

    protected function casts(): array
    {
        return [
            'lat' => 'decimal:6',
            'lng' => 'decimal:6',
        ];
    }
}
