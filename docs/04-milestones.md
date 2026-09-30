# Milestones

Each milestone is one or more PRs. "Done" = acceptance criteria met + Pest tests green + reviewer sign-off.
The reviewer (part-time Laravel dev) checks security, data integrity and deploys to staging → production.

## M1 — Foundation, data, admin, seeds  (target: session 1–3)
- Fresh Laravel 11 app, `designs/assets` copied to `public/assets`, layout + header + footer components.
- All migrations from `database/migrations/` run clean; models, enums, factories for every table.
- Filament installed; resources for Sellers, Markets, Verifications, Reports, Sponsors, Posts, Pages.
- Seeders: categories, tags, states (50, 12 launch), cities (25), ZIP centroids (CSV), MEHKO counties, demo data.
- `php artisan ftl:import-markets {csv}`: USDA farmers market directory → markets (dedupe on name+zip).
- `php artisan ftl:import-sellers {csv} --source=`: name, town, state, zip, category, market → seeded sellers.
- Tests: migrations run; importer creates and dedupes; radius query returns Blue Oak Ranch within 25 mi of 78701 and not within 10.

## M2 — Public pages, search, maps  (session 3–6)
- Routes: `/`, `/browse`, `/{state}`, `/{state}/{city}`, `/{state}/{city}/{category}`, `/c/{category}`,
  `/search`, `/shop/{slug}` (product + service variants), `/market/{slug}`, `/ships`, `/supply`, `/share`,
  `/notes`, `/notes/{slug}`, `/how-it-works`, `/about`, `/sell`, `/advertise`, `/privacy`, `/terms`, 404.
- SellerSearch query object with all filters; results + MapLibre map with clustered pins colored by type.
- Geography pages cached 1h and busted on seller/market save; city-category threshold rule.
- Sponsors render only in permitted slots.
- Tests: each route 200 with three design strings; threshold rule; sponsor never appears in `/search` HTML;
  share page never contains a share's raw lat/lng.
- Acceptance: side-by-side screenshots vs designs at 1280px.

## M3 — Auth, onboarding, claim, verification, dashboard  (session 6–10)
- Passwordless login (SMS via Twilio, email via Postmark), rate-limited: 3 codes / 10 min per destination, 5 attempts.
- `/sell/start/1..5` exactly as designed: multi-type, producer gate, categories + products quick-add, buy
  methods, photos + links, verification upload + per-category attestations + prepared-food permit disclosure.
  Draft saved after step 1; resumable. Publishes as `listed` at step 5.
- Claim flow for seeded sellers: code to phone on file, document, or manager vouch.
- Admin verification queue: approve → status verified, level 1; vouch → level 2; deny/more_info with note.
  Document media deleted on decision; record kept.
- Seller dashboard: stats (views, clicks, messages, redemptions), products CRUD with status toggle,
  how-to-buy editor, photos, gear shelf editor, plan panel (read-only until M5).
- Buyer account: saved sellers, email preferences, delete account.
- Tests: login rate limits; onboarding validation per step; producer gate blocks; attestation keys required
  by category; claim by code; verification decision transitions; document deleted after decision.

## M4 — Messaging, neighbor shares, reports  (session 10–13)
- Conversations + messages with kinds (message, order inquiry, booking request, wholesale); system first line;
  SMS/email notifications with reply-by-email (Postmark inbound) for sellers.
- Neighbor Share post flow (phone code, no account), 14-day expiry job, one-tap extend, block snapping,
  hub page + cards in city results when `shares=1`.
- Report intake → admin queue; "Report a problem" links prefill seller.
- Tests: booking request stores meta; share expires; extend once only; snapped coordinates differ from input
  and never render raw; report creates queue item.

## M5 — Plans, Stripe, Partner attribution, sponsors, launch hardening  (session 13–16)
- Cashier: Pro monthly/annual, switch/cancel from dashboard; Featured badge + result priority when active.
- Partner: seller toggles Partner (exclusive of Pro); coupon redemption logging from dashboard; `/go/{seller}`
  tracked links; lead events on qualifying conversations; monthly statement job → Stripe Invoice.
- Gear affiliate: `/go/{seller}/{gear}` click logging; Supply page ranking from gear_items; sponsored shelves.
- Sponsors admin with placement/region/dates.
- Hardening: Lighthouse ≥90 on `/`, `/{state}/{city}`, `/shop/{slug}`; sitemap.xml; backups; error monitoring;
  README for local setup; `.env.example` complete.
- Tests: Pro/Partner exclusivity; priority ordering; statement totals; tracked link logs once per session.

## M6 — Mobile (8 screens), staff backend, weekly email  (session 16–20)
- Mobile layer per docs/07 for home, results (+map, +filters sheet), shop (+places sheet), share-post, sign-in,
  dashboard overview, products (+edit sheet), messages (+thread). `assets/mobile.css` lifted as-is.
- Staff screens per docs/08: overview, verification queue with auto-checks, reports, sellers (+"Login as"),
  shares, seeded importer (dry run → publish), featured & sponsors (creative label check), cities & launch
  (threshold from Settings, waitlist text on launch), money (statements → Stripe invoices, Connect payouts),
  templates, settings. `audit_log` on every decision.
- Weekly email builder + Thursday send job per state; SMS twin for buyers who chose SMS.
- Seasonal shares: window + repeat fields, T-7 reminder job (SMS-07), YES/STOP handling, nothing posts without YES.
- Redemption screen + SMS-12 text logging; empty-state alerts (SMS-14); "Text us a photo" intake (SMS-13).
- OG image generation; `seller_slugs` redirects; sitemap includes item pages and share pages.
- Tests: mobile pages have no horizontal overflow at 390px; sponsor creative without the label is rejected;
  a city below threshold returns the coming-soon page and stores the waitlist; a seasonal share without YES never
  posts; a rename keeps the old slug 301ing; Featured slot count per city never exceeds Settings.

## Phase 2 (after launch)
Mobile for every remaining page · market-manager dashboard · on-site checkout (opt-in) · buyer reviews (only if
sellers ask) · multi-language.
