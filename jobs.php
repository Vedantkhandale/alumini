<?php
$pageTitle = "AlumniX | Career Board";
include(__DIR__ . "/includes/header.php");
include(__DIR__ . "/includes/db.php");
require_once(__DIR__ . "/includes/public_helpers.php");

$jobs = fetchRows($conn, "SELECT id, title, company, location, description, apply_link, company_logo FROM jobs WHERE status='approved' ORDER BY id DESC");
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<style>
    :root {
        --primary: #ff3b3b;
        --bg-dark: #050505;
        --card-bg: rgba(255, 255, 255, 0.03);
        --border: rgba(255, 255, 255, 0.08);
    }

    /* 🌑 FULL PAGE SETUP */
    .public-shell {
        background: var(--bg-dark);
        min-height: 100vh;
        padding: 160px 8% 0px; /* Space adjusted for footer */
        color: #fff;
        position: relative;
        overflow-x: hidden;
    }

    /* 🎭 MESH BACKGROUND (Events Page Matching) */
    .mesh-bg {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: radial-gradient(circle at 10% 20%, rgba(255, 59, 59, 0.08) 0%, transparent 50%),
                    radial-gradient(circle at 90% 80%, rgba(255, 59, 59, 0.08) 0%, transparent 50%);
        z-index: 0;
        pointer-events: none;
    }

    /* 🔥 HEADER SYNCED WITH EVENTS */
    .subpage-hero {
        text-align: center;
        margin-bottom: 100px;
        z-index: 2;
        position: relative;
    }

    .hero-tag {
        text-transform: uppercase;
        letter-spacing: 5px;
        font-size: 12px;
        color: var(--primary);
        font-weight: 800;
        margin-bottom: 15px;
        display: block;
    }

    .subpage-title {
        font-size: clamp(45px, 10vw, 100px);
        font-weight: 950;
        line-height: 0.8;
        letter-spacing: -6px;
        text-transform: uppercase;
    }

    .subpage-title span {
        display: block;
        color: transparent;
        -webkit-text-stroke: 1.5px rgba(255, 255, 255, 0.2);
    }

    /* 💎 BENTO GRID SYNC */
    .subpage-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 40px;
        position: relative;
        z-index: 2;
        margin-bottom: 60px; /* Space between last card and footer */
    }

    /* 💼 JOB CARD - SEXY DARK VIBE */
    .job-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 50px;
        padding: 20px;
        backdrop-filter: blur(20px);
        transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        display: flex;
        flex-direction: column;
    }

    .job-card:hover {
        transform: translateY(-15px) scale(1.02);
        border-color: var(--primary);
        box-shadow: 0 30px 60px rgba(255, 59, 59, 0.15);
    }

    .card-banner {
        width: 100%;
        height: 280px;
        border-radius: 40px;
        overflow: hidden;
        position: relative;
        background: #111;
    }

    .card-banner img {
        width: 100%; height: 100%;
        object-fit: cover;
        filter: grayscale(100%);
        opacity: 0.5;
        transition: 0.8s;
    }

    .job-card:hover .card-banner img {
        filter: grayscale(0%);
        opacity: 0.8;
        transform: scale(1.1);
    }

    .floating-badge {
        position: absolute;
        bottom: 25px;
        right: 25px;
        background: var(--primary);
        color: #fff;
        padding: 12px 22px;
        border-radius: 20px;
        font-weight: 900;
        box-shadow: 0 10px 30px rgba(255, 59, 59, 0.4);
        z-index: 3;
        font-size: 13px;
    }

    /* CARD CONTENT */
    .card-content {
        padding: 25px 15px 10px;
    }

    .card-title {
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 12px;
        letter-spacing: -1px;
        color: #fff;
    }

    .meta-row {
        display: flex;
        gap: 12px;
        margin-bottom: 20px;
    }

    .chip {
        padding: 8px 15px;
        background: rgba(255, 255, 255, 0.05);
        color: #94a3b8;
        border-radius: 15px;
        font-size: 11px;
        font-weight: 700;
        border: 1px solid var(--border);
    }

    .chip i { color: var(--primary); margin-right: 5px; }

    .job-desc {
        font-size: 14px;
        color: #888;
        line-height: 1.6;
        margin-bottom: 25px;
    }

    /* 🔘 APPLY BUTTON SYNC */
    .btn-apply {
        display: block;
        padding: 18px;
        background: #fff;
        color: #000;
        text-decoration: none;
        border-radius: 25px;
        font-weight: 900;
        font-size: 13px;
        text-transform: uppercase;
        letter-spacing: 2px;
        transition: 0.4s;
        text-align: center;
    }

    .btn-apply:hover {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 0 30px rgba(255, 59, 59, 0.5);
    }

    /* FOOTER FIX */
    footer { margin-top: 0 !important; }

    @media(max-width:768px){
        .subpage-grid { grid-template-columns: 1fr; }
        .subpage-title { font-size: 55px; }
        .public-shell { padding: 120px 5% 0px; }
    }
</style>

<div class="public-shell">
    <div class="mesh-bg"></div>

    <section class="subpage-hero">
        <span class="hero-tag">Career Opportunities</span>
        <h1 class="subpage-title">
            <span>Career</span>
            Board 2026
        </h1>
    </section>

    <div class="subpage-grid">
        <?php if ($jobs): ?>
            <?php foreach ($jobs as $job): 
                $jobLink = !empty($job["apply_link"]) ? $job["apply_link"] : "login.php";
                $imgName = htmlspecialchars($job['company_logo']);
                $imgUrl = !empty($imgName) ? "uploads/logos/" . $imgName : "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80";
            ?>
                <article class="job-card reveal">
                    <div class="card-banner">
                        <img src="<?= $imgUrl ?>" alt="Job Banner">
                        <div class="floating-badge"><?= htmlspecialchars($job["company"]) ?></div>
                    </div>

                    <div class="card-content">
                        <h3 class="card-title"><?= htmlspecialchars($job["title"]) ?></h3>
                        
                        <div class="meta-row">
                            <div class="chip"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job["location"] ?: "Remote") ?></div>
                            <div class="chip"><i class="fas fa-clock"></i> Full-Time</div>
                        </div>

                        <p class="job-desc">
                            <?= substr(htmlspecialchars($job["description"]), 0, 110) ?>...
                        </p>

                        <a href="<?= htmlspecialchars($jobLink) ?>" class="btn-apply">
                            View Position <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 100px; z-index: 10; position: relative;">
                <h2 style="color: #444; letter-spacing: 2px;">NO OPEN POSITIONS</h2>
                <p style="color: #666;">Our network is currently full. Check back soon!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    // GSAP Sync Animation
    gsap.set(".reveal", { opacity: 0, y: 50 });

    window.onload = () => {
        gsap.to(".reveal", {
            y: 0,
            opacity: 1,
            duration: 1,
            stagger: 0.15,
            ease: "power4.out"
        });

        gsap.from(".subpage-title", {
            scale: 0.9,
            opacity: 0,
            duration: 1.2,
            ease: "expo.out"
        });
    };
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>