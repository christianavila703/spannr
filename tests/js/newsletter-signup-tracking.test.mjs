// Run with: npm test   (or npm run test:js; Node's built-in test runner, no dependencies)
//
// The tracking code only needs window.gtag, window.location.href and window.addEventListener, so these tests pass in a small fake window.
import test from 'node:test';
import assert from 'node:assert/strict';
import {
    ALLOWED_POSITIONS,
    MAX_URL_LENGTH,
    registerNewsletterSignupTracking,
    trackNewsletterSignup,
} from '../../resources/js/newsletter-signup-tracking.mjs';

function fakeWindow(href = 'https://spannr.com/articles/sleep?x=1') {
    const calls = [];
    const listeners = {};

    return {
        calls,
        listeners,
        location: { href },
        gtag: (...args) => calls.push(args),
        addEventListener(name, handler) {
            (listeners[name] ||= []).push(handler);
        },
    };
}

test('fires gtag once with the event name, the page and the position', () => {
    const win = fakeWindow();

    trackNewsletterSignup({ position: 'footer' }, win);

    assert.deepEqual(win.calls, [['event', 'newsletter_signup', { block_position: 'footer', page_location: 'https://spannr.com/articles/sleep?x=1' }]]);
});

test('every allowed position is passed through unchanged', () => {
    for (const position of ALLOWED_POSITIONS) {
        const win = fakeWindow();
        trackNewsletterSignup({ position }, win);
        assert.equal(win.calls[0][2].block_position, position);
    }
});

test('an unknown, missing or non-string position becomes other', () => {
    for (const detail of [{ position: 'banner' }, { position: '</script>' }, { position: 'HEADER_MODAL' }, { position: 5 }, {}, null, undefined, 'footer']) {
        const win = fakeWindow();
        trackNewsletterSignup(detail, win);
        assert.equal(win.calls.length, 1);
        assert.equal(win.calls[0][2].block_position, 'other', JSON.stringify(detail));
    }
});

test('does nothing and does not throw when gtag is missing', () => {
    const win = fakeWindow();
    delete win.gtag;

    assert.doesNotThrow(() => trackNewsletterSignup({ position: 'footer' }, win));
    assert.deepEqual(win.calls, []);

    win.gtag = 'not a function';
    assert.doesNotThrow(() => trackNewsletterSignup({ position: 'footer' }, win));
    assert.doesNotThrow(() => trackNewsletterSignup({ position: 'footer' }, undefined));
    assert.doesNotThrow(() => trackNewsletterSignup({ position: 'footer' }, null));
});

test('the #fragment is removed from the page URL', () => {
    const win = fakeWindow('https://spannr.com/glossary/nad?utm=1#section#two');

    trackNewsletterSignup({ position: 'glossary' }, win);

    assert.equal(win.calls[0][2].page_location, 'https://spannr.com/glossary/nad?utm=1');
});

test('a 700 character URL is cut to 500 characters', () => {
    const win = fakeWindow('https://spannr.com/a?q=' + 'x'.repeat(700 - 'https://spannr.com/a?q='.length));
    assert.equal(win.location.href.length, 700);

    trackNewsletterSignup({ position: 'homepage' }, win);

    assert.equal(win.calls[0][2].page_location.length, MAX_URL_LENGTH);
    assert.equal(MAX_URL_LENGTH, 500);
    assert.ok(win.calls[0][2].page_location.startsWith('https://spannr.com/a?q=xxx'));
});

test('a gtag that throws does not make the tracking throw', () => {
    const win = fakeWindow();
    win.gtag = () => {
        throw new Error('gtag is broken');
    };

    assert.doesNotThrow(() => trackNewsletterSignup({ position: 'footer' }, win));
});

test('a window with no location still sends the event, without a page', () => {
    const win = fakeWindow();
    win.location = undefined;

    trackNewsletterSignup({ position: 'menu' }, win);

    assert.deepEqual(win.calls, [['event', 'newsletter_signup', { block_position: 'menu' }]]);
});

test('the listener is registered once and passes event.detail on', () => {
    const win = fakeWindow();

    assert.equal(registerNewsletterSignupTracking(win), true);
    assert.equal(registerNewsletterSignupTracking(win), false, 'a second call registers nothing');
    assert.equal(win.listeners['newsletter-signup'].length, 1);

    win.listeners['newsletter-signup'][0]({ detail: { position: 'investors' } });

    assert.equal(win.calls.length, 1);
    assert.equal(win.calls[0][2].block_position, 'investors');
});

test('registering on a window that cannot take listeners does not throw', () => {
    assert.doesNotThrow(() => registerNewsletterSignupTracking({}));
    assert.doesNotThrow(() => registerNewsletterSignupTracking(null));
});

test('the allowed list has no duplicates and ends with the fallback', () => {
    assert.equal(new Set(ALLOWED_POSITIONS).size, ALLOWED_POSITIONS.length);
    assert.equal(ALLOWED_POSITIONS.at(-1), 'other');
});
