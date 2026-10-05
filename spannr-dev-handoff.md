# Spannr Dev Handoff

For Claude Code. Everything needed to start work without re-explaining context.

Owner: Brent Wallace
Date: October 5, 2026

---

## What Spannr is

A longevity clinic directory and content site. Two audiences: people looking for a longevity clinic, and people buying longevity gear and services.

The business model behind it: the city guides produce a list of independent clinics, which become sales prospects for LongevityClinicMarketing.com, a separate agency Brent runs. Directory listings may become paid placements later.

## Stack

- Laravel (PHP), Blade templates
- Hosted on a Hostinger VPS
- Code in GitHub
- Routes are partly hardcoded in `routes/web.php`, which has caused CMS pages not to resolve in the past
- Admin CMS has Records (Organizations, People, Investors, Jobs, Events, Content, Glossary, Locations, Marketplace Listings) and supports parent/child records

## Working rules

1. Work on a branch, never directly on main.
2. Explain what you found before changing it. Brent wants to see where the logic lives.
3. Anything touching `routes/web.php` or redirects gets reviewed by the developer before merge. A syntax error there is a site-wide 500.
4. Test on staging before production.
5. No em dashes in any user-facing copy. That's a standing content rule.

---

## Decisions already made. Do not revisit these.

**Jobs board dies.** 650 job application pages from 2023 listings consume 52% of the crawl budget. Remove the section, redirect `/jobs` somewhere sensible.

**`/investors` goes.** Not part of the strategy. Redirect it.

**`/companies` stays.** It's where longevity companies and clinics get listed, and it ties into affiliate work.

**The national hub is `/articles/longevity-doctors`.** It pulls 74,800 impressions and 296 clicks in 3 months, the biggest page on the site. `/longevity-doctors` is an empty test page with zero traffic and 301s into the article. Any roadmap reference to building the hub at `/longevity-doctors` is wrong.

**Author of record is Brent Wallace.** Cody Shirk is the founder, an advisor and a partial owner, and stays on pieces he wrote.

**Team pages get cut to Brent and Cody only.** The others no longer work there.

**No clinic currently pays for inclusion or placement.** That may change later, and would be labeled.

---

## Priority order

Do these three first. They carry most of the value.

1. DEV-1, titles and meta descriptions, 208 pages
2. DEV-4, crawl hygiene, jobs board removal
3. DEV-2, redirects and link fixes (blocked, see below)

Everything else can wait until Q1 2027.

**DEV-2 is blocked.** It includes 301s for merged city guides. The content merge is being done by Brent's writer and the final redirect list isn't ready. The non-city parts of DEV-2 (404s, internal links to redirects, sitemap, robots.txt) can proceed now.

---

## robots.txt

Current file:

```
User-agent: *
Disallow:
```

Replace with:

```
User-agent: *
Disallow: /jobs/apply/
Disallow: /login

Sitemap: https://spannr.com/sitemap.xml
```

Two notes on this:

Disallowing `/jobs/apply/` is safe because those pages are already noindexed, so there's no index equity to lose. Do not disallow `/jobs` itself until after it's been redirected and Google has processed the redirect. Blocking a URL before Google sees its redirect means Google never sees the redirect.

`/confirmed` needs a noindex meta tag, not a Disallow. It's the post-newsletter-signup page and needs to stay trackable as a GA4 conversion.

---

## The ten tickets

Source: SE Ranking site audit 358636, October 5 2026. Crawled 1,239 URLs, scored 86/100, 299 errors and 514 warnings. Full per-issue URL lists are exportable from that audit.

### DEV-1: Unique titles and meta descriptions (3h) — DO THIS FIRST

**Problem.** 208 URLs share duplicate titles and have no meta description. 71 pages output two `<title>` tags. 1 page has none. 69 pages have more than one H1.

Affected: nearly every `/marketplace/clinic/`, `/marketplace/doctor/`, `/marketplace/retreat/` and `/about/` page.

The cost: `/marketplace/clinic/cascade-integrative-medicine` gets 678 impressions at position 6.6 with a 0.15% CTR.

**Fix.** Template-driven titles and descriptions:

- Clinic: `{Clinic name} – Longevity Clinic in {City}, {ST} | Spannr`
  Description: `{Clinic name} in {City}, {ST} offers {top 3 services}. See doctors, pricing model and how to book.`
