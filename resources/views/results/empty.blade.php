{{--
    Empty state for /{state}/{city} — reproduces designs/city-austin-empty.html, rendered when no seller matches.
    The page-level <style> block is the design's own, kept verbatim for this page.
    Differences: copy is generic rather than Alpine-specific; badges use the docs/06 type colours; the
    "Tell me when someone lists" form (SMS-14) is shown but not wired — it needs the SMS stack (M4/M6).
--}}
@extends('layouts.site')

@section('title', ($category ? "No {$categoryLower}" : 'No sellers')." near {$city->name}, {$state->code} — Farm to Local")
@section('page', 'results-empty')
@section('frame', "width: 1280px; box-sizing: border-box; display: flex; flex-direction: column; background: #FAFAF8; font-family: 'Instrument Sans', system-ui, sans-serif; color: #1E1B16; min-height: 100vh;")

@php($activeNav = 'browse')

@push('head')
<style>
.h1{font-family:'Fraunces',Georgia,serif;font-size:40px;font-weight:600;letter-spacing:-0.01em;line-height:1.1;margin:0}
.h2{font-family:'Fraunces',Georgia,serif;font-size:26px;font-weight:600;letter-spacing:-0.01em;margin:0}
.h3{font-family:'Fraunces',Georgia,serif;font-size:19px;font-weight:600;margin:0}
.eyebrow{font-size:12px;font-weight:700;letter-spacing:.09em;text-transform:uppercase;color:#24512F}
.card{background:#fff;border:1px solid #E4E1DA;border-radius:16px}
.inset{background:#FCFCFA;border:1px solid #E4E1DA;border-radius:12px}
.btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;height:46px;padding:0 22px;border-radius:999px;background:#2F6B3F;color:#fff;font-weight:600;font-size:15px;text-decoration:none;border:0;cursor:pointer;font-family:inherit}
.btn.o{background:#fff;color:#1E1B16;border:1.5px solid #E4E1DA}
.btn.d{background:#1E1B16}
.in{height:46px;border:1.5px solid #E4E1DA;border-radius:10px;padding:0 14px;background:#fff;font-size:15px;font-family:inherit;color:#1E1B16;width:100%;box-sizing:border-box}
textarea.in{height:auto;padding:12px 14px;resize:vertical}
select.in{appearance:none;-webkit-appearance:none;background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%236B6357' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 14px center}
.lbl{font-size:13px;font-weight:600;display:block;margin-bottom:6px}
.mut{color:#6B6357}
.bd{display:inline-flex;align-items:center;gap:4px;padding:3px 9px;border-radius:999px;font-size:12px;font-weight:600;white-space:nowrap}
.bd.farm{background:#2F8F4E;color:#fff}.bd.ver{background:#2563EB;color:#fff}.bd.feat{background:#D9471F;color:#fff}.bd.part{background:#F5E9C8;color:#6E5314}.bd.ok{background:#E4EFE3;color:#24512F}.bd.mut{background:#ECEAE5;color:#6B6357}.bd.pre{background:#DDE8EE;color:#23516B}.bd.spon{background:#F5E9C8;color:#6E5314}.bd.share{background:#E4EFE3;color:#24512F}
.pill{display:inline-flex;align-items:center;padding:3px 9px;border-radius:999px;border:1px solid #C9D9CC;background:#F3F8F4;color:#24512F;font-size:12px;font-weight:600}
.row{display:flex;align-items:center;gap:10px}
.tbl{width:100%;border-collapse:collapse;font-size:14px}
.tbl th{text-align:left;font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#6B6357;padding:10px 14px;border-bottom:1px solid #E4E1DA}
.tbl td{padding:12px 14px;border-bottom:1px solid #ECEAE5;vertical-align:top}
.tbl tr:last-child td{border-bottom:0}
.acc{border-top:1px solid #ECEAE5}.acc summary{list-style:none;display:flex;justify-content:space-between;align-items:center;padding:14px 0;font-weight:600;font-size:15px;cursor:pointer}.acc summary::-webkit-details-marker{display:none}.acc summary::after{content:"+";font-size:20px;color:#6B6357}.acc[open] summary::after{content:"–"}.acc .bdy{padding:0 0 16px;font-size:14px;line-height:1.6;color:#4A443B}
.sq{aspect-ratio:1/1;background-size:cover;background-position:center;border-radius:12px}
</style>
@endpush

@section('content')
<div style="display: flex; align-items: center; gap: 8px; padding: 0 64px; height: 44px; font-size: 14px; color: #5C554A;"><a href="{{ url('/') }}" style="color: #5C554A; text-decoration: none;">Home</a><span>›</span><a href="{{ url('/'.$state->slug) }}" style="color: #5C554A; text-decoration: none;">{{ $state->name }}</a><span>›</span><a href="{{ $pageUrl }}" style="color: #5C554A; text-decoration: none;">{{ $city->name }}</a><span>›</span><span style="color: #1E1B16; font-weight: 600;">{{ $category?->name ?? 'All sellers' }}</span><span style="margin-left: auto; font-size: 13px; color: #6B6357;">farmtolocal.com/{{ $state->slug }}/{{ $city->slug }}/</span></div>

<div style="padding: 20px 64px 64px; display:flex; flex-direction:column; gap:20px">
  <form method="get" action="{{ $pageUrl }}" class="card" style="padding:14px 16px;display:flex;gap:10px;align-items:center;margin:0">
    <select name="cat" class="in" style="width:220px" aria-label="Category">@if ($category)<option value="{{ $category->slug }}" selected>{{ $category->name }}</option>@endif<option value="">Everything local</option></select>
    <input name="zip" class="in" value="{{ $centre->zip }}" aria-label="ZIP" inputmode="numeric" maxlength="10" style="width:160px"><select name="radius" class="in" style="width:120px" aria-label="Radius">@foreach ($radii as $r)<option value="{{ $r }}"@selected($r === $radius)>{{ $r }} mi</option>@endforeach</select>
    @if ($verifiedOnly)<input type="hidden" name="verified" value="1">@endif
    <button type="submit" class="btn">Search</button>
    <span class="mut" style="font-size:13px;margin-left:auto">{{ $zipNotice ?? $placeLine }}</span>
  </form>
  <div style="display:flex;gap:40px;align-items:flex-start">
    <div style="flex:1 1 0;display:flex;flex-direction:column;gap:22px">
      <div class="card" style="padding:40px;display:flex;gap:32px;align-items:center">
        <div class="sq" style="width:220px;flex:0 0 auto;background-image:url('{{ asset('assets/photos/'.$emptyPhoto) }}');border-radius:16px;filter:grayscale(.6);opacity:.8"></div>
        <div><div class="eyebrow">No results</div><h1 class="h1" style="margin-top:8px;font-size:34px">No {{ $category ? $categoryLower.' ' : '' }}sellers within {{ $radius }} miles of {{ $centre->zip ?? $city->name }} yet.</h1><p style="font-size:15px;line-height:1.6;color:#4A443B;margin:10px 0 0">Here's what's close, what ships, and how to get told when someone lists.</p>
          <div class="row" style="gap:10px;margin-top:16px;flex-wrap:wrap">@if ($widen)<a class="btn" href="{{ $widen['url'] }}">Widen to {{ $widen['radius'] }} mi · {{ $widen['count'] }} {{ Str::plural('result', $widen['count']) }}</a>@endif<a class="btn o" href="{{ url('/ships') }}">{{ $category ? $category->name : 'Sellers' }} that ship to {{ $centre->zip ?? $state->code }} · {{ $shipCount }}</a>@if ($everything)<a class="btn o" href="{{ $everything['url'] }}">Everything local near {{ $city->name }} · {{ $everything['count'] }}</a>@endif</div></div>
      </div>
      @if ($shippers->isNotEmpty())
      <div><div class="row" style="justify-content:space-between;margin-bottom:12px"><h2 class="h2">Ships to {{ $centre->zip ?? $state->name }}</h2><a href="{{ url('/ships') }}" style="color:#2F6B3F;font-weight:600;text-decoration:none">All {{ $shipCount }} →</a></div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px">
          @foreach ($shippers as $s)<a href="{{ url('/shop/'.$s->slug) }}" class="card" style="overflow:hidden;text-decoration:none;color:inherit"><div class="sq" style="background-image:url('{{ App\Support\PlaceholderPhoto::url($s) }}');border-radius:0"></div><div style="padding:12px 14px;display:flex;flex-direction:column;gap:4px"><span class="row" style="gap:4px">@if ($s->primaryType())<x-badge :type="$s->primaryType()" class="bd" />@endif @if ($s->isVerified())<span class="bd ver">✓ Verified</span>@endif</span><div style="font-family:Fraunces,Georgia,serif;font-size:17px;font-weight:600">{{ $s->name }}</div><div class="mut" style="font-size:13px">{{ $s->city }}, {{ $s->state }}</div><div style="font-size:13px;font-weight:600;color:#24512F">📦 {{ $s->ships_scope === 'nationwide' ? 'Ships nationwide' : 'Ships '.implode(', ', $s->ships_states ?? []) }}{{ $s->ship_fee_cents ? ' · $'.number_format($s->ship_fee_cents / 100).' flat' : '' }}</div></div></a>@endforeach
        </div></div>
      @endif
      @if ($local->isNotEmpty())
      <div><div class="row" style="justify-content:space-between;margin-bottom:12px"><h2 class="h2">Local near {{ $city->name }}, everything</h2><a href="{{ $everything['url'] ?? $pageUrl }}" style="color:#2F6B3F;font-weight:600;text-decoration:none">All {{ $everything['count'] ?? $local->count() }} →</a></div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px">
          @foreach ($local as $s)<a href="{{ url('/shop/'.$s->slug) }}" class="card" style="display:flex;gap:12px;padding:12px;text-decoration:none;color:inherit"><span class="sq" style="width:84px;flex:0 0 auto;background-image:url('{{ App\Support\PlaceholderPhoto::url($s) }}');border-radius:8px"></span><span style="display:flex;flex-direction:column;gap:3px;min-width:0"><span class="row" style="gap:4px">@if ($s->primaryType())<x-badge :type="$s->primaryType()" class="bd" size="11" />@endif @if ($s->isVerified())<span class="bd ver" style="font-size:11px">✓ Verified</span>@endif</span><span style="font-weight:600">{{ $s->name }}</span><span class="mut" style="font-size:13px">{{ $s->city }} · {{ $s->distanceLabel() }}</span></span></a>@endforeach
        </div></div>
      @endif
    </div>
    <aside style="width:340px;flex:0 0 auto;display:flex;flex-direction:column;gap:14px">
      <div class="card" style="padding:22px;display:flex;flex-direction:column;gap:12px;background:#EAF1EA;border-color:#C9D9CC"><div style="font-family:Fraunces,Georgia,serif;font-size:20px;font-weight:600;color:#1F3D2B">Tell me when someone lists</div><p style="margin:0;font-size:14px;line-height:1.5;color:#24512F">One text when a {{ $category ? $categoryLower.' ' : '' }}seller within 100 mi of {{ $centre->zip ?? $city->name }} goes live. Nothing else, ever.</p><input class="in" placeholder="Your mobile number" aria-label="Your mobile number" disabled><button type="button" class="btn" disabled title="Text alerts arrive with the SMS milestone">Text me when it happens</button></div>
      <div class="card" style="padding:20px;display:flex;flex-direction:column;gap:8px"><div style="font-weight:700">Know a {{ $category ? $categoryLower.' ' : '' }}seller near here?</div><p class="mut" style="margin:0;font-size:14px;line-height:1.5">Send them the link — listing is free and takes 5 minutes. Or tell us and we'll reach out.</p><div class="row" style="gap:8px"><a class="btn o" href="{{ url('/sell') }}" style="flex:1">Copy invite link</a><a class="btn o" href="{{ url('/contact') }}" style="flex:1">Suggest a seller</a></div></div>
      <div class="inset" style="padding:14px 16px;font-size:13px;line-height:1.6;color:#4A443B"><b style="color:#1E1B16">Why not show far-away sellers anyway?</b><br>Because a 300-mile "local" result is a lie. Shippers are labeled and kept separate so you always know which is which.</div>
    </aside>
  </div>
</div>
@endsection
