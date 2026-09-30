# Farm to Local — product spec (v1.1, Sep 28 2026)

> v1.1 additions live in docs/06 (design rules), 07 (mobile), 08 (staff backend), 09 (templates), 10 (revenue map).
> Where this file and those disagree, the later docs win.

## Positioning
A directory with a direct line to the seller. Search by ZIP; buy the way the seller already sells
(farm stand, market, CSA, delivery, shipping, booking). No cart, no fees between buyer and seller.
Tagline on the home page: "Buy from the people who grow it."

Principle that decides every edge case: **free for the people who grow and make; paid by the people who
ship and advertise.**

## Seller types (multi-select at signup; first checked = main badge, others show as tags)
| Type | Badge | Extra questions |
|---|---|---|
| Farm or ranch | Farm | — |
| Fisherman or dock | Fisherman | drop point / dock |
| Home maker | Homemade | cottage-food attestation |
| Bakery / small producer | Local Maker | — |
| Home cook | Home Kitchen | permit status disclosure (see Prepared Food) |
| Nursery | Nursery | nursery license optional |
| Food hub / co-op | Food Hub | member farms list |
| Service provider | Service provider | service area radius + days; license if state requires |
| Farmers market | Market | separate listing kind (days, hours, vendors), not a seller |
| Backyard / neighbor share | Neighbor | separate 3-field flow, no account |
| National retailer | Sponsored | separate model (`sponsors`), never a seller |

Gate at signup: "Do you grow, raise, catch or make what you sell — or name the farm that does?"
No → cannot list; link to Advertise.

## Categories (12; a seller checks all that apply)
Produce · Meat & Poultry · Eggs & Dairy · Seafood · Bakery & Sweets · Pantry & Preserves ·
Wellness & Body · Plants & Starts · Prepared Food & Meals · Homestead Goods · Farm & Garden Services ·
Farmers Markets (browse tile only; markets are their own model)

**"What kind?" tags (Sep 29).** Every category has a tag group (docs/11-taxonomy.md): Beef / Pork / Bulk under
Meat, Soap / Tallow balm under Wellness, Mexican / Thai under Prepared Food, Coop builders / Farrier under Services,
and so on — 150 tags across 11 groups. Sellers pick all that fit and can suggest new ones (pending until staff
approve). Buyers filter by them; city sub-pages publish per tag at ≥ 5 sellers. A top-level "Suggest a category"
link sends a request to staff; the 12 categories themselves stay fixed.

Cross-cutting tags (checkboxes): USDA Organic (certificate required at verification), No spray (self-declared),
Grass-fed & finished, Pasture-raised, No antibiotics, Rotational grazing, Non-GMO feed, Soy-free,
CSA available, U-pick, Local delivery, Ships nationwide, Wholesale/restaurants.

## Buy methods (a seller turns on any)
Farm stand / on-farm pickup (days, hours, exact address opt-in, payment methods) · Farmers markets
(pick markets from the directory + day) · CSA (share size/price, season, sign-up link) · Local delivery
(radius, fee, minimum, days) · Ships (nationwide or states, fee, ship days) · Wholesale/restaurants
(notes; adds "Wholesale inquiry" button) · Online ordering link (the Order button target).
Service providers instead set: service area radius, days, booking window; buy method = booking request.

## Products and services
No inventory counts. Each item: name, unit, price (or price basis: fixed / per unit / from / ask),
status: In stock · Low this week · Sold out · Reserve (month) · Pre-order · Seasonal. Services carry a
pricing basis such as "$175 + $1.10/lb hanging". Shop page shows "Prices set by the seller · updated N days ago".

## Verification
| Level | Requirement | Badge |
|---|---|---|
| Listed | phone + email verified, US address, producer attestation | none |
| Verified | one of: state ag registration, cottage food permit, market vendor letter/badge, business license, nursery license, custom-exempt processor registration, contractor license, OR a photo of the operation with the seller's name visible — reviewed by a human in admin | ✓ Verified |
| Verified + vouched | a market manager or extension agent on the platform vouches, or a video call | ✓✓ |

