gsap.registerPlugin(ScrollTrigger);

// Hero Entrance
const heroTl = gsap.timeline();
heroTl.to(".animate-hero", {
    opacity: 1,
    y: 0,
    duration: 1,
    stagger: 0.2,
    ease: "power4.out"
});

// Reveal Cards on Scroll
gsap.utils.toArray(".reveal").forEach((card, i) => {
    gsap.to(card, {
        scrollTrigger: {
            trigger: card,
            start: "top 90%",
            toggleActions: "play none none none"
        },
        opacity: 1,
        y: 0,
        duration: 0.6,
        delay: (i % 3) * 0.1,
        ease: "power2.out"
    });
});