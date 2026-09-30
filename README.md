# Farm to Local

A directory of local food producers, searchable by ZIP + radius. Not a marketplace: sellers get a free shop
page and buyers contact them directly. Product docs live in `docs/`, the finished design in `designs/`, and
the conventions in `CLAUDE.md`.

This branch is the **paid test task**: a slice of Milestones 1 and 2. See [TEST-TASK.md](TEST-TASK.md) for what
was built and every deviation, and [STACK-CHANGE.md](STACK-CHANGE.md) for why it runs on Laravel 12, not 11.

## Stack

Laravel **12.69** · PHP 8.3 · MySQL 8 · Blade (+ Alpine later) · Pest **3.8**. No Tailwind: the design ships its
own CSS in `public/assets/` (copied byte-for-byte from `designs/assets/`).

## Local setup (about 10 minutes)

Requirements: PHP 8.3 with `pdo_mysql`, Composer 2, MySQL 8. Node is not needed for this slice (no build step).

```bash
composer install
cp .env.example .env
php artisan key:generate

# Two MySQL databases, same user: the app and the test suite.
mysql -u root -p -e "CREATE DATABASE farmtolocal; CREATE DATABASE farmtolocal_test;
  CREATE USER IF NOT EXISTS 'ftl'@'localhost' IDENTIFIED BY 'choose-a-password';
  GRANT ALL ON farmtolocal.* TO 'ftl'@'localhost'; GRANT ALL ON farmtolocal_test.* TO 'ftl'@'localhost';"
# then set DB_USERNAME / DB_PASSWORD in .env

php artisan migrate --seed      # categories, Texas, Austin, 200 Texas ZIPs, 50 demo sellers
php artisan serve               # http://localhost:8000/texas/austin
```

MySQL is required. The radius search uses a spatial `POINT` column and `ST_Distance_Sphere`, which SQLite
cannot run, so tests also use MySQL (`farmtolocal_test`, set in `phpunit.xml`).

Pages to try:

| URL | What it shows |
|---|---|
| `/texas/austin` | results, 50 mi around 78701 (designs/city-austin.html) |
| `/texas/austin?radius=25&cat=meat-poultry` | filtered |
| `/texas/austin?zip=78620` | re-centred on a ZIP (Dripping Springs) — or click **change** beside "Austin, TX 78701" and type one |
| `/texas/austin?zip=99999` | unknown ZIP: falls back to Austin with a notice |
| `/texas/austin?cat=homestead-goods&radius=25` | empty state (designs/city-austin-empty.html) |

## Tests

```bash
php artisan test          # 44 tests, ~3 s, MySQL farmtolocal_test
./vendor/bin/pint --test  # code style
```

The tests cover docs/04's radius acceptance ("returns Blue Oak Ranch within 25 mi of 78701" / "does NOT … within
10 mi"), paid-first then distance ordering, radius 25/50/100, unknown ZIPs, the empty state, "no sponsor markup
in results", and an N+1 guard (10 rows and 33 rows run the same number of queries).

Migrations are proven both ways: `php artisan migrate:fresh` and `php artisan migrate:rollback` (a full rollback
leaves only the `migrations` table).

## Screenshots

`docs/screenshots/` has 1280px full-page captures of `/texas/austin` and the empty state, each beside its design
file, plus side-by-side composites. To regenerate (Node 22+, Chrome on PATH, app on :8000):

```bash
node docs/screenshots/capture.mjs docs/screenshots/city-austin.app.png http://127.0.0.1:8000/texas/austin
```

## Scope of this test task

**In:** fresh Laravel app with the site layout (header, footer, components) · every migration running clean up
and down (with the fixes recorded in TEST-TASK.md) · models/enums/factories for Seller, SellerType, Category,
Tag, State, City, Zip · Austin seed data at real coordinates · `app/Queries/SellerSearch.php` (the docs/02 radius
query) · the `/{state}/{city}` results page and its empty state · Pest tests.

**Out (by instruction):** Filament, `ftl:import-*` commands, Cashier, medialibrary (photos are placeholders),
authentication, the seller dashboard, MapLibre (the map is the design's drawn SVG with real pin positions),
geography caching, the city-category threshold rule, sponsors, OG images, and every route other than
`/{state}/{city}`. Links to those pages are in the markup and will 404 until their milestones.

## Build kit contents

```
CLAUDE.md                   conventions — stack, design fidelity rules, product rules
docs/01-spec.md             product spec: seller types, categories, buy methods, verification, plans, rules
docs/02-data-model.md       every table + the radius query + seeds
docs/03-routes.md           URL structure and which design file each route reproduces
docs/04-milestones.md       M1–M6 with acceptance criteria and tests
docs/05-reviewer-checklist.md   what the reviewer checks on each PR + production runbook
docs/06-design-rules.md     design system: palette, badges, square photos, shelves, footer, OG images
docs/07-mobile.md           mobile layer
docs/08-admin.md            staff backend
docs/09-message-templates.md  every SMS and email
docs/10-revenue-map.md      where each dollar comes from
docs/11-taxonomy.md         "What kind?" tag groups per category
database/migrations/        hand-written migrations for the whole model
designs/                    81 finished HTML pages + assets
prompts/first-session.md    the original first-session prompt
JOB-LISTING-reviewer.md     listing for the part-time Laravel reviewer
```

ZIP centroids in `database/data/zips.csv` are a 200-row Texas sample from GeoNames (CC BY 4.0,
geonames.org). Replace the file with the full ZCTA list and re-run `php artisan db:seed --class=ZipSeeder`.
