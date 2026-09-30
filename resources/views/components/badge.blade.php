{{--
    Pill badge (docs/06 "Badges"). Pass a seller type for its semantic colours, or explicit bg/color.
      <x-badge :type="App\Enums\SellerType::Farm" />
      <x-badge bg="#F5E9C8" color="#6E5314">Partner</x-badge>
    Type and Verified badges go in the text block, never on a photo — only x-featured-badge may sit on a photo.
--}}
@props(['type' => null, 'bg' => null, 'color' => null, 'size' => 12])
@php
    [$typeBg, $typeColor] = $type ? $type->badgeColors() : [null, null];
@endphp
<span {{ $attributes->merge(['style' => 'background: '.($bg ?? $typeBg).'; color: '.($color ?? $typeColor).'; font-size: '.$size.'px; font-weight: 600; padding: 3px 9px; border-radius: 999px;']) }}>{{ $slot->isEmpty() ? $type?->label() : $slot }}</span>