- Doctor: `{Full name}, {credential} – Longevity Doctor in {City}, {ST} | Spannr`
  Description: `{Full name} is a {specialty} doctor in {City} focused on {top services}. Read the profile and book a consult.`
- Retreat: `{Name} – Longevity Retreat in {Location} | Spannr`
- Team page: `{Name} – {Role} at Spannr`, description from the first 155 characters of the bio

Drop `| Spannr` when a title would exceed 60 characters. Cap descriptions at 155. If a field is empty, fall back to the listing's own description field.

Then find the layout that prints a default `<title>` and H1 alongside the page's own, and remove the duplicate.

**Example URLs.** `/marketplace/clinic/the-biostation`, `/marketplace/doctor/ann-shippy-md`, `/marketplace/retreat/hippocrates-wellness`, `/about/brent`. Double titles on `/marketplace`, `/guide-to-longevity`, `/investors`, `/reports/2022-longevity-funding`, `/companies/regeneron`.

**Done when.** Re-run audit shows duplicate titles under 10, missing descriptions 0, multiple titles 0, missing title 0.

### DEV-4: Crawl hygiene (1h)

**Problem.** 650 noindexed URLs (649 job application pages plus `/login`) take 52% of the crawl. About 100 `/jobs?search=` parameter URLs duplicate `/jobs`.

**Fix.** Jobs board is being retired, so this is mostly deletion.

- Remove the jobs section
- 301 `/jobs` and `/jobs?search=*` to a sensible destination
- Update robots.txt per the section above
- Add `noindex` to `/confirmed`

**Done when.** Noindexed URLs are under 10% of the next audit's crawl.

### DEV-2: Redirects and link fixes (2h) — partly blocked

**Problem.** Duplicate city guides, 3 URLs returning 404, 16 pages linking internally to redirects, 2 temporary redirects, 2 redirected URLs in the sitemap, sitemap not declared in robots.txt.

**Fix, ready now:**

- 301 `/articles/longevity-supplements-what-they-are-benefits-and-more` → `/articles/best-longevity-supplements`
- 301 `/articles/beat-inflammation-anti-aging-100` → destination TBD from Brent
- Redirects must ignore query strings. One of those 404s still gets traffic from ChatGPT with `?utm_source=chatgpt.com` appended
- Update internal links on the 16 pages pointing to redirects so they point at final URLs. Includes `/articles/longevity-clinics`, `/articles/longevity-doctors-denver-co`, `/articles/cryotherapy-and-longevity`
- Change the 2 temporary redirects (302/307) to 301
- Generate the XML sitemap from indexable, 200-status, self-canonical URLs only
- 301 `/longevity-doctors` → `/articles/longevity-doctors`
- Redirect `/investors`

**Fix, waiting on the writer's redirect list:**

- City guide consolidations. Content is being merged onto the older URLs, and the newer ones redirect into them. For example `/articles/longevity-doctors-new-york-city-ny` → `/articles/longevity-doctors-new-york`. Same pattern for Los Angeles, Miami and Austin. Direction is old URL wins, because the old URLs carry the authority and the new content moves onto them.

**Done when.** Audit shows 4xx 0, internal links to 3xx 0, temporary redirects 0, 3xx in sitemap 0.

### DEV-3: Timeouts, speed, compression (2h)

**Problem.** 2 articles time out for the crawler. 9 pages load slowly, 5 HTML documents oversized, 14 images too large. JS and CSS served uncompressed on 579 pages. 6 external scripts return 3xx/4xx/5xx.

**Fix.** Profile server response on the timing-out articles and get TTFB under 800ms. Enable gzip or Brotli for `text/css` and `application/javascript`. Convert the 14 large images to WebP with explicit width and height. Remove or replace the 6 failing external scripts.

**Example URLs.** `/articles/best-longevity-supplements`, `/articles/low-testosterone`. Both are high-intent pages that may not be getting served to Googlebot at all.

**Done when.** Timeouts 0, slow pages 3 or fewer, uncompressed JS/CSS 0, broken external JS 0.

### DEV-5: Structured data (4h)

**Problem.** Schema could not be verified in the audit. Assume missing or partial.

**Fix.** Start with a Rich Results Test on one clinic, one doctor, one article and the homepage. Then add JSON-LD:

