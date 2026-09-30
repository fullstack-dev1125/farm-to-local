{{-- ✓ Verified (#2563EB). Means identity + one document reviewed by a human — never inspection or legality (rule 3). --}}
@props(['size' => 12])
<span {{ $attributes->merge(['style' => 'display: inline-flex; align-items: center; gap: 4px; background: #2563EB; color: #FFFFFF; font-size: '.$size.'px; font-weight: 600; padding: 3px 9px; border-radius: 999px;']) }}><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5L20 7"></path></svg>Verified</span>
