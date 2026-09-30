<?php

use App\Enums\Plan;
use App\Models\Seller;
use App\Models\Tag;
use App\Queries\SellerSearch;

function near78701(int $radius): SellerSearch
{
    return SellerSearch::forZip('78701')->radius($radius);
}

// docs/04 M1, in its own words.
test('the radius query returns Blue Oak Ranch within 25 mi of 78701', function () {
    $names = near78701(25)->get()->pluck('name');

    expect($names)->toContain('Blue Oak Ranch');
});

test('the radius query does NOT return Blue Oak Ranch within 10 mi of 78701', function () {
    $names = near78701(10)->get()->pluck('name');

    expect($names)->not->toContain('Blue Oak Ranch')
        ->and($names)->not->toBeEmpty();   // the query works; Blue Oak is simply too far
});

test('Blue Oak Ranch is placed by real geography, about 21 mi from 78701', function () {
    $blueOak = near78701(50)->get()->firstWhere('slug', 'blue-oak-ranch');

    expect((float) $blueOak->lat)->toBe(30.19)
        ->and((float) $blueOak->lng)->toBe(-98.09)
        ->and($blueOak->miles)->toBeGreaterThan(20)->toBeLessThan(23);
});

test('results order by plan (non-free first), then distance ascending', function () {
    $results = near78701(100)->get();

    $paid = $results->filter(fn (Seller $s) => $s->plan !== Plan::Free)->values();
    $free = $results->filter(fn (Seller $s) => $s->plan === Plan::Free)->values();

    // The seed has paid sellers farther out than free ones, so this ordering is genuinely exercised.
    expect($paid)->not->toBeEmpty()
        ->and($free)->not->toBeEmpty()
        ->and($paid->max('miles'))->toBeGreaterThan($free->min('miles'))
        ->and($results->pluck('id')->all())->toBe($paid->concat($free)->pluck('id')->all())
        ->and($paid->pluck('miles')->all())->toBe($paid->pluck('miles')->sort()->values()->all())
        ->and($free->pluck('miles')->all())->toBe($free->pluck('miles')->sort()->values()->all());
});

test('radius honours 25, 50 and 100', function () {
    $counts = [];
    foreach ([25, 50, 100] as $radius) {
        $results = near78701($radius)->get();
        expect($results->max('miles'))->toBeLessThanOrEqual($radius);
        $counts[$radius] = $results->count();
    }

    // Each step out finds sellers the smaller radius could not.
    expect($counts[25])->toBeLessThan($counts[50])
        ->and($counts[50])->toBeLessThan($counts[100]);

    // Nothing within 100 mi is missed by the bounding box: compare with a brute-force distance over every searchable seller.
    $all = Seller::query()->whereIn('status', ['listed', 'verified'])
        ->selectRaw('id, ST_Distance_Sphere(location, ST_SRID(POINT(-97.7426, 30.2713), 4326)) / 1609.344 AS miles')
        ->get()->filter(fn ($s) => $s->miles <= 100)->pluck('id')->sort()->values()->all();
    expect(near78701(100)->get()->pluck('id')->sort()->values()->all())->toBe($all);
});

test('seeded, suspended and deleted sellers never appear', function () {
    Seller::where('slug', 'cedar-bend-nursery')->first()->delete();
    $results = near78701(100)->get();

    expect($results->pluck('status')->map->value->unique()->sort()->values()->all())->toBe(['listed', 'verified'])
        ->and($results->pluck('name'))->not->toContain('Wimberley Valley Honey')   // seeded, unclaimed
        ->and($results->pluck('name'))->not->toContain('Cedar Bend Nursery');     // soft-deleted
});

test('sellers outside Texas and past the radius are excluded even when paid', function () {
    $results = near78701(100)->get();

    expect($results->pluck('state')->unique()->all())->toBe(['TX'])
        ->and($results->pluck('name'))->not->toContain('Gulf Line Seafood')       // Pro, Port Aransas, ~173 mi
        ->and($results->pluck('name'))->not->toContain('High Plains Lamb Co.');   // Amarillo
});

test('category and verified-only filters narrow the results', function () {
    $meat = near78701(50)->categories(['meat-poultry'])->get();
    expect($meat->pluck('name'))->toContain('Blue Oak Ranch')
        ->and($meat->pluck('name'))->not->toContain('Hollow Creek Bakehouse');

    $verified = near78701(50)->verifiedOnly()->get();
    expect($verified)->not->toBeEmpty()
        ->and($verified->every(fn (Seller $s) => $s->isVerified()))->toBeTrue();
});

test('an unknown ZIP is handled gracefully', function (?string $zip) {
    expect(SellerSearch::forZip($zip))->toBeNull()
        ->and(SellerSearch::forPlace($zip))->toBeNull();
})->with(['unknown ZIP' => '99999', 'letters' => 'abcde', 'too short' => '787', 'empty' => '', 'null' => null, 'injection' => "78701' OR 1=1 --"]);

test('a city name geocodes against cities, then zips', function () {
    expect(SellerSearch::forPlace('Austin, TX')->centre->label)->toBe('Austin, TX 78701')
        ->and(SellerSearch::forPlace('Round Rock')->centre->zip)->toStartWith('786')
        ->and(SellerSearch::forPlace('78701-1234')->centre->zip)->toBe('78701');
});

test('pending tags are never eager-loaded for results (rule 13)', function () {
    $blueOak = Seller::where('slug', 'blue-oak-ranch')->first();
    $pending = Tag::factory()->pending()->create(['name' => 'Wagyu-ish', 'group' => 'meat']);
    $blueOak->tags()->attach($pending);

    $row = near78701(25)->get()->firstWhere('slug', 'blue-oak-ranch');

    expect($row->tags->pluck('name'))->not->toContain('Wagyu-ish')->toContain('Beef');
});
