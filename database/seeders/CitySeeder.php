<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

/**
 * Austin only for this slice. The centre is the 78701 (downtown) ZIP centroid, which is what the
 * design's "Austin, TX 78701" / "50 mi around 78701" labels refer to.
 */
class CitySeeder extends Seeder
{
    public function run(): void
    {
        $austin = City::updateOrCreate(
            ['state' => 'TX', 'slug' => 'austin'],
            ['name' => 'Austin', 'lat' => 30.271300, 'lng' => -97.742600, 'radius_mi' => 50],
        );
        $austin->forceFill(['published' => true])->save();
    }
}
