<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'phone' => '+1512'.fake()->unique()->numerify('#######'),
            'email' => fake()->unique()->safeEmail(),
            'phone_verified_at' => now(),
            'home_zip' => '78701',
        ];
    }
}
