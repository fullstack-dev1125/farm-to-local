<?php

namespace App\Queries;

use App\Enums\SellerStatus;
use App\Models\City;
use App\Models\Seller;
use App\Models\Zip;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * ZIP-radius seller search (docs/02 "Geo query").
 *
 *   SELECT s.*, ST_Distance_Sphere(location, ST_SRID(POINT(:lng,:lat),4326)) / 1609.344 AS miles
 *   FROM sellers s
 *   WHERE status IN ('listed','verified')
 *     AND lat BETWEEN :lat - :dlat AND :lat + :dlat
 *     AND lng BETWEEN :lng - :dlng AND :lng + :dlng
 *   HAVING miles <= :radius
 *   ORDER BY (plan <> 'free') DESC, miles ASC
 *
 * dlat = radius / 69, dlng = radius / (69 * cos(radians(lat))).
 *
 * Only sellers are returned. Sponsors are a separate model and are never joined here (CLAUDE.md rule 2).
 */
final class SellerSearch
{
    /** Radii the results page offers (?radius=25|50|100, docs/03). */
    public const RADII = [25, 50, 100];

    public const DEFAULT_RADIUS = 50;

    public const SORTS = ['distance', 'updated', 'saved', 'name'];

    private int $radius = self::DEFAULT_RADIUS;

    /** @var list<string> category slugs */
    private array $categories = [];

    /** @var list<string> */
    private array $buyMethods = [];

    private bool $verifiedOnly = false;

    private bool $includeShares = false;

    private bool $openThisWeekend = false;

    private string $sort = 'distance';

    private function __construct(public readonly SearchCentre $centre) {}

    public static function around(SearchCentre $centre): self
    {
        return new self($centre);
    }

    /** Resolve a ZIP to its centroid. Unknown or malformed ZIPs return null; they never throw. */
    public static function forZip(?string $zip): ?self
    {
        $centre = self::geocodeZip($zip);

        return $centre ? new self($centre) : null;
    }

    /** A ZIP, or a city name ("Austin" / "Austin, TX"): cities first, then the zips table. Null if unknown. */
    public static function forPlace(?string $input): ?self
    {
        $centre = self::geocode($input);

        return $centre ? new self($centre) : null;
    }

    public static function geocode(?string $input): ?SearchCentre
    {
        $input = trim((string) $input);
        if ($input === '') {
            return null;
        }
        if (preg_match('/^\d{5}(-\d{4})?$/', $input)) {
            return self::geocodeZip($input);
        }

        [$name, $state] = array_pad(array_map('trim', explode(',', $input, 2)), 2, null);
        $state = $state !== null && preg_match('/^[A-Za-z]{2}$/', $state) ? strtoupper($state) : null;

        $city = City::query()
            ->where('name', $name)
            ->when($state, fn (Builder $q) => $q->where('state', $state))
            ->orderByDesc('published')
            ->first();
        if ($city) {
            return self::cityCentre($city);
        }

        $zip = Zip::query()
            ->where('city', $name)
            ->when($state, fn (Builder $q) => $q->where('state', $state))
            ->orderBy('zip')
            ->first();

        return $zip ? new SearchCentre((float) $zip->lat, (float) $zip->lng, "{$zip->city}, {$zip->state} {$zip->zip}", $zip->zip) : null;
    }

    public static function geocodeZip(?string $zip): ?SearchCentre
    {
        if (! is_string($zip) || ! preg_match('/^(\d{5})(-\d{4})?$/', trim($zip), $m)) {
            return null;
        }
        $row = Zip::find($m[1]);

        return $row ? new SearchCentre((float) $row->lat, (float) $row->lng, trim("{$row->city}, {$row->state} {$row->zip}"), $row->zip) : null;
    }

    /** City centre, labelled with the nearest ZIP ("Austin, TX 78701") when the ZIP table has one close by. */
    public static function cityCentre(City $city): SearchCentre
    {
        $lat = (float) $city->lat;
        $lng = (float) $city->lng;
        $nearest = Zip::query()
            ->where('state', $city->state)
            ->whereBetween('lat', [$lat - 0.1, $lat + 0.1])
            ->whereBetween('lng', [$lng - 0.1, $lng + 0.1])
            ->orderByRaw('POW(lat - ?, 2) + POW(lng - ?, 2)', [$lat, $lng])
            ->value('zip');

        return new SearchCentre($lat, $lng, trim("{$city->name}, {$city->state} {$nearest}"), $nearest);
    }

