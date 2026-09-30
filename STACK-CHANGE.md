# Why Farm to Local runs on Laravel 12, not Laravel 11

**Short version:** Laravel 11 no longer gets security fixes, and its last release still has known security
holes. Laravel 12 is the same framework one year newer, with those holes fixed. We switched to 12 with your
approval. Nothing about the product, the design or the plan changes.

## What Laravel is, and what "11" and "12" mean

Laravel is the toolkit the site is built with. It handles pages, the database and forms. A new major
version comes out each year. Each version gets security fixes for a limited time, then stops.

- **Laravel 11** (2024): security fixes have ended.
- **Laravel 12** (2025): still gets security fixes, until about February 2027.

The build instructions (CLAUDE.md) say "Laravel 11". That was right when they were written. By the time
building started, 11 had reached the end of its support.

## What went wrong when we tried Laravel 11

The standard install tool refused to install it: every Laravel 11 release is flagged for security problems
that will never be fixed. The ones that matter for this site:

| Problem | How serious | Why it matters to us |
|---|---|---|
| Email checking can be tricked into accepting hidden extra lines | **High** | Sellers, the contact form and the advertise form all take email addresses. Attackers can use this to slip extra content into emails the site sends. |
| Time-limited links can be reused on other pages | Medium | Links like "confirm your account" or "claim your listing" could open more than intended. |
| Error pages can run someone else's script | Low | Mostly affects the development setup, but it shouldn't ship. |

We could have forced Laravel 11 in and told the tool to ignore these warnings. That means launching with known
holes, and the reviewer's checklist says to reject exactly that.

## What we chose instead

Laravel **12.69**, the current patched release. The security scan (`composer audit`) now reports **no known
problems**. The testing tool stays on Pest 3, as planned.

## What this changes for you

- **Product, design, features, milestones:** no change.
- **Cost and timeline:** no change. Laravel 12 is built the same way as 11, so everything planned for 11 works
  as written.
- **Hosting:** no change. Same PHP 8.3 and MySQL 8, same Laravel Forge setup as in docs/05.
- **Paperwork:** CLAUDE.md and docs/04 still say "Laravel 11". Please update them to "Laravel 12" (or let us), so
  future work and reviews use the right version.

## What to plan for

Laravel 12's security support ends around **February 2027**. Plan to move to the next version before then. It
is normally a routine task of a day or two, well worth scheduling alongside launch hardening (Milestone 5).
