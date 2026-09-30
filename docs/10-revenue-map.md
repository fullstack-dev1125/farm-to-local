# Revenue map — where each dollar comes from and where it renders

Principle: free for the people who grow and make; paid by the people who ship and advertise. Buyers are never
charged; nothing happens at the point of sale.

| Source | Price | Who pays | Where it shows | Model |
|---|---|---|---|---|
| Pro | $19/mo or $149/yr (Cashier) | seller | ★ Featured badge, top of regional results, 30 photos, analytics, credential review ✓ priority, awards box, Pro block in weekly email | `subscriptions` |
| Partner | 5–10% of tracked sales (default 6%), monthly statement → Stripe invoice on the 5th | seller | same placement as Pro; FTL10 coupon; tracked order link `?ref=ftl`; booking requests | `lead_events`, `statements` |
| Featured placement | $15/wk per city slot, 4 slots per city | Pro/Partner seller | top of city results, home/city carousels "Featured first", results sidebar | `featured_slots` |
| Sponsored · National retailer | from $400/mo (Ships slot) · $650/mo regional · $900/mo category · $350/send email line · $4,500/qtr launch package | brands, shippers | Ships hub top slots, home "Ships to your door" strip, bottom of state/city pages (regional), below results on a category (category), weekly email sponsor line. **Never** in local results or on shop pages. Always labeled. | `sponsors` |
| Gear affiliate | 50/50 split on site-wrapped links; 100% to seller on their own ambassador code | gear brands (commission) | "Products & services we use" shelf on shop pages; `/supply` (ranked by number of verified sellers listing the item; sponsored shelves labeled) | `gear_items`, `gear_clicks` |
| Stand sign | $6 weatherproof (optional) | seller | dashboard Quick actions, redeem screen | one-off charge |

A seller is Free, Pro or Partner — never Pro and Partner. Partner attribution events: coupon redemption logged
at the stand (`/dashboard/redeem` or SMS-12 by text), tracked click on the order link, qualified lead (message
with product context), confirmed booking. Under-logging isn't audited; Featured rotation weights on tracked
sales, which is the incentive to log.

Sep 2026 prototype numbers used in the designs: MRR $9,420 = Pro $2,698 (142) + Partner $4,120 fees + Featured
$2,220 + sponsors $2,460 run-rate; gear $860 (our half).
