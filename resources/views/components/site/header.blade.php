{{-- Site header (designs/city-austin.html). $active = browse|markets|ships|share|how — underlines that nav item. --}}
@props(['active' => null])
@php
    $nav = [
        'browse' => ['Browse', url('/browse')],
        'markets' => ['Farmers Markets', url('/c/farmers-markets')],
        'ships' => ['Ships to You', url('/ships')],
        'share' => ['Neighbor Share', url('/share')],
        'how' => ['How it works', url('/how-it-works')],
    ];
@endphp
<header style="display: flex; align-items: center; justify-content: space-between; padding: 0 64px; height: 72px; background: #FFFFFF; border-bottom: 1px solid #E4E1DA;">
  <a href="{{ url('/') }}" style="display: flex; align-items: center; gap: 10px; text-decoration: none; color: #1E1B16;">
    <span style="width: 34px; height: 34px; border-radius: 9px; background: #2F6B3F; display: flex; align-items: center; justify-content: center;">
      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 20c0-9 5-14 16-16-1 11-6 16-16 16z"></path><path d="M4 20c4-6 8-9 12-12"></path></svg>
    </span>
    <span style="font-family: 'Fraunces', Georgia, serif; font-size: 23px; font-weight: 600; letter-spacing: -0.01em;">Farm to Local</span>
  </a>
  <nav style="display: flex; gap: 30px; font-size: 15px; font-weight: 500;">
    @foreach ($nav as $key => [$label, $href])
    <a href="{{ $href }}" style="color: #1E1B16; text-decoration: none;{{ $active === $key ? ' border-bottom: 2px solid #1E1B16; padding-bottom: 2px;' : '' }}"@if ($active === $key) aria-current="page"@endif>{{ $label }}</a>
    @endforeach
  </nav>
  <div style="display: flex; gap: 14px; align-items: center;">
    <a href="{{ url('/login') }}" style="color: #1E1B16; text-decoration: none; font-size: 15px; font-weight: 500; padding: 0 6px; height: 44px; display: flex; align-items: center;">Sign in</a>
    <a href="{{ url('/sell/start/1') }}" style="background: #2F6B3F; color: #FFFFFF; padding: 0 20px; height: 44px; display: flex; align-items: center; border-radius: 999px; font-weight: 600; font-size: 15px; text-decoration: none;">List your farm — free</a>
  </div>
</header>
