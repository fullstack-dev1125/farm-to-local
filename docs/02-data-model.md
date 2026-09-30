# Data model

Migrations for every table below are in `database/migrations/` (hand-written; run and fix as needed).
Money = integer cents. Distances = miles. Coordinates = decimal(9,6). Enums are string columns with
PHP enums in `app/Enums/`.

## Identity
- **users** — id, name, phone (unique, E.164, nullable), email (unique, nullable), phone_verified_at,
  email_verified_at, home_zip, role [buyer|seller|market_manager|admin], timestamps
- **login_codes** — user_id (nullable: first login creates the user on success), channel [sms], destination (E.164),
  code_hash (4 digits), attempts (max 3), expires_at (5 min), consumed_at. Max 3 codes per destination per 10 min.
- **saved_sellers** — user_id, seller_id (unique pair)
- **email_preferences** — user_id, state, categories json, weekly bool, shares_near_me bool

## Sellers
- **sellers** — id, user_id (nullable while seeded), slug (unique, stable), name, description, phone, email,
  website, instagram, facebook, order_url, address_line, city, state (2), zip, county, lat, lng,
  show_exact_address bool, status [seeded|listed|verified|suspended], verification_level tinyint (0/1/2),
  verified_at, plan [free|pro|partner], partner_rate_bps, lead_fee_cents, coupon_code (default FTL10),
  claimed_at, source (seed source label), founded_year, owners, processor, practices json,
  permit_status [none|permitted|no_permit], permit_number, permit_type,
  delivers bool, delivery_radius_mi, delivery_fee_cents, delivery_min_cents, delivery_days,
  ships bool, ships_scope [none|states|nationwide], ships_states json, ship_fee_cents, ship_days,
  csa bool, csa_notes, csa_url, wholesale bool, wholesale_notes,
  service_radius_mi, service_days, booking_window, products_updated_at, timestamps, softDeletes
- **seller_types** — seller_id, type (enum string), is_primary bool. Unique (seller_id, type).
- **categories** — id, slug, name, sort, icon (svg name). Seeded with the 12.
- **category_seller** — category_id, seller_id
- **tags** — id, slug, name, group [practice|certification|produce|meat|eggs|seafood|bakery|pantry|wellness|plants|prepared|homestead|services], requires_document bool (USDA
  Organic), status [approved|pending|merged], merged_into_id nullable, suggested_by_seller_id nullable, use_count_cache.
  **seller_tag** pivot. A category group is offered only when that category is checked. See docs/11-taxonomy.md for the seed list.
- **category_suggestions** — seller_id, name, note, status [open|declined|done], created_at.
- **products** — id, seller_id, kind [product|service], name, unit, price_cents (nullable),
  price_basis [fixed|per_unit|from|ask], price_text (e.g. "$175 + $1.10/lb hanging"), status
  [in_stock|low|sold_out|reserve|preorder|seasonal], status_note ("Nov"), sort, timestamps
- **seller_hours** — seller_id, kind [stand|pickup], days json, opens, closes, notes, payment_methods json
- **market_attendances** — seller_id, market_id, day, hours
- **photos** — via spatie medialibrary (`media` table); collections: cover, gallery, verification
- **gear_items** — seller_id, name, why, url, link_mode [site|own], affiliate_code, sort
- **gear_clicks** — gear_item_id, user_id nullable, ip_hash, created_at
- **attestations** — seller_id, key (producer|meat_processing|cottage_food|no_health_claims|prepared_food|license), accepted_at, ip_hash

## Verification
- **verifications** — seller_id, doc_type [ag_registration|cottage_permit|market_letter|business_license|nursery_license|custom_exempt|contractor_license|operation_photo|vouch], permit_number, media (medialibrary),
  submitted_at, reviewed_by (user), reviewed_at, decision [pending|approved|denied|more_info], notes,
  voucher_user_id (for vouch)
- **reports** — id, reporter_contact, seller_id nullable, conversation_id nullable, reason
  [reselling|identity|health_claim|unsafe|harassment|other], details, media, status [open|reviewing|closed],
  outcome, handled_by, handled_at

## Markets
- **markets** — id, slug, name, address_line, city, state, zip, lat, lng, days json, hours, season,
  snap bool, website, operator, source [usda|manual], manager_user_id nullable, claimed_at, vendor_count_cache
- **market_vendors** — market_id, seller_id nullable, name (when not a seller), category_hint, verified_link bool

## Neighbor shares
- **neighbor_shares** — id, phone (E.164, verified), user_id nullable, title, category
  [fruit|vegetables|herbs|eggs|cuttings|other], description, lat, lng (snapped), block_label, zip,
  pickup_mode [message_first|curbside|come_pick], price_mode [free|pwyw], media, starts_at, expires_at,
  extended_once bool, status [active|expired|removed], timestamps

