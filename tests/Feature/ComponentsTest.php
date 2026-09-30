<?php

use App\Models\Category;
use Illuminate\Support\Facades\Blade;

// Components built for later pages (they are not on the results design) still render correctly.
test('category-tile renders name, count and link', function () {
    $html = Blade::render('<x-category-tile :category="$c" href="/texas/austin?cat=produce" count="41 near you" />', ['c' => Category::where('slug', 'produce')->first()]);

    expect($html)->toContain('Produce')->toContain('41 near you')->toContain('href="/texas/austin?cat=produce"');
});

test('category-tile never uses the retired pink tint (docs/06)', function () {
    foreach (Category::all() as $category) {
        expect(Blade::render('<x-category-tile :category="$c" href="#" />', ['c' => $category]))->not->toContain('#F8E5DA');
    }
});

test('cta-zip is a GET form that submits zip', function () {
    $html = Blade::render('<x-cta-zip action="/search" />');

    expect($html)->toContain('method="get"')->toContain('action="/search"')->toContain('name="zip"');
});

test('header underlines the active nav item', function () {
    $html = Blade::render('<x-site.header active="share" />');

    expect($html)->toContain('aria-current="page">Neighbor Share');
});

test('footer follows docs/06: four columns on forest green, directory line', function () {
    $html = Blade::render('<x-site.footer />');

    expect($html)->toContain('background: #1F3D2B')->toContain('repeat(4, minmax(0, 1fr))')
        ->toContain('A directory, not a marketplace.')
        ->toContain('Report a listing');
});
