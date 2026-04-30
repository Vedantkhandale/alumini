<?php
include("includes/header.php");
include("includes/db.php");
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<style>
    :root {
        --primary: #ff4d4d; /* Premium Coral Red */
        --primary-hover: #ef4444;
        --bg-soft: #f8f8f8; /* Soft White Background */
        --white: #ffffff;
        --text-rich: #111111; /* Rich Black */
        --text-gray: #6b7280; /* Elegant Gray */
        --border-light: #e5e7eb; /* Soft Gray Border */
        --shadow-clean: 0 4px 20px rgba(0, 0, 0, 0.03);
        --shadow-hover: 0 20px 40px rgba(0, 0, 0, 0.06);
    }

    body {
        background: var(--bg-soft);
        color: var(--text-rich);
        font-family: 'Plus Jakarta Sans', sans-serif;
        margin: 0;
        -webkit-font-smoothing: antialiased;
    }

    /* --- HERO SECTION: CLEAN & POWERFUL --- */
    .hero-section {
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        background: #000;
    }

    .hero-video-wrap {
        position: absolute;
        inset: 0;
        z-index: 1;
    }

    .hero-video-wrap video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.6); /* Text visibility control */
    }

    .hero-content {
        z-index: 10;
        text-align: center;
        padding: 0 25px;
    }

    .hero-badge {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        padding: 10px 24px;
        border-radius: 100px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 4px;
        text-transform: uppercase;
        margin-bottom: 30px;
        display: inline-block;
    }

    .hero-content h1 {
        font-family: 'Inter', sans-serif;
        font-size: clamp(60px, 14vw, 130px);
        font-weight: 900;
        letter-spacing: -6px;
        line-height: 0.85;
        color: #fff;
        margin-bottom: 35px;
        text-transform: uppercase;
    }

    .hero-content h1 span {
        color: var(--primary);
    }

    .btn-premium {
        background: var(--primary);
        color: #fff;
        padding: 22px 55px;
        border-radius: 12px;
        font-weight: 800;
        text-decoration: none;
        letter-spacing: 1px;
        display: inline-block;
        transition: 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        text-transform: uppercase;
        font-size: 14px;
    }

    .btn-premium:hover {
        background: var(--primary-hover);
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(255, 77, 77, 0.3);
    }

    /* --- SECTION HEADINGS --- */
    .container-fluid {
        padding: 120px 10%;
    }

    .section-label {
        margin-bottom: 60px;
    }

    .label-line {
        width: 40px;
        height: 3px;
        background: var(--primary);
        margin-bottom: 15px;
        border-radius: 10px;
    }

    .section-title {
        font-family: 'Inter', sans-serif;
        font-size: clamp(35px, 6vw, 75px);
        font-weight: 900;
        letter-spacing: -3px;
        line-height: 0.95;
        color: var(--text-rich);
        text-transform: uppercase;
    }

    /* --- PREMIUM WHITE CARDS (HALL OF FAME) --- */
    .premium-alumni-card {
        flex: 0 0 340px;
        background: var(--white);
        border: 1px solid var(--border-light);
        border-radius: 32px;
        padding: 50px 35px;
        text-align: center;
        transition: 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        box-shadow: var(--shadow-clean);
    }

    .premium-alumni-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-hover);
        border-color: var(--primary);
    }

    .alumni-avatar {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 25px;
        border: 4px solid var(--bg-soft);
    }

    /* --- BENTO EVENTS (REAL WEB LOOK) --- */
    .bento-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
    }

    .bento-item {
        min-height: 420px;
        border-radius: 32px;
        position: relative;
        overflow: hidden;
        background: #000;
    }

    .bento-item.large { grid-column: span 2; }

    .event-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.75;
        transition: 0.5s;
    }

    .bento-item:hover .event-img { transform: scale(1.05); }

    .event-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, transparent 60%);
    }

    .event-content {
        position: absolute;
        bottom: 35px;
        left: 35px;
        z-index: 5;
    }

    /* --- FEED & JOB STRIPS --- */
    .post-card {
        background: var(--white);
        border: 1px solid var(--border-light);
        border-radius: 24px;
        padding: 30px;
        box-shadow: var(--shadow-clean);
        margin-bottom: 20px;
    }

    .job-strip {
        background: var(--white);
        border: 1px solid var(--border-light);
        padding: 25px 40px;
        border-radius: 20px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: 0.3s;
        box-shadow: var(--shadow-clean);
    }

    .job-strip:hover {
        border-color: var(--primary);
        box-shadow: var(--shadow-hover);
        transform: scale(1.005);
    }

    .job-icon-box {
        width: 55px;
        height: 55px;
        background: #fff5f5; /* Light Red Tint */
        color: var(--primary);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .job-btn {
        background: var(--text-rich);
        color: #fff;
        padding: 12px 30px;
        border-radius: 10px;
        font-weight: 700;
        text-decoration: none;
        font-size: 13px;
        transition: 0.3s;
    }

    .job-btn:hover { background: var(--primary); }

    .reveal { opacity: 0; transform: translateY(30px); }

    @media (max-width: 992px) {
        .bento-grid { grid-template-columns: 1fr; }
        .bento-item.large { grid-column: span 1; }
        .job-strip { flex-direction: column; text-align: center; gap: 20px; }
    }
</style>

<section class="hero-section">
    <div class="hero-video-wrap">
        <video autoplay muted loop playsinline>
            <source src="images/hero.mp4" type="video/mp4">
        </video>
    </div>
    <div class="hero-content">
        <div class="hero-badge reveal">AluminiX Excellence</div>
        <h1 class="reveal" id="mainTitle">ALUMNI <span>X</span></h1>
        <p class="reveal" style="font-size: 20px; color: rgba(255,255,255,0.8); max-width: 650px; margin: 0 auto 40px; font-weight: 400; line-height: 1.6;">
            A premium network for the visionaries. Connect, collaborate, and carry the legacy forward.
        </p>
        <div class="reveal">
            <a href="registration.php" class="btn-premium">Claim Your Access</a>
        </div>
    </div>
</section>

<section class="container-fluid">
    <div class="section-label reveal">
        <div class="label-line"></div>
        <h2 class="section-title">Hall Of <span style="color: var(--primary);">Fame</span></h2>
    </div>

    <div class="horizontal-scroll-container" style="display: flex; gap: 30px; overflow-x: auto; padding-bottom: 40px; scrollbar-width: none;">
        <?php
        $res = $conn->query("SELECT * FROM alumni ORDER BY id DESC LIMIT 8");
        while ($row = $res->fetch_assoc()) { ?>
            <div class="premium-alumni-card reveal">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['name']) ?>&background=ff4d4d&color=fff&size=200&bold=true" class="alumni-avatar">
                <h3 style="font-weight: 800; font-size: 22px; margin-bottom: 5px; color: var(--text-rich);"><?= $row['name'] ?></h3>
                <p style="color: var(--primary); font-weight: 700; font-size: 12px; text-transform: uppercase; letter-spacing: 1px;"><?= $row['company'] ?></p>
                <p style="color: var(--text-gray); font-size: 14px; margin-top: 15px; line-height: 1.6; font-weight: 500;">Pioneering excellence and leadership in the global tech ecosystem.</p>
            </div>
        <?php } ?>
    </div>
