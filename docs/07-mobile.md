# Mobile layer (v1 scope: 8 screens, one stylesheet)

`designs/mobile.html` shows 13 phone frames (8 screens + their sheets/views). Each is a real page:
`m-index.html`, `m-results.html` (+`?view=map`, `?sheet=filters`), `m-shop.html` (+`?sheet=places`),
`m-share-post.html`, `m-sign-in.html`, `m-dashboard.html`, `m-dash-products.html` (+`?sheet=edit`),
`m-dash-messages.html` (+`?view=thread`). All use `designs/assets/mobile.css` — class-based, lift it into
`public/assets/mobile.css` and load it under `@media (max-width: 720px)` (or serve mobile Blade views by
user agent + width; either is fine, pick one and keep it).

## Breakpoints
≤ 720 px → these layouts · 721–1279 → desktop scaled (no separate design) · ≥ 1280 → desktop as built.

## Patterns (reuse on every page not listed above)
- **Top bar** 56px sticky, white: logo or ← back + title, right icons (search, ☰). Dashboard: ☰ opens the full
  sidebar as a right drawer, bell with unread count, initials avatar → shop page.
- **Bottom tab bar** 72px fixed. Public: Home · Browse · Share · Messages · Account. Dashboard: Overview ·
  Products · Messages(n) · Bookings(n) · More(drawer). Active tab green.
- **Swipe rails** replace every grid of cards (230px cards, snap). Section header keeps "See all →".
- **Bottom sheets** for filters, edit forms, "All places we sell", any side panel on desktop.
- **Sticky action bar** on shop pages: promo dot + text, Call, Message. On share-post: single "Post share — free".
- **Filter chips** row under the search pill; active filters as green "Showing:" chips with ×.
- **Map toggle** in the results top bar; map view shows swipeable result cards over pins.
- Buttons 48px, chips 36px, icon hit area 40px, inputs 16px font (prevents iOS zoom), body 14–15px, headings
  Fraunces 22–30px. 16px side gutters. No horizontal page scroll — check `scrollWidth === 390` in tests.

## Not designed yet (build from the patterns, no new components)
State/city, Ships, Services, Share browse/detail, How it works, Sell, Buy, catalog and uses pages, product page,
remaining dashboard tabs, sign-up steps 1–5 (single column, same stepper as share-post), staff backend (desktop only).

## Acceptance
Screenshot at 390×844 matches the `m-*.html` page; every tap target ≥ 40px; Lighthouse mobile ≥ 85 on `/`,
results and shop.
