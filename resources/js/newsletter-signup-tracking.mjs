/*
 * GA4 tracking for newsletter signups. DEV-6.
 *
 * The signup form (the Livewire component NewsletterOptIn) does not redirect anywhere after a signup: it shows "check your
 * email" on the same page. So the event is sent here, in the browser, at the moment of the submit. The component dispatches
 * a 'newsletter-signup' browser event with the position of the block; Livewire v2 puts that data in event.detail.
 *
 * This file must never be able to break a page: every function returns quietly if anything is missing or throws.
 */

// Every block position. Kept equal to NewsletterSignupContext::POSITIONS in PHP (a test checks it).
export const ALLOWED_POSITIONS = [
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

export const SIGNUP_EVENT = 'newsletter_signup';
export const BROWSER_EVENT = 'newsletter-signup';
export const FALLBACK_POSITION = 'other';
export const MAX_URL_LENGTH = 500;

// The page's window, or undefined where there is none (a default parameter is evaluated outside the try blocks below).
function defaultWindow() {
    return typeof window === 'undefined' ? undefined : window;
}

// Sends the GA4 event. `detail` is event.detail of the browser event ({ position }); `win` is the window.
export function trackNewsletterSignup(detail, win = defaultWindow()) {
    try {
        if (!win || typeof win.gtag !== 'function') {
            return;
        }

        const position = detail && ALLOWED_POSITIONS.includes(detail.position) ? detail.position : FALLBACK_POSITION;
        const href = String((win.location && win.location.href) || '');
        const pageLocation = href.split('#')[0].slice(0, MAX_URL_LENGTH);
        const parameters = { block_position: position };

        if (pageLocation !== '') {
            parameters.page_location = pageLocation;
        }

        win.gtag('event', SIGNUP_EVENT, parameters);
    } catch (error) {
        // Tracking is never allowed to throw.
    }
}

// Registers the one window listener. Returns true the first time, false if it was already registered on this window.
export function registerNewsletterSignupTracking(win = defaultWindow()) {
    try {
        if (!win || typeof win.addEventListener !== 'function' || win.__newsletterSignupTrackingRegistered) {
            return false;
        }

        win.__newsletterSignupTrackingRegistered = true;
        win.addEventListener(BROWSER_EVENT, (event) => trackNewsletterSignup(event && event.detail, win));

        return true;
    } catch (error) {
        return false;
    }
}
