# Farm to Local — build conventions (read this first)

Farm to Local is a directory of local food producers (farms, ranches, fishermen, home makers, bakeries,
nurseries, food hubs, service providers, farmers markets) searchable by ZIP + radius. It is NOT a
marketplace: no cart, no buyer checkout. Sellers get a free shop page; buyers contact them directly.
Revenue: Pro subscriptions, Partner referral fees, sponsored placements, gear affiliate links.

Everything product-level is in `docs/`. Read `docs/01-spec.md`, `docs/02-data-model.md`,
`docs/03-routes.md` and `docs/04-milestones.md` before writing code. Work milestone by milestone.

## Stack (fixed — do not substitute)
- Laravel 11, PHP 8.3, MySQL 8. Blade + Alpine.js. Tailwind is NOT used; the design ships its own CSS.
- Filament v3 for `/admin`. Laravel Cashier (Stripe) for Pro subscriptions. Laravel Scout is NOT needed;
  radius search is a MySQL spatial/haversine query (see docs/02, "Geo").
- Twilio for SMS login codes, Postmark for email. Livewire only via Filament — public pages are Blade + Alpine.
- Photos: `spatie/laravel-medialibrary` to S3-compatible storage (local disk in dev).
- Tests: Pest. Queue: database driver in dev, Redis in prod.

## The design is finished. Reproduce it, don't reinterpret it.
- `designs/` holds 81 static HTML pages + `designs/assets/` (52 public/seller pages, 9 `m-*.html` mobile
  screens + `mobile.html` index, 12 `admin-*.html` staff pages, 8 new public pages added Sep 28). These are the source of truth for markup,
  copy, colors, spacing and fonts. Convert them to Blade **without changing the design system**.
- Copy `designs/assets/site.css`, `site.js`, `mobile.css`, `fonts/`, `photos/`, `icons/`, `favicon.svg` into
  `public/assets/` unchanged. `photos/` are placeholders: production photos are seller uploads (800×800 crops).
  Extend `site.css` with new rules if needed; never rewrite it.
- Build a single layout `resources/views/layouts/site.blade.php` (head, header, footer from the designs) and
  extract shared components into `resources/views/components/`:
  `seller-card`, `seller-row` (city results), `badge`, `verified-badge`, `chip`, `category-tile`,
  `product-card`, `service-card`, `gear-card`, `share-card`, `market-row`, `stat`, `cta-zip`, `cta-seller`,
  `field` (label + input + note), `pill-button`, `toggle`, `map-panel`, `sponsored-card`.
- Inline styles in the designs are acceptable to keep as-is inside components. Do not "clean them up" into
  utility classes unless a component is used in 3+ places; then move the styles to a class in site.css
  with the same values.
- Acceptance for every public page: a screenshot at 1280px wide is visually indistinguishable from the
  corresponding file in `designs/`. Fonts, colors, spacing, copy. If the data model forces a difference,
  note it in the PR description.
- Desktop at 1280px for every page. Mobile (≤720px) is in v1 for the 8 screens in `designs/mobile.html`
  (home, results, shop, share-post, sign-in, dashboard, products, messages) — reproduce `designs/m-*.html`
  using `designs/assets/mobile.css` (class-based; lift it as-is). Every other page reuses the same mobile
  patterns (top bar, swipe rails, bottom sheets, tab bar) — see docs/07-mobile.md. Tablet = desktop scaled.
- Read docs/06-design-rules.md before building any component: palette, badge colors, the square-photo rule,
  "Featured is the only badge on a photo", the two product shelves, footer, carousel, torn-edge bands.

## Non-negotiable product rules (enforce in code, not just copy)
1. Producers only: a seller must attest they grow/raise/catch/make what they sell, or name the producing farm
   (food hubs, multi-farm brands). Sponsored national retailers are a separate model, never a Seller.
2. Sponsored placements never appear in search results or on shop pages. Only on the Ships hub, the home
   "Ships to your door" strip, regional footer slots, and the email. Always labeled.
3. "Verified" means identity + one document reviewed by a human. It never implies inspection or legality.
4. Prepared Food & Meals is available in every state. The seller declares permit status; the shop page shows
   "Permitted" (permit # on file) or "Home kitchen · no permit on file".
5. Neighbor shares expire 14 days after posting (extendable once by the poster). Block-level location only:
   never render a share's exact coordinates or address; snap to ~0.005° and show the neighborhood label.
6. A city page (`/{state}/{city}`) goes live at 12 verified sellers within 25 mi (early at 8 if a farmers
   market is listed); below that it shows "coming soon" + a buyer waitlist that gets one text on launch.
   City category pages (`/{state}/{city}/{category}`) publish only when ≥5 listings match. Thresholds live in
   admin Settings, not in code.
7. No passwords anywhere. Login is phone-only: a 4-digit SMS code (design shows 4 boxes), 5-minute expiry,
   3 attempts, max 3 codes per 10 min per number. Email is optional and used for statements/weekly email only.
   One account works as buyer, seller and neighbor (`users.role` is a capability set, not a switch).
9. Neighbor shares can be seasonal: a share stores a window (start/end month-day) and a repeat rule. Seven days
   before the window opens we text the poster (template SMS-07); reply YES posts it, STOP skips the year,
   no reply posts nothing. Never auto-post without a YES.
10. Slugs are permanent. If a seller renames, keep the old slug in `seller_slugs` and 301 it to the current one.
    `/go/{seller}` and `/go/{seller}/{gear}` are 302 tracked redirects with `rel="nofollow sponsored"`, logged once
    per session per target.
11. Every seller, market, product and share page has an OG image generated on save: 1200×630, the entity's
    square photo on the left, name + type badge + city on the right, on `#FAFAF8`. Fallback: the state hero.
13. Tags have groups: practice, certification, and one "what kind" group per category (docs/11-taxonomy.md). Sellers can suggest a tag; it is `pending` until
    staff approve, visible only on that seller's page meanwhile. Never let a pending tag appear in filters.
12. Photos: sellers upload ONE square photo per item/page slot (we crop to 800×800 on upload); the UI never
    stretches a non-square image. Featured (★, `#D9471F`) is the only badge that may sit on a photo; type and
    Verified badges go in the text block below.
8. Farm to Local never handles buyer payment. Stripe is used only to bill sellers (Pro) and issue Partner statements.

## Code conventions
- Slugs are stable once published (`sellers.slug`, `markets.slug`, `posts.slug`); changing a name never changes a slug.
- Money in integer cents. Distances in miles. Coordinates as decimal(9,6).
- All state/city pages are rendered from data, cached (Cache::remember, 1h), busted on seller save.
- Every form request has a FormRequest class. Every model has a factory. Every milestone adds Pest tests for the
  behaviors listed in docs/04.
- Commit small. PR per milestone section. PR description lists which design file each new page reproduces.

## Definition of done for a page
Route + controller + Blade view(s) + factory-seeded demo data so the page renders like the design + a Pest
feature test that the route returns 200 and contains three distinctive strings from the design + screenshot
compared against `designs/<page>.html`.

## Commands
```
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed          # seeds ZIP centroids, categories, seller types, demo sellers around Austin TX
php artisan serve                   # http://localhost:8000
php artisan test                    # Pest
php artisan ftl:import-markets file.csv   # USDA farmers market directory importer (Milestone 1)
```
