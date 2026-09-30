<?php

namespace App\Enums;

/**
 * Seller types (docs/01 "Seller types"). Farmers markets, neighbor shares and national retailers are
 * separate models, never a seller type (CLAUDE.md rule 1).
 */
enum SellerType: string
{
    case Farm = 'farm';
    case Fisherman = 'fisherman';
    case HomeMaker = 'home_maker';
    case Bakery = 'bakery';
    case HomeCook = 'home_cook';
    case Nursery = 'nursery';
    case FoodHub = 'food_hub';
    case Service = 'service';

    /** Badge text (docs/01). */
    public function label(): string
    {
        return match ($this) {
            self::Farm => 'Farm',
            self::Fisherman => 'Fisherman',
            self::HomeMaker => 'Homemade',
            self::Bakery => 'Local Maker',
            self::HomeCook => 'Home Kitchen',
            self::Nursery => 'Nursery',
            self::FoodHub => 'Food Hub',
            self::Service => 'Service provider',
        };
    }

    /**
     * Badge [background, text] (docs/06 "Badges"). Food Hub has no row in docs/06; it uses the Farm colours
     * because a hub sells named farms' produce.
     *
     * @return array{0: string, 1: string}
     */
    public function badgeColors(): array
    {
        return match ($this) {
            self::Farm, self::Nursery, self::FoodHub => ['#2F8F4E', '#FFFFFF'],
            self::Bakery => ['#EEDFC8', '#7A4B2A'],
            self::Fisherman, self::Service => ['#DDE8EE', '#23516B'],
            self::HomeMaker, self::HomeCook => ['#E9E1EE', '#5B3E6E'],
        };
    }

    /** Map pin colour (docs/06 "Maps"; legend in designs/city-austin.html). */
    public function pinColor(): string
    {
        return match ($this) {
            self::Farm, self::Nursery, self::FoodHub => '#2F6B3F',
            self::Bakery => '#7A4B2A',
            self::Fisherman, self::Service => '#23516B',
            self::HomeMaker, self::HomeCook => '#5B3E6E',
        };
    }

    public function isService(): bool
    {
        return $this === self::Service;
    }
}
