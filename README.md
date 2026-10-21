# Spannr

Spannr is a longevity clinic directory and content site. It serves two audiences: people looking for a longevity clinic or doctor, and people researching longevity products and services.

The site is a Laravel 9 app rendered with Blade and Livewire, with an admin CMS for managing its records.

## What's in it

| Area | Public URL | What it is |
| --- | --- | --- |
| Articles | `/articles`, `/articles/{slug}` | Editorial content, including the city guides and the national hub at `/articles/longevity-doctors` |
| Glossary | `/glossary`, `/glossary/{slug}` | Longevity terms |
| Marketplace | `/marketplace`, `/marketplace/{type}/{slug}` | Clinic, doctor and retreat listings, plus category pages (sauna, cryotherapy, IV therapy and others) |
| Companies | `/companies`, `/companies/{slug}` | Longevity companies, with stock data and charts |
| About | `/about`, `/about/{slug}` | Team and author pages |
| Admin CMS | `/admin` | Records for organizations, people, content, glossary, locations, marketplace listings and more. Requires login. |

The jobs board and `/investors` have been retired and now redirect elsewhere.

## Tech stack

- **Backend:** PHP 8, Laravel 9, Livewire 2, Sanctum
- **Frontend:** Blade, Bootstrap 5, Sass, Vite 3
- **Data:** MySQL, Redis (queues via Horizon), Algolia (search via Scout)
- **Services:** Stripe, beehiiv and Mailchimp (newsletter), Resend and Mailgun (mail), Polygon (stock data), Geoapify (geocoding), Sentry (errors), Slack (alerts)
- **Hosting:** Hostinger VPS, deployed by GitHub Actions

## Requirements

- PHP 8.0.2 or newer, with the `dom` extension
- Composer 2
- Node.js 20 and npm
- MySQL
- Redis, if you run queues through Horizon

## Getting started

```bash
git clone git@github.com:christianavila703/spannr.git
cd spannr

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database credentials, then set up the database:

```bash
php artisan migrate --seed
php artisan storage:link
```

Run the app in two terminals:

```bash
php artisan serve   # http://localhost:8000
npm run dev         # Vite dev server with hot reload
```

If you use Laravel Valet with a `spannr.test` site, Vite picks up the Valet certificate on its own and serves over HTTPS.

## Configuration

Every setting is in [.env.example](.env.example). Most third-party keys are optional locally. Leave a key empty and that feature simply does nothing.

| Variable(s) | Used for |
| --- | --- |
| `DB_*` | MySQL connection |
| `REDIS_*`, `QUEUE_CONNECTION` | Queues and Horizon |
| `ALGOLIA_APP_ID`, `ALGOLIA_SECRET`, `SCOUT_PREFIX` | Site search |
| `BEEHIIV_API_KEY`, `BEEHIIV_PUBLICATION_ID` | Newsletter signups |
| `MAILCHIMP_API_KEY` | Legacy newsletter sync |
| `RESEND_API_KEY`, `MAILGUN_*`, `MAIL_*` | Outgoing mail |
| `FORM_EMAIL_GENERAL` | Where site form submissions are emailed |
| `STRIPE_*` | Payments and subscriptions |
| `POLYGON_KEY` | Company stock data |
| `GEOAPIFY_API_KEY` | Address lookup for listings |
| `WORDPRESS_*` | Importing articles and glossary from WordPress |
| `SENTRY_LARAVEL_DSN` | Error reporting |
| `SLACK_ALERTS_WEBHOOK_URL`, `HORIZON_SLACK_WEBHOOK_URL` | Slack alerts |

Secrets belong in `.env` only and must never be committed. `tests/Feature/SecretsNotInCodeTest.php` checks for this.

## Testing

PHP tests use an in-memory SQLite database, so no setup is needed:

```bash
php artisan test
```

JavaScript tests use Node's built-in test runner:

```bash
npm test
```

## Scheduled jobs and queues

On the server, cron should call the Laravel scheduler every minute:

```
* * * * * cd /path/to/spannr && php artisan schedule:run >> /dev/null 2>&1
```

The scheduler is defined in [app/Console/Kernel.php](app/Console/Kernel.php). Active jobs:

- `record-locks:clear`, every minute
- `beehiiv:backfill`, hourly
- `magic-link-tokens:clear`, daily at 02:30

When `QUEUE_CONNECTION=redis`, run queue workers with `php artisan horizon`. The dashboard is at `/horizon`.

### Useful commands

| Command | What it does |
| --- | --- |
| `php artisan wordpress:sync-articles` | Import articles from WordPress |
| `php artisan wordpress:sync-glossary` | Import glossary entries from WordPress |
| `php artisan images:optimize` | Shrink oversized jpg and png images. Dry run unless `--apply` |
| `php artisan content:extract-embedded-images` | Move base64 images pasted into rich text out into resized files. Dry run unless `--apply` |
| `php artisan listings:report-non-us` | List marketplace listings with an address outside the US (read-only) |
| `php artisan beehiiv:backfill` | Send newsletter signups that never reached beehiiv. Dry run unless `--send` |

## Project layout

```
app/
  Console/Commands/   Artisan commands
  Http/Controllers/   Public/ (site) and Admin/ (CMS) controllers
  Http/Livewire/      Livewire components, such as forms
  Models/             Eloquent models
  Search/             Algolia search setup
config/               App and package config
database/             Migrations, factories, seeders
resources/
  views/              Blade templates
  js/, sass/          Frontend sources built by Vite
routes/web.php        All site, admin and redirect routes
tests/
  Feature/            PHPUnit tests
  js/                 Node tests
```

## Branches and deployment

- Work on a feature branch and open a PR into `staging`.
- Pushing to `staging` deploys to the staging site. Check your change there.
- Merge `staging` into `main` to deploy to production.

The [deploy workflow](.github/workflows/deploy.yml) connects to the VPS over SSH, resets the checkout to the pushed branch, and runs `npm install`, `npm run build`, `composer install` and `php artisan migrate --force`.

### Before you merge

- Get changes to `routes/web.php` and redirects reviewed. One syntax error there brings down the whole site with a 500.
- Don't use em dashes in user-facing copy.
- Make every new redirect a permanent `301`, and make sure it ignores query strings.
