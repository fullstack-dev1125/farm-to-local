{{--
    ZIP search box (designs/buy.html, designs/browse.html): input + green button, submits ?zip= by GET.
      <x-cta-zip :action="url('/search')" placeholder="Your ZIP or city" button="Search near me" />
    Not used on the results page, whose design has its own search bar; built for home/browse/buy (M2).
--}}
@props(['action', 'placeholder' => 'Your ZIP or city', 'button' => 'Search near me', 'value' => null])
<form method="get" action="{{ $action }}" {{ $attributes->merge(['style' => 'display: flex; background: #FFFFFF; border: 1px solid #E4E1DA; border-radius: 14px; padding: 6px; gap: 6px; max-width: 520px; margin: 0;']) }}>
  <input type="text" name="zip" value="{{ $value }}" placeholder="{{ $placeholder }}" aria-label="ZIP or city" inputmode="numeric" maxlength="60" style="flex: 1 1 0; min-width: 0; border: 0; background: transparent; font-size: 16px; padding: 0 14px; height: 48px; font-family: inherit; outline: none;">
  <button type="submit" style="background: #2F6B3F; color: #FFFFFF; border: 0; border-radius: 10px; padding: 0 22px; height: 48px; font-size: 15px; font-weight: 600; display: flex; align-items: center; font-family: inherit; cursor: pointer;">{{ $button }}</button>
</form>
