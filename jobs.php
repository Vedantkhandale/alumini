<?php

$pageTitle = "AlumniX | Career Board";

// Sahi path define karne ke liye
include(__DIR__ . "/includes/header.php");
include(__DIR__ . "/includes/db.php");
require_once(__DIR__ . "/includes/public_helpers.php");

// Baki ka code...

// Fetching jobs
$jobs = fetchRows($conn, "SELECT id, title, company, location, description, apply_link FROM jobs WHERE status='approved' ORDER BY id DESC");
$jobCount = count($jobs);
?>

<style>
    /* 🎨 MODERN UI OVERHAUL */
    :root {
        --primary: #ff3b3b;
        --dark-bg: #0f172a;
        --card-bg: #ffffff;
    }

    .public-shell {
        background: #f8fafc;
        min-height: 100vh;
        font-family: 'Inter', sans-serif;
    }

    /* 🔥 SEXY HERO SECTION */
    .subpage-hero {
        padding: 140px 20px 80px;
        text-align: center;
        background: radial-gradient(circle at 50% 0%, rgba(255, 59, 59, 0.12) 0%, transparent 50%);
        position: relative;
    }

    .section-kicker {
        background: var(--primary);
        color: white;
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .subpage-title {
        font-size: clamp(36px, 6vw, 56px);
        font-weight: 900;
        letter-spacing: -2.5px;
        color: var(--dark-bg);
        margin: 20px 0;
        line-height: 1;
    }

    /* 🔍 FLOATING SEARCH BAR */
    .search-container {
        max-width: 700px;
        margin: -40px auto 60px;
        padding: 0 20px;
        position: relative;
        z-index: 10;
    }

    .search-bar {
        width: 100%;
        padding: 22px 30px;
        border-radius: 100px;
        border: 1px solid rgba(0,0,0,0.05);
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.08);
        font-size: 18px;
        outline: none;
        transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
    }

    .search-bar:focus {
        transform: scale(1.02);
        border-color: var(--primary);
        box-shadow: 0 25px 50px -12px rgba(255, 59, 59, 0.2);
    }

    /* 🏢 RESPONSIVE GRID */
    .subpage-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 30px;
        padding-bottom: 100px;
    }

    /* 💼 PREMIUM CARD DESIGN */
    .job-card {
        background: var(--card-bg);
        border-radius: 32px;
        padding: 35px;
        border: 1px solid rgba(15, 23, 42, 0.04);
        transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
        display: flex;
        flex-direction: column;
        height: 100%;
        position: relative;
        overflow: hidden;
    }

    .job-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 100%; height: 6px;
        background: var(--primary);
        transform: scaleX(0);
        transition: 0.4s;
        transform-origin: left;
    }

    .job-card:hover {
        transform: translateY(-15px);
        box-shadow: 0 40px 80px -20px rgba(0,0,0,0.12);
        border-color: rgba(255, 59, 59, 0.2);
    }

    .job-card:hover::before { transform: scaleX(1); }

    .icon-chip {
        width: 55px; height: 55px;
        background: var(--dark-bg);
        color: #fff;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .status-tag {
        font-size: 11px;
        font-weight: 800;
        background: #f0fdf4;
        color: #16a34a;
        padding: 6px 14px;
        border-radius: 100px;
        border: 1px solid #dcfce7;
    }

    .card-title {
        font-size: 24px;
        font-weight: 850;
        color: var(--dark-bg);
        margin-top: 25px;
        letter-spacing: -0.5px;
    }

    .card-subtitle {
        color: var(--primary);
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 20px;
    }

    .meta-chip {
        background: #f1f5f9;
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .card-copy {
        font-size: 15px;
        line-height: 1.7;
        color: #64748b;
        margin: 20px 0;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* 🚀 BUTTON GLOW */
    .btn-apply {
        margin-top: auto;
        padding: 18px;
        background: var(--dark-bg);
        color: white;
        text-align: center;
        border-radius: 18px;
        text-decoration: none;
        font-weight: 800;
        transition: 0.3s;
        border: none;
        cursor: pointer;
    }

    .btn-apply:hover {
        background: var(--primary);
        transform: scale(1.02);
        box-shadow: 0 15px 30px rgba(255, 59, 59, 0.3);
    }

    /* RESPONSIVE FIXES */
    @media (max-width: 768px) {
        .subpage-title { font-size: 32px; }
        .subpage-grid { grid-template-columns: 1fr; }
        .job-card { padding: 25px; }
    }
</style>

<div class="public-shell">
    <section class="subpage-hero reveal">
        <span class="section-kicker">Career Board</span>
        <h1 class="subpage-title">Find your next <br><span>Elite Opportunity</span></h1>
        
        <div style="margin-top: 30px;">
            <span class="meta-chip">
                <i class="fas fa-fire"></i> 
                <strong><?= $jobCount ?></strong> New Openings
            </span>
        </div>
    </section>

    <div class="search-container">
        <input type="text" id="jobSearch" class="search-bar" placeholder="Try 'Remote', 'Frontend', or 'Google'...">
    </div>

    <section style="max-width: 1300px; margin: auto; padding: 0 25px;">
        <div class="subpage-grid" id="jobsGrid">
            <?php if ($jobs): ?>
                <?php foreach ($jobs as $job): 
                    $jobLink = !empty($job["apply_link"]) ? $job["apply_link"] : "login.php";
                    $isExternal = preg_match('/^(https?:\/\/)/i', $jobLink);
                ?>
                    <article class="job-card reveal">
                        <div class="card-top" style="display:flex; justify-content:space-between; align-items:start;">
                            <div class="icon-chip"><i class="fas fa-bolt"></i></div>
                            <span class="status-tag">Verified</span>
                        </div>

                        <h3 class="card-title"><?= htmlspecialchars($job["title"]) ?></h3>
                        <p class="card-subtitle"><?= htmlspecialchars($job["company"]) ?></p>

                        <div class="meta-row">
                            <span class="meta-chip">
                                <i class="fas fa-map-marker-alt" style="color:var(--primary)"></i> 
                                <?= htmlspecialchars($job["location"] ?: "Global Remote") ?>
                            </span>
                        </div>

                        <p class="card-copy"><?= htmlspecialchars($job["description"]) ?></p>

                        <a href="<?= htmlspecialchars($jobLink) ?>" 
                           class="btn-apply" 
                           <?= $isExternal ? 'target="_blank"' : '' ?>>
                            <?= $isExternal ? 'Apply Now <i class="fas fa-external-link-alt" style="font-size:12px; margin-left:5px;"></i>' : 'Login to Apply' ?>
                        </a>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align:center; padding: 100px;">
                    <h2 style="color:#cbd5e1;">No jobs active at the moment.</h2>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<script>
// Live Filtering Logic
document.getElementById('jobSearch').addEventListener('input', function() {
    let filter = this.value.toLowerCase();
    let cards = document.querySelectorAll('.job-card');
    
    cards.forEach(card => {
        let text = card.innerText.toLowerCase();
        if(text.includes(filter)) {
            card.style.display = "flex";
            card.style.opacity = "1";
        } else {
            card.style.display = "none";
            card.style.opacity = "0";
        }
    });
});
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>