    // ---- Filters (docs/02: categories, buy methods, verified_only, include_shares, open_this_weekend, sort) ----

    /**
     * Any radius from 1 to 500 mi. The public page only offers RADII (enforced in CitySearchRequest);
     * internal callers such as the empty state and tests may use others.
     */
    public function radius(int $miles): self
    {
        $this->radius = max(1, min(500, $miles));

        return $this;
    }

    /** @param  list<string>|string|null  $slugs */
    public function categories(array|string|null $slugs): self
    {
        $this->categories = array_values(array_filter((array) $slugs, fn ($s) => is_string($s) && $s !== ''));

        return $this;
    }

    /**
     * TODO(M2): farm stand / market / CSA / delivery / ships / pre-order / U-pick. Needs seller_hours and
     * market_attendances models, which are outside this slice. Stored but not yet applied to the query.
     *
     * @param  list<string>  $methods
     */
    public function buyMethods(array $methods): self
    {
        $this->buyMethods = $methods;

        return $this;
    }

    public function verifiedOnly(bool $on = true): self
    {
        $this->verifiedOnly = $on;

        return $this;
    }

    /** TODO(M4): neighbor shares are merged into results by ShareSearch when on. Not applied yet. */
    public function includeShares(bool $on = true): self
    {
        $this->includeShares = $on;

        return $this;
    }

    /** TODO(M2): needs seller_hours (stand/pickup days) and market_attendances. Not applied yet. */
    public function openThisWeekend(bool $on = true): self
    {
        $this->openThisWeekend = $on;

        return $this;
    }

    /**
     * Paid placement always comes first (Pro/Partner "top of regional results", docs/01); the sort picks
     * the order within each group. "saved" falls back to distance until saved_sellers is modelled (M3).
     */
    public function sort(?string $sort): self
    {
        $this->sort = in_array($sort, self::SORTS, true) ? $sort : 'distance';

        return $this;
    }

    public function radiusMiles(): int
    {
        return $this->radius;
    }

    public function sortKey(): string
    {
        return $this->sort;
    }

    // ---- Execution ----

    public function query(): Builder
    {
        $lat = $this->centre->lat;
        $lng = $this->centre->lng;
        $dlat = $this->radius / 69;
        $dlng = $this->radius / (69 * cos(deg2rad($lat)));

        $query = Seller::query()
            ->select('sellers.*')
            ->selectRaw('ST_Distance_Sphere(location, ST_SRID(POINT(?, ?), 4326)) / 1609.344 AS miles', [$lng, $lat])
            ->whereIn('status', $this->verifiedOnly ? [SellerStatus::Verified->value] : SellerStatus::searchable())
            ->whereBetween('lat', [$lat - $dlat, $lat + $dlat])
            ->whereBetween('lng', [$lng - $dlng, $lng + $dlng])
            ->having('miles', '<=', $this->radius)
            ->when($this->categories, fn (Builder $q) => $q->whereHas(
                'categories', fn (Builder $c) => $c->whereIn('slug', $this->categories),
            ))
            ->orderByRaw("(plan <> 'free') DESC");

        match ($this->sort) {
            'updated' => $query->orderByDesc('updated_at')->orderBy('miles'),
            'name' => $query->orderBy('name'),
            default => $query->orderBy('miles'),
        };

        // Stable tiebreak so pagination never repeats or skips a row.
        return $query->orderBy('sellers.id')->with($this->eagerLoads());
    }

    /** @return Collection<int, Seller> */
    public function get(): Collection
    {
        return $this->query()->get();
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->query()->paginate($perPage)->withQueryString();
    }

    public function count(): int
    {
        return $this->query()->toBase()->getCountForPagination();
    }

    /** Everything a results row renders (x-seller-row), so a page of results is a fixed number of queries. */
    private function eagerLoads(): array
    {
        return [
            'types',
            'tags' => fn (BelongsToMany $q) => $q->where('tags.status', 'approved')->orderBy('tags.id'),
        ];
    }
}
