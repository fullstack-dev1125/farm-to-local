# First session — paste this into Claude Code from the repo root

(Before you start: install PHP 8.3, Composer, Node 20, MySQL 8 locally. Create an empty MySQL database
`farmtolocal`. Then, in an empty folder: `composer create-project laravel/laravel farm-to-local && cd farm-to-local`,
copy this kit's `CLAUDE.md`, `docs/`, `designs/`, `database/migrations/*` and `prompts/` into it, `git init`,
`git add -A && git commit -m "Farm to Local build kit"`, then run `claude`.)

---

Read CLAUDE.md, then docs/01-spec.md, docs/02-data-model.md, docs/03-routes.md and docs/04-milestones.md.
Read docs/06-design-rules.md, then look at designs/index.html, designs/city-austin.html and
designs/shop-blue-oak-ranch.html to understand the design system (designs/mobile.html for phones, designs/admin.html
for the staff backend); designs/assets/site.css and site.js are the shared styles and interactions.

We are building Milestone 1 this session. Do it in this order, committing after each step:

1. Install and configure: Filament v3, Laravel Cashier, spatie/laravel-medialibrary, Pest. Set MySQL in .env.
   Copy designs/assets/* to public/assets/.
2. Run the migrations in database/migrations (they were hand-written; fix anything that fails and tell me what
   you changed). Add Cashier's migrations. Confirm `migrate:fresh` and `migrate:rollback` both work.
3. Create Enums (app/Enums) for: SellerType, SellerStatus, Plan, ProductStatus, PriceBasis, VerificationDocType,
   VerificationDecision, ShareCategory, PickupMode, PriceMode, ConversationKind, LeadEventKind, SponsorPlacement.
4. Create Models with relationships, casts, and factories for every table in docs/02.
5. Seeders per docs/02 "Seeds": categories, tags, states (12 launch), 25 cities (look up lat/lng), MEHKO counties
   (from the table in designs/notes-mehko-california-counties.html), and a DemoSeeder that recreates the sample
   data in the designs around Austin, TX. For ZIP centroids, write the seeder to read `database/data/zips.csv`
   (columns zip,lat,lng,city,state,county); I'll supply the file — generate a 200-row Texas sample for now so
   tests run.
6. `app/Queries/SellerSearch.php`: the radius query from docs/02 with all filters and Pro/Partner-first ordering.
   Same for MarketSearch and ShareSearch. Keep a `location` POINT in sync with lat/lng via a model observer.
7. Console commands `ftl:import-markets {csv}` and `ftl:import-sellers {csv} --source=` per docs/04 M1.
8. Filament resources: Sellers, Markets, Verifications (queue view: pending first), Reports, Sponsors, Posts,
   Pages, NeighborShares, MehkoCounties. Admin role gate.
9. Layout: resources/views/layouts/site.blade.php with the header and footer from designs/index.html as
   components (x-site.header with active-nav prop, x-site.footer). Render a placeholder home route using the layout
   so I can confirm fonts and header match the design exactly.
10. Pest tests for M1 per docs/04, then `php artisan test`.

Rules: don't change the design system; don't add Tailwind; keep money in cents; no passwords anywhere.
When you finish, write a short SESSION-1.md summary: what was built, what deviated from the docs and why, and
what you recommend for session 2 (Milestone 2, public pages).
