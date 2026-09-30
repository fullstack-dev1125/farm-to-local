<?php

namespace App\Support;

use App\Enums\SellerType;
use App\Models\Seller;

/**
 * Demo photos until seller uploads exist (spatie/laravel-medialibrary, 800×800 crops — outside this slice).
 * Everything returned here is one of the square placeholders shipped in public/assets/photos/.
 */
final class PlaceholderPhoto
{
    /** Named demo sellers → their photo in the design. */
    private const BY_SLUG = [
        'blue-oak-ranch' => 'list-blue-oak-ranch.jpg',
        'hollow-creek-bakehouse' => 'list-hollow-creek-bakehouse.jpg',
        'cedar-bend-nursery' => 'list-cedar-bend-nursery.jpg',
        'two-sisters-cottage-kitchen' => 'list-two-sisters-cottage-kitchen.jpg',
        'hill-country-mobile-processing' => 'list-hill-country-mobile-processing.jpg',
        'stonewall-peach-co' => 'card-stonewall-peach.jpg',
        'wimberley-valley-honey' => 'card-wimberley-honey.jpg',
        'gulf-line-seafood' => 'card-gulf-line.jpg',
        'high-plains-lamb-co' => 'product-half-beef.jpg',
    ];

    /** Everyone else → a square placeholder for their main type. */
    private const BY_TYPE = [
        'farm' => ['product-pastured-eggs.jpg', 'product-whole-chicken.jpg', 'product-ground-beef.jpg', 'share-zucchini.jpg'],
        'bakery' => ['list-hollow-creek-bakehouse.jpg'],
        'home_maker' => ['product-beef-tallow.jpg', 'share-meyer-lemons.jpg'],
        'home_cook' => ['product-bone-broth.jpg'],
        'nursery' => ['share-rosemary.jpg', 'share-fig-tree.jpg'],
        'fisherman' => ['card-gulf-line.jpg'],
        'food_hub' => ['share-pecan.jpg'],
        'service' => ['list-hill-country-mobile-processing.jpg'],
    ];

    public static function url(Seller $seller): string
    {
        $file = self::BY_SLUG[$seller->slug] ?? null;
        if (! $file) {
            $options = self::BY_TYPE[($seller->primaryType() ?? SellerType::Farm)->value];
            $file = $options[$seller->id % count($options)];
        }

        return asset('assets/photos/'.$file);
    }
}
