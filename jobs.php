<?php
$pageTitle = "AlumniX | Career Board";
include(__DIR__ . "/includes/header.php");
include(__DIR__ . "/includes/db.php");
require_once(__DIR__ . "/includes/public_helpers.php");

$jobs = fetchRows($conn, "SELECT id, title, company, location, description, apply_link, company_logo FROM jobs WHERE status='approved' ORDER BY id DESC");
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<style>
    /* 🔴 LUXURY RED-BLACK-WHITE THEME */
    :root {
        --primary: #ff3b3b;
        --primary-glow: rgba(255, 59, 59, 0.3);
        --bg-dark: #0a0a0a; /* Rich Charcoal */
        --card-bg: rgba(255, 255, 255, 0.04);
        --border: rgba(255, 255, 255, 0.12);
        --text-main: #ffffff;
        --text-dim: #a1a1aa;
    }

    /* 🌑 FULL PAGE SETUP - NO WHITE GAP */
    html, body { background-color: var(--bg-dark); margin: 0; padding: 0; }

    .public-shell {
        background: var(--bg-dark);
        min-height: auto; /* Fix: No extra stretching */
        padding: 140px 8% 40px; 
        color: var(--text-main);
        position: relative;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
    }

    .mesh-bg {
        position: fixed;
        top: 0; left: 0; width: 100%; height: 100%;
        background: radial-gradient(circle at 15% 15%, rgba(255, 59, 59, 0.07) 0%, transparent 40%),
                    radial-gradient(circle at 85% 85%, rgba(255, 59, 59, 0.05) 0%, transparent 40%);
        z-index: 0;
        pointer-events: none;
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
    }

    .subpage-title span {
        display: block;
        color: transparent;
        -webkit-text-stroke: 1.2px rgba(255, 255, 255, 0.25);
    }

    /* 💎 3-COLUMN GRID SYNC */
    .subpage-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr); 
        gap: 30px;
        position: relative;
        z-index: 2;
        margin-bottom: 40px;
    }

    /* 💼 JOB CARD - SEXY PREMIUM VIBE */
    .job-card {
        background: var(--card-bg);
        border: 1px solid var(--border);
        border-radius: 30px;
        padding: 16px;
        backdrop-filter: blur(25px);
        -webkit-backdrop-filter: blur(25px);
        transition: all 0.5s cubic-bezier(0.19, 1, 0.22, 1);
        display: flex;
        flex-direction: column;
    }

    .job-card:hover {
        transform: translateY(-12px);
        border-color: var(--primary);
        background: rgba(255, 255, 255, 0.07);
        box-shadow: 0 30px 60px -15px rgba(0,0,0,0.6), 0 0 15px var(--primary-glow);
    }

    .card-banner {
        width: 100%;
        height: 240px;
        border-radius: 22px;
        overflow: hidden;
        position: relative;
        background: #000;
    }

    .card-banner img {
        width: 100%; height: 100%;
        object-fit: cover;
        filter: grayscale(100%) brightness(0.7);
        transition: 0.8s ease;
    }

    .job-card:hover .card-banner img {
        filter: grayscale(0%) brightness(1);
        transform: scale(1.08);
    }

    .floating-badge {
        position: absolute;
        top: 20px;
        right: 20px;
        background: #fff;
        color: #000;
        padding: 8px 16px;
        border-radius: 14px;
        font-weight: 900;
        font-size: 11px;
        z-index: 3;
        box-shadow: 0 10px 20px rgba(0,0,0,0.3);
    }

    .card-content {
        padding: 22px 10px 5px;
    }

    .card-title {
        font-size: 24px;
        font-weight: 800;
        margin-bottom: 18px;
        letter-spacing: -0.5px;
        color: #fff;
        min-height: 58px; /* Keeps layout consistent */
        line-height: 1.2;
    }

    .meta-row {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
    }

    .chip {
        padding: 6px 12px;
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-dim);
        border-radius: 12px;
        font-size: 10px;
        font-weight: 700;
        border: 1px solid var(--border);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .chip i { color: var(--primary); margin-right: 5px; }

    .job-desc {
        font-size: 14px;
        color: var(--text-dim);
        line-height: 1.6;
        margin-bottom: 25px;
        height: 65px; /* Alignment for text */
        overflow: hidden;
    }

    /* 🔘 APPLY BUTTON - CLEAN WHITE CONTRAST */
    .btn-apply {
        display: block;
        padding: 15px;
        background: #fff;
        color: #000;
        text-decoration: none;
        border-radius: 18px;
        font-weight: 800;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        transition: 0.4s;
        text-align: center;
    }

    .btn-apply:hover {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 10px 25px var(--primary-glow);
    }

    /* 🛑 FOOTER FIX */
    footer { margin-top: 0 !important; border-top: 1px solid var(--border); }

    @media(max-width:1100px){
        .subpage-grid { grid-template-columns: repeat(2, 1fr); }
    }

    @media(max-width:768px){
        .subpage-grid { grid-template-columns: 1fr; }
        .subpage-title { font-size: 50px; }
        .public-shell { padding: 100px 6% 30px; }
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
                            <div class="chip"><i class="fas fa-briefcase"></i> Full-Time</div>
                        </div>

                        <p class="job-desc">
                            <?= substr(htmlspecialchars($job["description"]), 0, 100) ?>...
                        </p>

                        <a href="<?= htmlspecialchars($jobLink) ?>" class="btn-apply">
                            View Position <i class="fas fa-arrow-right" style="margin-left:5px;"></i>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 100px; z-index: 10;">
                <h2 style="color: var(--text-dim); letter-spacing: 2px;">NO OPEN POSITIONS</h2>
                <p style="color: #444;">Check back soon for new opportunities.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    gsap.set(".reveal", { opacity: 0, y: 30 });
    window.onload = () => {
        gsap.to(".reveal", {
            y: 0, opacity: 1, duration: 1.2, stagger: 0.1, ease: "power4.out"
        });
    };
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>