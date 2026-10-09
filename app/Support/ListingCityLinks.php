<?php

namespace App\Support;

use App\Models\Address;
use App\Models\Article;
use App\Models\Listing;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

/**
 * Internal links between marketplace listings and the city guides (DEV-8).
 *
 * A listing's city and state are its primary address. A city guide's city (and, when the slug has one, its state) come from
 * its slug, parsed by CityGuideIndex: longevity-doctors-{city}-{st} or the older longevity-doctors-{city}. A city is reduced
 * to a normalized key (cityKey) so spellings match. State-coded guides match on city and state; stateless guides match on the
 * city alone, across US states. US addresses only; a listing with no usable city and state gets no city links.
 */
class ListingCityLinks
{
    public const NEXT_COUNT = 6;

    private const US_COUNTRY_CODES = ['us', 'usa', 'united states'];

    // Stateless guides (longevity-doctors-austin) are matched on the city name alone, so a guide that is about a city
    // outside the US must never pick up a US town of the same name (Paris, TX; London, KY). These are the known ones.
    private const NON_US_GUIDE_CITIES = ['bangkok', 'dubai', 'london', 'paris', 'singapore', 'sydney'];

    // Different spellings of one city. Both the listing side and the guide side go through cityKey().
    private const CITY_ALIASES = [
        'new york city' => 'new york',
        'nyc' => 'new york',
    ];

    /** "St. Louis", "st louis" and "Saint Louis" are one key; so are "New York" and "New York City". */
    public static function cityKey(?string $city): string
    {
        $key = Str::of(Str::ascii((string) $city))
            ->lower()
            ->replace(['.', ',', "'", '-'], ' ')
            ->squish()
            ->toString();

        $key = preg_replace('/^st /', 'saint ', $key);

        return self::CITY_ALIASES[$key] ?? $key;
    }

    /** Full state name from a two-letter code, or from a full name; null when it is neither. */
    public static function stateName(?string $stateCode, ?string $state): ?string
    {
        $code = strtolower(trim((string) $stateCode));

        if (isset(CityGuideIndex::STATES[$code])) {
            return CityGuideIndex::STATES[$code];
        }

        $name = trim((string) $state);

        foreach (CityGuideIndex::STATES as $full) {
            if (strcasecmp($full, $name) === 0) {
                return $full;
            }
        }

        return null;
    }

    /**
     * Where a listing is, from its primary address (already loaded by the profile page, so no extra query there).
     *
     * @return array{city: string, key: string, state: string}|null
     */
    public static function locationOf(Listing $listing): ?array
    {
        /** @var Address|null $address */
        $address = $listing->primaryAddress;

        if (! $address || ! in_array(strtolower(trim((string) $address->country_code)), self::US_COUNTRY_CODES, true)) {
            return null;
        }

        $state = self::stateName($address->state_code, $address->state);
        $key = self::cityKey($address->city);

        return ($state && $key !== '') ? ['city' => trim((string) $address->city), 'key' => $key, 'state' => $state] : null;
    }

    /**
     * Public listings of the given types whose primary address is in this city and state, alphabetical by name.
     * One query: it narrows to the state in SQL and compares the normalized city in PHP.
     *
     * @param array<int, string> $types
     */
    public static function listingsIn(string $cityKey, string $stateName, array $types): Collection
    {
        $stateCode = strtolower((string) array_search($stateName, CityGuideIndex::STATES, true));

        return self::sorted(
            self::usListings($types)
                ->where(function ($query) use ($stateCode, $stateName) {
                    $query->whereRaw('lower(addresses.state_code) = ?', [$stateCode])
                        ->orWhereRaw('lower(addresses.state) = ?', [strtolower($stateName)]);
                })
                ->get()
                ->filter(fn (Listing $listing) => self::cityKey($listing->match_city) === $cityKey)
        );
    }

    /**
     * For a guide whose slug has no state code: the Public listings of the given types in this city in any US state.
     * When more than one state has a city of that name, only the state with the most listings is kept (a tie goes to the
     * alphabetically first state name), so "Austin" means Austin, TX and not a handful of listings in Austin, MN.
     *
     * @param array<int, string> $types
     */
    public static function listingsInCityAnyState(string $cityKey, array $types): Collection
    {
        $matches = self::usListings($types)
            ->get()
            ->filter(fn (Listing $listing) => self::cityKey($listing->match_city) === $cityKey)
            ->unique('id');

        $byState = $matches
            ->map(function (Listing $listing) {
                $listing->match_state_name = self::stateName($listing->match_state_code, $listing->match_state);

                return $listing;
            })
            ->filter(fn (Listing $listing) => $listing->match_state_name !== null)
            ->groupBy('match_state_name');

        if ($byState->isEmpty()) {
            return collect();
        }

        $counts = $byState->map->count();
        $dominant = $counts->keys()
            ->sort(fn ($a, $b) => $counts[$b] <=> $counts[$a] ?: strcmp($a, $b))
            ->first();

        return self::sorted($byState[$dominant]);
    }

