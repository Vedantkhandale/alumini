<?php
$pageTitle = "AlumniX | Home";
$bodyClass = "home-page";
include(__DIR__ . "/header.php");
include(__DIR__ . "/db.php");
require_once(__DIR__ . "/public_helpers.php");

// Fetching data
$featuredJobs = fetchRows($conn, "SELECT id, title, company, location, apply_link, description FROM jobs WHERE status='approved' ORDER BY id DESC LIMIT 6");
$upcomingEvents = fetchRows($conn, "SELECT id, title, description, event_date FROM events ORDER BY event_date ASC LIMIT 3");
$featuredAlumni = fetchRows($conn, "SELECT id, name, course, batch, company FROM alumni ORDER BY id DESC LIMIT 4");

$stats = [
    ["value" => fetchCount($conn, "SELECT COUNT(*) FROM alumni"), "label" => "Alumni Stories", "icon" => "fa-user-graduate"],
    ["value" => fetchCount($conn, "SELECT COUNT(*) FROM jobs WHERE status='approved'"), "label" => "Opportunities", "icon" => "fa-briefcase"],
    ["value" => fetchCount($conn, "SELECT COUNT(*) FROM events"), "label" => "Events", "icon" => "fa-calendar-alt"],
];
?>

<style>
    /* --- HERO SECTION --- */
    .hero {
        position: relative;
        height: 90vh;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        color: #fff;
        text-align: center;
    }

    .hero-video {
        position: absolute;
        top: 50%; left: 50%;
        min-width: 100%; min-height: 100%;
        width: auto; height: auto;
        transform: translate(-50%, -50%);
        z-index: -2;
        object-fit: cover;
    }

    .overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: radial-gradient(circle, rgba(0,0,0,0.4) 0%, rgba(0,0,0,0.8) 100%);
        z-index: -1;
    }

    .hero-content { max-width: 800px; padding: 20px; }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(10px);
        padding: 8px 20px;
        border-radius: 50px;
        border: 1px solid rgba(255,255,255,0.2);
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 25px;
    }

    .hero h1 {
        font-size: clamp(40px, 8vw, 72px);
        font-weight: 800;
        line-height: 1.1;
        letter-spacing: -2px;
        margin-bottom: 20px;
    }

    .hero p {
        font-size: clamp(16px, 2vw, 20px);
        opacity: 0.9;
        margin-bottom: 35px;
    }

    .btn-main {
        padding: 16px 40px;
        background: #ff3b3b;
        color: #fff;
        text-decoration: none;
        border-radius: 50px;
        font-weight: 700;
        font-size: 16px;
        box-shadow: 0 10px 30px rgba(255, 59, 59, 0.4);
        transition: 0.3s;
    }

    .btn-main:hover { transform: translateY(-3px); box-shadow: 0 15px 40px rgba(255, 59, 59, 0.5); }

    /* --- STATS OVERLAY --- */
    .stats-container {
        margin-top: -60px;
        position: relative;
        z-index: 10;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        max-width: 1000px;
        margin-left: auto; margin-right: auto;
        padding: 0 20px;
    }

    .stat-card {
        background: #fff;
        padding: 30px;
        border-radius: 24px;
        text-align: center;
        box-shadow: 0 20px 50px rgba(0,0,0,0.05);
        border: 1px solid rgba(0,0,0,0.02);
    }

    .stat-card i { color: #ff3b3b; font-size: 24px; margin-bottom: 10px; display: block; }
    .stat-card strong { font-size: 32px; display: block; color: #111; letter-spacing: -1px; }
    .stat-card span { font-size: 14px; color: #666; font-weight: 600; }

    /* --- SECTION HEAD --- */
    .section-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-bottom: 40px;
        padding: 0 20px;
    }

    .section-title { font-size: 32px; font-weight: 800; letter-spacing: -1px; }
    .section-link { color: #ff3b3b; text-decoration: none; font-weight: 700; font-size: 14px; }

    /* --- CARDS GRID --- */
    .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 25px;
        padding: 20px;
    }

    /* --- CTA STRIP --- */
    .cta-strip {
        background: #111;
        border-radius: 30px;
        padding: 60px;
        margin: 80px 20px;
        color: #fff;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 40px;
        background-image: radial-gradient(at top right, #333, #111);
    }

    .cta-strip h3 { font-size: 32px; margin-bottom: 15px; letter-spacing: -1px; }
    .cta-strip p { opacity: 0.7; }

    @media (max-width: 768px) {
        .stats-container { grid-template-columns: 1fr; margin-top: -30px; }
        .cta-strip { flex-direction: column; text-align: center; padding: 40px 20px; }
        .section-head { flex-direction: column; align-items: flex-start; gap: 15px; }
    }
</style>

<section class="hero">
    <video autoplay muted loop playsinline class="hero-video">
        <source src="images/hero.mp4" type="video/mp4">
    </video>
    <div class="overlay"></div>
    <div class="hero-content">
        <div class="hero-badge"><i class="fas fa-bolt"></i> Official Community Portal</div>
        <h1>Powering the <br>Next Generation.</h1>
        <p>A private network for alumni to share jobs, events, and expertise.</p>
        <a href="registration.php" class="btn-main">Get Started Today</a>
    </div>
</section>

<div class="stats-container">
    <?php foreach ($stats as $stat): ?>
        <div class="stat-card">
            <i class="fas <?php echo $stat['icon']; ?>"></i>
            <strong><?php echo number_format($stat["value"]); ?></strong>
            <span><?php echo e($stat["label"]); ?></span>
        </div>
    <?php endforeach; ?>
</div>

<div class="public-shell" style="max-width: 1200px; margin: 80px auto;">
    
    <section class="section">
        <div class="section-head">
            <div>
                <span class="section-kicker">Career Board</span>
                <h2 class="section-title">Roles worth forwarding.</h2>
            </div>
            <a href="jobs.php" class="section-link">Explore all jobs <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="card-grid">
            <?php foreach (array_slice($featuredJobs, 0, 3) as $job): ?>
                <article class="card-panel">
                    <div class="panel-head">
                        <div class="avatar-chip"><i class="fas fa-briefcase"></i></div>
                        <span class="tag">New</span>
                    </div>
                    <h3 class="card-title"><?php echo e($job["title"]); ?></h3>
                    <p class="card-subtitle"><?php echo e($job["company"]); ?> • <?php echo e($job["location"] ?: "Remote"); ?></p>
                    <div class="action-row" style="margin-top: 20px;">
                        <a href="jobs.php" class="card-link">View Details</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="section" style="margin-top: 100px;">
        <div class="section-head">
            <div>
                <span class="section-kicker">Meetups</span>
                <h2 class="section-title">Upcoming gatherings.</h2>
            </div>
            <a href="events.php" class="section-link">Full calendar <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="card-grid">
            <?php foreach ($upcomingEvents as $event): ?>
                <?php $eventDate = strtotime($event["event_date"]); ?>
                <article class="card-panel">
                    <div class="panel-head">
                        <div class="date-chip">
                            <strong><?php echo date("d", $eventDate); ?></strong>
                            <span><?php echo date("M", $eventDate); ?></span>
                        </div>
                    </div>
                    <h3 class="card-title"><?php echo e($event["title"]); ?></h3>
                    <p class="card-copy"><?php echo e(substr($event["description"], 0, 80)); ?>...</p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="cta-strip">
        <div>
            <h3>Join the 1% club of our alumni.</h3>
            <p>Access exclusive jobs and network with the industry leaders.</p>
        </div>
        <div class="cta-actions">
            <a href="registration.php" class="btn-main">Create Account</a>
        </div>
    </section>
</div>

<?php include(__DIR__ . "/footer.php"); ?>