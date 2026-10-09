<?php

namespace App\Support;

use App\Models\Article;
use Illuminate\Support\Str;

/**
 * Builds the "Longevity Doctors by City" list from the city guides that are actually published.
 *
 * A city guide is a published article whose slug is longevity-doctors-{city} or longevity-doctors-{city}-{st}.
 * Articles have no city or state column, so both come from the slug:
 *   longevity-doctors-austin-tx -> Austin, Texas
 * A slug with no trailing US state code (longevity-doctors-london, and older guides such as
 * longevity-doctors-austin) cannot be placed in a state without guessing, so it goes in the "Other cities" group.
 */
class CityGuideIndex
{
    public const NATIONAL_SLUG = 'longevity-doctors';
    public const OTHER_GROUP = 'Other cities';

    private const PREFIX = self::NATIONAL_SLUG . '-';

    public const STATES = [
        'al' => 'Alabama', 'ak' => 'Alaska', 'az' => 'Arizona', 'ar' => 'Arkansas', 'ca' => 'California',
        'co' => 'Colorado', 'ct' => 'Connecticut', 'de' => 'Delaware', 'dc' => 'District of Columbia',
        'fl' => 'Florida', 'ga' => 'Georgia', 'hi' => 'Hawaii', 'id' => 'Idaho', 'il' => 'Illinois',
        'in' => 'Indiana', 'ia' => 'Iowa', 'ks' => 'Kansas', 'ky' => 'Kentucky', 'la' => 'Louisiana',
        'me' => 'Maine', 'md' => 'Maryland', 'ma' => 'Massachusetts', 'mi' => 'Michigan', 'mn' => 'Minnesota',
        'ms' => 'Mississippi', 'mo' => 'Missouri', 'mt' => 'Montana', 'ne' => 'Nebraska', 'nv' => 'Nevada',
        'nh' => 'New Hampshire', 'nj' => 'New Jersey', 'nm' => 'New Mexico', 'ny' => 'New York',
        'nc' => 'North Carolina', 'nd' => 'North Dakota', 'oh' => 'Ohio', 'ok' => 'Oklahoma', 'or' => 'Oregon',
        'pa' => 'Pennsylvania', 'ri' => 'Rhode Island', 'sc' => 'South Carolina', 'sd' => 'South Dakota',
        'tn' => 'Tennessee', 'tx' => 'Texas', 'ut' => 'Utah', 'vt' => 'Vermont', 'va' => 'Virginia',
        'wa' => 'Washington', 'wv' => 'West Virginia', 'wi' => 'Wisconsin', 'wy' => 'Wyoming',
    ];

    /**
     * @return array<string, array<int, array{city: string, slug: string, url: string}>>
     *         Group name => cities. States first in alphabetical order, then "Other cities". Cities are alphabetical in each group.
     */
    public static function grouped(): array
    {
        $groups = [];

        Article::published()
            ->where('slug', 'like', self::PREFIX . '%')
            ->get(['id', 'slug'])
            ->each(function (Article $article) use (&$groups) {
                [$city, $state] = self::parse($article->slug);

                if ($city === null) {
                    return;
                }

                $groups[$state ?? self::OTHER_GROUP][] = [
                    'city' => $city,
                    'slug' => $article->slug,
                    'url' => route('public.content.articles.show', $article->slug),
                ];
            });

        foreach ($groups as &$cities) {
            usort($cities, fn ($a, $b) => strcasecmp($a['city'], $b['city']) ?: strcmp($a['slug'], $b['slug']));
        }
        unset($cities);

        $other = $groups[self::OTHER_GROUP] ?? null;
        unset($groups[self::OTHER_GROUP]);
        ksort($groups, SORT_NATURAL | SORT_FLAG_CASE);

        if ($other) {
            $groups[self::OTHER_GROUP] = $other;
        }

        return $groups;
    }

    /**
     * @return array{0: string|null, 1: string|null} [city name, state name]. City is null when the slug is not a city guide.
     */
    public static function parse(string $slug): array
    {
        if (! str_starts_with($slug, self::PREFIX)) {
            return [null, null];
        }

        $parts = explode('-', substr($slug, strlen(self::PREFIX)));
        $state = null;

        if (count($parts) > 1 && isset(self::STATES[end($parts)])) {
            $state = self::STATES[array_pop($parts)];
        }

        $city = trim(Str::title(implode(' ', $parts)));

        return $city === '' ? [null, null] : [$city, $state];
    }
}
