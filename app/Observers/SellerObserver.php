<?php

namespace App\Observers;

use App\Models\Seller;
use App\Models\Zip;
use InvalidArgumentException;
use LogicException;

/**
 * Keeps sellers.location in step with lat/lng.
 *
 * `location` itself is a STORED generated column (ST_SRID(POINT(lng, lat), 4326)), so MySQL recomputes it
 * on every write — including bulk query-builder updates and imports that bypass Eloquent. This observer
 * guarantees the inputs to that column are usable on every Eloquent save:
 *  - a seller with a ZIP but no coordinates is placed at the ZIP centroid (seeded/imported sellers);
 *  - coordinates are range-checked and rounded to decimal(9,6);
 *  - after a coordinate change, the stale in-memory `location` is dropped so it can't be reused.
 * It also enforces permanent slugs (CLAUDE.md rule 10).
 */
class SellerObserver
{
    public function saving(Seller $seller): void
    {
        if (($seller->lat === null || $seller->lng === null) && $seller->zip) {
            $centroid = Zip::find(substr((string) $seller->zip, 0, 5));
            if ($centroid) {
                $seller->lat = $centroid->lat;
                $seller->lng = $centroid->lng;
            }
        }

        if ($seller->lat === null || $seller->lng === null) {
            throw new InvalidArgumentException("Seller [{$seller->slug}] needs lat/lng or a known ZIP to be searchable.");
        }

        $lat = (float) $seller->lat;
        $lng = (float) $seller->lng;
        if ($lat < -90 || $lat > 90 || $lng < -180 || $lng > 180) {
            throw new InvalidArgumentException("Seller [{$seller->slug}] has out-of-range coordinates ({$lat}, {$lng}).");
        }

        $seller->lat = round($lat, 6);
        $seller->lng = round($lng, 6);
    }

    public function updating(Seller $seller): void
    {
        if ($seller->isDirty('slug')) {
            throw new LogicException('Seller slugs are permanent; renames keep the slug (CLAUDE.md rule 10).');
        }
    }

    public function saved(Seller $seller): void
    {
        if ($seller->wasChanged(['lat', 'lng']) || $seller->wasRecentlyCreated) {
            $seller->offsetUnset('location');
        }
    }
}
