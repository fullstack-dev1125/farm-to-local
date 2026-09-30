<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

/** The 12 fixed categories (docs/01 "Categories"). Idempotent. */
class CategorySeeder extends Seeder
{
    public const CATEGORIES = [
        ['produce', 'Produce'],
        ['meat-poultry', 'Meat & Poultry'],
        ['eggs-dairy', 'Eggs & Dairy'],
        ['seafood', 'Seafood'],
        ['bakery-sweets', 'Bakery & Sweets'],
        ['pantry-preserves', 'Pantry & Preserves'],
        ['wellness-body', 'Wellness & Body'],
        ['plants-starts', 'Plants & Starts'],
        ['prepared-food', 'Prepared Food & Meals'],
        ['homestead-goods', 'Homestead Goods'],
        ['farm-garden-services', 'Farm & Garden Services'],
        ['farmers-markets', 'Farmers Markets'],   // browse tile only; markets are their own model
    ];

    public function run(): void
    {
        foreach (self::CATEGORIES as $i => [$slug, $name]) {
            Category::updateOrCreate(['slug' => $slug], ['name' => $name, 'icon' => $slug, 'sort' => $i + 1]);
        }
    }
}
