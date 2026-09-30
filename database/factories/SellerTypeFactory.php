<?php

namespace Database\Factories;

use App\Enums\SellerType as SellerTypeEnum;
use App\Models\Seller;
use App\Models\SellerType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SellerType>
 */
class SellerTypeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'seller_id' => Seller::factory(),
            'type' => fake()->randomElement(SellerTypeEnum::cases()),
            'is_primary' => true,
        ];
    }
}