## Messaging
- **conversations** — id, seller_id, buyer_user_id, neighbor_share_id nullable, kind
  [message|order_inquiry|booking_request|wholesale_inquiry], product_id nullable, meta json
  (booking: service, head_count, zip, when), last_message_at, seller_unread_count, buyer_unread_count
- **messages** — conversation_id, sender_user_id (nullable = system), body, read_at, timestamps

## Plans and revenue
- Cashier tables (`subscriptions`, `subscription_items`, customer columns on sellers or users — bill the seller's user).
- **lead_events** — seller_id, conversation_id nullable, kind [coupon_redemption|tracked_click|lead|booking],
  amount_cents nullable, fee_cents, occurred_at, statement_id nullable, meta json
- **statements** — seller_id, period (YYYY-MM), total_fee_cents, stripe_invoice_id, status [draft|sent|paid|void]
- **sponsors** — id, name, description, url, media (logo), placement [ships_hub|home_strip|region|email|supply_shelf],
  region, shelf, starts_at, ends_at, active bool, sort
- **supply_shelves** — id, slug, name, sort, sponsor_id nullable. Supply page items are aggregated from
  gear_items grouped by normalized name (`gear_items.canonical_key`).

## Geography and content
- **zips** — zip (pk), lat, lng, city, state, county. Seed from a public ZIP-centroid CSV (e.g. the
  US Census ZCTA gazetteer). ~33k rows.
- **states** — code, name, slug, seller_count_cache, market_count_cache, launch bool
- **cities** — id, slug, name, state, lat, lng, radius_mi (default 50), published bool, seller_count_cache
- **counties_mehko** — state, county, status [permanent|pilot|pending|introduced|verify], since, permits, notes, source
- **posts** — id, slug, title, type [buying_guide|in_season|seller_playbook|neighbor_share|laws], excerpt,
  body (markdown), media (cover), state (nullable), published_at
- **pages** — key, title, body (markdown) — about, how-it-works FAQ, privacy, terms (editable in admin)

## Geo query (radius search)
Store `lat`, `lng` on sellers/markets/shares plus a generated `POINT` column `location` (SRID 4326) with a
SPATIAL index. Bounding-box prefilter, then `ST_Distance_Sphere` for exact miles:

```sql
SELECT s.*, ST_Distance_Sphere(location, ST_SRID(POINT(:lng,:lat),4326)) / 1609.344 AS miles
FROM sellers s
WHERE status IN ('listed','verified')
  AND lat BETWEEN :lat - :dlat AND :lat + :dlat
  AND lng BETWEEN :lng - :dlng AND :lng + :dlng
HAVING miles <= :radius
ORDER BY (plan <> 'free') DESC, miles ASC
```
`dlat = radius/69`, `dlng = radius/(69*cos(radians(lat)))`. Wrap in a `SellerSearch` query object with
filters: categories, buy methods, verified_only, include_shares, open_this_weekend, sort.
ZIP → centroid via `zips`. If the input is a city name, geocode against `cities` then `zips`.

## Seeds (database/seeders)
- CategorySeeder (12), TagSeeder, SellerTypeSeeder (enum only), StateSeeder (50 + launch flag on 12),
  CitySeeder (25 launch cities), ZipSeeder (CSV), MehkoCountySeeder (from docs/01 table),
  DemoSeeder: the sample sellers used in the designs around Austin, TX (Blue Oak Ranch, Hollow Creek
  Bakehouse, Gulf Line Seafood, Cedar Bend Nursery, Two Sisters Cottage Kitchen, Wimberley Valley Honey
  [seeded/unclaimed], Stonewall Peach Co., High Plains Lamb Co., Hill Country Mobile Processing [service]),
  SFC Farmers' Market Downtown + 3 Austin markets, 9 neighbor shares, 4 conversations, gear items,
  3 posts (quarter beef guide, MEHKO tracker, gear farms use).


## Added Sep 28 (see docs/08 for columns)
`settings`, `audit_log`, `featured_slots`, `city_waitlist`, `seller_slugs`, `imports` + `import_rows`,
`share_reminders`, `email_sends`; `neighbor_shares` gains recurring/window/repeat/first_year; `lead_events`
gains source/place/buyer_last4 for redemptions; `sellers` gains `awards json`, `social json` (instagram, facebook,
youtube, linkedin, x, pinterest, google, yelp, bbb, trustpilot URLs), `booking_notes`, `promo_text`, `promo_url`.
