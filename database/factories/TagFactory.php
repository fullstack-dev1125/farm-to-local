<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Tag>
 */
class TagFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return ['slug' => Str::slug($name), 'name' => Str::ucfirst($name), 'group' => 'practice', 'requires_document' => false];
    }

    public function pending(): static
    {
        return $this->afterMaking(fn (Tag $t) => $t->forceFill(['status' => 'pending']));
    }
}
