<?php
$pageTitle = "AlumniX | Career Board";
include(__DIR__ . "/includes/header.php");
include(__DIR__ . "/includes/db.php");
require_once(__DIR__ . "/includes/public_helpers.php");

$jobs = fetchRows($conn, "SELECT id, title, company, location, description, apply_link, company_logo FROM jobs WHERE status='approved' ORDER BY id DESC");
?>

<style>
    :root {
        --primary: #ff3b3b;
        --dark-bg: #0f172a;
        --card-bg: #ffffff;
    }

    .public-shell {
        background: #f1f5f9;
        min-height: 100vh;
        padding-bottom: 100px;
    }

    .subpage-hero {
        padding: 150px 20px 60px;
        text-align: center;
        background: white;
        margin-bottom: 50px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }

    .subpage-title {
        font-size: clamp(32px, 5vw, 50px);
        font-weight: 900;
        color: var(--dark-bg);
        letter-spacing: -2px;
    }

    .subpage-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 30px;
        max-width: 1300px;
        margin: auto;
        padding: 0 25px;
    }

    /* 💼 FULL IMAGE CARD */
    .job-card {
        background: var(--card-bg);
        border-radius: 25px;
        overflow: hidden; /* Image corners round karne ke liye */
        border: 1px solid rgba(0,0,0,0.05);
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        flex-direction: column;
    }

    .job-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.15);
    }

    /* 🖼️ TOP BANNER IMAGE */
    .card-banner {
        width: 100%;
        height: 180px;
        position: relative;
        overflow: hidden;
        background: #e2e8f0;
    }

    .card-banner img {
        width: 100%;
        height: 100%;
        object-fit: cover; /* Image perfectly fit hogi */
        transition: 0.5s;
    }

    .job-card:hover .card-banner img {
        transform: scale(1.1);
    }

    /* 🏷️ FLOATING COMPANY LOGO (Optional Overlay) */
    .floating-badge {
        position: absolute;
        bottom: 15px;
        left: 15px;
        background: white;
        padding: 5px 15px;
        border-radius: 10px;
        font-weight: 800;
        font-size: 12px;
        color: var(--dark-bg);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .card-content {
        padding: 25px;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .card-title {
        font-size: 22px;
        font-weight: 850;
        color: var(--dark-bg);
        margin-bottom: 10px;
        line-height: 1.2;
    }

    .meta-info {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
    }

    .meta-info i { color: var(--primary); }

    .btn-apply {
        margin-top: auto;
        background: var(--dark-bg);
        color: white;
        text-align: center;
        padding: 15px;
        border-radius: 15px;
        text-decoration: none;
        font-weight: 800;
        transition: 0.3s;
    }

    .btn-apply:hover {
        background: var(--primary);
        box-shadow: 0 10px 20px rgba(255, 59, 59, 0.2);
    }
</style>

<div class="public-shell">
    <section class="subpage-hero">
        <h1 class="subpage-title">Career <span>Opportunities</span></h1>
    </section>

    <div class="subpage-grid">
        <?php if ($jobs): ?>
            <?php foreach ($jobs as $job): 
                $jobLink = !empty($job["apply_link"]) ? $job["apply_link"] : "login.php";
                
                // 🛠️ Image Logic: Agar DB me image h to wo, warna ek sexy default tech image
                $imgName = htmlspecialchars($job['company_logo']);
                $imgUrl = !empty($imgName) ? "uploads/logos/" . $imgName : "https://images.unsplash.com/photo-1497215728101-856f4ea42174?auto=format&fit=crop&w=800&q=80";
            ?>
                <article class="job-card">
                    <div class="card-banner">
                        <img src="<?= $imgUrl ?>" alt="Job Banner">
                        <div class="floating-badge"><?= htmlspecialchars($job["company"]) ?></div>
                    </div>

                    <div class="card-content">
                        <h3 class="card-title"><?= htmlspecialchars($job["title"]) ?></h3>
                        
                        <div class="meta-info">
                            <span><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($job["location"] ?: "Remote") ?></span>
                            <span><i class="fas fa-briefcase"></i> Full-Time</span>
                        </div>

                        <p style="font-size:14px; color:#64748b; line-height:1.6; margin-bottom: 20px;">
                            <?= substr(htmlspecialchars($job["description"]), 0, 90) ?>...
                        </p>

                        <a href="<?= htmlspecialchars($jobLink) ?>" class="btn-apply">
                            Apply Now
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php include(__DIR__ . "/includes/footer.php"); ?>