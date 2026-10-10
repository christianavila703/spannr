<?php

namespace App\Support;

use App\Models\Article;
use App\Models\Content;
use App\Models\Glossary;
use App\Models\Listing;
use App\Models\Person;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\HtmlString;

/**
 * JSON-LD for the public pages (DEV-5).
 *
 * Rules every block follows:
 *  - Only properties with real data are output. Nulls, empty and whitespace-only strings and empty arrays are removed
 *    before encoding, and an object left with nothing but its @type is removed too.
 *  - There is never an aggregateRating or a review.
 *  - Encoded with JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE, plus JSON_HEX_TAG and JSON_HEX_AMP so that a
 *    "</script>" (or "<!--") inside the data is written with hex escapes for "<" and ">" and cannot end the script tag.
 *  - One block per type per page. The Article and BreadcrumbList blocks of articles used to come from the SEO package;
 *    they are printed here instead (the package's FAQPage block is unchanged), so there are no duplicates.
 */
class StructuredData
{
    public const ORGANIZATION_NAME = 'Spannr';
    public const FOUNDER_NAME = 'Cody Shirk';

    /** The social profile links the site footer already shows. A test keeps this list and the footer in step. */
    public const SOCIAL_PROFILES = [
        'https://www.facebook.com/spannr',
        'http://www.twitter.com/spannr_',
        'https://www.linkedin.com/company/spannr',
        'https://www.instagram.com/spannr_/',
    ];

    private const LOGO_ASSET = 'resources/images/logo-color.png';

    /* ------------------------------------------------------------------ pages */

    public static function forHomepage(?Content $seo = null): HtmlString
    {
        // The homepage prints the stored head HTML from content.seo as it is. If that already holds an Organization
        // JSON-LD block, adding another would be a duplicate.
        if ($seo && self::storedHtmlHasOrganization((string) $seo->seo)) {
            return new HtmlString('');
        }

        return self::scripts([self::organization()]);
    }

    public static function forArticle(Article $article): HtmlString
    {
        return self::scripts([
            self::article($article),
            self::breadcrumbs([
                'Home' => route('home'),
                'Insights' => route('public.content.articles.index'),
                (string) $article->name => $article->url,
            ]),
        ]);
    }

    public static function forGlossary(Glossary $entry): HtmlString
    {
        return self::scripts([
            self::glossaryEntry($entry),
            self::breadcrumbs([
                'Home' => route('home'),
                'Glossary' => route('public.content.glossary.index'),
                (string) $entry->name => $entry->url,
            ]),
        ]);
    }

    /** Clinic and Doctor listings only. Coach and Retreat listings output nothing. */
    public static function forListing(Listing $listing): HtmlString
    {
        $entity = match (strtolower((string) $listing->type)) {
            strtolower(Listing::TYPE_CLINIC) => self::clinic($listing),
            strtolower(Listing::TYPE_DOCTOR) => self::physician($listing),
            default => null,
        };

        if ($entity === null) {
            return new HtmlString('');
        }

        return self::scripts([
            $entity,
            self::breadcrumbs([
                'Home' => route('home'),
                'Longevity Marketplace' => route('public.marketplace.index'),
                (string) $listing->name => $listing->show_url,
            ]),
        ]);
    }

    /* ------------------------------------------------------------------ blocks */

