<?php

namespace App\Queries;

/** Where a radius search is centred: a ZIP centroid or a city centre. */
final readonly class SearchCentre
{
    public function __construct(
        public float $lat,
        public float $lng,
        public string $label,      // "Austin, TX 78701"
        public ?string $zip = null,
    ) {}
}
