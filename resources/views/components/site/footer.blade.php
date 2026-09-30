{{--
    Site footer per docs/06 "Footer (identical on every page)": four columns — Browse / For sellers / Company /
    Legal — over #1F3D2B, copyright line "A directory, not a marketplace."
    docs/06 wins over the design files, whose footer is #1E1B16 with five columns (brand + four link lists).
    Type sizes, spacing and link colours are the designs' own values.
--}}
@php
    $columns = [
        'Browse' => [
            'All states' => '/browse', 'Categories' => '/browse#categories', 'Farmers markets' => '/c/farmers-markets',
            'Ships to you' => '/ships', 'Neighbor share' => '/share', 'Service providers' => '/c/farm-garden-services',
        ],
        'For sellers' => [
            'List for free' => '/sell/start/1', 'Pro & Partner' => '/sell', 'Claim your page' => '/sell#claim',
            'Seller rules' => '/seller-rules', 'What sellers use' => '/supply', 'Advertise' => '/advertise',
        ],
        'Company' => [
            'For buyers' => '/for-buyers', 'About' => '/about', 'How it works' => '/how-it-works', 'Notes' => '/notes', 'Contact' => '/contact',
        ],
        'Legal' => [
            'Terms' => '/terms', 'Privacy' => '/privacy', 'Report a listing' => '/report',
        ],
    ];
@endphp
<footer style="margin-top: auto; background: #1F3D2B; color: #E8E1D3; padding: 48px 64px 32px; display: flex; flex-direction: column; gap: 32px;">
  <div style="display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 32px;">
    @foreach ($columns as $heading => $links)
    <div style="display: flex; flex-direction: column; gap: 8px; font-size: 14px;"><span style="font-weight: 700; color: #FFFFFF; margin-bottom: 4px;">{{ $heading }}</span>@foreach ($links as $label => $href)<a href="{{ url($href) }}" style="color: #D6CEBF; text-decoration: none;">{{ $label }}</a>@endforeach</div>
    @endforeach
  </div>
  <div style="display: flex; justify-content: space-between; font-size: 13px; color: #B8AE9C; border-top: 1px solid #3A352C; padding-top: 20px; gap: 24px;">
    <span>© {{ date('Y') }} Farm to Local · A directory, not a marketplace. Sellers are responsible for compliance with state and federal food laws. Farm to Local lists; it doesn't sell.</span>
    <span style="display: flex; gap: 16px;"><a href="{{ url('/terms') }}" style="color: #B8AE9C; text-decoration: none;">Terms</a><a href="{{ url('/privacy') }}" style="color: #B8AE9C; text-decoration: none;">Privacy</a><a href="{{ url('/contact') }}" style="color: #B8AE9C; text-decoration: none;">Contact</a></span>
  </div>
</footer>