    public static function organization(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => self::ORGANIZATION_NAME,
            'url' => route('home'),
            'logo' => self::logo(),
            'sameAs' => self::SOCIAL_PROFILES,
            'founder' => ['@type' => 'Person', 'name' => self::FOUNDER_NAME],
        ];
    }

    public static function article(Article $article): array
    {
        $article->loadMissing(['authors.person', 'seo']);

        $published = $article->date?->toIso8601String();
        // The hand-set date only. updated_at changes on any save and is never used here.
        $modified = $article->content_updated_at?->toDateString() ?? $published;

        $main = $article->authors->first();
        $author = $main
            ? [
                '@type' => 'Person',
                'name' => $article->author_byline,
                'url' => filled($main->slug) ? route('public.content.about.user', $main->slug) : null,
                'jobTitle' => $main->title,
                'sameAs' => self::personSameAs($main->person),
            ]
            : ['@type' => 'Organization', 'name' => self::ORGANIZATION_NAME, 'url' => route('home')];

        $reviewerName = trim((string) $article->reviewer_name);

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $article->url],
            'headline' => $article->name,
            'description' => $article->structuredDataDescription(),
            'image' => $article->structuredDataImage(),
            'datePublished' => $published,
            'dateModified' => $modified,
            'author' => $author,
            'publisher' => self::publisher(),
            'reviewedBy' => $reviewerName === '' ? null : [
                '@type' => 'Person',
                'name' => $reviewerName,
                'jobTitle' => $article->reviewer_title,
            ],
        ];
    }

    /**
     * Glossary entries are an Article too: the existing code does not tell them apart from articles in its markup.
     * They have no author, no image, and no hand-set updated date, so dateModified equals datePublished, and
     * datePublished is the row's created_at.
     */
    public static function glossaryEntry(Glossary $entry): array
    {
        $published = $entry->created_at?->toIso8601String();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $entry->url],
            'headline' => $entry->name,
            'description' => $entry->meta_description,
            'datePublished' => $published,
            'dateModified' => $published,
            'publisher' => self::publisher(),
        ];
    }

    public static function clinic(Listing $listing): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'MedicalClinic',
            'name' => $listing->name,
            'url' => $listing->show_url,
            'image' => $listing->featured_image_url ?: null,
            'telephone' => $listing->phone,
            'address' => self::postalAddress($listing),
            'geo' => self::geo($listing),
            'medicalSpecialty' => self::specialties($listing),
            'availableService' => $listing->treatments
                ->pluck('name')->map(fn ($name) => trim((string) $name))->filter()->unique()->sort()->values()
                ->map(fn ($name) => ['@type' => 'MedicalProcedure', 'name' => $name])->all(),
            'sameAs' => self::listingSameAs($listing),
        ];
    }

    public static function physician(Listing $listing): array
    {
        $clinics = self::linkedClinics($listing)
            ->map(fn (Listing $clinic) => ['@type' => 'MedicalClinic', 'name' => $clinic->name, 'url' => $clinic->show_url])
            ->values()
            ->all();

        return [
            '@context' => 'https://schema.org',
            '@type' => 'Physician',
            'name' => $listing->name,
            'url' => $listing->show_url,
            'image' => $listing->featured_image_url ?: null,
            'address' => self::postalAddress($listing),
            'medicalSpecialty' => self::specialties($listing),
            'sameAs' => self::listingSameAs($listing),
            'memberOf' => count($clinics) === 1 ? $clinics[0] : $clinics,
        ];
    }

    /**
     * @param array<string, string> $trail page name => URL, in order, ending with the current page
     */
    public static function breadcrumbs(array $trail): array
    {
        $items = [];

        foreach ($trail as $name => $url) {
            $items[] = [
                '@type' => 'ListItem',
                'position' => count($items) + 1,
                'name' => (string) $name,
                'item' => $url,
            ];
        }

        return ['@context' => 'https://schema.org', '@type' => 'BreadcrumbList', 'itemListElement' => $items];
    }

    /* ------------------------------------------------------------------ output */

    /**
     * One <script type="application/ld+json"> per non-empty block.
     *
     * @param array<int, array<string, mixed>> $blocks
     */
    public static function scripts(array $blocks): HtmlString
    {
        $html = '';

        foreach ($blocks as $block) {
            $json = self::encode($block);

            if ($json !== '') {
                $html .= '<script type="application/ld+json">' . $json . '</script>' . "\n";
            }
        }

        return new HtmlString($html);
    }

    /** Cleaned and encoded; an empty string when nothing is left. Never throws: structured data must not break a page. */
    public static function encode(array $data): string
    {
        $data = self::clean($data);

        // A block with nothing but @context and @type has no data in it.
        if (array_diff(array_keys($data), ['@context', '@type']) === []) {
            return '';
        }

        $json = json_encode(
            $data,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_INVALID_UTF8_SUBSTITUTE
        );

        return $json === false ? '' : $json;
    }

    /** Removes null, empty and whitespace-only strings and empty arrays, at every depth. */
    public static function clean(array $data): array
    {
        $out = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = self::clean($value);

                // An object that kept only its @type (a PostalAddress with no address fields) is empty.
                if ($value === [] || array_keys($value) === ['@type']) {
                    continue;
                }
            } elseif (is_string($value)) {
                $value = trim($value);

                if ($value === '') {
                    continue;
                }
            } elseif ($value === null) {
                continue;
            }

            $out[$key] = $value;
        }

        // Lists stay lists: a gap left by a removed item must not turn [..] into {"1":..}.
        return $data === [] || array_keys($data) === range(0, count($data) - 1) ? array_values($out) : $out;
    }

    /* ------------------------------------------------------------------ pieces */

    private static function publisher(): array
    {
        return [
            '@type' => 'Organization',
            'name' => self::ORGANIZATION_NAME,
            'url' => route('home'),
            'logo' => self::logo(),
        ];
    }

    private static function logo(): ?array
    {
        $url = self::logoUrl();

        return $url ? ['@type' => 'ImageObject', 'url' => $url] : null;
    }

    /** The coloured Spannr wordmark, served through Vite like the other images. Null if the asset cannot be resolved. */
    public static function logoUrl(): ?string
    {
        try {
            $url = (string) Vite::asset(self::LOGO_ASSET);
        } catch (\Exception) {
            return null;
        }

        return $url === '' ? null : $url;
    }

    private static function postalAddress(Listing $listing): array
    {
        $address = $listing->primaryAddress;

        if (! $address) {
            return [];
        }

        $isUs = in_array(strtolower(trim((string) $address->country_code)), ['us', 'usa', 'united states'], true);
        $countryCode = trim((string) $address->country_code);
        $region = trim((string) $address->state_code) !== '' ? trim((string) $address->state_code) : trim((string) $address->state);

        return [
            '@type' => 'PostalAddress',
            'streetAddress' => trim(trim((string) $address->street) . ' ' . trim((string) $address->street2)),
            'addressLocality' => $address->city,
            'addressRegion' => strlen($region) <= 3 ? strtoupper($region) : $region,
            'postalCode' => $address->postal_code,
            'addressCountry' => $isUs ? 'US' : (strlen($countryCode) === 2 ? strtoupper($countryCode) : $address->country),
        ];
    }

    private static function geo(Listing $listing): array
    {
        $address = $listing->primaryAddress;

        foreach ([[$address?->latitude, $address?->longitude], [$listing->latitude, $listing->longitude]] as [$lat, $lng]) {
            if (is_numeric($lat) && is_numeric($lng)
                && abs((float) $lat) <= 90 && abs((float) $lng) <= 180
                && ((float) $lat !== 0.0 || (float) $lng !== 0.0)) {
                return ['@type' => 'GeoCoordinates', 'latitude' => (float) $lat, 'longitude' => (float) $lng];
            }
        }

        return [];
    }

    /** One name as a string, several as a list. Specialties come from the "Clinic Focus" tag set. */
    private static function specialties(Listing $listing): string|array|null
    {
        $names = $listing->clinicFocus
            ->pluck('name')->map(fn ($name) => trim((string) $name))->filter()->unique()->sort()->values();

        return match ($names->count()) {
            0 => null,
            1 => $names->first(),
            default => $names->all(),
        };
    }

    /** Listings keep no social columns, only a website. */
    private static function listingSameAs(Listing $listing): array
    {
        return array_values(array_filter([self::externalUrl($listing->website)]));
    }

    private static function personSameAs(?Person $person): array
    {
        if (! $person) {
            return [];
        }

        return array_values(array_unique(array_filter([
            self::externalUrl($person->website),
            $person->facebook_url,
            $person->twitter_url,
            $person->linkedin_url,
            $person->instagram_url,
        ])));
    }

    /** A usable absolute URL ("example.com" becomes https://example.com), or null. */
    private static function externalUrl(?string $url): ?string
    {
        $url = trim((string) $url);

        if ($url === '') {
            return null;
        }

        if (! preg_match('#^https?://#i', $url)) {
            $url = 'https://' . $url;
        }

        $host = parse_url($url, PHP_URL_HOST);

        return (filter_var($url, FILTER_VALIDATE_URL) && is_string($host) && str_contains($host, '.')) ? $url : null;
    }

    /**
     * Public clinics this doctor is connected to, from either side of listing_relationships
     * (the doctor lists the clinic, or the clinic lists the doctor), alphabetical.
     *
     * @return Collection<int, Listing>
     */
    private static function linkedClinics(Listing $doctor): Collection
    {
        $reverseIds = DB::table('listing_relationships')
            ->where('listable_type', $doctor->getMorphClass())
            ->where('listable_id', $doctor->id)
            ->pluck('listing_id');

        $reverse = $reverseIds->isEmpty() ? collect() : Listing::public()->whereIn('id', $reverseIds)->get();

        return $doctor->connectedListings
            ->merge($reverse)
            ->filter(fn (Listing $other) => strcasecmp((string) $other->type, Listing::TYPE_CLINIC) === 0
                && strcasecmp((string) $other->status, Listing::STATUS_PUBLIC) === 0)
            ->unique('id')
            ->sortBy('name')
            ->values();
    }

    private static function storedHtmlHasOrganization(string $html): bool
    {
        return stripos($html, 'application/ld+json') !== false
            && preg_match('/"@type"\s*:\s*(?:\[[^\]]*)?"Organization"/i', $html) === 1;
    }
}
