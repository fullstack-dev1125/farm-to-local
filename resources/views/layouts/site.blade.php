{{--
    Site layout: head, header and footer from designs/*.html.
    Fonts are self-hosted by assets/site.css (@font-face); the designs' Google Fonts <link> is dropped
    because docs/06 says the fonts ship in assets/fonts/.
--}}
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=1280">
<title>@yield('title', 'Farm to Local')</title>
<meta name="description" content="@yield('description', 'Farm to Local — verified local farms, makers, markets and neighbor shares, searchable by ZIP.')">
<link rel="icon" href="{{ asset('assets/favicon.svg') }}" type="image/svg+xml">
<link rel="stylesheet" href="{{ asset('assets/site.css') }}">
@stack('head')
</head>
<body data-page="@yield('page')">
<div style="@yield('frame', 'width: 100%; max-width: 1520px; min-width: 1100px; margin: 0 auto; box-sizing: border-box; display: flex; flex-direction: column; background: #FAFAF8; font-family: \'Instrument Sans\', system-ui, sans-serif; color: #1E1B16;')">

<x-site.header :active="$activeNav ?? null" />

@yield('content')

<x-site.footer />

</div>
<script src="{{ asset('assets/site.js') }}"></script>
@stack('scripts')
</body>
</html>