    /** Public listings of these types with a primary US address, joined to that address. Not yet narrowed to a place. */
    private static function usListings(array $types)
    {
        return Listing::public()
            ->whereIn('listings.type', $types)
            ->join('addresses', function ($join) {
                $join->on('addresses.addressable_id', '=', 'listings.id')
                    ->where('addresses.addressable_type', '=', Listing::class)
                    ->where('addresses.primary', '=', 1);
            })
            ->whereRaw('lower(addresses.country_code) in (?, ?, ?)', self::US_COUNTRY_CODES)
            ->select(
                'listings.id', 'listings.name', 'listings.slug', 'listings.type',
                'addresses.city as match_city', 'addresses.state_code as match_state_code', 'addresses.state as match_state'
            );
    }

    private static function sorted(Collection $listings): Collection
    {
        return $listings
            ->unique('id')
            ->sort(fn (Listing $a, Listing $b) => self::compare($a, $b))
            ->values();
    }

    /**
     * The next six listings of the same type in the same city, after this one alphabetically, wrapping around to the start.
     * Never includes the listing itself; fewer than six when the city has fewer.
     */
    public static function nextInCity(Listing $listing): Collection
    {
        $location = self::locationOf($listing);

        if (! $location || ! in_array($listing->type, [Listing::TYPE_CLINIC, Listing::TYPE_DOCTOR], true)) {
            return collect();
        }

        $others = self::listingsIn($location['key'], $location['state'], [$listing->type])
            ->reject(fn (Listing $other) => $other->id === $listing->id)
            ->values();

        [$after, $before] = $others->partition(fn (Listing $other) => self::compare($other, $listing) > 0);

        return $after->concat($before)->take(self::NEXT_COUNT)->values();
    }

    /**
     * The published city guide for the listing's city, or null. Both kinds of guide slug are considered:
     *   - stateless, longevity-doctors-austin: matched on the city name alone;
     *   - state-coded, longevity-doctors-austin-tx: matched on city and state.
     * When both exist for one city the stateless one wins (the older URL, which keeps the authority); among several
     * stateless guides the shortest slug, among several coded ones the alphabetically first. The national hub is never returned.
     */
    public static function guideFor(Listing $listing): ?Article
    {
        $location = self::locationOf($listing);

        if (! $location) {
            return null;
        }

        $matches = Article::published()
            ->where('slug', 'like', CityGuideIndex::NATIONAL_SLUG . '-%')
            ->orderBy('slug')
            ->get(['id', 'slug'])
            ->map(function (Article $article) use ($location) {
                [$city, $state] = CityGuideIndex::parse($article->slug);

                if ($city === null || self::cityKey($city) !== $location['key']) {
                    return null;
                }

                if ($state === null) {
                    return in_array($location['key'], self::NON_US_GUIDE_CITIES, true) ? null : ['stateless' => true, 'article' => $article];
                }

                return $state === $location['state'] ? ['stateless' => false, 'article' => $article] : null;
            })
            ->filter();

        $stateless = $matches->where('stateless', true)->pluck('article')
            ->sort(fn (Article $a, Article $b) => strlen($a->slug) <=> strlen($b->slug) ?: strcmp($a->slug, $b->slug));

        return $stateless->first() ?? $matches->where('stateless', false)->pluck('article')->first();
    }

    /**
     * Every Public Clinic and Doctor in a city guide's city, alphabetical. Empty for the national hub and for any slug
     * that is not a city guide. A state-coded guide (…-austin-tx) matches that city and state. A stateless guide
     * (…-austin) matches the city in any US state, keeping the state with the most listings. International guides match nothing.
     *
     * @return array{city: string|null, listings: Collection}
     */
    public static function forGuide(string $slug): array
    {
        if ($slug === CityGuideIndex::NATIONAL_SLUG) {
            return ['city' => null, 'listings' => collect()];
        }

        [$city, $state] = CityGuideIndex::parse($slug);

        if ($city === null) {
            return ['city' => null, 'listings' => collect()];
        }

        $types = [Listing::TYPE_CLINIC, Listing::TYPE_DOCTOR];
        $key = self::cityKey($city);

        if ($state !== null) {
            return ['city' => $city, 'listings' => self::listingsIn($key, $state, $types)];
        }

        if (in_array($key, self::NON_US_GUIDE_CITIES, true)) {
            return ['city' => $city, 'listings' => collect()];
        }

        return ['city' => $city, 'listings' => self::listingsInCityAnyState($key, $types)];
    }

    private static function compare(Listing $a, Listing $b): int
    {
        return strcmp(mb_strtolower($a->name), mb_strtolower($b->name)) ?: $a->id <=> $b->id;
    }
}
