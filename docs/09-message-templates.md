# SMS and email templates

Twilio (10DLC-registered) for SMS, Postmark for email. SMS ≤ 160 chars where possible; variables in `{braces}`.
"Reply STOP to opt out" is appended automatically on the first message to any number. Phone numbers are never
shown to the other party: every buyer↔seller thread gets a proxy number (Twilio), and replies from the seller's
phone are threaded back into the dashboard inbox. Buyers get at most one marketing text per week (the weekly
email's SMS twin, if they chose SMS); sellers get transactional texts only. Every send is logged on the thread.

| ID | Trigger | To | Body |
|---|---|---|---|
| SMS-01 | login | anyone | `Farm to Local code: {code}. Expires in 5 min. Never share it.` |
| SMS-02 | new buyer message | seller | `New message from {buyer_first} ({city}) about {product}: "{first_80_chars}" Reply to this text or open {short_link}` |
| SMS-03 | seller reply | buyer | `{seller} replied: "{first_80_chars}" Reply here to answer. Their number stays private.` |
| SMS-04 | verification approved | seller | `You're Verified ✓ on Farm to Local. Your page: {page_url}. Print a stand sign: {sign_url}` |
| SMS-05 | ask for more | seller | `Farm to Local: we need one more thing to verify you — {reason}. Text a photo back to this number.` |
| SMS-06 | booking request | seller | `Booking request from {buyer_first} ({city}): {service}, {when}. Reply with a date or open {short_link}` |
| SMS-06b | booking confirmed | buyer | `{seller} confirmed {service} for {date}. We'll text you a week before. Reply CHANGE to pick another date.` |
| SMS-07 | seasonal share, T-7 days | poster | `Your "{share_title}" share goes live {date}. Reply YES to post it, STOP to skip this year.` |
| SMS-08 | listing untouched 60 d | seller | `Farm to Local: your page hasn't changed in 60 days. Reply UP if prices/hours are still right, or open {dash_url}. After 90 days we mark it "may be out of date".` |
| SMS-09 | city launched | waitlist | `Farm to Local is live in {city} — {n} verified sellers within 25 mi of you. {city_url}` |
| SMS-10 | seeded page created | owner on record | `Someone listed your farm on Farm to Local from public records. Claim it free (2 min) and add your prices: {claim_url}` |
| SMS-11 | report closed | reporter | `Thanks for the report on {seller}. We reviewed it and {outcome}.` |
| SMS-12 | redemption logged | seller | `Logged: {code} · ${sale} sale · ${fee} fee (billed {date}). {month} total: {n}.` |
| SMS-13 | photo onboarding | seller | `Got your photo. We'll turn it into your product list within 48 h and text you to check it.` |
| SMS-14 | empty-search alert | buyer | `A {category} seller just listed within {radius} mi of {zip}: {seller}. {url}` |
| EM-01 | Thursday 6:00 local | buyers (state) | built in the email builder — see docs/08 |
| EM-02 | 1st of month | Partner sellers | `Your {month} statement: {n} redemptions · ${tracked} tracked · ${fee} due {date}.` + line items |
| EM-03 | buyer signup | buyer | `You're in. One text or email per week, in-season only. Change ZIP or stop any time.` |
| EM-04 | seller listed | seller | `Your page is live. Three things that double clicks: a photo of you, hours, and one in-season item.` |
| EM-05 | verification decision | seller | same as SMS-04/05 with the reviewed document type and date |
| EM-06 | Pro receipt / renewal | seller | Cashier default, branded |

## "Text us a photo" onboarding (rule, not a template)
Any listed seller can text a photo of their price board, stand or product list to the Farm to Local number.
A reviewer (or an extraction job with review) turns it into draft products within 48 h; the seller gets SMS-13
then a "check your list" link. Same path is offered at step 2 of listing ("Rather not type?") and in the
dashboard's Quick actions ("Add photos · Text us one, we crop it").
