{{--
    One result row (designs/city-austin.html), following docs/06 "Cards are uniform":
    182px tall; square photo left; badges → name → one-liner → highlight line → awards line (when present) →
    location/distance + how-they-sell chips; actions right.

    docs/06 wins over the design file in three places:
      - the photo is a 96px square thumb (docs/06 "Result rows use a 96px square thumb (desktop)"); the design
        shows 150px. Change $thumb below if the owner confirms the design size instead;
      - no "Partner · code FTL10" badge (docs/06: Partner is "shop header only"); paid rows keep the
        "Paid placement" label;
      - Service provider uses the Fisherman/Service blue, not the design's Local Maker brown.
    Featured is the only badge on the photo. Expects $seller->types and ->tags eager-loaded (SellerSearch).
--}}
@props(['seller'])
@php
    $thumb = 96;
    $type = $seller->primaryType();
    $service = $seller->isService();
    $featured = $seller->isFeatured();
    $href = url('/shop/'.$seller->slug);
    $awards = $seller->awards ?? [];
    $location = collect([$seller->city, $seller->distanceLabel(), $service && $seller->service_radius_mi ? "serves {$seller->service_radius_mi} mi" : null])->filter()->implode(' · ');
    $frame = $featured
        ? 'background: #FFFBF9; border: 1px solid #F0C9B8; border-radius: 18px; padding: 16px; box-shadow: inset 4px 0 0 #D9471F;'
        : 'background: #FFFFFF; border: 1px solid #E4E1DA; border-radius: 18px; padding: 16px;';
@endphp
<a href="{{ $href }}" data-seller="{{ $seller->slug }}" style="display: grid; grid-template-columns: {{ $thumb }}px minmax(0, 1fr) 150px; gap: 18px; align-items: stretch; {{ $frame }} text-decoration: none; color: #1E1B16; min-height: 182px; box-sizing: border-box;">
  <div style="width: {{ $thumb }}px; aspect-ratio: 1 / 1; align-self: start; border-radius: 12px; background-image: url('{{ App\Support\PlaceholderPhoto::url($seller) }}'); background-size: cover; background-position: center; background-color: #F1F0EB; position: relative;">@if ($featured)<x-featured-badge />@endif</div>
  <div style="display: flex; flex-direction: column; min-width: 0;">
    <div style="display: flex; gap: 6px; align-items: center; height: 24px; overflow: hidden; white-space: nowrap;">@if ($type)<x-badge :type="$type" />@endif @if ($seller->isVerified())<x-verified-badge />@endif @if ($seller->permitLabel())<span style="font-size: 12px; color: #6B6357;">{{ $seller->permitLabel() }}</span>@endif @if ($featured)<span style="font-size: 12px; color: #8C7A45;">Paid placement</span>@endif</div>
    <div style="font-family: 'Fraunces', Georgia, serif; font-size: 21px; font-weight: 600; line-height: 1.2; margin-top: 8px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $seller->name }}</div>
    <div style="font-size: 14px; color: #5C554A; line-height: 1.45; margin-top: 4px; height: 40px; overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">{{ $seller->description }}</div>
    <div style="display: flex; gap: 6px; margin-top: 8px; height: 22px; overflow: hidden; white-space: nowrap;">@if ($seller->promo_text)<span style="font-size: 12px; {{ $service ? 'color: #23516B; background: #DDE8EE;' : 'color: #24512F; background: #E4EFE3;' }} padding: 2px 8px; border-radius: 6px;">{{ $seller->promo_text }}</span>@endif</div>
    <div style="display: flex; gap: 6px; margin-top: 6px; height: 22px; overflow: hidden; flex-wrap: wrap; align-content: flex-start;">@foreach ($awards as $award)<span style="display: inline-flex; align-items: center; gap: 5px; color: #6E5314; background: #F5E9C8; font-size: 12px; font-weight: 600; padding: 2px 8px; border-radius: 6px; white-space: nowrap;"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M8 21h8M12 17v4M6 3h12v5a6 6 0 0 1-12 0z"/><path d="M6 5H3v2a3 3 0 0 0 3 3M18 5h3v2a3 3 0 0 1-3 3"/></svg>{{ $award }}</span>@endforeach</div>
    <div style="display: flex; gap: 12px; align-items: center; font-size: 13px; color: #5C554A; margin-top: auto; white-space: nowrap; overflow: hidden;"><span style="display: inline-flex; gap: 5px; align-items: center; flex-shrink: 0;"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s7-7.1 7-12a7 7 0 1 0-14 0c0 4.9 7 12 7 12z"></path><circle cx="12" cy="10" r="2.5"></circle></svg>{{ $location }}</span><span style="display: inline-flex; gap: 6px; flex-wrap: wrap; height: 22px; overflow: hidden; align-content: flex-start;">@foreach ($seller->buyChips() as $chip)<x-chip>{{ $chip }}</x-chip>@endforeach</span></div>
  </div>
  <div style="display: flex; flex-direction: column; gap: 8px; justify-content: center;"><span style="background: #2F6B3F; color: #FFFFFF; height: 40px; width: 150px; border-radius: 999px; font-size: 14px; font-weight: 600; display: flex; align-items: center; justify-content: center; box-sizing: border-box;">{{ $service ? 'View services' : 'View shop' }}</span><span style="border: 1px solid #E4E1DA; color: #1E1B16; height: 40px; width: 150px; border-radius: 999px; font-size: 14px; font-weight: 600; display: flex; align-items: center; justify-content: center; box-sizing: border-box;">{{ $service ? 'Request booking' : 'Message' }}</span></div>
</a>
