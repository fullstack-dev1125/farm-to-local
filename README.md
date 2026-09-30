# Farm to Local — build kit

Everything your developer (or Claude Code) needs to build the Farm to Local application (Laravel 11), plus what a part-time
reviewer needs to keep it safe and deploy it.

```
CLAUDE.md                   conventions Claude Code reads first — stack, design fidelity rules, product rules
docs/01-spec.md             product spec: seller types, categories, buy methods, verification, plans, rules
docs/02-data-model.md       every table + the radius query + seeds
docs/03-routes.md           URL structure and which design file each route reproduces
docs/04-milestones.md       M1–M5 with acceptance criteria and tests
docs/05-reviewer-checklist.md   what the reviewer checks on each PR + production runbook
database/migrations/        hand-written Laravel migrations for the whole model (run, fix, commit)
designs/                    81 finished HTML pages + assets (fonts, CSS, mobile.css, JS, photos, icons)
                            open designs/index.html; designs/mobile.html = phone screens; designs/admin.html = staff backend
docs/06-design-rules.md     design system: palette, badges, square photos, shelves, footer, OG images
docs/07-mobile.md           mobile layer: breakpoints, patterns, which screens are done
docs/08-admin.md            staff backend: queues, importer, sponsors, email builder, settings, money
docs/09-message-templates.md  every SMS and email the system sends (Twilio/Postmark)
docs/10-revenue-map.md      where each dollar comes from and where it renders
docs/11-taxonomy.md         "What kind?" tag groups per category — the seed list, seller-suggested tags
prompts/first-session.md    the exact prompt to start Claude Code with, and how to set up the repo
JOB-LISTING-reviewer.md     listing for the part-time Laravel reviewer/deployer (Philippines)
```

Start: read `prompts/first-session.md`. Regenerated Sep 28, 2026 — supersedes any earlier kit.

Sessions after the first: open Claude Code in the repo and say "Read CLAUDE.md and docs/04-milestones.md;
we're on Milestone N. Read SESSION-(N-1).md for context. Continue." Send any new design page as a file in
`designs/` and ask for the matching route.
