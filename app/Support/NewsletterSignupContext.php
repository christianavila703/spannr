<?php

namespace App\Support;

/**
 * DEV-6. Where a newsletter signup happened, so GA4 can show which placement produces signups.
 *
 * Two GA4 events, with different meanings:
 *
 *  - newsletter_signup (SIGNUP_EVENT): the signup form was SUBMITTED. It is fired in the browser at the moment of the
 *    submit (see resources/js/newsletter-signup-tracking.mjs), so it counts every signup, from every device.
 *  - newsletter_confirmed (CONFIRMED_EVENT): the visitor reached /confirmed in the same browser session as their signup.
 *    The signup form never redirects: /confirmed is reached later, from the confirm link in the beehiiv email, often on
 *    another device or after the session has ended. So this is a partial count of the people who finished, and it carries
 *    the placement only when the session is still there.
 *
 * The signup form (App\Http\Livewire\Forms\NewsletterOptIn) calls remember() when a signup succeeds. The /confirmed page
 * calls pull() and, when there is something, prints one gtag 'newsletter_confirmed' event (see script()).
 *
 * Nothing here trusts its input. A position outside POSITIONS becomes 'other'. A page URL must be an http(s) URL on this
 * site's own host, has its fragment removed, and is cut to 500 characters. Anything that does not pass is dropped.
 */
class NewsletterSignupContext
{
    /**
     * Every block position there is. A new placement needs a new entry here, in ALLOWED_POSITIONS in
     * resources/js/newsletter-signup-tracking.mjs (a test keeps the two equal), and a position="..." where it is rendered.
     */
    public const POSITIONS = [
        'header_modal',
        'footer',
        'homepage',
        'mid_article',
        'end_of_article',
        'listing_page',
        'city_guide',
        'glossary',
        'menu',
        'about',
        'investors',
        'other',
    ];

    public const FALLBACK_POSITION = 'other';

    public const SESSION_KEY = 'newsletter_signup_context';

    public const MAX_URL_LENGTH = 500;

    /** Fired in the browser when the signup form is submitted. */
    public const SIGNUP_EVENT = 'newsletter_signup';

    /** Fired on /confirmed, and only when the visitor's session still holds the signup context. */
    public const CONFIRMED_EVENT = 'newsletter_confirmed';

    public static function normalizePosition(mixed $position): string
    {
        return is_string($position) && in_array($position, self::POSITIONS, true) ? $position : self::FALLBACK_POSITION;
    }

    /**
     * The page a signup happened on: the request referer, else the page URL the component was rendered on.
     * Null when neither is a usable URL on this site.
     */
    public static function pageUrl(?string $referer, ?string $componentUrl, string $host): ?string
    {
        return self::cleanUrl($referer, $host) ?? self::cleanUrl($componentUrl, $host);
    }

    /** An http(s) URL on $host with no fragment and at most MAX_URL_LENGTH characters, or null. */
    public static function cleanUrl(mixed $url, string $host): ?string
    {
        if (! is_string($url)) {
            return null;
        }

        $url = trim($url);

        // No spaces or control characters in a URL that is going to be written into a page.
        if ($url === '' || preg_match('/[\x00-\x20\x7F]/', $url) === 1) {
            return null;
        }

        $parts = parse_url($url);

        if ($parts === false
            || ! in_array(strtolower((string) ($parts['scheme'] ?? '')), ['http', 'https'], true)
            || strcasecmp((string) ($parts['host'] ?? ''), $host) !== 0) {
            return null;
        }

        $withoutFragment = explode('#', $url, 2)[0];

        return mb_substr($withoutFragment, 0, self::MAX_URL_LENGTH);
    }

    /** Keep the context of a successful signup in the session until /confirmed asks for it. */
    public static function remember(mixed $position, ?string $componentUrl, ?string $referer): void
    {
        session()->put(self::SESSION_KEY, [
            'block_position' => self::normalizePosition($position),
            'page_location' => self::pageUrl($referer, $componentUrl, request()->getHost()),
        ]);
    }

    /**
     * Take the context out of the session (it is gone after this, so a reload counts nothing twice).
     *
     * @return array{block_position: string, page_location: string|null}|null null when there is none
     */
    public static function pull(): ?array
    {
        $stored = session()->pull(self::SESSION_KEY);

        if (! is_array($stored)) {
            return null;
        }

        // Whatever is in the session is checked again: it is about to be written into a page.
        return [
            'block_position' => self::normalizePosition($stored['block_position'] ?? null),
            'page_location' => self::cleanUrl($stored['page_location'] ?? null, request()->getHost()),
        ];
    }

    /**
     * One small inline script for /confirmed: fires the GA4 newsletter_confirmed event, and does nothing (and throws nothing) when gtag is not there.
     * The values are written with the JSON_HEX_* flags, so "<", ">", quotes and "&" cannot end the script or the string.
     */
    public static function script(array $context): string
    {
        $flags = JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP;

        $parameters = [];

        if (filled($context['page_location'] ?? null)) {
            $parameters[] = 'page_location: ' . json_encode((string) $context['page_location'], $flags);
        }

        $parameters[] = 'block_position: ' . json_encode((string) ($context['block_position'] ?? self::FALLBACK_POSITION), $flags);

        return "<script>\n"
            . "(function () {\n"
            . "    if (typeof window.gtag !== 'function') { return; }\n"
            . "    try {\n"
            . "        window.gtag('event', '" . self::CONFIRMED_EVENT . "', { " . implode(', ', $parameters) . " });\n"
            . "    } catch (e) {}\n"
            . "})();\n"
            . '</script>';
    }
}
