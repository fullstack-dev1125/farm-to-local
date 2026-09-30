{{--
    Category tile with icon and count (designs/city-austin-overview.html "categories with counts").
    Not used on the results page — the results design has no tiles; built for the city/state/home pages (M2).
      <x-category-tile :category="$category" :href="$url" count="41 near you" />
    Icons and colours are the overview's, except where it used the pink #F8E5DA/#8A3A1B that docs/06 retires
    outside the "Low" chip (Meat & Poultry, Prepared Food) — those take the Local Maker pair instead.
--}}
@props(['category', 'href', 'count' => null])
@php
    $icons = [
        'produce' => ['#E4EFE3', '#24512F', '<path d="M12 6c-4-1-8 2-8 7 0 4 3 8 6 8 1 0 1.5-.5 2-.5s1 .5 2 .5c3 0 6-4 6-8 0-5-4-8-8-7z"></path><path d="M12 6c0-2 1-3 3-4"></path>'],
        'meat-poultry' => ['#EEDFC8', '#7A4B2A', '<path d="M15.5 3a5.5 5.5 0 0 1 3.9 9.4l-7.1 7.1-3.8-3.8 7.1-7.1A5.5 5.5 0 0 1 15.5 3z"></path><path d="M8.5 15.7l-2.2 2.2-1.3 3.1 3.1-1.3 2.2-2.2"></path>'],
        'eggs-dairy' => ['#F5E9C8', '#6E5314', '<path d="M12 3c-4 0-7 6-7 11a7 7 0 0 0 14 0c0-5-3-11-7-11z"></path>'],
        'seafood' => ['#DDE8EE', '#23516B', '<path d="M3 12s4-6 10-6c4 0 8 6 8 6s-4 6-8 6c-6 0-10-6-10-6z"></path><path d="M21 12l-4-3v6z"></path><circle cx="8.5" cy="11.5" r="1"></circle>'],
        'bakery-sweets' => ['#EEDFC8', '#7A4B2A', '<path d="M5 11a3.5 3.5 0 0 1 0-7h14a3.5 3.5 0 0 1 0 7v8a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1z"></path><path d="M9.5 4v16M14.5 4v16"></path>'],
        'pantry-preserves' => ['#F5E9C8', '#6E5314', '<rect x="6" y="7" width="12" height="14" rx="2"></rect><path d="M8 7V4h8v3"></path><path d="M6 12h12"></path>'],
        'wellness-body' => ['#E9E1EE', '#5B3E6E', '<path d="M12 3s6 6 6 11a6 6 0 0 1-12 0c0-5 6-11 6-11z"></path>'],
        'plants-starts' => ['#E4EFE3', '#24512F', '<path d="M12 22V10"></path><path d="M12 10c0-4 3-6 7-6 0 4-3 6-7 6z"></path><path d="M12 14c0-3-2-5-6-5 0 3 2 5 6 5z"></path>'],
        'prepared-food' => ['#EEDFC8', '#7A4B2A', '<path d="M3 11h18a9 9 0 0 1-18 0z"></path><path d="M8 7c0-2 1-3 1-3M12 7c0-2 1-3 1-3M16 7c0-2 1-3 1-3"></path>'],
        'farmers-markets' => ['#DDE8EE', '#23516B', '<path d="M3 10l2-5h14l2 5"></path><path d="M3 10v2a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0v-2"></path><path d="M5 13v8h14v-8"></path><path d="M10 21v-5h4v5"></path>'],
        'homestead-goods' => ['#EEDFC8', '#7A4B2A', '<path d="M14 4l6 6-2.5 2.5-6-6z"></path><path d="M11.5 6.5L4 14l6 6 7.5-7.5"></path><path d="M3 21l3-3"></path>'],
        'farm-garden-services' => ['#DDE8EE', '#23516B', '<path d="M12 3v6"></path><path d="M8 9h8v3a4 4 0 0 1-8 0z"></path><path d="M12 16v5"></path><path d="M5 21h14"></path>'],
    ];
    [$bg, $fg, $paths] = $icons[$category->slug] ?? $icons['produce'];
@endphp
<a href="{{ $href }}" {{ $attributes->merge(['style' => 'display: flex; flex-direction: column; gap: 14px; padding: 20px; background: #FFFFFF; border: 1px solid #E4E1DA; border-radius: 16px; text-decoration: none; color: #1E1B16;']) }}>
  <span style="width: 44px; height: 44px; border-radius: 12px; background: {{ $bg }}; color: {{ $fg }}; display: flex; align-items: center; justify-content: center;"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $paths !!}</svg></span>
  <span style="display: flex; flex-direction: column; gap: 2px;"><span style="font-weight: 600; font-size: 15px;">{{ $category->name }}</span>@if ($count)<span style="font-size: 13px; color: #5C554A;">{{ $count }}</span>@endif</span>
</a>