Documents are stored encrypted, never shown publicly, deleted after review (keep the record: type, reviewer, date).
Per-category attestations at step 5: producer (all); meat processing (Meat & Poultry); cottage food
(Bakery, Pantry, Homemade); no health claims + no "sunscreen/SPF" wording (Wellness & Body); prepared-food
permit disclosure (Prepared Food & Meals); license (Services, where the state requires one).

## Plans and revenue
| Plan | Price | Gets |
|---|---|---|
| Free | $0 forever | shop page, unlimited products, messages, map pin, Verified badge |
| Pro | $19/mo or $149/yr (Stripe subscription) | top of regional results, Featured badge, up to 30 photos, analytics, priority in in-season email |
| Partner | no monthly; 5–10% per tracked sale, or flat lead fee | same placement as Pro; billed monthly by statement |
A seller is on Free, Pro, or Partner — never Pro and Partner together.

Partner attribution events: coupon redemption (code FTL10 logged by seller), tracked click (`?ref=ftl` on
order link), qualified lead (message with a product/booking context), service booking request. Each event
has a fee; the month's events become a statement → Stripe Invoice.

Gear affiliate ("What we use"): seller lists up to 10 items they use; site-wrapped links split 50/50,
seller's own ambassador code 100% seller. Supply page ranks items by count of verified sellers listing them.
Sponsored shelves are labeled and never reorder.

Sponsored placements (national retailers, gear brands): admin-managed `sponsors` with placement, region,
dates. Rendered only in permitted slots, always labeled "Sponsored · National retailer / Not a farm. Paid placement."

## Neighbor Share
Post in 3 fields (what, where, when) + free / pay-what-you-want + optional photo. Phone code to confirm,
no account. Expires 14 days; one-tap extend. Location snapped to block level; pickup mode: message first /
curbside / come pick. No resale, no prepared food.

## Farmers markets
Seeded from the USDA National Farmers Market Directory. Fields: name, address, lat/lng, days, hours,
season, SNAP, website. Vendors: linked sellers (verified ones link to shop pages) + manager-uploaded names.
Market managers can claim a market; a manager can vouch for a vendor (instant Verified).

## Claim flow (seeded sellers)
Seeded from state ag registries and market vendor rosters: name, town, category, market. Status `seeded`,
shows "Unclaimed" on the card and "Is this yours?" on the page with view/message-attempt counts.
Claim by: code to phone on file, one document, or manager vouch.

## Geography pages
`/browse` → `/{state}` → `/{state}/{city}` (50-mi radius) → `/{state}/{city}/{category}` (published when ≥5).
Launch seeds: 12 states, 25 cities (see docs/03). Pages are templates over data: counts, category tiles,
map, top cities, featured (Pro/Partner) sellers, this week's markets, "in season now" from products marked
in season this week.

## Messaging
Buyer ↔ seller threads. Every thread starts with the system line "Farm to Local never handles payment.
You're buying from {seller} directly." Structured kinds: order inquiry (product context), booking request
(service, head count, ZIP, when), wholesale inquiry. Notifications by SMS/email; seller replies from the
dashboard or by email reply-to.

## Content
Field Notes (blog): buying guides, in season, seller playbook, laws by state (cottage food table, MEHKO
county tracker, raw milk, herd shares). Every post has ZIP CTA, seller CTA, email CTA. Weekly in-season
email per state (built in the staff backend — docs/08).

## Legal posture (drafts in designs/privacy.html, designs/terms.html)
Farm to Local lists; it doesn't sell. Sellers responsible for compliance. Seller rules 1–7 + 3a (prepared food
disclosure) + 3b (services). Reports reviewed by a person within 48h; reseller/misrepresentation = account closed.

## Out of scope for v1
Buyer checkout, reviews/ratings, in-app payments, native apps, multi-language.
