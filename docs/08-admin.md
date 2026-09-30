# Staff backend (`/admin`) — 12 screens in `designs/admin-*.html`

Filament v3 stays for CRUD (Sellers, Markets, Sponsors, Posts, Pages, Settings). The queue screens below are
custom Filament pages (or plain Blade under the admin gate — your call) that reproduce the designs. Staff log in
like everyone else (phone code) and need `users.role` to include `admin` or `reviewer`. Every decision writes an
`audit_log` row (who, what, seller, before/after).

| Screen | Design | What it must do |
|---|---|---|
| Overview | admin.html | counts (verified, awaiting review, open reports, SMS buyers, MRR, searches/day); "Needs a human today" = union of reviews > 24 h, reports > 48 h, claims, sponsor creative pending; this-week table; cities near threshold; weekly-email status |
| Verification queue | admin-review.html | pending verifications oldest first; auto-checks (doc type ↔ seller type, expiry date parsed, name match against owner/LLC, phone area code ↔ county, duplicate within 5 mi); decisions Approve / Ask for more (template + editable SMS) / Reject; document media deleted 24 h after decision, record kept |
| Reports | admin-reports.html | open reports with reporter quotes, signals (no producer doc, commercial parcel, stock-photo match), actions Remove / Pause & ask for proof / Dismiss; outcome texted to all reporters (SMS-11) and to the seller |
| Sellers | admin-sellers.html | search + filters (state, type, plan, status, sort by health/views/newest/stale); bulk pause/feature/message/export; "Login as" = read-only impersonation with red banner, logged |
| Tag suggestions | (in Verification queue, tab "Tags") | pending "what kind" tags and category suggestions from sellers: approve, rename, or merge into an existing tag; seller texted either way |
| Neighbor shares | admin-shares.html | live / flagged / scheduled seasonal / expired; hide; preview the next seasonal reminder batch (SMS-07) |
| Seeded importer | admin-import.html | CSV upload (registry, cottage/MEHKO list, fishing licenses, USDA markets, Places search) → column mapping → dry run → publish as `status=seeded` with "Unclaimed · info from public records"; auto-reject chains/aggregators (list in Settings) and dupes (same phone or name within 0.3 mi); one postcard + one text (SMS-10) to the owner |
| Featured & sponsors | admin-sponsors.html | 4 Featured slots per city at $15/wk (count and price in Settings), waitlist, empty slots filled by closest Free sellers; national sponsor contracts with placement/term/creative approval — creative must contain "Sponsored · National retailer · Not a farm. Paid placement." or it cannot go live |
| Weekly email builder | admin-email.html | per state, Thursday 6:00 local; 7 blocks: subject/intro (drafted), in-season by reader ZIP, Pro block (one line per Pro seller sorted by distance to reader, product status callouts), new sellers within 25 mi (max 6), 3 closest shares, sponsor line (toggle, needs approval), footer; preview for a sample ZIP; schedule |
| Cities & launch | admin-cities.html | every city with verified count vs threshold (12 / 25 mi), markets, buyer waitlist; Launch (page live + waitlist texted SMS-09 + email starts + Featured slots open); "Launch early" allowed at ≥ 8 with a market |
| Money | admin-money.html | MRR by source, Partner statements (rate 5–10% per seller, default 6%) → Stripe invoices on the 5th, gear affiliate payouts 50/50 (100% on ambassador code) via Stripe Connect, revenue-mix bar |
| SMS & email templates | admin-templates.html | editable templates with variables; see docs/09 |
| Settings | admin-settings.html | launch threshold, radius, early-launch minimum, radius options, stale/nudge days, plan prices, Partner default rate, Featured price/slots, sponsor minimum, affiliate split, coupon code, review/report SLAs, auto-pause after N reports, document retention hours, photo rules toggles, staff list |

## Tables this adds to docs/02
- `settings` (key, value json) — every number above; cached; changes logged.
- `audit_log` (user_id, action, subject_type, subject_id, before json, after json, created_at).
- `featured_slots` (city_id, seller_id, starts_at, ends_at, price_cents, status [active|waitlist|ended]).
- `city_waitlist` (city_id, phone, created_at, notified_at).
- `seller_slugs` (seller_id, slug, created_at) — for permanent redirects.
- `imports` (source, state, file, rows, published, rejected, dupes, dry_run bool, ran_by, created_at) + `import_rows`.
- `share_reminders` (neighbor_share_id, year, sent_at, reply [yes|stop|none], posted_at).
- `email_sends` (state, scheduled_for, subject, blocks json, sponsor_id, recipients, opens, clicks, status).
- `redemptions` = `lead_events` rows with kind `coupon_redemption`, plus `source [logged|texted|tracked_link]`,
  `place`, `buyer_last4`.
- `neighbor_shares` gains: `recurring bool`, `window_start` (MM-DD), `window_end` (MM-DD), `repeat [once|yearly|twice]`,
  `first_year`, `poster_phone`.
