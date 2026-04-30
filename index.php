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
        --primary: #ff4d4d;
        --primary-hover: #ef4444;
        --bg-soft: #f8f8f8;
        --white: #ffffff;
        --text-rich: #111111;
        --text-gray: #6b7280;
        --border-light: #e5e7eb;
        --shadow-clean: 0 4px 20px rgba(0, 0, 0, 0.03);
        --shadow-hover: 0 20px 40px rgba(0, 0, 0, 0.06);
    }

    body {
        background: var(--bg-soft);
        color: var(--text-rich);
        font-family: 'Plus Jakarta Sans', sans-serif;
        margin: 0;
        overflow-x: hidden;
        /* Added to prevent horizontal jump */
        -webkit-font-smoothing: antialiased;
    }

    /* --- HERO SECTION --- */
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
        filter: brightness(0.5);
        /* Slightly darker for better readability */
    }

    .hero-content {
        z-index: 10;
        text-align: center;
        padding: 0 5%;
        /* Responsive padding */
        width: 100%;
    }

    .hero-badge {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
        padding: 12px 28px;
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
        font-size: clamp(55px, 12vw, 115px);
        /* Better scaling */
        font-weight: 900;
        letter-spacing: -4px;
        line-height: 0.9;
        color: #fff;
        margin: 0 auto 30px;
        text-transform: uppercase;
        max-width: 1000px;
    }

    .hero-content h1 span {
        color: var(--primary);
    }

    .btn-premium {
        background: var(--primary);
        color: #fff;
        padding: 20px 50px;
        border-radius: 14px;
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
        box-shadow: 0 15px 35px rgba(255, 77, 77, 0.35);
    }

    /* --- SECTION ALIGNMENTS --- */
    .container-fluid {
        padding: 120px 8%;
        /* Balanced padding */
    }

    .section-label {
        margin-bottom: 50px;
    }

    .label-line {
        width: 50px;
        height: 4px;
        background: var(--primary);
        margin-bottom: 20px;
        border-radius: 10px;
    }

    .section-title {
        font-family: 'Inter', sans-serif;
        font-size: clamp(32px, 5vw, 65px);
        font-weight: 900;
        letter-spacing: -2px;
        line-height: 1;
        color: var(--text-rich);
        text-transform: uppercase;
    }

    /* --- HORIZONTAL SCROLL FIX --- */
    .horizontal-scroll-container {
        display: flex;
        gap: 30px;
        overflow-x: auto;
        padding: 20px 0 60px;
        scrollbar-width: none;
        /* Firefox */
        -ms-overflow-style: none;
        /* IE/Edge */
    }

    .horizontal-scroll-container::-webkit-scrollbar {
        display: none;
    }

    /* Chrome/Safari */

    .premium-alumni-card {
        flex: 0 0 320px;
        /* Consistent width */
        background: var(--white);
        border: 1px solid var(--border-light);
        border-radius: 35px;
        padding: 45px 30px;
        text-align: center;
        transition: 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        box-shadow: var(--shadow-clean);
    }

    .premium-alumni-card:hover {
        transform: translateY(-12px);
        box-shadow: var(--shadow-hover);
        border-color: var(--primary);
    }

    .alumni-avatar {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 20px;
        border: 4px solid var(--bg-soft);
    }

    /* --- BENTO GRID REFINEMENT --- */
    .bento-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .bento-item {
        min-height: 400px;
        border-radius: 30px;
        position: relative;
        overflow: hidden;
        background: #000;
    }

    .bento-item.large {
        grid-column: span 2;
    }

    .event-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.7;
        transition: 0.7s cubic-bezier(0.165, 0.84, 0.44, 1);
    }

    .bento-item:hover .event-img {
        transform: scale(1.08);
    }

    .event-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to top, rgba(0, 0, 0, 0.9) 0%, transparent 70%);
    }

    .event-content {
        position: absolute;
        bottom: 30px;
        left: 30px;
        right: 30px;
        z-index: 5;
    }

    /* --- JOB STRIPS REFINEMENT --- */
    .job-strip {
        background: var(--white);
        border: 1px solid var(--border-light);
        padding: 22px 35px;
        border-radius: 22px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: 0.3s ease;
        box-shadow: var(--shadow-clean);
    }

    .job-strip:hover {
        border-color: var(--primary);
        box-shadow: var(--shadow-hover);
        transform: scale(1.01);
    }

    .job-icon-box {
        width: 52px;
        height: 52px;
        background: #fff5f5;
        color: var(--primary);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
    }

    .job-btn {
        background: var(--text-rich);
        color: #fff;
        padding: 12px 28px;
        border-radius: 12px;
        font-weight: 700;
        text-decoration: none;
        font-size: 13px;
        transition: 0.3s;
    }

    .job-btn:hover {
        background: var(--primary);
    }

    .reveal {
        opacity: 0;
        transform: translateY(40px);
    }

    @media (max-width: 992px) {
        .container-fluid {
            padding: 80px 6%;
        }

        .bento-grid {
            grid-template-columns: 1fr;
        }

        .bento-item.large {
            grid-column: span 1;
        }

        .job-strip {
            flex-direction: column;
            text-align: center;
            gap: 20px;
            padding: 30px;
        }

        .job-strip>div {
            flex-direction: column;
        }
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
        <p class="reveal" style="font-size: clamp(16px, 2vw, 20px); color: rgba(255,255,255,0.8); max-width: 650px; margin: 0 auto 40px; font-weight: 400; line-height: 1.6;">
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

    <div class="horizontal-scroll-container reveal">
        <?php
        $res = $conn->query("SELECT * FROM alumni ORDER BY id DESC LIMIT 8");
        while ($row = $res->fetch_assoc()) { ?>
            <div class="premium-alumni-card">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['name']) ?>&background=ff4d4d&color=fff&size=200&bold=true" class="alumni-avatar">
                <h3 style="font-weight: 800; font-size: 22px; margin-bottom: 5px; color: var(--text-rich);"><?= $row['name'] ?></h3>
                <p style="color: var(--primary); font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: 1px;"><?= $row['company'] ?></p>
                <p style="color: var(--text-gray); font-size: 14px; margin-top: 15px; line-height: 1.6; font-weight: 500;">Pioneering excellence and leadership in the global tech ecosystem.</p>
            </div>
        <?php } ?>
    </div>
