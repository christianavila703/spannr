// Run with: npm run test:js   (Node's built-in test runner, no dependencies)
//
// The placement code only needs a few DOM methods, so these tests use a tiny stand-in tree (FakeNode) that
// implements exactly those: closest(), textContent, nextElementSibling, after(), querySelectorAll('p').
import test from 'node:test';
import assert from 'node:assert/strict';
import { isEligibleParagraph, pickAnchor, placePostOptIn, SKIP_INSIDE } from '../../resources/js/post-optin-placement.mjs';

class FakeNode {
    constructor(tag, { classes = [], text = '' } = {}) {
        this.tag = tag;
        this.classes = classes;
        this.text = text;
        this.parent = null;
        this.children = [];
    }

    add(child) {
        child.parent = this;
        this.children.push(child);
        return child;
    }

    get textContent() {
        return this.text;
    }

    get nextElementSibling() {
        if (!this.parent) return null;
        const siblings = this.parent.children;
        return siblings[siblings.indexOf(this) + 1] ?? null;
    }

    // Supports a comma list of tag names and .class names, which is all SKIP_INSIDE uses.
    closest(selector) {
        const wanted = selector.split(',').map((s) => s.trim());
        const matches = (node) => wanted.some((w) => (w.startsWith('.') ? node.classes.includes(w.slice(1)) : node.tag === w));
        for (let node = this.parent; node; node = node.parent) {
            if (matches(node)) return node;
        }
        return null;
    }

    after(node) {
        if (node.parent) node.parent.children.splice(node.parent.children.indexOf(node), 1);
        node.parent = this.parent;
        this.parent.children.splice(this.parent.children.indexOf(this) + 1, 0, node);
    }

    querySelectorAll(selector) {
        assert.equal(selector, 'p');
        const found = [];
        const walk = (node) => node.children.forEach((child) => {
            if (child.tag === 'p') found.push(child);
            walk(child);
        });
        walk(this);
        return found;
    }
}

const LONG = 'A real paragraph of body text that is comfortably longer than forty characters.';

/** A body with `n` long top-level paragraphs, p0..p(n-1). */
function bodyWithParagraphs(n) {
    const body = new FakeNode('div', { classes: ['post-content'] });
    for (let i = 0; i < n; i++) body.add(new FakeNode('p', { text: `${LONG} #${i}` }));
    return body;
}

/** The box is rendered outside the body, at the bottom of the page, as in the Blade view. */
function pageBox() {
    const page = new FakeNode('div');
    const box = page.add(new FakeNode('div', { classes: ['outline-newsletter-optin'] }));
    return { page, box };
}

const indexOfBox = (body, box) => body.children.indexOf(box);

test('paragraphs only: the box goes after the middle paragraph', () => {
    const body = bodyWithParagraphs(10);
    const { box } = pageBox();

    const anchor = placePostOptIn(box, body);

    assert.equal(anchor, body.children[5]);
    assert.equal(indexOfBox(body, box), 6);
    assert.equal(box.parent, body);
});

test('paragraphs plus a table full of paragraphs: only the body paragraphs are counted and the box is never in the table', () => {
    const body = bodyWithParagraphs(6);
    const table = body.add(new FakeNode('table'));
    const row = table.add(new FakeNode('tr'));
    for (let i = 0; i < 8; i++) row.add(new FakeNode('td')).add(new FakeNode('p', { text: `${LONG} cell ${i}` }));
    const { box } = pageBox();

    const anchor = placePostOptIn(box, body);

    assert.equal(anchor, body.children[3], 'the middle of the 6 eligible paragraphs, not of all 14');
    assert.equal(box.parent, body, 'a direct child of the body, not of a table cell');
    assert.equal(box.closest('table'), null);
});

test('a page with only table paragraphs: the box stays where it was rendered', () => {
    const body = new FakeNode('div', { classes: ['post-content'] });
    const table = body.add(new FakeNode('table'));
    for (let i = 0; i < 12; i++) table.add(new FakeNode('td')).add(new FakeNode('p', { text: `${LONG} cell ${i}` }));
    const { page, box } = pageBox();

    const anchor = placePostOptIn(box, body);

    assert.equal(anchor, null);
    assert.equal(box.parent, page, 'still at the bottom of the page');
});

