gsap.registerPlugin(ScrollTrigger);

let mm = gsap.matchMedia();

// Tablet
mm.add("(min-width: 768px)", () => {
    // Team
    gsap.set('.about-team-card', {opacity: 0, y: 100});

    ScrollTrigger.batch(".about-team-card", {
        scrub: 1,
        onEnter: (elements, triggers) => {
            gsap.to(elements, {opacity: 1, y: 0, stagger: 0.15});
        }
    });

    // Newsletter
    gsap.set('#newsletter', {opacity: 0, y: 100});

    ScrollTrigger.batch("#newsletter", {
        scrub: 1,
        onEnter: (elements, triggers) => {
            gsap.to(elements, {opacity: 1, y: 0, stagger: 0.15});
        },
    });
});

// Desktop
mm.add("(min-width: 992px)", () => {

    ScrollTrigger.create({
        trigger: "#about",
        start: "top top",
        pin: "blockquote.first",
        markers: false,
        pinSpacing: false
    });

    ScrollTrigger.create({
        trigger: "#fuel",
        start: "top top",
        pin: "blockquote.second",
        markers: false,
        pinSpacing: false
    });

    ScrollTrigger.create({
        trigger: "#trust",
        start: "top top",
        pin: "blockquote.third",
        markers: false,
        pinSpacing: false
    });
});