</section>

<section class="container-fluid" style="background: var(--white); border-top: 1px solid var(--border-light); border-bottom: 1px solid var(--border-light); padding: 100px 8%;">
    <div class="row g-5">

        <div class="col-lg-8">
            <div class="section-label reveal">
                <div class="label-line"></div>
                <h2 class="section-title">Global <span style="color: var(--primary);">Summits</span></h2>
            </div>

            <div class="bento-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                <?php
                $res = $conn->query("SELECT * FROM events ORDER BY event_date ASC LIMIT 3");
                $count = 0;
                while ($row = $res->fetch_assoc()) {
                    $count++;
                    // Pehla card full width (col-span-2), baaki do side-by-side
                    $isLarge = ($count == 1) ? 'grid-column: span 2;' : '';
                ?>
                    <div class="reveal sexy-event-card" style="<?= $isLarge ?> position: relative; border-radius: 30px; overflow: hidden; min-height: <?= ($count == 1) ? '400px' : '300px' ?>; background: #000;">
                        <img src="https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=800" class="event-img" style="width:100%; height:100%; object-fit:cover; opacity:0.7; transition: 0.6s;">
                        <div class="event-overlay" style="position:absolute; inset:0; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);"></div>

                        <div class="date-badge-floating" style="position: absolute; top: 20px; left: 20px; background: rgba(255,255,255,0.1); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.2); padding: 10px; border-radius: 15px; text-align: center; min-width: 55px;">
                            <span style="display:block; font-weight:800; color:#fff; font-size:18px;"><?= date('d', strtotime($row['event_date'])) ?></span>
                            <span style="display:block; font-weight:700; color:var(--primary); font-size:10px; text-transform:uppercase;"><?= date('M', strtotime($row['event_date'])) ?></span>
                        </div>

                        <div class="event-content" style="position: absolute; bottom: 30px; left: 30px; right: 30px; z-index: 5;">
                            <h3 style="font-size: <?= ($count == 1) ? '28px' : '22px' ?>; font-weight: 850; color: #fff; margin: 0; line-height: 1.2;"><?= $row['title'] ?></h3>
                            <p style="color: rgba(255,255,255,0.7); font-size: 13px; margin-top: 10px;"><i class="fas fa-location-dot"></i> <?= $row['location'] ?></p>
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

            <div class="timeline-container" style="position: relative; padding-left: 20px; margin-top: 10px;">
                <div class="timeline-line" style="position: absolute; left: 0; top: 10px; bottom: 10px; width: 2px; background: linear-gradient(to bottom, var(--primary), transparent); opacity: 0.2;"></div>

                <div class="post-card reveal sexy-post">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                        <img src="https://i.pravatar.cc/100?img=12" style="width: 45px; height: 45px; border-radius: 12px; border: 2px solid var(--bg-soft);">
                        <div>
                            <h4 style="font-weight: 800; margin: 0; font-size: 15px;">Rahul Sharma</h4>
                            <small style="color: var(--text-gray); font-size: 11px;"><i class="far fa-clock"></i> 2 hours ago</small>
                        </div>
                    </div>
                    <p style="color: #4b5563; font-size: 14px; line-height: 1.5; font-weight: 500;">Placed at <strong style="color:var(--text-rich)">Microsoft</strong>! Huge thanks to the network. 🚀</p>
                </div>

                <div class="post-card reveal sexy-post">
                    <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 15px;">
                        <img src="https://i.pravatar.cc/100?img=5" style="width: 45px; height: 45px; border-radius: 12px; border: 2px solid var(--bg-soft);">
                        <div>
                            <h4 style="font-weight: 800; margin: 0; font-size: 15px;">Priya Verma</h4>
                            <small style="color: var(--text-gray); font-size: 11px;"><i class="far fa-clock"></i> Yesterday</small>
                        </div>
                    </div>
                    <p style="color: #4b5563; font-size: 14px; line-height: 1.5; font-weight: 500;">Great session on <strong style="color:var(--text-rich)">AI Ethics</strong> today. Good to see everyone! 👋</p>
                </div>
            </div>
        </div>

    </div>
</section>

<style>
    /* 🔴 UI REFINEMENTS */
    .sexy-event-card:hover .event-img {
        transform: scale(1.05);
    }

    .sexy-post {
        background: var(--white);
        border: 1px solid var(--border-light);
        border-radius: 22px;
        padding: 20px;
        margin-bottom: 20px;
        transition: 0.4s;
    }

    .sexy-post:hover {
        transform: translateX(8px);
        border-color: var(--primary);
        box-shadow: var(--shadow-hover);
    }

    @media (max-width: 992px) {
        .bento-grid {
            grid-template-columns: 1fr !important;
        }

        .sexy-event-card {
            grid-column: span 1 !important;
        }

        .col-lg-4 {
            margin-top: 50px;
        }

        /* Mobile pe Summits ke baad gap */
    }
</style>

<div class="col-lg-4" style="padding-top: 15px;">
    <div class="section-label reveal">
        <div class="label-line"></div>
        <h2 class="section-title">Latest <span style="color: var(--primary);">Feed</span></h2>
    </div>

    <div class="timeline-container" style="position: relative; padding-left: 20px; margin-top: 30px;">
        <div class="timeline-line" style="position: absolute; left: 0; top: 10px; bottom: 10px; width: 2px; background: linear-gradient(to bottom, var(--primary), transparent); opacity: 0.3;"></div>

        <div class="post-card reveal sexy-post">
            <div class="post-header">
                <div class="avatar-wrapper">
                    <img src="https://i.pravatar.cc/100?img=12" alt="Rahul">
                    <div class="status-indicator"></div>
                </div>
                <div class="user-meta">
                    <h4>Rahul Sharma</h4>
                    <small><i class="far fa-clock"></i> 2 hours ago</small>
                </div>
                <div class="post-platform"><i class="fab fa-microsoft"></i></div>
            </div>
            <div class="post-body">
                <p>Thrilled to announce my new role at <span>Microsoft</span>! Huge thanks to the Raisoni community for the mentorship. 🚀</p>
            </div>
            <div class="post-footer">
                <span class="like-tag"><i class="fas fa-heart"></i> 24</span>
                <span class="comment-tag"><i class="fas fa-comment"></i> 5</span>
            </div>
        </div>

        <div class="post-card reveal sexy-post">
            <div class="post-header">
                <div class="avatar-wrapper">
                    <img src="https://i.pravatar.cc/100?img=5" alt="Priya">
                    <div class="status-indicator"></div>
                </div>
                <div class="user-meta">
                    <h4>Priya Verma</h4>
                    <small><i class="far fa-clock"></i> Yesterday</small>
                </div>
                <div class="post-platform"><i class="fas fa-users"></i></div>
            </div>
            <div class="post-body">
                <p>Attending the <span>Annual Tech Meet</span>. Looking forward to meeting fellow alumni! 👋</p>
            </div>
            <div class="post-footer">
                <span class="like-tag"><i class="fas fa-heart"></i> 42</span>
                <span class="comment-tag"><i class="fas fa-comment"></i> 12</span>
            </div>
        </div>
    </div>
</div>

<style>
    /* 🔴 SEXY FEED STYLES - FIXED ALIGNMENTS */
    .sexy-post {
        background: var(--white) !important;
        border: 1px solid var(--border-light) !important;
        border-radius: 24px !important;
        padding: 25px !important;
        margin-bottom: 25px !important;
        position: relative;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
        overflow: hidden;
        box-shadow: var(--shadow-clean) !important;
    }

    .sexy-post:hover {
        transform: translateX(10px) translateY(-5px) !important;
        border-color: var(--primary) !important;
        box-shadow: 0 20px 40px rgba(255, 77, 77, 0.1) !important;
    }

    /* Avatar Styling */
    .avatar-wrapper {
        position: relative;
        width: 50px;
        height: 50px;
    }

    .avatar-wrapper img {
        width: 100%;
        height: 100%;
        border-radius: 14px;
        object-fit: cover;
        border: 2px solid var(--bg-soft);
    }

    .status-indicator {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 12px;
        height: 12px;
        background: #22c55e;
        border: 2px solid #fff;
        border-radius: 50%;
    }

    .post-header {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 18px;
    }

    .user-meta h4 {
        margin: 0;
        font-size: 16px;
        font-weight: 800;
        color: var(--text-rich);
    }

    .user-meta small {
        color: var(--text-gray);
        font-size: 11px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .post-platform {
        margin-left: auto;
        color: var(--border-light);
        font-size: 18px;
        transition: 0.3s;
    }

    .sexy-post:hover .post-platform {
        color: var(--primary);
        transform: rotate(12deg);
    }

    /* Body Text Re-Alignment */
    .post-body p {
        color: #4b5563;
        font-size: 14px;
        line-height: 1.6;
        font-weight: 500;
        margin: 0;
    }

    .post-body p span {
        color: var(--text-rich);
        font-weight: 700;
        border-bottom: 2px solid rgba(255, 77, 77, 0.2);
    }

    /* Footer Tags Styling */
    .post-footer {
        display: flex;
        gap: 20px;
        margin-top: 18px;
        padding-top: 15px;
        border-top: 1px dashed var(--border-light);
    }

    .like-tag,
    .comment-tag {
        font-size: 12px;
        font-weight: 700;
        color: var(--text-gray);
        cursor: pointer;
        transition: 0.3s;
        display: flex;
        align-items: center;
    }

    .like-tag i {
        color: #f43f5e;
        margin-right: 6px;
    }

    .comment-tag i {
        color: #3b82f6;
        margin-right: 6px;
    }

    .like-tag:hover {
        color: #f43f5e;
        transform: scale(1.05);
    }

    .comment-tag:hover {
        color: #3b82f6;
        transform: scale(1.05);
    }

    /* Mobile Adjustment */
    @media (max-width: 992px) {
        .col-lg-4 {
            padding-top: 60px;
        }

        /* More space on mobile when stacked */
    }
</style>

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
                        <h3 style="font-weight: 800; font-size: 19px; margin: 0; color: var(--text-rich);"><?= $row['title'] ?></h3>
                        <p style="color: var(--text-gray); font-weight: 600; margin-top: 5px; font-size: 14px;"><?= $row['company'] ?> • <?= $row['location'] ?: 'Remote' ?></p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 30px;">
                    <span style="font-weight: 700; color: var(--text-gray); font-size: 11px; text-transform: uppercase; letter-spacing: 1px;">Full-Time</span>
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
            scrollTrigger: {
                trigger: el,
                start: "top 92%",
                toggleActions: "play none none none"
            },
            opacity: 1,
            y: 0,
            duration: 1.2,
            ease: "expo.out"
        });
    });
</script>

<?php include("includes/footer.php"); ?>