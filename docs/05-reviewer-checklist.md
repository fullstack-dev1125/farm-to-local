# Reviewer checklist (part-time Laravel developer)

You review every milestone PR, own staging and production, and take the pieces that punish mistakes.
You are not being asked to build features from scratch; you are asked to keep an AI-built codebase safe,
correct and deployable. Say "no" in review when it isn't.

## On every PR
- [ ] Runs locally from README in < 1 hour; `php artisan test` green; migrations up and down
- [ ] No secrets in the repo; `.env.example` covers every key used
- [ ] FormRequest validation on every POST; mass-assignment guarded (`$fillable`)
- [ ] Authorization: policies on Seller, Conversation, NeighborShare, Verification — a seller can only edit
      their own; a buyer only sees their own conversations; admin routes behind Filament auth + role check
- [ ] Rate limits: login codes (3 / 10 min per destination, 5 attempts), messages (20 / hour per user),
      share posts (3 / day per phone), report form (5 / day per IP), `/go/` redirects (no open redirect: targets
      only from DB)
- [ ] Uploads: mime + size checks, images re-encoded, verification docs on a private disk, deleted after decision
- [ ] Neighbor shares: raw coordinates never rendered; snapping applied before save
- [ ] Sponsors never appear in search/shop HTML (grep the test)
- [ ] N+1 queries checked on results, shop, dashboard (Debugbar or `DB::listen` in tests)
- [ ] Design fidelity: screenshot vs `designs/*.html` at 1280px attached to the PR

## Milestone-specific
- M1: spatial columns exist with SRID 4326; importer idempotent; ZIP seed complete
- M2: geography caches bust on save; threshold rule tested; sitemap valid
- M3: Twilio/Postmark keys in env only; code hashes never logged; verification docs private; claim flow can't be
      used to take over a listed (non-seeded) seller
- M4: Postmark inbound webhook verified by signature; expiry job scheduled; extend-once enforced server-side
- M5: Stripe webhooks verified; Pro/Partner exclusivity enforced in DB and UI; statements idempotent per period;
      tracked links dedupe per session

## Production runbook (Laravel Forge + one VPS to start)
1. Forge server (2 GB / 2 vCPU is enough to launch), PHP 8.3, MySQL 8, Redis, nginx. Site: farmtolocal.com + staging subdomain.
2. Env: APP_ENV, APP_KEY, DB, REDIS, QUEUE_CONNECTION=redis, MAIL (Postmark), TWILIO_*, STRIPE_*, AWS_* (or DO Spaces), SENTRY_DSN.
3. Deploy script: `composer install --no-dev --optimize-autoloader && php artisan migrate --force &&
   php artisan config:cache route:cache view:cache && php artisan queue:restart`
4. Scheduler (cron every minute): share expiry, statement generation (1st of month), sitemap nightly, cache warm for launch cities.
5. Backups: nightly MySQL dump to object storage (Forge backups or `spatie/laravel-backup`), 30-day retention; test a restore once.
6. Monitoring: Sentry for errors; uptime check on `/` and `/{state}/{city}`; Forge alerts.
7. Cloudflare in front: DNS, TLS, caching for `/assets/*`, basic WAF rules, rate limiting on `/login` and `/share/new`.
8. Before real users: run the security checklist above end to end on production once; rotate every key that was ever in a dev `.env`.

## What to escalate to the owner
- Any rule in CLAUDE.md "Non-negotiable product rules" that a change would break
- Anything that touches money math (fees, statements) or verification decisions
- Design changes — never make them in code; send the page back to be regenerated
