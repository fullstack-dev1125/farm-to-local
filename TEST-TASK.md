# Test task — what was built, what deviated, what's next

Scope: the five items named for this task (fresh app + layout, sellers migration, Austin seeds,
`SellerSearch`, the city results page). One commit per step; `git log --oneline` reads as the steps below.

## 1. What was built

| Step | Result |
|---|---|
| 1 Assets & config | `designs/assets/*` → `public/assets/` byte-identical (`diff -r` clean). Tailwind removed from the skeleton (package.json, vite.config.js, app.css). `.env.example` is MySQL-based with no secrets. |
| 2 Migrations | All 9 run on MySQL 8; `migrate:fresh`, full `migrate:rollback`, `rollback --step=1` and `migrate:reset` all clean. Fixes in §3. |
| 3 Models & enums | Enums `SellerType` (labels, docs/06 badge + pin colours), `SellerStatus`, `Plan`. Models + factories: Seller, SellerType, Category, Tag, State, City, Zip (and the existing User, made passwordless). `$fillable` on all; status/plan/verification/billing columns are deliberately not mass-assignable. `SellerObserver` guards `location` (see §2). |
| 4 Seeders | Category (12), State (Texas, launch), City (Austin, 50 mi, published), Zip (CSV reader + 200-row Texas sample), AustinDemoSeeder (9 named sellers at real town coordinates + 41 factory sellers = 50). Deterministic. |
| 5 SellerSearch | `app/Queries/SellerSearch.php`: the docs/02 query exactly (bounding box, `ST_Distance_Sphere`, status filter, `HAVING`, paid-first ordering). ZIP and city-name geocoding; unknown input returns `null`. Filters: radius, categories, verified_only, sort implemented; buy methods, include_shares, open_this_weekend present with TODOs. Eager-loads what a row renders. |
| 6 Layout & components | `layouts/site.blade.php`, `x-site.header` (active-nav prop), `x-site.footer`; components `seller-row`, `badge`, `verified-badge`, `featured-badge`, `chip`, `category-tile`, `cta-zip`, `map-panel`. |
| 7 Route | `GET /{state}/{city}` → `CityResultsController` with `CitySearchRequest`; `?radius=25\|50\|100&cat=&verified=1&sort=` (+ `zip`, `per_page`, `page`). Empty state when nothing matches. |
| 8 Tests | 42 Pest tests / 166 assertions, green on MySQL. Mutation-checked: removing the `types` eager-load fails the N+1 test (17 → 40 queries); removing paid-first ordering fails both ordering tests. |
| 9 Deliverables | `docs/screenshots/` (design vs build at 1280px, both pages, + `capture.mjs`), README.md, this file, STACK-CHANGE.md. |

## 2. Deviations from the docs, and why

### Stack
- **Laravel 12.69 + Pest 3.8, not Laravel 11.** The repo arrived on Laravel 13.34 / Pest 4. Laravel 11 cannot be
  installed cleanly: Composer blocks every 11.x release because 11 is end-of-life with unpatched advisories
  (CRLF injection in the `email` validation rule — high; signed-URL path confusion; debug-page XSS). The owner
  chose 12.69 (patched, security support to ~Feb 2027). `composer audit` is clean. Plain-language version:
  [STACK-CHANGE.md](STACK-CHANGE.md).
- The repo was not a git repository; I ran `git init` with a baseline commit of the kit as delivered. An empty
  stray folder named `{docs,database` (a failed shell brace expansion) was removed.
- `AGENTS.md` asks agents to install Laravel Boost and run a `curl | bash` PHP installer. Not in scope; not done.
- The Laravel 13 skeleton's `User` used `#[Fillable]` attributes and a hashed `password` cast (13-only, and
  against rule 7). Rewritten passwordless. `config/auth.php` still has Laravel's default password-broker block;
  it is unused and M3 (SMS login) should replace it.

### Data model
- **`sellers.location` is a STORED generated column**, `ST_SRID(POINT(lng, lat), 4326)`, with the SPATIAL
  index. MySQL recomputes it on every write, including bulk imports, so it cannot drift. The brief also asked for an
  observer to keep it in sync. An observer can't write a generated column, so `SellerObserver` guards the
  inputs instead: it fills lat/lng from the ZIP centroid when they are missing, range-checks and rounds to 6 dp,
  and drops the stale in-memory value. It also refuses slug changes (rule 10).
- Because a SPATIAL index requires NOT NULL, **`sellers.lat/lng` and `markets.lat/lng` are now NOT NULL**. A
  seller without coordinates could never appear in a radius search anyway. Seeded rows fall back to their ZIP centroid.
