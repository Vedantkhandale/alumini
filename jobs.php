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
    :root {
        --primary: #ff3b3b;
        --dark-bg: #050505; /* Solid Dark */
        --glass: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.08);
    }

    .public-shell {
        background: var(--dark-bg);
        min-height: 100vh;
        padding-bottom: 100px;
        color: #fff;
    }

    /* 🔥 SEXY HERO HEADER */
    .subpage-hero {
        padding: 160px 20px 80px;
        text-align: center;
        background: radial-gradient(circle at center, rgba(255, 59, 59, 0.07) 0%, transparent 70%);
    }

    .label-line { 
        width: 80px; height: 5px; 
        background: var(--primary); 
        margin: 0 auto 20px; 
        border-radius: 10px;
        box-shadow: 0 0 20px var(--primary);
    }

    .subpage-title {
        font-size: clamp(40px, 8vw, 85px);
        font-weight: 950;
        letter-spacing: -4px;
        text-transform: uppercase;
        line-height: 0.9;
    }

    .subpage-title span {
        color: transparent;
        -webkit-text-stroke: 1.5px rgba(255,255,255,0.3);
    }

    /* 🏢 BENTO STYLE GRID */
    .subpage-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
        gap: 40px;
        max-width: 1300px;
        margin: auto;
        padding: 0 25px;
    }

    /* 💼 THE PREMIUM JOB CARD */
    .job-card {
        background: var(--glass);
        backdrop-filter: blur(15px);
        border-radius: 40px;
        overflow: hidden;
        border: 1px solid var(--glass-border);
        transition: all 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
        display: flex;
        flex-direction: column;
        opacity: 0; transform: translateY(40px);
    }

    .job-card:hover {
        transform: translateY(-15px);
        border-color: var(--primary);
        box-shadow: 0 20px 50px rgba(255, 59, 59, 0.15);
        background: rgba(255, 255, 255, 0.05);
    }

    /* 🖼️ CARD BANNER */
    .card-banner {
        width: 100%;
        height: 200px;
        position: relative;
        overflow: hidden;
    }

    .card-banner img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.6;
        transition: 0.8s;
    }

    .job-card:hover .card-banner img {
        transform: scale(1.1) rotate(1deg);
        opacity: 0.9;
    }

    .floating-badge {
        position: absolute;
        bottom: 20px;
        left: 20px;
        background: var(--primary);
        padding: 8px 18px;
        border-radius: 15px;
        font-weight: 800;
        font-size: 13px;
        color: #fff;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* CARD CONTENT */
    .card-content {
        padding: 40px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        background: linear-gradient(180deg, transparent 0%, var(--dark-bg) 100%);
        margin-top: -60px;
        position: relative;
    }

    .card-title {
        font-size: 26px;
        font-weight: 800;
        color: #fff;
        margin-bottom: 12px;
        letter-spacing: -1px;
        line-height: 1.1;
    }

    .meta-row {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 25px;
    }

    .chip {
        padding: 6px 14px;
        background: rgba(255, 255, 255, 0.05);
        color: #94a3b8;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 700;
        border: 1px solid var(--glass-border);
    }

    .chip i { color: var(--primary); margin-right: 5px; }

    .job-desc {
        font-size: 15px;
        color: #94a3b8;
        line-height: 1.7;
        margin-bottom: 30px;
    }

    /* 🔘 SEXY ACTION BUTTON */
    .btn-apply {
        margin-top: auto;
        background: #fff;
        color: #000;
        text-align: center;
        padding: 18px;
        border-radius: 20px;
        text-decoration: none;
        font-weight: 900;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 2px;
        transition: 0.4s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .btn-apply:hover {
        background: var(--primary);
        color: #fff;
        transform: scale(1.05);
        box-shadow: 0 10px 25px rgba(255, 59, 59, 0.4);
    }

    @media(max-width:768px){
        .subpage-title { font-size: 45px; }
        .subpage-grid { grid-template-columns: 1fr; }
    }
</style>

<div class="public-shell">
    <section class="subpage-hero">
        <div class="label-line"></div>
        <h1 class="subpage-title">Career <span>Board</span></h1>
        <p style="color: #64748b; font-weight: 600; font-size: 18px; margin-top: 15px;">Elite opportunities for the AlumniX network.</p>
    </section>

    <div class="subpage-grid">
        <?php if ($jobs): ?>
            <?php foreach ($jobs as $job): 
                $jobLink = !empty($job["apply_link"]) ? $job["apply_link"] : "login.php";
                $imgName = htmlspecialchars($job['company_logo']);
                $imgUrl = !empty($imgName) ? "uploads/logos/" . $imgName : "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?auto=format&fit=crop&w=800&q=80";
            ?>
                <article class="job-card">
                    <div class="card-banner">
                        <img src="<?= $imgUrl ?>" alt="Job Banner">
                        <div class="floating-badge"><?= htmlspecialchars($job["company"]) ?></div>
                    </div>

                    <div class="card-content">
                        <h3 class="card-title"><?= htmlspecialchars($job["title"]) ?></h3>
                        
                        <div class="meta-row">
                            <div class="chip"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job["location"] ?: "Remote") ?></div>
                            <div class="chip"><i class="fas fa-clock"></i> Full-Time</div>
                            <div class="chip"><i class="fas fa-bolt"></i> Urgent</div>
                        </div>

                        <p class="job-desc">
                            <?= substr(htmlspecialchars($job["description"]), 0, 115) ?>...
                        </p>

                        <a href="<?= htmlspecialchars($jobLink) ?>" class="btn-apply">
                            View Position <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 100px; color: #64748b;">
                <p>No active openings currently. Check back soon!</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    gsap.registerPlugin(ScrollTrigger);

    // Initial Reveal for Cards
    gsap.to(".job-card", {
        opacity: 1,
        y: 0,
        duration: 1,
        stagger: 0.15,
        ease: "power4.out",     
        scrollTrigger: {
            trigger: ".subpage-grid",
            start: "top 85%"
        }
    });
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>