- Clinic pages: `MedicalClinic` with name, url, image, telephone, `PostalAddress`, `GeoCoordinates`, `medicalSpecialty`, `availableService`, `sameAs`. No `aggregateRating` unless Spannr collects its own verified reviews.
- Doctor pages: `Physician` with name, url, image, address, `medicalSpecialty`, `sameAs`, linked to the clinic page
- Articles and glossary: `Article` or `MedicalWebPage` with headline, `datePublished`, `dateModified`, `author` (Person linking to `/about/{slug}` with `sameAs`), `reviewedBy` when a reviewer exists, `publisher`
- All listing and article pages: `BreadcrumbList`
- Homepage: `Organization` with logo, url, `sameAs`, founder

**Done when.** All four templates validate in Rich Results Test and GSC Enhancements show 0 errors.

### DEV-6: Newsletter signup blocks and tracking (3h)

**Problem.** Signup placement unknown. Signups happen on beehiiv. `/confirmed` is orphaned and untracked.

**Fix.** One reusable signup component (beehiiv embed or API) in three variants: inline after roughly 40% of an article or glossary entry, end of article, and below the details on listing pages and after the first clinic list in city guides. No full-screen mobile interstitials. On success, redirect to `/confirmed` and fire a GA4 event `newsletter_signup` with `page_location` and `block_position`.

**Done when.** Component renders on all article, glossary, city-guide and listing templates, and GA4 records the event with position.

### DEV-7: Author and reviewer box (1h)

**Problem.** Visible authorship and review dates missing on health content. This matters because Spannr is a YMYL site.

**Fix.** Under the H1: "By {author}" linking to `/about/{slug}`, "Medically reviewed by {name, credential}" when set, and "Updated {date}". At the end, a short author bio box and a link to the editorial policy. Add CMS fields for reviewer and last-reviewed date.

**Done when.** Box appears on all article and glossary templates and matches DEV-5 schema.

### DEV-8: Links between listings and city guides (3h)

**Problem.** 135 pages have only one inbound internal link. Searches for listed doctors land on city guides instead of their profile pages. Example: "dr richard firshein" ranks #16 on the NYC guide even though `/marketplace/doctor/richard-firshein` exists.

**Fix.** On clinic and doctor pages, add "More longevity clinics in {City}" (up to 6 same-city listings), plus links to the city guide and the national hub. On city guides, link every listed clinic or doctor name to its profile, or add a "Clinics in this guide" list at the end.

**Done when.** Pages with one or fewer inbound internal links drop from 135 to under 30.

### DEV-9: National city index hub (3h)

**Problem.** Nothing groups listings by state and city.

**Fix.** Build the index on `/articles/longevity-doctors`, not `/longevity-doctors`. Render server-side as states → cities, each with its city guide link and listing count. Add it to the main navigation and footer.

**Done when.** Hub is linked from nav and footer and links to every city guide.

### DEV-10: Homepage rendering and badge (2h)

**Problem.** Fetching the homepage returned headings for "Popular Longevity Services" and "Most Recent" with no items, which suggests client-side loading.

**Fix.** Check raw HTML with curl and GSC URL Inspection. If items load via JavaScript, render them server-side. Separately, build a small static SVG "Featured on Spannr" badge with a copy-paste snippet linking to each listing's profile URL, shown on the listing page.

**Done when.** Homepage sections appear in raw HTML and the badge snippet is live on listing pages.

---

## Known issues outside the ticket list

- `app.spannr.com` exists as a leftover from a hosting migration. Google has nothing indexed there, so it's low priority, but a path-preserving 301 to spannr.com is cheap insurance. Remove any robots.txt Disallow on that subdomain first, or Google can't see the redirect.
- The articles listing at `/articles` sorts by published date rather than last modified, so rewritten articles stay buried. Changing it to sort by modified date was agreed but may not be done.
- 232 outbound links on content pages return 4xx. Dead citations on health articles.
- Several marketplace listings are French practices. The audience is US only.
- `/articles/longevity-books` still has "2024" in its title.

## Context that explains some of the mess

Spannr's article library was largely not being indexed. GSC reported "Crawled, currently not indexed" on the pages checked, and the cause was duplication: four pages about sleep, four about personalized medicine, duplicate city guides, roughly 30 pages of dated 2022 industry news. A consolidation effort is underway on the content side, which is where the redirect list for DEV-2 comes from.
