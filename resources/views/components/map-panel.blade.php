{{--
    Results map (designs/city-austin.html). The drawn base map (ground, river, roads, road labels) is the
    design's SVG as it stands; MapLibre + OpenFreeMap tiles replace it in Milestone 2 (docs/06 "Maps").
    Pins are the design's pin markup, placed from real coordinates: an equirectangular projection around the
    search centre where `radius` miles spans the map's half-width. Pin colours follow the badge colours;
    Featured (Pro/Partner) pins are orange. Up to three pins carry a name label, as in the design.
--}}
@props(['sellers', 'centre', 'radius'])
@php
    $scale = 170 / max(1, $radius);                     // px per mile; viewBox is 380 × 560
    $cosLat = cos(deg2rad($centre->lat));
    $pins = collect($sellers)->map(function ($s) use ($centre, $scale, $cosLat) {
        $x = 190 + ((float) $s->lng - $centre->lng) * 69 * $cosLat * $scale;
        $y = 280 - ((float) $s->lat - $centre->lat) * 69 * $scale;

        return [
            'seller' => $s,
            'x' => round(max(14, min(366, $x))),
            'y' => round(max(40, min(530, $y))),
            'color' => $s->isFeatured() ? '#D9471F' : ($s->primaryType()?->pinColor() ?? '#2F6B3F'),
        ];
    })->values();
    // Name labels, as in the design: up to three, skipping any that would collide with one already placed.
    $placed = [];
    $pins = $pins->map(function ($pin) use (&$placed) {
        $w = min(180, 20 + mb_strlen($pin['seller']->name) * 7);
        $clear = count($placed) < 3 && collect($placed)->every(
            fn ($o) => abs($o['x'] - $pin['x']) > ($o['w'] + $w) / 2 + 4 || abs($o['y'] - $pin['y']) > 30,
        );
        if ($clear) {
            $placed[] = ['x' => $pin['x'], 'y' => $pin['y'], 'w' => $w];
        }

        return $pin + ['label' => $clear, 'w' => $w];
    });
    $first = $pins->first()['seller'] ?? null;
