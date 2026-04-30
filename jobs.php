<?php
$pageTitle = "AlumniX | Career Board";
include(__DIR__ . "/includes/header.php");
include(__DIR__ . "/includes/db.php");
require_once(__DIR__ . "/includes/public_helpers.php");

$jobs = fetchRows($conn, "SELECT id, title, company, location, description, apply_link, company_logo FROM jobs WHERE status='approved' ORDER BY id DESC");
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<style>
    /* 🔴 LUXURY WHITE-RED THEME (INDEX SYNC) */
    :root {
        --primary: #ff4d4d;
        --bg-soft: #f8f8f8;
        --white: #ffffff;
        --text-main: #111111;
        --text-gray: #6b7280;
        --border-light: #e5e7eb;
    }

    html, body { background-color: var(--bg-soft); margin: 0; padding: 0; }

    .public-shell {
        background: var(--bg-soft);
        min-height: auto;
        padding: 140px 8% 40px; 
        color: var(--text-main);
        position: relative;
        overflow-x: hidden;
        display: flex;
        flex-direction: column;
    }

    /* Mesh background hataya taaki clean white look aaye */
    .mesh-bg { display: none; }

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

    .subpage-title span {
        display: block;
        color: var(--primary); /* Empty stroke ki jagah solid red */
    }

    .subpage-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr); 
        gap: 30px;
        position: relative;
        z-index: 2;
        margin-bottom: 40px;
    }

    /* 💼 JOB CARD - PREMIUM WHITE */
    .job-card {
        background: var(--white);
        border: 1px solid var(--border-light);
        border-radius: 30px;
        padding: 16px;
        transition: all 0.5s cubic-bezier(0.19, 1, 0.22, 1);
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }

    .job-card:hover {
        transform: translateY(-12px);
        border-color: var(--primary);
        box-shadow: 0 30px 60px -15px rgba(0,0,0,0.08);
    }

    .card-banner {
        width: 100%;
        height: 200px; /* Thoda chota kiya */
        border-radius: 22px;
        overflow: hidden;
        position: relative;
        background: #eee;
    }

    .card-banner img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: 0.8s ease;
    }

    .floating-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: var(--primary);
        color: #fff;
        padding: 6px 14px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 10px;
        z-index: 3;
    }

    .card-content {
        padding: 20px 10px 5px;
    }

    .card-title {
        font-size: 22px;
        font-weight: 800;
        margin-bottom: 12px;
        letter-spacing: -0.5px;
        color: var(--text-main);
        min-height: 52px;
        line-height: 1.2;
    }

    .meta-row {
        display: flex;
        gap: 10px;
        margin-bottom: 15px;
    }

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

    .chip i { color: var(--primary); margin-right: 5px; }

    .job-desc {
        font-size: 14px;
        color: var(--text-gray);
        line-height: 1.6;
        margin-bottom: 20px;
        height: 65px;
        overflow: hidden;
    }

    /* 🔘 APPLY BUTTON - CLEAN BLACK STYLE */
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

    .btn-apply:hover {
        background: var(--primary);
        color: #fff;
    }

    footer { margin-top: 0 !important; border-top: 1px solid var(--border-light); }

    @media(max-width:1100px){ .subpage-grid { grid-template-columns: repeat(2, 1fr); } }
    @media(max-width:768px){ 
        .subpage-grid { grid-template-columns: 1fr; }
        .subpage-title { font-size: 50px; }
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
                <h2 style="color: var(--text-gray); letter-spacing: 2px;">NO OPEN POSITIONS</h2>
                <p style="color: #999;">Check back soon for new opportunities.</p>
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