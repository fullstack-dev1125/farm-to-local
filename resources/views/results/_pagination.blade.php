{{-- Pager (designs/city-austin.html): "Showing 1–10 of 62", up to four page numbers + Next, per-page 10/20/50. --}}
@php
    $current = $sellers->currentPage();
    $last = $sellers->lastPage();
    $from = max(1, min($current - 1, $last - 3));
    $pages = range($from, min($last, $from + 3));
    $pill = 'width: 40px; height: 40px; border-radius: 999px; display: flex; align-items: center; justify-content: center; font-size: 14px;';
@endphp
<div style="display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; padding: 10px 0;">
  <span style="font-size: 13px; color: #6B6357;">Showing {{ $sellers->firstItem() }}–{{ $sellers->lastItem() }} of {{ $sellers->total() }}</span>
  <div style="display: flex; justify-content: center; gap: 6px;">
  @if ($last > 1)
  @foreach ($pages as $p)
  @if ($p === $current)
  <span aria-current="page" style="{{ $pill }} background: #1E1B16; color: #FFFFFF; font-weight: 600;">{{ $p }}</span>
  @else
  <a href="{{ $sellers->url($p) }}" style="{{ $pill }} border: 1px solid #E4E1DA; color: #1E1B16; text-decoration: none;">{{ $p }}</a>
  @endif
  @endforeach
  @if ($sellers->hasMorePages())
  <a href="{{ $sellers->nextPageUrl() }}" rel="next" style="height: 40px; padding: 0 14px; border-radius: 999px; border: 1px solid #E4E1DA; color: #1E1B16; display: flex; align-items: center; justify-content: center; font-size: 14px; text-decoration: none;">Next</a>
  @endif
  @endif
  </div>
  <span style="justify-self: end; display: inline-flex; align-items: center; gap: 8px; font-size: 13px; color: #6B6357;">Per page
    <span style="display: inline-flex; border: 1px solid #E4E1DA; border-radius: 999px; overflow: hidden; height: 36px;">
      @foreach ([10, 20, 50] as $n)
      <a href="{{ request()->fullUrlWithQuery(['per_page' => $n, 'page' => null]) }}" style="border: 0; background: {{ $perPage === $n ? '#1E1B16' : '#FFFFFF' }}; color: {{ $perPage === $n ? '#FFFFFF' : '#1E1B16' }}; padding: 0 14px; font-size: 13px;{{ $perPage === $n ? ' font-weight: 600;' : '' }} display: flex; align-items: center; text-decoration: none;">{{ $n }}</a>
      @endforeach
    </span>
  </span>
</div>
