gsap.registerPlugin(ScrollTrigger);

let mm = gsap.matchMedia();

// Tablet
mm.add("(min-width: 768px)", () => {

    // Insights
    gsap.set('#home-insights .single-post-item', {opacity: 0, y: 100});

    ScrollTrigger.batch("#home-insights .single-post-item", {
        scrub: 1,
        onEnter: (elements, triggers) => {
            gsap.to(elements, {opacity: 1, y: 0, stagger: 0.15});
        },
        // onLeave: (elements, triggers) => {
        //     gsap.to(elements, {opacity: 0, y: -100, stagger: 0.15});
        // },
        // onEnterBack: (elements, triggers) => {
        //     gsap.to(elements, {opacity: 1, y: 0, stagger: 0.15});
        // },
        // onLeaveBack: (elements, triggers) => {
        //     gsap.to(elements, {opacity: 0, y: 100, stagger: 0.15});
        // },
    });

    // Investors
    gsap.set('.fade-in-when-visible', {opacity: 0, y: 100});

    ScrollTrigger.batch(".fade-in-when-visible", {
        scrub: 1,
        onEnter: (elements, triggers) => {
            gsap.to(elements, {opacity: 1, y: 0, stagger: 0.15});
        },
        onLeave: (elements, triggers) => {
            gsap.to(elements, {opacity: 0, y: -100, stagger: 0.15});
        },
        onEnterBack: (elements, triggers) => {
            gsap.to(elements, {opacity: 1, y: 0, stagger: 0.15});
        },
        onLeaveBack: (elements, triggers) => {
            gsap.to(elements, {opacity: 0, y: 100, stagger: 0.15});
        },
    });
});

// Desktop
mm.add("(min-width: 1024px)", () => {

    // Hero
    ScrollTrigger.create({
        trigger: ".header",
        start: "top top",
        end: "top+=40%",
        endTrigger: "#home-hero",
        pin: '#home-hero .mobile-phone',
        pinSpacing: false,
        markers: false,
    });
});

// on resize

function reloadKittens() {
    if (window.innerWidth > 767) {
        document.getElementById('kittens').contentDocument.location.reload();
    }
}

window.onresize = reloadKittens;
