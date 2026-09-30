<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<City>
 */
class CityFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->city();

        return [
            'slug' => Str::slug($name),
            'name' => $name,
            'state' => State::factory(),
            'lat' => fake()->latitude(26, 36),
            'lng' => fake()->longitude(-106, -94),
            'radius_mi' => 50,
        ];
    }

    public function published(): static
    {
        return $this->afterMaking(fn (City $c) => $c->forceFill(['published' => true]));
    }
}
