# Routes and the design each one reproduces

| Route | Design file | Notes |
|---|---|---|
| `/` | designs/index.html | ZIP from `?zip=` or user's home_zip, else Austin demo |
| `/browse` | designs/browse.html | all states with counts |
| `/{state}` | designs/state-texas.html | state slug e.g. `/texas` |
| `/{state}/{city}` | designs/city-austin.html | results + map, 50-mi default, `?radius=25|50|100&cat=&buy=&verified=1&shares=1&weekend=1&sort=` |
| `/{state}/{city}/{category}` | same template filtered | 404 unless ≥5 matching listings |
| `/{state}/{city}/{category}/{tag}` | same results template filtered | "what kind" tag sub-pages (docs/11); 404 unless ≥5 matching sellers |
| `/c/{category}` | designs/category-meat-poultry.html | national category landing |
| `/search?zip=&radius=` | designs/city-austin.html | resolves ZIP → nearest published city, else renders results with "Near {zip}" |
| `/shop/{slug}` | designs/shop-blue-oak-ranch.html · designs/shop-hill-country-mobile-processing.html | product vs service variant by seller types |
| `/shop/{slug}/claim` | designs/claim-wimberley-valley-honey.html | only for status=seeded |
| `/market/{slug}` | designs/market-sfc-downtown.html | |
| `/market/{slug}/claim` | — | market-manager claim, reuse claim design |
| `/ships` | designs/ships.html | farm-direct shippers first, sponsors below |
| `/supply` | designs/supply.html | aggregated gear |
| `/share` | designs/share.html | shares near ZIP |
| `/share/new` | designs/share-post.html | mobile-native form, phone code, no account |
| `/notes` | designs/notes.html | |
| `/notes/{slug}` | designs/notes-how-to-buy-a-quarter-beef.html · designs/notes-mehko-california-counties.html | post types |
| `/login` | designs/sign-in.html | phone → 4-digit code; one account for buyer/seller/neighbor |
| `/account` | designs/account.html | buyer |
| `/messages` · `/messages/{conversation}` | designs/messages.html | |
| `/sell` | designs/sell.html | pricing + rules |
| `/sell/start/1..5` | designs/list-step-1.html … list-step-5.html | draft seller saved after step 1; resume anywhere |
| `/dashboard` | designs/dashboard.html | overview; sidebar per designs/dash-*.html |
| `/advertise` | designs/advertise.html | |
| `/how-it-works` | designs/how-it-works.html | |
| `/about` · `/contact` · `/report` · `/privacy` · `/terms` | matching designs | contact/report post to admin queues |
| `/shop/{slug}/products` | designs/shop-blue-oak-ranch-products.html | catalog: tabs with counts, search, Available now, sort, detail drawer |
| `/shop/{slug}/uses` | designs/shop-blue-oak-ranch-uses.html | gear + service providers the seller uses; tracked links |
| `/shop/{slug}/{item}` | designs/product-blue-oak-quarter-beef.html | shareable item page; `LocalBusiness`+`Product` schema; OG image |
| `/share/{state}/{city}/{slug}` | designs/neighbor-share.html | share detail; block-level map; message poster |
| `/seller-rules` | designs/seller-rules.html | footer "Seller rules" points here |
| `/verified` | designs/get-verified.html | what the badge means; linked from every ✓ block |
| `/{state}/{city}/{category}` with 0 results | designs/city-austin-empty.html | empty state: widen / ships-to-you / everything local / "text me when someone lists" (SMS-14) |
| `/booking/{id}` | designs/booking-confirmed.html | buyer side after a booking request; Cancel; CHANGE by text |
| `/dashboard/redeem` | designs/redeem.html | Partner sellers log FTL10 at the stand; fee math; month log |
| `/advertise` | designs/advertise.html | rate card + inquiry form → admin queue |
| `/services/{state}/{city}` | designs/services-austin.html | service providers directory; booking request |
| `/ships` | designs/ships.html | searchable shippers directory by ZIP, region, minimum, speed |
| `/for-buyers` | designs/buy.html | |
| `/dashboard/*` | designs/dash-*.html | 14 tabs: products, shares, uses, messages, bookings, photos, where, about, credentials, featured, email, analytics, billing, settings |
| `/admin/*` | designs/admin-*.html | 12 staff screens — docs/08 |
| mobile (≤720px) | designs/m-*.html via designs/mobile.html | docs/07 |
| `/{old-slug}` | — | 301 to the current slug via `seller_slugs` |
| `/go/{seller}/{gear?}` | — | tracked redirect: logs lead_events (tracked_click) or gear_clicks, then 302 to target |
| `/admin` | Filament | resources: Sellers, Verifications (queue), Reports, Markets, Sponsors, Posts, Pages, Shares, Statements, MehkoCounties |
| 404 | designs/404.html | |

## Launch geography
States (12): Texas, California, Florida, New York, Pennsylvania, North Carolina, Tennessee, Colorado,
Washington, Oregon, Georgia, Ohio. All 50 exist as pages; these 12 are `launch=true` (bold in /browse).

Cities (25): Austin, Dallas–Fort Worth, Houston, San Antonio, Los Angeles, San Francisco Bay Area,
Sacramento, San Diego, Miami, Tampa, Orlando, NYC/Hudson Valley, Philadelphia, Lancaster, Pittsburgh,
Raleigh, Charlotte, Asheville, Nashville, Denver, Seattle, Portland, Atlanta, Columbus, Cincinnati.

## SEO
- Title/meta per page type; canonical on geography pages; `sitemap.xml` generated nightly (states, published
  cities, published city-categories, sellers, markets, posts).
- Structured data: `LocalBusiness` on shop pages, `Event` on markets (weekly), `Article` on posts.
- No `noindex` on seeded sellers — they are the SEO seed.
