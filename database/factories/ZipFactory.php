<?php

namespace Database\Factories;

use App\Models\Zip;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Zip>
 */
class ZipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'zip' => fake()->unique()->numerify('7####'),
            'lat' => fake()->latitude(26, 36),
            'lng' => fake()->longitude(-106, -94),
            'city' => fake()->city(),
            'state' => 'TX',
            'county' => fake()->lastName(),
        ];
    }
}