</section>

<section class="container-fluid" style="background: var(--white); border-top: 1px solid var(--border-light); border-bottom: 1px solid var(--border-light);">
    <div class="row g-5">
        <div class="col-lg-8">
            <div class="section-label reveal">
                <div class="label-line"></div>
                <h2 class="section-title">Global <span style="color: var(--primary);">Summits</span></h2>
            </div>
            <div class="bento-grid">
                <?php
                $res = $conn->query("SELECT * FROM events ORDER BY event_date ASC LIMIT 3");
                $count = 0;
                while ($row = $res->fetch_assoc()) {
                    $count++;
                    $isLarge = ($count == 1) ? 'large' : '';
                ?>
                    <div class="bento-item <?= $isLarge ?> reveal">
                        <img src="https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=800" class="event-img">
                        <div class="event-overlay"></div>
                        <div class="event-content">
                            <span style="background: var(--primary); color: #fff; padding: 5px 15px; border-radius: 50px; font-size: 11px; font-weight: 800;"><?= date('M d, Y', strtotime($row['event_date'])) ?></span>
                            <h3 style="font-size: 28px; font-weight: 800; color: #fff; margin-top: 12px;"><?= $row['title'] ?></h3>
                            <p style="color: rgba(255,255,255,0.7); font-size: 14px; font-weight: 500;"><i class="fas fa-location-dot"></i> <?= $row['location'] ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="section-label reveal">
                <div class="label-line"></div>
                <h2 class="section-title">Latest <span style="color: var(--primary);">Feed</span></h2>
            </div>
            <div class="post-card reveal">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                    <img src="https://i.pravatar.cc/100?img=12" style="width: 45px; height: 45px; border-radius: 10px;">
                    <div>
                        <h4 style="font-weight: 800; margin: 0; font-size: 16px;">Rahul Sharma</h4>
                        <small style="color: var(--text-gray); font-weight: 600;">2 hours ago</small>
                    </div>
                </div>
                <p style="color: var(--text-gray); font-size: 14px; line-height: 1.5; font-weight: 500;">Thrilled to announce my new role at Microsoft! Huge thanks to the Raisoni community for the mentorship. 🚀</p>
            </div>
            <div class="post-card reveal">
                <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                    <img src="https://i.pravatar.cc/100?img=5" style="width: 45px; height: 45px; border-radius: 10px;">
                    <div>
                        <h4 style="font-weight: 800; margin: 0; font-size: 16px;">Priya Verma</h4>
                        <small style="color: var(--text-gray); font-weight: 600;">Yesterday</small>
                    </div>
                </div>
                <p style="color: var(--text-gray); font-size: 14px; line-height: 1.5; font-weight: 500;">Attending the annual tech meet. Looking forward to meeting fellow alumni! 👋</p>
            </div>
        </div>
    </div>
</section>

<section class="container-fluid">
    <div class="section-label reveal">
        <div class="label-line"></div>
        <h2 class="section-title">Career <span style="color: var(--primary);">Board</span></h2>
    </div>

    <div class="job-list">
        <?php
        $res = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC LIMIT 5");
        while ($row = $res->fetch_assoc()) { ?>
            <div class="job-strip reveal">
                <div style="display: flex; align-items: center; gap: 25px;">
                    <div class="job-icon-box"><i class="fas fa-briefcase"></i></div>
                    <div>
                        <h3 style="font-weight: 800; font-size: 20px; margin: 0; color: var(--text-rich);"><?= $row['title'] ?></h3>
                        <p style="color: var(--text-gray); font-weight: 600; margin-top: 4px; font-size: 14px;"><?= $row['company'] ?> • <?= $row['location'] ?: 'Remote' ?></p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 30px;">
                    <span style="font-weight: 700; color: var(--text-gray); font-size: 12px; text-transform: uppercase;">Full-Time</span>
                    <a href="jobs.php" class="job-btn">Apply Now</a>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

<script>
    gsap.registerPlugin(ScrollTrigger);

    const reveals = document.querySelectorAll('.reveal');
    reveals.forEach(el => {
        gsap.to(el, {
            scrollTrigger: { trigger: el, start: "top 90%" },
            opacity: 1, y: 0, duration: 1, ease: "expo.out"
        });
    });
</script>

<?php include("includes/footer.php"); ?>