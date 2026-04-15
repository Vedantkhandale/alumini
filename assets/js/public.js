document.addEventListener("DOMContentLoaded", () => {
    const revealImmediately = () => {
        document.querySelectorAll(".animate-hero, .reveal").forEach((element) => {
            element.style.opacity = "1";
            element.style.transform = "translateY(0)";
        });
    };

    if (!window.gsap) {
        revealImmediately();
        return;
    }

    const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
    const gsap = window.gsap;

    if (window.ScrollTrigger) {
        gsap.registerPlugin(window.ScrollTrigger);
    }

    if (prefersReducedMotion) {
        revealImmediately();
        return;
    }

    const heroNodes = document.querySelectorAll(".animate-hero");
    if (heroNodes.length) {
        gsap.to(heroNodes, {
            opacity: 1,
            y: 0,
            duration: 0.9,
            ease: "power3.out",
            stagger: 0.12
        });
    }

    const stack = document.querySelector(".hero-stack");
    if (stack) {
        gsap.to(stack, {
            y: -10,
            repeat: -1,
            yoyo: true,
            duration: 4,
            ease: "sine.inOut"
        });
    }

    const revealNodes = document.querySelectorAll(".reveal");
    if (!revealNodes.length || !window.ScrollTrigger) {
        revealImmediately();
        return;
    }

    revealNodes.forEach((node, index) => {
        gsap.to(node, {
            opacity: 1,
            y: 0,
            duration: 0.8,
            ease: "power2.out",
            delay: index % 3 === 0 ? 0 : 0.05,
            scrollTrigger: {
                trigger: node,
                start: "top 88%",
                once: true
            }
        });
    });
});