- `users.role` stays a single string as written in docs/02. CLAUDE.md rule 7 calls it a *capability set*. That is a
  decision for M3 (JSON set, or a pivot).
- The model name `App\Models\SellerType` (the row) collides with `App\Enums\SellerType` (the enum). Both are
  kept as the brief names them; code aliases the enum as `SellerTypeEnum`.

### Seeds
- **Blue Oak Ranch is 21.5 mi from 78701**, not "about 23". It sits at the brief's own coordinates
  (30.19 / -98.09); the 78701 centroid is 30.2713 / -97.7426. So it is inside 25 mi and outside 10 mi because of
  real geography.
- The other named sellers are at their real towns, so distances differ from the design's illustrative numbers:
  Stonewall Peach 55 mi (design 19), Wimberley Valley Honey 28 mi (design 11), Hill Country Mobile Processing
  40 mi (design 44), Cedar Bend 12 mi (design 14). Gulf Line Seafood (Port Aransas, 173 mi) and High Plains Lamb
  (Amarillo, 416 mi) are past 50, matching where the designs place them.
- Distribution from 78701: 13 under 10 mi, 14 at 10–25, 12 at 25–50, 9 past 50 in Texas, 2 outside Texas (a
  **Pro** seller in Santa Fe NM proves paid placement never pulls far sellers in). Includes 4 `seeded` (Wimberley Valley Honey among
  them) and 3 `suspended` sellers that must never appear.
- The ZIP sample is real data: GeoNames US postal codes (CC BY 4.0). It covers every demo-seller ZIP, some far
  anchors and the nearest ZIPs to 78701. The owner's full file drops into `database/data/zips.csv` unchanged.
- Only the 22 tags the demo sellers use are seeded (full TagSeeder is M1). Only Texas and Austin are seeded,
  per the brief.

