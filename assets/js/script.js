// 1. Plugins ko Register karo
gsap.registerPlugin(ScrollTrigger);

// 2. Hero Entrance (Jab page load hoga)
// Timeline use karne se animations ek ke baad ek sequence mein aate hain
const heroTl = gsap.timeline();

heroTl.to(".animate-hero", {
    opacity: 1,
    y: 0,
    duration: 1, // Thoda slow aur premium feel ke liye
    stagger: 0.3, // Text ke beech ka gap
    ease: "expo.out" // Premium cubic easing
});

// 3. Card Reveal on Scroll
// Har card scroll hone par smoothly reveal hoga
const cards = document.querySelectorAll(".reveal");

cards.forEach((card, i) => {
    gsap.to(card, {
        scrollTrigger: {
            trigger: card,
            start: "top 92%", // Jab card screen ke niche se 8% upar aaye tab start ho
            toggleActions: "play none none none" // Ek hi baar animate hoga (best for performance)
        },
        opacity: 1,
        y: 0,
        duration: 0.8,
        delay: (i % 3) * 0.2, // Row wise delay (sexy sequence effect)
        ease: "power2.out"
    });
});

// 4. Hover Effect (Optional but Sexy)
// Cards par mouse le jaane par subtle scale effect
document.querySelectorAll('.card').forEach(card => {
    card.addEventListener('mouseenter', () => {
        gsap.to(card, { scale: 1.05, duration: 0.3, ease: "power1.out" });
    });
    card.addEventListener('mouseleave', () => {
        gsap.to(card, { scale: 1, duration: 0.3, ease: "power1.out" });
    });
});