test('a long article (over 30 eligible paragraphs): the box goes after index 18', () => {
    const body = bodyWithParagraphs(40);
    const { box } = pageBox();

    const anchor = placePostOptIn(box, body);

    assert.equal(anchor, body.children[18]);
    assert.equal(indexOfBox(body, box), 19);
});

test('exactly 30 eligible paragraphs use the middle, 31 use index 18', () => {
    const thirty = bodyWithParagraphs(30);
    assert.equal(placePostOptIn(pageBox().box, thirty), thirty.children[15]);

    const thirtyOne = bodyWithParagraphs(31);
    assert.equal(placePostOptIn(pageBox().box, thirtyOne), thirtyOne.children[18]);
});

test('only eligible paragraphs are counted for the 30 / 18 rule', () => {
    const body = bodyWithParagraphs(25);
    const list = body.add(new FakeNode('ul'));
    for (let i = 0; i < 20; i++) list.add(new FakeNode('li')).add(new FakeNode('p', { text: `${LONG} item ${i}` }));
    const { box } = pageBox();

    // 45 paragraphs in total, but only 25 are eligible: the middle one, not index 18.
    assert.equal(placePostOptIn(box, body), body.children[12]);
});

test('paragraphs inside lists, quotes, figures, details, accordions and collapsibles are not eligible', () => {
    const body = new FakeNode('div', { classes: ['post-content'] });
    const { box } = pageBox();

    for (const [tag, classes] of [['ul', []], ['ol', []], ['blockquote', []], ['figure', []], ['details', []], ['div', ['accordion']], ['div', ['collapse']]]) {
        const wrapper = body.add(new FakeNode(tag, { classes }));
        const p = wrapper.add(new FakeNode('p', { text: LONG }));
        assert.equal(isEligibleParagraph(p, box), false, `${tag} ${classes.join('.')}`);
    }

    // And a paragraph nested deeper inside one of them.
    const quote = body.add(new FakeNode('blockquote'));
    const p = quote.add(new FakeNode('div')).add(new FakeNode('p', { text: LONG }));
    assert.equal(isEligibleParagraph(p, box), false);
    assert.ok(SKIP_INSIDE.includes('.collapse'));
});

test('short paragraphs (under 40 characters once trimmed) are not eligible and not counted', () => {
    const body = new FakeNode('div', { classes: ['post-content'] });
    const { box } = pageBox();
    const stub = body.add(new FakeNode('p', { text: '   Short line.   ' }));
    const thirtyNine = body.add(new FakeNode('p', { text: '  ' + 'x'.repeat(39) + '  ' }));
    const forty = body.add(new FakeNode('p', { text: '  ' + 'x'.repeat(40) + '  ' }));

    assert.equal(isEligibleParagraph(stub, box), false);
    assert.equal(isEligibleParagraph(thirtyNine, box), false);
    assert.equal(isEligibleParagraph(forty, box), true);
});

test('a paragraph whose next sibling is already the box is not eligible', () => {
    const body = bodyWithParagraphs(4);
    const { box } = pageBox();
    body.children[1].after(box);

    assert.equal(isEligibleParagraph(body.children[1], box), false);
    assert.equal(isEligibleParagraph(body.children[0], box), true);
});

test('no paragraphs at all, or a missing box or body: nothing happens and nothing throws', () => {
    const empty = new FakeNode('div', { classes: ['post-content'] });
    const { page, box } = pageBox();

    assert.equal(placePostOptIn(box, empty), null);
    assert.equal(box.parent, page);
    assert.equal(placePostOptIn(null, empty), null);
    assert.equal(placePostOptIn(box, null), null);
});

test('pickAnchor is a pure function of the eligible list', () => {
    const items = (n) => Array.from({ length: n }, (_, i) => i);

    assert.equal(pickAnchor([]), null);
    assert.equal(pickAnchor(items(1)), 0);
    assert.equal(pickAnchor(items(2)), 1);
    assert.equal(pickAnchor(items(9)), 4);
    assert.equal(pickAnchor(items(30)), 15);
    assert.equal(pickAnchor(items(31)), 18);
    assert.equal(pickAnchor(items(100)), 18);
});
