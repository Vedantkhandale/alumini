<?php
$pageTitle = "AlumniX | Career Board";
include(__DIR__ . "/includes/header.php");
include(__DIR__ . "/includes/db.php");
require_once(__DIR__ . "/includes/public_helpers.php");

$jobs = fetchRows($conn, "SELECT id, title, company, location, description, apply_link, company_logo FROM jobs WHERE status='approved' ORDER BY id DESC");
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<style>
    /* 🔴 LUXURY WHITE-RED THEME */
    :root {
        --primary: #ff4d4d;
        --bg-soft: #f8f8f8;
        --white: #ffffff;
        --text-main: #111111;
        --text-gray: #6b7280;
        --border-light: #e5e7eb;
    }

    html, body { background-color: var(--bg-soft); margin: 0; padding: 0; overflow-x: hidden; }

    .public-shell {
        background: var(--bg-soft);
        min-height: 100vh;
        padding: 140px 8% 80px; 
        color: var(--text-main);
        position: relative;
        display: flex;
        flex-direction: column;
    }

    .subpage-hero {
        text-align: center;
        margin-bottom: 70px;
        z-index: 2;
    }

    .hero-tag {
        text-transform: uppercase;
        letter-spacing: 4px;
        font-size: 11px;
        color: var(--primary);
        font-weight: 800;
        margin-bottom: 15px;
        display: block;
    }

    .subpage-title {
        font-size: clamp(40px, 8vw, 85px);
        font-weight: 950;
        line-height: 0.9;
        letter-spacing: -3px;
        text-transform: uppercase;
        color: var(--text-main);
    }

    .subpage-title span { display: block; color: var(--primary); }

    .subpage-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr); 
        gap: 30px;
        position: relative;
        z-index: 2;
    }

    /* 💼 JOB CARD - ANIMATED */
    .job-card {
        background: var(--white);
        border: 1px solid var(--border-light);
        border-radius: 32px;
        padding: 16px;
        transition: border-color 0.4s ease;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
        cursor: pointer;
        will-change: transform;
    }

    .job-card:hover {
        border-color: var(--primary);
    }

    .card-banner {
        width: 100%;
        height: 200px;
        border-radius: 24px;
        overflow: hidden;
        position: relative;
        background: #eee;
    }

    .card-banner img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform 0.8s cubic-bezier(0.2, 1, 0.3, 1);
    }

    .job-card:hover .card-banner img { transform: scale(1.1); }

    .floating-badge {
        position: absolute;
        top: 15px; right: 15px;
        background: var(--primary);
        color: #fff;
        padding: 6px 14px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 10px;
        z-index: 3;
    }

    .card-content { padding: 20px 10px 5px; }

    .card-title {
        font-size: 22px;
        font-weight: 800;
        margin-bottom: 12px;
        color: var(--text-main);
        min-height: 52px;
        line-height: 1.2;
    }

    .meta-row { display: flex; gap: 10px; margin-bottom: 15px; }

    .chip {
        padding: 6px 12px;
        background: var(--bg-soft);
        color: var(--text-gray);
        border-radius: 10px;
        font-size: 10px;
        font-weight: 700;
        border: 1px solid var(--border-light);
        text-transform: uppercase;
    }

    .btn-apply {
        display: block;
        padding: 14px;
        background: var(--text-main);
        color: #fff;
        text-decoration: none;
        border-radius: 15px;
        font-weight: 800;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: 0.4s;
        text-align: center;
    }

    .btn-apply:hover { background: var(--primary); letter-spacing: 2px; }

    /* Footer Fix */
    footer { margin-top: auto; }

    @media(max-width:1100px){ .subpage-grid { grid-template-columns: repeat(2, 1fr); } }
    @media(max-width:768px){ .subpage-grid { grid-template-columns: 1fr; } }
</style>

<div class="public-shell">
    <section class="subpage-hero">
        <span class="hero-tag reveal-top">Career Opportunities</span>
        <h1 class="subpage-title reveal-top">
            <span>Career</span> Board 2026
        </h1>
    </section>

    <div class="subpage-grid">
        <?php if ($jobs): ?>
            <?php foreach ($jobs as $job): 
                $jobLink = !empty($job["apply_link"]) ? $job["apply_link"] : "login.php";
                $imgName = htmlspecialchars($job['company_logo']);
                $imgUrl = !empty($imgName) ? "uploads/logos/" . $imgName : "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80";
            ?>
                <article class="job-card reveal-card">
                    <div class="card-banner">
                        <img src="<?= $imgUrl ?>" alt="Job Banner">
                        <div class="floating-badge"><?= htmlspecialchars($job["company"]) ?></div>
                    </div>
                    <div class="card-content">
                        <h3 class="card-title"><?= htmlspecialchars($job["title"]) ?></h3>
                        <div class="meta-row">
                            <div class="chip"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job["location"] ?: "Remote") ?></div>
                            <div class="chip"><i class="fas fa-briefcase"></i> Full-Time</div>
                        </div>
                        <p class="job-desc" style="color: var(--text-gray); font-size: 14px; height: 65px; overflow: hidden;">
                            <?= substr(htmlspecialchars($job["description"]), 0, 100) ?>...
                        </p>
                        <a href="<?= htmlspecialchars($jobLink) ?>" class="btn-apply">View Position</a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        gsap.registerPlugin(ScrollTrigger);

        // 1. Hero Reveal
        gsap.from(".reveal-top", {
            y: 50,
            opacity: 0,
            duration: 1.2,
            stagger: 0.2,
            ease: "power4.out"
        });

        // 2. Cards Stagger Reveal
        gsap.from(".reveal-card", {
            scrollTrigger: {
                trigger: ".subpage-grid",
                start: "top 85%",
            },
            scale: 0.9,
            y: 60,
            opacity: 0,
            duration: 1,
            stagger: 0.15,
            ease: "expo.out"
        });

        // 3. Magnetic Hover Effect
        const cards = document.querySelectorAll('.job-card');
        cards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                gsap.to(card, { y: -15, scale: 1.02, boxShadow: "0 40px 80px rgba(255, 77, 77, 0.12)", duration: 0.4 });
            });
            card.addEventListener('mouseleave', () => {
                gsap.to(card, { y: 0, scale: 1, boxShadow: "0 4px 20px rgba(0,0,0,0.03)", duration: 0.4 });
            });
        });
    });
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>