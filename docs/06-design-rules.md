# Design rules (the system behind the 81 pages)

Read this before building any component. Where a page in `designs/` disagrees with this file, this file wins
(the page is stale) — note it in the PR.

## Palette
| Token | Hex | Use |
|---|---|---|
| page | `#FAFAF8` | body background everywhere |
| inset | `#FCFCFA` | inset boxes inside cards |
| border | `#E4E1DA` | card borders, inputs |
| divider | `#ECEAE5` | row dividers, tab bars |
| ink | `#1E1B16` | text, dark buttons |
| muted | `#6B6357` | secondary text |
| green | `#2F6B3F` / hover `#24512F` | primary buttons, links |
| forest | `#1F3D2B` | dark bands (Neighbor Share), staff sidebar, footer |
| wash | `#EAF1EA` / `#E6EEE3` | pale green behind dark bands, info boxes |
| sand | `#EDE6D3` | home hero band only |
| map ground | `#F1F0EB` | drawn maps |
No cream (`#F6F1E7`) or pink (`#F8E5DA`) backgrounds anywhere. Pink/`#8A3A1B` survives only as the "Low / warning" chip tint.

Fonts: Fraunces 500/600/700 for headings, Instrument Sans 400–700 for everything else (self-hosted in `assets/fonts/`).

## Badges (exact colors — these are semantic)
| Badge | Background / text | Where |
|---|---|---|
| Farm, Nursery | `#2F8F4E` / white | text block |
| ✓ Verified | `#2563EB` / white | text block; tap → "what was reviewed and when" |
| ★ Featured | `#D9471F` / white | **the only badge allowed on a photo** (top-left) |
| Partner | `#F5E9C8` / `#6E5314` | shop header only |
| Sponsored · National retailer | `#F5E9C8` / `#6E5314` + "Not a farm. Paid placement." | never in local results or shop pages |
| Local Maker | `#EEDFC8` / `#7A4B2A` | |
| Fisherman, Service | `#DDE8EE` / `#23516B` | |
| Homemade / Home cook | `#E9E1EE` / `#5B3E6E` | |
| Market | `#DDE8EE` / `#23516B` | |
| Neighbor share | `#E4EFE3` / `#24512F` | |
Practice / certification pills: outlined `#C9D9CC` on `#F3F8F4`, text `#24512F` (Grass-fed, Certified organic, Wild-caught…). Credential pills: Reviewed `#E4EFE3`, Self-reported `#ECEAE5`/`#6B6357`, Award `#F5E9C8`/`#6E5314`.
Status chips on products: In stock `#E4EFE3`; Low `#F8E5DA`/`#8A3A1B`; Sold out `#ECEAE5`; Pre-order / Reserve `#DDE8EE`/`#23516B`.

## Photos — the square rule
Every card image is `aspect-ratio: 1 / 1`, `object-fit/background-size: cover`. A seller uploads one square
photo per slot; we crop to 800×800 on upload (medialibrary conversion) and generate 400 and 160 variants.
Result rows use a 96px square thumb (desktop) / 96px (mobile list) / 56px (dashboard). Gallery on the shop page:
one 500px square + two 244px squares (desktop); full-width swipe of squares (mobile). No stock photos; no
brand logos in gear photos. Reverse-image check at review flags stock.

## Cards are uniform
Results: every row is 182px tall, image left, three text lines (name / one-liner / distance) + how-they-sell line
+ badges + awards line ("🏆 …") when present. Carousels: 230px cards, same internal order (photo → badges → name →
one-liner → distance → practice pills). Never let content change a card's height in a grid.

## The two shelves on a shop page (exact labels)
1. **"Our Products & Services"** — the seller's own items, with a "View all N →" link to `/shop/{slug}/products`.
   Services show as a sub-row with a Book button.
2. **"Products & services we use for our business"** — up to 10 gear items and service providers, with
   "View all →" to `/shop/{slug}/uses`. Each item says why they use it. CTA differs: gear → "Shop this ↗"
   (tracked affiliate link, 50/50; 100% on the seller's own ambassador code), service provider → "See their page →".
No third shelf; no "From a neighbor". Sponsored shelves exist only on `/supply`, labeled.

## Shop page section order (both product and service sellers)
Gallery + How to buy (or the booking form for service sellers) → badges/title/one-liner/distance/practice pills →
actions row (About ↓ · save · share · Call · Message) + Awards & recognitions box → promo strip (becomes a sticky
bottom bar with a pulsing dot once scrolled past) → Our Products & Services → Products & services we use → About
card (story + facts + accordions "Why we do it this way", "What to expect", FAQ | map + farm stand + Get directions
+ "All N places we sell" overlay + message form + social/review icon tiles + Verified block) → Licenses,
certifications & awards row → More sellers near {town} (Featured first, then closest) → footer.
`seller_type` view switch: service sellers swap How-to-buy → booking form, product grid → "Services & pricing"
cards, "About the ranch" → "About the shop". Everything else identical.

## Location pages (state and city) mirror the home page
Hero "…near/in {place}" with the search card → stats → categories with counts → map + top cities / neighborhoods →
Featured carousel → Neighbor Share band → markets + in-season list → shippers → sell CTA → footer.

## Footer (identical on every page)
Four columns — Browse / For sellers / Company / Legal — over `#1F3D2B`, copyright line "A directory, not a
marketplace." Links: Browse (All states, Categories, Farmers markets, Ships to you, Neighbor share, Service
providers) · For sellers (List for free, Pro & Partner, Claim your page, Seller rules, What sellers use,
Advertise) · Company (For buyers, About, How it works, Notes, Contact) · Legal (Terms, Privacy, Report a listing).

## Motion
- Torn-edge bands: an SVG edge path at 200% width drifting 60s/75s (`ftl-drift-a/b`), dark band inset 40px inside
  a pale wash. Used on the home Neighbor Share band and the sand hero's bottom edge. `prefers-reduced-motion` stops it.
- Carousels: any `[data-carousel]` gets arrows (looping), dots and snap scroll (site.js §7). Never hide an arrow.
- Sticky promo bar: appears when the promo strip leaves the viewport (IntersectionObserver), pulsing 9px dot.
- Pages open at the top on navigation (`history.scrollRestoration='manual'`) unless a hash is present.

## Maps
Drawn SVG maps in the prototype = MapLibre in production (OpenFreeMap tiles). Pin colors follow the badge colors
(Featured orange, Farm green, Maker brown, Fisherman/Service blue, Home purple). Neighbor shares render a
translucent green circle at block level, never a precise pin.

## OG images
Generated on save for sellers, markets, products, shares and posts: 1200×630, square photo left (630×630), right
side on `#FAFAF8` with name (Fraunces 56), type badge, city · state, and the leaf mark. Fallback: state hero photo.
Cache to storage; regenerate when the photo or name changes.

## Copy rules
Direct, no exclamation marks, numbers over adjectives ("6 of 14 quarters open", not "limited availability").
Buyers are "you"; the seller is named. "Farm to Local lists; it doesn't sell." appears wherever money is implied.