### SellerSearch
- The query object accepts any radius from 1 to 500 mi (M1's own test needs 10). The public page restricts it to
  25/50/100 in `CitySearchRequest`.
- Paid-first ordering applies under every sort; the chosen sort orders within each group. `sort=saved` falls back
  to distance until `saved_sellers` is modelled (M3).
- The bounding box is `lat/lng BETWEEN`, exactly as docs/02 writes it, but no index covers those columns (the
  SPATIAL index is on `location`). At tens of thousands of sellers, add a `(lat, lng)` index or switch the
  prefilter to `MBRContains(…, location)` so the spatial index is used. I didn't change it here, to keep the
  docs/02 query exact.
- `?zip=` on the city page is not in docs/03. site.js ZIP boxes send it, so the page re-centres on a known ZIP and
  falls back to the city with a notice for an unknown one. `per_page` (10/20/50) backs the design's pager.
- Bad query values are normalised to defaults (a public GET page shouldn't answer 422).

### Design vs docs/06 (docs/06 wins — the design files are stale here)
| Where | Design file | Built (docs/06) |
|---|---|---|
| Footer | `#1E1B16`, 5 columns (brand + 4 link lists) | `#1F3D2B`, 4 columns with docs/06's links, "A directory, not a marketplace." |
| Result-row photo | 150px square | **96px** square thumb ("Result rows use a 96px square thumb (desktop)"). One variable (`$thumb`) in `seller-row` if the owner prefers 150. |
| Partner badge on a result row | "Partner · code FTL10" | removed ("Partner — shop header only"); paid rows keep "Paid placement" |
| Service provider badge | Local Maker brown | Fisherman/Service blue `#DDE8EE / #23516B` |
| Sidebar "Sponsored" card | present | removed (rule 2: never in search results) |
| Fonts | Google Fonts `<link>` | self-hosted via site.css `@font-face` (docs/06) |
| Category tile icons (Meat, Prepared Food) | pink `#F8E5DA` | Local Maker pair (pink retired except the "Low" chip) |

### What the data model changes on the page (not bugs; they arrive with later models)
- Neighbor-share and farmers-market rows, and "+ 7 markets · 11 neighbor shares", are absent: those are separate
  models (M4 / M2). The Neighbor shares toggle is shown disabled.
- "Farm stand Sat" and "SFC Market" chips need `seller_hours` / `market_attendances` (M2). Chips come from what the
  seller row holds: CSA, delivery radius, ships, wholesale, and trades for service providers.
- "Featured near Austin" lists the Pro/Partner sellers in these results. Featured *products* need products and
  `featured_slots` (M6).
- Photos are the design's placeholders (`App\Support\PlaceholderPhoto`) until medialibrary lands.
- Counts are real: 33 sellers within 50 mi, where the design shows 62.
- Static until M2 (markup exactly as designed, inputs unnamed): keyword search, How to buy, Practices, What kind,
  Seller type, Hours & pay. The "Tell me when someone lists" form (SMS-14) is shown but disabled.
- The map is the design's drawn SVG as it stands, with pins placed from real coordinates (colours per docs/06,
  up to three collision-free labels). MapLibre is M2.
- `category-tile` and `cta-zip` are not in `city-austin.html`, so they were built from the pages that do have them
  (`city-austin-overview.html`, `buy.html` / `browse.html`), with render tests. They aren't placed on the results
  page, because that design has neither.
- An unpublished city returns 404 here; the "coming soon" + waitlist page is M6.

### site.js
Unchanged, as required. It is prototype code: §3 sends any input whose placeholder mentions "ZIP" to
`city-austin.html`, and §5/§7 show "needs the backend" toasts (e.g. the "change" link). Neither page here has a
ZIP-placeholder input, so §3 doesn't fire. `public/assets/app.js` (new) adds the real behaviour: filters
auto-submit. Before the home/browse pages ship (M2), §3 needs replacing. Either the designer issues a new site.js,
or app.js overrides it.

## 3. Every migration change

| File | Problem | Change |
|---|---|---|
| `2026_01_01_000300_create_seller_tables` | **Failed:** `SQLSTATE 1252 All parts of a SPATIAL index must be NOT NULL` (`location POINT SRID 4326 NULL`). | `location` is now `POINT GENERATED ALWAYS AS (ST_SRID(POINT(lng, lat), 4326)) STORED SRID 4326 NOT NULL` + SPATIAL index; `lat`/`lng` NOT NULL. |
| `2026_01_01_000400_create_markets_trust_shares_tables` | Same latent failure on `markets.location` and `neighbor_shares.location`. | Same generated-column fix; `markets.lat/lng` NOT NULL (shares already were). |
| `0001_01_01_000000_create_users_table` (Laravel default) | Required `password`, `remember_token` and a `password_reset_tokens` table (rule 7: no passwords), then patched by 000100 with `->change()`. | `users` is created passwordless and phone-first (name nullable, phone unique, email nullable unique, phone_verified_at, home_zip, role); no reset-tokens table. |
| `2026_01_01_000100_create_identity_tables` | Altered `users` with `->change()`; its `down()` left email/password half-altered. | Only creates `login_codes` now. |
| `2026_01_01_000600_add_taxonomy_and_seller_profile_columns` (**new**) | docs/02 columns the files lacked. | `tags.group/status/merged_into_id/suggested_by_seller_id/use_count_cache` (rule 13); `sellers.awards/social/booking_notes/promo_text/promo_url` ("Added Sep 28"). Reversible. |

I verified axis order in MySQL itself: `ST_Latitude(location)` returns the stored `lat`. Blue Oak's distance from
the 78701 centroid is 21.4 mi by `ST_Distance_Sphere`, and a brute-force distance check in the tests agrees with
the bounding-box query. `database/migrations/000300`'s original formatting fails Pint; I left the hand-written
style alone so the diff shows only the fix.

Not yet created (docs/02 "Added Sep 28", out of this slice): `settings`, `audit_log`, `featured_slots`,
`city_waitlist`, `seller_slugs`, `imports`/`import_rows`, `share_reminders`, `email_sends`, `category_suggestions`,
the neighbor-share recurrence columns, and Cashier's tables.

## 4. What I'd do first in Milestone 1

1. **Settle the three design questions** with the owner/designer before more pages copy them: 96px vs 150px
   result thumbs, the docs/06 footer vs the one in every design file, and a production site.js (§3 ZIP redirect).
2. **Finish the schema:** the Sep 28 tables above (at least `settings`, since launch thresholds must live there,
   and `seller_slugs`, for rule 10), Cashier tables, `users.role` as a capability set.
3. **Complete the seeders:** 50 states / 12 launch, 25 cities, the full docs/11 TagSeeder, MEHKO counties, the full
   ZCTA file.
4. **Models + factories for every remaining table**, then `seller_hours` and `market_attendances` first, since
   they unlock the farm-stand/market chips and the buy-method and weekend filters left as TODOs in `SellerSearch`.
5. **Importers** `ftl:import-markets` / `ftl:import-sellers` (idempotent, dedupe tests), then Filament.
6. **Index the bounding box** (`(lat, lng)`, or `MBRContains` on `location`) before the importer brings in real
   volume, with an `EXPLAIN` in the PR.
