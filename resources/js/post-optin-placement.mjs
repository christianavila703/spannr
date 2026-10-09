/*
 * Where the mid-article newsletter box (#post-newsletter) goes. DEV-13.
 *
 * The box is rendered at the bottom of the article page and moved up into the body. It must only land after a
 * top-level body paragraph: never inside a table, list, quote, figure or collapsible block, never after a stub
 * paragraph, and if there is no such paragraph it stays at the bottom where the page rendered it.
 */

// Paragraphs inside any of these are not top-level body paragraphs.
export const SKIP_INSIDE = 'table, ul, ol, blockquote, figure, details, .accordion, .collapse';
export const MIN_TEXT_LENGTH = 40;
export const LONG_ARTICLE_PARAGRAPHS = 30;
export const LONG_ARTICLE_POSITION = 18;

export function isEligibleParagraph(paragraph, box) {
    if (paragraph.closest(SKIP_INSIDE)) {
        return false;
    }

    if ((paragraph.textContent || '').trim().length < MIN_TEXT_LENGTH) {
        return false;
    }

    return paragraph.nextElementSibling !== box;
}

// Pure: the paragraph to insert after, from the list of eligible paragraphs (null when there are none).
// More than 30 eligible paragraphs: the 19th (index 18). Otherwise the middle one.
export function pickAnchor(eligible) {
    if (eligible.length === 0) {
        return null;
    }

    const index = eligible.length > LONG_ARTICLE_PARAGRAPHS ? LONG_ARTICLE_POSITION : Math.floor(eligible.length / 2);

    return eligible[index];
}

// `body` is the element that holds the editor content (.post-content). Returns the paragraph the box went after, or null.
export function placePostOptIn(box, body) {
    if (!box || !body) {
        return null;
    }

    const eligible = Array.from(body.querySelectorAll('p')).filter((paragraph) => isEligibleParagraph(paragraph, box));
    const anchor = pickAnchor(eligible);

    if (anchor) {
        anchor.after(box);
    }

    return anchor;
}
