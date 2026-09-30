{{--
    /{state}/{city} results — reproduces designs/city-austin.html.
    Differences from the design file, each required by docs/06 or CLAUDE.md (listed in TEST-TASK.md):
      - no "Sponsored" card in the sidebar (rule 2: sponsored placements never appear in search results);
      - neighbor-share and farmers-market rows, and the "+ 7 markets · 11 neighbor shares" count, are absent:
        shares and markets are separate models outside this slice (M4 / M2);
      - "Featured near Austin" lists the Pro/Partner sellers from these results (featured_slots is M6).
--}}
@extends('layouts.site')

@section('title', "Farm to Local — {$city->name} results")
@section('page', 'city-results')

@php($activeNav = 'browse')

@section('content')
<div style="display: flex; align-items: center; gap: 8px; padding: 0 64px; height: 44px; font-size: 14px; color: #5C554A;">
  <a href="{{ url('/') }}" style="color: #5C554A; text-decoration: none;">Home</a><span>›</span><a href="{{ url('/'.$state->slug) }}" style="color: #5C554A; text-decoration: none;">{{ $state->name }}</a><span>›</span><a href="{{ $pageUrl }}" style="color: #5C554A; text-decoration: none;">{{ $city->name }}</a><span>›</span><span style="color: #1E1B16; font-weight: 600;">{{ $category?->name ?? 'All sellers' }}</span>
  <span style="margin-left: auto; font-size: 13px; color: #6B6357;">farmtolocal.com/{{ $state->slug }}/{{ $city->slug }}/</span>
</div>

@include('results._filters')

<div style="display: flex; gap: 24px; padding: 24px 64px 40px; align-items: flex-start;">
  <div style="flex: 1 1 0; display: flex; flex-direction: column; gap: 14px;">
    <div style="display: flex; justify-content: space-between; align-items: baseline; padding: 0 4px;">
      <h1 style="margin: 0; font-family: 'Fraunces', Georgia, serif; font-size: 26px; font-weight: 600; letter-spacing: -0.01em;">{{ $sellers->total() }} {{ Str::plural('seller', $sellers->total()) }} within {{ $radius }} mi of {{ $city->name }}</h1>
      <div style="display: flex; align-items: center; gap: 16px; flex-shrink: 0;">@if ($zipNotice)<span role="status" style="font-size: 14px; color: #5C554A; white-space: nowrap;">{{ $zipNotice }}</span>@endif<label style="display: flex; align-items: center; gap: 8px; font-size: 14px; color: #5C554A;"><span>Sort</span><select data-sort style="border: 1px solid #E4E1DA; border-radius: 999px; background: #FFFFFF; height: 40px; padding: 0 12px; font-size: 14px; font-family: inherit; color: #1E1B16;">@foreach (['distance' => 'Distance', 'updated' => 'Recently updated', 'saved' => 'Most saved', 'name' => 'A–Z'] as $value => $label)<option value="{{ $value }}"@selected($sort === $value)>{{ $label }}</option>@endforeach</select></label></div>
    </div>

    @foreach ($sellers as $seller)
    <x-seller-row :seller="$seller" />

    @endforeach
    @include('results._pagination')
  </div>

  <div style="width: 380px; flex-shrink: 0; position: sticky; top: 24px; display: flex; flex-direction: column; gap: 12px;">
    <x-map-panel :sellers="$sellers->items()" :centre="$centre" :radius="$radius" />
    @if ($featured->isNotEmpty())
    <div style="background: #FFFFFF; border: 1px solid #E4E1DA; border-radius: 14px; padding: 14px; display: flex; flex-direction: column; gap: 10px;">
      <div style="display: flex; justify-content: space-between; align-items: baseline;"><span style="font-weight: 600; font-size: 14px;">Featured near {{ $city->name }}</span><span style="font-size: 11px; color: #6B6357;">Paid placements · local sellers only</span></div>
      @foreach ($featured as $f)
      <a href="{{ url('/shop/'.$f->slug) }}" style="display: grid; grid-template-columns: 64px 1fr; gap: 12px; align-items: center; text-decoration: none; color: #1E1B16; padding: 8px; border-radius: 12px; background: #FCFCFA; border: 1px solid #ECEAE5;"><div style="width: 64px; height: 64px; border-radius: 10px; background-image: url('{{ App\Support\PlaceholderPhoto::url($f) }}'); background-size: cover; background-position: center;"></div><div style="display: flex; flex-direction: column; gap: 2px; min-width: 0;"><span style="font-size: 11px; font-weight: 700; color: #6E5314; text-transform: uppercase; letter-spacing: .05em;">{{ $f->isService() ? 'Featured service' : 'Featured listing' }}</span><span style="font-size: 14px; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $f->name }}</span><span style="font-size: 12px; color: #5C554A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ collect([$f->promo_text, $f->city, $f->distanceLabel()])->filter()->implode(' · ') }}</span></div></a>
      @endforeach
      <a href="{{ url('/advertise') }}" style="font-size: 12px; color: #24512F; font-weight: 600; text-decoration: none; align-self: flex-start;">Feature your listing here →</a>
    </div>
    @endif
    <div style="background: #FFFFFF; border: 1px solid #E4E1DA; border-radius: 14px; padding: 16px; display: flex; flex-direction: column; gap: 8px;">
      <span style="font-weight: 600; font-size: 14px;">Sell in {{ $city->name }}?</span>
      <span style="font-size: 13px; color: #5C554A; line-height: 1.45;">A free shop page puts you on this map. No listing or transaction fees.</span>
      <a href="{{ url('/sell/start/1') }}" style="color: #24512F; font-weight: 600; font-size: 14px; text-decoration: none; height: 36px; display: flex; align-items: center;">List your farm →</a>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('assets/app.js') }}"></script>
@endpush
