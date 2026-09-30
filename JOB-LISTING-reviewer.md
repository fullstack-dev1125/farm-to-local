# Part-time Laravel developer — code review, hardening and deployment (remote, Philippines)

Farm to Local is a nationwide directory of local farms, ranches, bakers, fishermen, nurseries and farmers
markets, searchable by ZIP. It's a directory, not a marketplace: sellers get a free shop page, buyers contact
them directly, no checkout. The application is being built in-house in Laravel 11 from finished designs.
I'm hiring one developer, part-time and long-term, to own three things:

1. Review — every milestone arrives as a pull request. You review it for security, data integrity,
   performance and Laravel best practice against a written checklist, and you say no when it isn't right.
2. Hardening — you take the pieces that punish mistakes: passwordless SMS/email login (Twilio, Postmark),
   Stripe Billing and monthly invoicing, file uploads, rate limiting, the MySQL spatial radius query.
3. Production — Laravel Forge on a VPS, staging + production, backups you've tested restoring, Sentry,
   Cloudflare, the scheduler and queues. You are the person who gets paged.

Stack: Laravel 11, PHP 8.3, MySQL 8, Blade + Alpine.js, Filament v3, Cashier, Pest. No WordPress, no SPA.

Hours and pay: 10–15 hours/week to launch (about 3 months), then 5–10 hours/week ongoing.
₱25,000–40,000/month for the part-time term, reviewed at launch. Paid via Wise on the 1st and 15th.

Time zone: I'm in California (PST). Async is fine — Loom, written PR reviews, a daily end-of-day note —
but I need one fixed 30-minute call per week; tell me which hours you can hold.

You:
- 4+ years Laravel in production, with at least one app you deployed and ran yourself (Forge or equivalent)
- Filament experience specifically; Stripe Billing/Cashier at least once; queues and the scheduler
- You've reviewed other people's code before and can point to written reviews
- Comfortable reading AI-written code critically. This role is judgment, not typing.

To apply (skip any of these and I'll skip you):
1. A 5-minute screen recording walking through a Laravel app you built — the code, not the UI
2. Which of these three would you fix first in a new codebase and why: missing FormRequests, no rate limit on
   login codes, or N+1 queries on the search page
3. The hours (PST) you can commit to each week and the one fixed weekly call slot
4. One thing in this listing you'd push back on

Paid test task before hiring (4 hours at ₱400/hr): review a real PR from the project and write the review.
