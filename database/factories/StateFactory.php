<?php

namespace Database\Factories;

use App\Models\State;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<State>
 */
class StateFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->state();

        return ['code' => fake()->unique()->stateAbbr(), 'name' => $name, 'slug' => Str::slug($name), 'launch' => false];
    }
}
