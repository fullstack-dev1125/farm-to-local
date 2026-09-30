<?php

use App\Enums\Plan;
use App\Enums\SellerStatus;
use App\Enums\SellerType;

it('uses the docs/06 badge colours for seller types', function (SellerType $type, string $bg, string $fg) {
    expect($type->badgeColors())->toBe([$bg, $fg]);
})->with([
    [SellerType::Farm, '#2F8F4E', '#FFFFFF'],
    [SellerType::Nursery, '#2F8F4E', '#FFFFFF'],
    [SellerType::Bakery, '#EEDFC8', '#7A4B2A'],
    [SellerType::Fisherman, '#DDE8EE', '#23516B'],
    [SellerType::Service, '#DDE8EE', '#23516B'],
    [SellerType::HomeMaker, '#E9E1EE', '#5B3E6E'],
    [SellerType::HomeCook, '#E9E1EE', '#5B3E6E'],
]);

it('only searches listed and verified sellers', function () {
    expect(SellerStatus::searchable())->toBe(['listed', 'verified']);
});

it('treats Pro and Partner as paid placement', function () {
    expect(Plan::Free->isPaid())->toBeFalse()
        ->and(Plan::Pro->isPaid())->toBeTrue()
        ->and(Plan::Partner->isPaid())->toBeTrue();
});
