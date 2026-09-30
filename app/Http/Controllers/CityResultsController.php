<?php

namespace App\Http\Controllers;

use App\Enums\SellerStatus;
use App\Http\Requests\CitySearchRequest;
use App\Models\Category;
use App\Models\City;
use App\Models\Seller;
use App\Models\State;
use App\Models\Zip;
use App\Queries\SearchCentre;
use App\Queries\SellerSearch;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * GET /{state}/{city} — designs/city-austin.html, or designs/city-austin-empty.html when nothing matches.
 *
 * Out of scope for this slice (docs/04 M2 / M6): the 1-hour geography cache, the "coming soon" page for
 * unpublished cities (they 404 here) and city-category pages.
 */
class CityResultsController extends Controller
{
    public function __invoke(CitySearchRequest $request, string $state, string $city): View
    {
        $state = State::where('slug', $state)->firstOrFail();
        $city = City::where('state', $state->code)->where('slug', $city)->where('published', true)->firstOrFail();

        [$centre, $zipNotice] = $this->centre($city, $request->validated('zip'));

        $radius = (int) $request->validated('radius');
        $cat = $request->validated('cat');
        $verifiedOnly = (bool) $request->validated('verified');
        $sort = $request->validated('sort');
        $perPage = (int) $request->validated('per_page');

        $search = $this->search($centre, $radius, $cat, $verifiedOnly, $sort);
        $sellers = $search->paginate($perPage);

        $shared = [
            'state' => $state,
            'city' => $city,
            'centre' => $centre,
            'zipNotice' => $zipNotice,
            'radius' => $radius,
            'radii' => SellerSearch::RADII,
            'category' => $cat ? Category::where('slug', $cat)->first() : null,
            'cat' => $cat,
            'verifiedOnly' => $verifiedOnly,
            'sort' => $sort,
            'perPage' => $perPage,
            'pageUrl' => url("/{$state->slug}/{$city->slug}"),
            // "…within 50 mi of Austin", or of the ZIP when ?zip= recentred the search.
            'placeName' => $request->validated('zip') && $zipNotice === null ? $centre->zip : $city->name,
            // A recognised ?zip= rides along on every filter change; an unknown one is dropped after its notice.
            'zipQuery' => $request->validated('zip') && $zipNotice === null ? $centre->zip : null,
        ];

        if ($sellers->total() === 0) {
            return view('results.empty', $shared + $this->emptyState($shared, $centre, $radius, $cat, $verifiedOnly));
        }

        return view('results.index', $shared + [
            'sellers' => $sellers,
            'categories' => Category::orderBy('sort')->get(),
            'featured' => $sellers->getCollection()->filter->isFeatured()->take(4)->values(),
        ]);
    }

    /** City centre by default; ?zip= recentres on a known ZIP. Unknown ZIPs fall back with a notice. */
    private function centre(City $city, ?string $zip): array
    {
        $cityCentre = SellerSearch::cityCentre($city);
        if ($zip === null) {
            return [$cityCentre, null];
        }

        $zipCentre = SellerSearch::geocodeZip($zip);

        return $zipCentre
            ? [$zipCentre, null]
            : [$cityCentre, "We couldn't find ZIP {$zip} — showing {$city->name}."];
    }

    private function search(SearchCentre $centre, int $radius, ?string $cat, bool $verifiedOnly, string $sort): SellerSearch
    {
        return SellerSearch::around($centre)
            ->radius($radius)
            ->categories($cat)
            ->verifiedOnly($verifiedOnly)
            ->sort($sort);
    }

    /** Data for designs/city-austin-empty.html: widen, what ships here, everything local. */
    private function emptyState(array $shared, SearchCentre $centre, int $radius, ?string $cat, bool $verifiedOnly): array
    {
        $query = fn (array $overrides) => $shared['pageUrl'].'?'.http_build_query(array_filter(
            $overrides + ['cat' => $cat, 'radius' => $radius, 'verified' => $verifiedOnly ? 1 : null, 'zip' => $centre->zip],
        ));

        $widest = max(SellerSearch::RADII);
        $widen = null;
        if ($radius < $widest) {
            $count = $this->search($centre, $widest, $cat, $verifiedOnly, 'distance')->count();
            $widen = $count > 0 ? ['radius' => $widest, 'count' => $count, 'url' => $query(['radius' => $widest])] : null;
        }

        $everything = null;
        $local = collect();
        $allLocal = $this->search($centre, $radius, null, $verifiedOnly, 'distance');
        if ($cat) {
            $everything = ['count' => $allLocal->count(), 'url' => $query(['cat' => null])];
        }
        if (! $cat || $everything['count'] > 0) {
            $local = $allLocal->query()->limit(3)->get();
        }

        $shipsHere = Seller::query()
            ->select('sellers.*')
            ->selectRaw('ST_Distance_Sphere(location, ST_SRID(POINT(?, ?), 4326)) / 1609.344 AS miles', [$centre->lng, $centre->lat])
            ->whereIn('status', SellerStatus::searchable())
            ->where('ships', true)
            ->where(fn (Builder $q) => $q->where('ships_scope', 'nationwide')
                ->orWhereJsonContains('ships_states', $shared['state']->code))
            ->when($cat, fn (Builder $q) => $q->whereHas('categories', fn (Builder $c) => $c->where('slug', $cat)))
            ->with('types');

        $zipRow = $centre->zip ? Zip::find($centre->zip) : null;
        $categoryLower = $shared['category'] ? Str::lower($shared['category']->name) : null;

        return [
            'widen' => $widen,
            'everything' => $everything,
            'local' => $local,
            'shipCount' => (clone $shipsHere)->toBase()->getCountForPagination(),
            'shippers' => $shipsHere->orderBy('miles')->limit(3)->get(),
            'categoryLower' => $categoryLower,
            'placeLine' => trim("{$shared['city']->name}, {$shared['state']->code}".($zipRow?->county ? " · {$zipRow->county} County" : '')),
            'emptyPhoto' => $cat === 'seafood' ? 'card-gulf-line.jpg' : 'hero-market.jpg',
        ];
    }
}
