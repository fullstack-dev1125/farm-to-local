<?php

use App\Models\City;
use App\Models\Seller;
use Illuminate\Support\Facades\DB;

test('GET /texas/austin returns 200 and contains distinctive strings from designs/city-austin.html', function () {
    $this->get('/texas/austin')
        ->assertOk()
        ->assertSee('sellers within 50 mi of Austin')
        ->assertSee('Featured near Austin')
        ->assertSee('Hover or tap a pin · click to open')
        ->assertSee('Sell in Austin?')
        ->assertSee('Blue Oak Ranch');
});

test('the page lists paid sellers first, then free sellers by distance', function () {
    $html = $this->get('/texas/austin?per_page=50')->assertOk()->getContent();
    preg_match_all('/data-seller="([^"]+)"/', $html, $m);

    $sellers = Seller::whereIn('slug', $m[1])->get()->keyBy('slug');
    $paidFlags = array_map(fn ($slug) => $sellers[$slug]->isFeatured(), $m[1]);

    expect($paidFlags)->toContain(true)->toContain(false)
        ->and($paidFlags)->toBe(collect($paidFlags)->sortDesc()->values()->all());
});

test('radius honours 25, 50 and 100 on the page', function () {
    $totals = [];
    foreach ([25, 50, 100] as $radius) {
        $html = $this->get("/texas/austin?radius={$radius}")->assertOk()
            ->assertSee("within {$radius} mi of Austin")
            ->assertSee("{$radius} mi around 78701")
            ->getContent();
        preg_match('/(\d+) sellers? within/', $html, $m);
        $totals[$radius] = (int) $m[1];
    }

    expect($totals[25])->toBeLessThan($totals[50])->and($totals[50])->toBeLessThan($totals[100]);
});

test('an unsupported radius or junk query falls back to defaults', function () {
    $this->get('/texas/austin?radius=7')->assertOk()->assertSee('within 50 mi of Austin');
    $this->get('/texas/austin?radius=abc&sort=zzz&cat=nope&per_page=999&verified[]=x')->assertOk()->assertSee('within 50 mi of Austin');
});

test('an unknown ZIP is handled gracefully on the page', function () {
    $this->get('/texas/austin?zip=99999')
        ->assertOk()
        ->assertSee('We couldn&#039;t find ZIP 99999 — showing Austin.', false)
        ->assertSee('within 50 mi of Austin');

    $this->get('/texas/austin?zip='.urlencode('<script>alert(1)</script>'))
        ->assertOk()
        ->assertDontSee('<script>alert(1)</script>', false);
});

test('a known ZIP recentres the search', function () {
    $this->get('/texas/austin?zip=78620')
        ->assertOk()
        ->assertSee('Dripping Springs, TX 78620')
        ->assertSee('within 50 mi of 78620');
});

test('the empty state renders when nothing matches', function () {
    $this->get('/texas/austin?cat=homestead-goods&radius=25')
        ->assertOk()
        ->assertSee('No results')
        ->assertSee('No homestead goods sellers within 25 miles of 78701 yet.')
        ->assertSee('Tell me when someone lists')
        ->assertSee('Why not show far-away sellers anyway?')
        ->assertSee('Everything local near Austin')
        ->assertDontSee('data-seller=', false);
});

test('no sponsor markup appears anywhere in the results HTML', function () {
    // A live, region-targeted sponsor exists; it must still never reach search results (rule 2).
    DB::table('sponsors')->insert([
        'name' => 'Acme Chest Freezers', 'description' => 'Freezers for bulk beef', 'url' => 'https://example.com',
        'placement' => 'region', 'region' => 'Austin', 'active' => true, 'created_at' => now(), 'updated_at' => now(),
    ]);

    foreach (['/texas/austin', '/texas/austin?radius=100&per_page=50', '/texas/austin?cat=homestead-goods&radius=25'] as $url) {
        $this->get($url)->assertOk()
            ->assertDontSee('Acme Chest Freezers')
            ->assertDontSee('Sponsored')
            ->assertDontSee('National retailer')
            ->assertDontSee('Not a farm')
            ->assertDontSee('data-sponsor', false);
    }
});

test('the results page runs no N+1 queries', function () {
    $small = countQueries(fn () => $this->get('/texas/austin?per_page=10')->assertOk());
    $large = countQueries(fn () => $this->get('/texas/austin?per_page=50')->assertOk());

    // 10 rows and 33 rows cost the same number of queries: relations are eager-loaded, not fetched per row.
    expect($large)->toBe($small)->and($small)->toBeLessThanOrEqual(12);
});

test('unknown or unpublished places return 404', function () {
    $this->get('/texas/nowhere')->assertNotFound();
    $this->get('/ohio/austin')->assertNotFound();

    City::factory()->create(['state' => 'TX', 'slug' => 'waco', 'name' => 'Waco']);   // not published
    $this->get('/texas/waco')->assertNotFound();
});

test('the page shows docs/06 badges and keeps Featured the only badge on a photo', function () {
    $html = $this->get('/texas/austin')->assertOk()->getContent();

    expect($html)->toContain('background: #2563EB')   // Verified
        ->toContain('background: #D9471F')              // Featured
        ->not->toContain('Partner · code FTL10');       // Partner badge is shop-header only

    // Inside every result photo, the only badge is Featured.
    preg_match_all('/aspect-ratio: 1 \/ 1;[^>]*>(.*?)<\/div>/s', $html, $photos);
    expect($photos[1])->not->toBeEmpty();
    foreach ($photos[1] as $inner) {
        expect(trim(strip_tags($inner)))->toBeIn(['', 'Featured']);
    }
});

test('home cooks show their permit disclosure (rule 4)', function () {
    $html = $this->get('/texas/austin?cat=prepared-food&per_page=50')->assertOk()->getContent();

    expect(str_contains($html, '>Permitted<') || str_contains($html, 'Home kitchen · no permit on file'))->toBeTrue();
});