@endphp
<div style="position: relative; height: 520px; border: 1px solid #E4E1DA; border-radius: 18px; overflow: hidden; background: #F1F0EB;"><svg viewBox="0 0 380 560" xmlns="http://www.w3.org/2000/svg" style="position:absolute;inset:0;width:100%;height:100%;display:block;" preserveAspectRatio="xMidYMid slice"><rect width="380" height="560" fill="#F1F0EB"/><g fill="#DFE6D6"><ellipse cx="76.0" cy="504.0" rx="114.0" ry="56.0"/><ellipse cx="342.0" cy="392.0" rx="76.0" ry="78.4"/><ellipse cx="114.0" cy="67.2" rx="95.0" ry="50.4"/><ellipse cx="323.0" cy="235.2" rx="45.6" ry="33.6"/></g><path d="M0 280.0 C76.0 246.4 114.0 336.0 190.0 302.40000000000003 S304.0 224.0 380 268.8" fill="none" stroke="#BBD3E2" stroke-width="14" stroke-linecap="round"/><path d="M0 280.0 C76.0 246.4 114.0 336.0 190.0 302.40000000000003 S304.0 224.0 380 268.8" fill="none" stroke="#D5E5EF" stroke-width="5" stroke-linecap="round"/><g stroke="#FFFFFF" stroke-width="4" fill="none"><path d="M0 140.0 L380 156.8"/><path d="M0 380.8 L380 403.2"/><path d="M95.0 0 L102.60000000000001 560"/><path d="M228.0 0 L220.39999999999998 560"/><path d="M342.0 0 L349.6 560"/><path d="M0 476.0 L380 487.2"/></g><path d="M19.0 560 L361.0 0" stroke="#E8CF8C" stroke-width="10" fill="none" stroke-linecap="round"/><path d="M19.0 560 L361.0 0" stroke="#F4E5B8" stroke-width="3" fill="none" stroke-dasharray="12 10"/><g fill="#6B6357" font-family="Instrument Sans, system-ui, sans-serif" font-size="10" font-weight="600"><text x="8" y="134.0">US-290</text><text x="8" y="374.8">RANCH RD 12</text><text x="235.6" y="12">I-35</text></g>@foreach ($pins as $pin)@php($w = $pin['w'])<g class="cpin" data-name="{{ $pin['seller']->name }}" data-sub="{{ $pin['seller']->distanceLabel() }}" transform="translate({{ $pin['x'] }},{{ $pin['y'] }})" style="cursor:pointer;"><path d="M0 0 C-8 -11 -12 -16 -12 -23 A12 12 0 1 1 12 -23 C12 -16 8 -11 0 0Z" fill="{{ $pin['color'] }}" stroke="#FFFFFF" stroke-width="2"/><circle cx="0" cy="-23" r="4.5" fill="#FFFFFF"/>@if ($pin['label'])<rect x="{{ -$w / 2 }}" y="5" width="{{ $w }}" height="21" rx="10.5" fill="#FFFFFF" stroke="#E4E1DA"/><text x="0" y="18" text-anchor="middle" font-family="Instrument Sans, system-ui, sans-serif" font-size="12" font-weight="600" fill="#1E1B16">{{ $pin['seller']->name }}</text>@endif<title>{{ $pin['seller']->name }} · {{ $pin['seller']->distanceLabel() }}</title></g>@endforeach</svg>
      <div style="position: absolute; left: 12px; right: 12px; top: 12px; display: flex; justify-content: space-between; align-items: center; pointer-events: none;"><span style="background: #FFFFFF; font-size: 12px; font-weight: 600; padding: 5px 10px; border-radius: 999px; border: 1px solid #E4E1DA;">{{ $radius }} mi around {{ $centre->zip ?? $centre->label }}</span><span style="background: #FFFFFF; font-size: 12px; padding: 5px 10px; border-radius: 999px; border: 1px solid #E4E1DA; pointer-events: auto; cursor: pointer;">Search as I move</span></div>
      <div style="position: absolute; right: 12px; bottom: 96px; display: flex; flex-direction: column; gap: 4px;"><button type="button" aria-label="Zoom in" style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid #E4E1DA; background: #FFFFFF; font-size: 18px; cursor: pointer;">+</button><button type="button" aria-label="Zoom out" style="width: 32px; height: 32px; border-radius: 8px; border: 1px solid #E4E1DA; background: #FFFFFF; font-size: 18px; cursor: pointer;">−</button></div>
      @if ($first)
      <div id="pin-preview" style="position: absolute; left: 12px; right: 12px; bottom: 12px; display: flex; flex-direction: column; gap: 4px; background: #FFFFFF; border-radius: 12px; padding: 12px; border: 1px solid #E4E1DA; box-shadow: 0 8px 20px rgba(30,27,22,.12);">
        <div style="display: flex; justify-content: space-between; align-items: center;"><span id="pin-name" style="font-weight: 600; font-size: 14px;">{{ $first->name }}</span><span id="pin-sub" style="font-size: 12px; color: #5C554A;">{{ $first->distanceLabel() }}</span></div>
        <span style="font-size: 12px; color: #5C554A;">Hover or tap a pin · click to open</span>
      </div>
      @endif
    </div>
    <div style="display: flex; gap: 12px; font-size: 12px; color: #5C554A; align-items: center; padding: 0 4px; flex-wrap: wrap;"><span style="display: flex; align-items: center; gap: 5px;"><span style="width: 10px; height: 10px; border-radius: 999px; background: #2F6B3F;"></span>Farm</span><span style="display: flex; align-items: center; gap: 5px;"><span style="width: 10px; height: 10px; border-radius: 999px; background: #7A4B2A;"></span>Maker</span><span style="display: flex; align-items: center; gap: 5px;"><span style="width: 10px; height: 10px; border-radius: 999px; background: #5B3E6E;"></span>Home</span><span style="display: flex; align-items: center; gap: 5px;"><span style="width: 10px; height: 10px; border-radius: 999px; background: #B8912F;"></span>Market</span><span style="display: flex; align-items: center; gap: 5px;"><span style="width: 10px; height: 10px; border-radius: 999px; background: #23516B;"></span>Service</span><span style="display: flex; align-items: center; gap: 5px;"><span style="width: 10px; height: 10px; border-radius: 999px; background: #8A3A1B;"></span>Share</span></div>
    <script>
      document.querySelectorAll('.cpin').forEach(function (g) {
        g.addEventListener('mouseenter', function () { document.getElementById('pin-name').textContent = g.dataset.name; document.getElementById('pin-sub').textContent = g.dataset.sub; });
      });
    </script>
