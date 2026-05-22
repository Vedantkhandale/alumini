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
    --bg-soft: #f8fbff;
    --bg-deep: #05060d;
    --white: #ffffff;
    --text-rich: #0f172a;
    --text-gray: #6b7280;
    --border-light: rgba(148, 163, 184, 0.2);
    --shadow-clean: 0 18px 60px rgba(15, 23, 42, 0.08);
    --shadow-hover: 0 20px 40px rgba(15, 23, 42, 0.12);
}

html,
body {
    background: #f8fbff;
    color: var(--text-rich);
    font-family: 'Plus Jakarta Sans', sans-serif;
    margin: 0;
    padding: 0;
    overflow-x: hidden;
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
        background: linear-gradient(180deg, #090b13 0%, #05060d 100%);
    }

    .hero-section::after {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background: radial-gradient(circle at top center, rgba(255, 77, 77, 0.14), transparent 20%),
                    radial-gradient(circle at 20% 70%, rgba(255, 255, 255, 0.06), transparent 18%);
    }

    .hero-content {
        z-index: 10;
        text-align: center;
        padding: 0 24px;
        width: 100%;
        max-width: 920px;
    }

    .hero-badge {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(14px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: #fff;
        padding: 14px 36px;
        border-radius: 100px;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 4px;
        text-transform: uppercase;
        margin-bottom: 32px;
        display: inline-block;
    }

    .hero-content h1 {
        font-family: 'Inter', sans-serif;
        font-size: clamp(48px, 11vw, 110px);
        font-weight: 900;
        letter-spacing: -3px;
        line-height: 1.05;
        color: #fff;
        margin: 0 auto 28px;
        text-transform: uppercase;
        max-width: 1000px;
        text-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
    }

    .hero-content h1 span {
        background: linear-gradient(135deg, #ff4d4d, #ff7a64);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .hero-content p {
        font-size: clamp(16px, 2vw, 21px);
        color: rgba(255, 255, 255, 0.85);
        max-width: 700px;
        margin: 0 auto 42px;
        font-weight: 400;
        line-height: 1.65;
        text-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
    }

    .btn-premium {
        background: linear-gradient(135deg, #ff4d4d, #ff7a64);
        color: #fff;
        padding: 18px 52px;
        border-radius: 16px;
        font-weight: 800;
        text-decoration: none;
        letter-spacing: 1px;
        display: inline-block;
        transition: 0.35s ease;
        text-transform: uppercase;
        font-size: 14px;
        box-shadow: 0 18px 50px rgba(255, 77, 77, 0.2);
    }

    .btn-premium:hover {
        background: linear-gradient(135deg, #ef4444, #ff6a5c);
        transform: translateY(-3px);
        box-shadow: 0 22px 55px rgba(255, 77, 77, 0.25);
    }

    .section-title {
        font-family: 'Inter', sans-serif;
        font-size: clamp(40px, 5.5vw, 75px);
        font-weight: 900;
        letter-spacing: -3px;
        line-height: 1.1;
        color: var(--text-rich);
        text-transform: uppercase;
    }

    .section-card {
        background: rgba(255, 255, 255, 0.96);
        box-shadow: 0 28px 85px rgba(15, 23, 42, 0.1);
        border-radius: 35px;
        border: 1.5px solid rgba(148, 163, 184, 0.18);
        padding: 48px;
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
    }

    /* --- SECTION ALIGNMENTS --- */
    .container-fluid {
        padding: 120px 6%;
        max-width: 1500px;
        margin: 0 auto;
        position: relative;
    }

    .section-label {
        margin-bottom: 70px;
        position: relative;
        z-index: 2;
    }

    .section-label::after {
        content: '';
        position: absolute;
        bottom: -20px;
        left: 0;
        width: 100px;
        height: 3px;
        background: linear-gradient(90deg, #ff4d4d, rgba(255, 77, 77, 0.2));
        border-radius: 10px;
    }

    .label-line {
        width: 60px;
        height: 5px;
        background: linear-gradient(90deg, #ff4d4d, rgba(255, 77, 77, 0.3));
        margin-bottom: 18px;
        border-radius: 10px;
    }

    .section-title {
        font-family: 'Inter', sans-serif;
        font-size: clamp(40px, 5.5vw, 75px);
        font-weight: 900;
        letter-spacing: -3px;
        line-height: 1.1;
        color: var(--text-rich);
        text-transform: uppercase;
    }

    .row {
        display: flex;
        flex-wrap: wrap;
        gap: 50px;
        justify-content: space-between;
        margin: 0;
        align-items: flex-start;
    }

    .col-lg-8,
    .col-lg-4 {
        box-sizing: border-box;
        padding: 0;
    }

    .col-lg-8 {
        flex: 0 0 62%;
        max-width: 62%;
    }

    .col-lg-4 {
        flex: 0 0 35%;
        max-width: 35%;
    }

    .hero-content {
        max-width: 1100px;
        margin: 0 auto;
    }

    /* --- HORIZONTAL SCROLL FIX --- */
    .horizontal-scroll-container {
        display: flex;
        gap: 35px;
        overflow-x: auto;
        padding: 30px 0 70px;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .horizontal-scroll-container::-webkit-scrollbar {
        display: none;
    }

    .premium-alumni-card {
        flex: 0 0 300px;
        background: rgba(255, 255, 255, 0.99);
        border: 2px solid rgba(148, 163, 184, 0.2);
        border-radius: 32px;
        padding: 50px 35px;
        text-align: center;
        transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
        box-shadow: 0 20px 70px rgba(15, 23, 42, 0.1);
        position: relative;
        overflow: hidden;
    }

    .premium-alumni-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255, 77, 77, 0.1), transparent);
        border-radius: 50%;
        z-index: 0;
    }

    .premium-alumni-card:hover {
        transform: translateY(-20px) scale(1.03);
        box-shadow: 0 40px 100px rgba(255, 77, 77, 0.15);
        border-color: #ff4d4d;
    }

    .alumni-avatar {
        width: 120px;
        height: 120px;
        border-radius: 35px;
        object-fit: cover;
        margin-bottom: 28px;
        border: 4px solid rgba(255, 77, 77, 0.1);
        transition: all 0.5s;
    }

    .premium-alumni-card:hover .alumni-avatar {
        border-radius: 50%;
        transform: rotate(5deg) scale(1.08);
    }

    /* --- BENTO GRID --- */
    .bento-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 35px;
        margin-bottom: 0;
    }

    .sexy-event-card {
        position: relative;
        border-radius: 38px;
        overflow: hidden;
        background: #000;
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 32px 90px rgba(15, 23, 42, 0.18);
    }

    .sexy-event-card::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, transparent, rgba(255, 77, 77, 0.05));
        z-index: 2;
        pointer-events: none;
    }

    .sexy-event-card:hover {
        transform: translateY(-14px) scale(1.02);
        box-shadow: 0 45px 120px rgba(255, 77, 77, 0.25);
    }

    /* --- LATEST FEED FIX --- */
    .timeline-container {
        position: relative;
        padding-left: 0;
        width: 100%;
        box-sizing: border-box;
        margin-top: 0;
    }

    .sexy-post {
        background: rgba(255, 255, 255, 0.98) !important;
        border: 1.5px solid rgba(148, 163, 184, 0.28) !important;
        border-radius: 28px !important;
        padding: 32px !important;
        margin-bottom: 28px !important;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
        width: 100% !important;
        box-sizing: border-box;
        box-shadow: 0 14px 45px rgba(15, 23, 42, 0.06) !important;
        position: relative;
        overflow: hidden;
    }

    .sexy-post::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 77, 77, 0.04), transparent);
        transition: left 0.6s ease;
    }

    .sexy-post:hover::before {
        left: 100%;
    }

    .sexy-post:hover {
        transform: translateX(12px) !important;
        border-color: #ff4d4d !important;
        box-shadow: 0 20px 60px rgba(255, 77, 77, 0.14) !important;
    }

    /* --- JOBS STRIPS --- */
    .job-strip {
        background: rgba(255, 255, 255, 0.96);
        border: 1.5px solid rgba(148, 163, 184, 0.25);
        padding: 35px 45px;
        border-radius: 28px;
        margin-bottom: 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.4s ease;
        box-shadow: 0 16px 50px rgba(15, 23, 42, 0.07);
        position: relative;
        overflow: hidden;
    }

    .job-strip::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: linear-gradient(180deg, #ff4d4d, transparent);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .job-strip:hover {
        border-color: #ff4d4d;
        transform: scale(1.01) translateY(-5px);
        box-shadow: 0 24px 65px rgba(255, 77, 77, 0.15);
    }

    .job-strip:hover::before {
        opacity: 1;
    }

    .job-icon-box {
        width: 75px;
        height: 75px;
        background: linear-gradient(135deg, #fff5f5, #fff0f0);
        color: #ff4d4d;
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        transition: all 0.3s ease;
        border: 1.5px solid rgba(255, 77, 77, 0.2);
        box-shadow: 0 8px 20px rgba(255, 77, 77, 0.08);
    }

    .job-strip:hover .job-icon-box {
        background: linear-gradient(135deg, #ff4d4d, #ff7a64);
        color: #fff;
        transform: scale(1.15) rotate(8deg);
        box-shadow: 0 12px 30px rgba(255, 77, 77, 0.2);
    }

    .job-btn {
        background: linear-gradient(135deg, #0f172a, #1a2341);
        color: #fff;
        padding: 14px 36px;
        border-radius: 16px;
        font-weight: 800;
        text-decoration: none;
        font-size: 13px;
        transition: all 0.3s;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: 1px solid rgba(255, 77, 77, 0.3);
    }

    .job-btn:hover {
        background: linear-gradient(135deg, #ff4d4d, #ff7a64);
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(255, 77, 77, 0.25);
    }

    .reveal {
        opacity: 0;
        transform: translateY(30px);
    }

    @media (max-width: 992px) {
        .container-fluid { padding: 60px 6%; }
        .bento-grid { grid-template-columns: 1fr !important; gap: 24px; }
        .job-strip { flex-direction: column; text-align: center; gap: 20px; }
        .timeline-container { padding-left: 0; }
        .col-lg-4 { padding-left: 15px !important; border-left: none !important; margin-top: 60px; }
        .hero-section { height: 85vh; }
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

<section class="container-fluid" style="background: rgba(248, 251, 255, 0.95); overflow: hidden;">
    <div class="section-label reveal">
        <div class="label-line"></div>
        <h2 class="section-title">
            Hall Of <span style="color: var(--primary);">Fame</span>
        </h2>
    </div>

    <div class="horizontal-scroll-container reveal" id="alumniTrack" style="display: flex; cursor: grab;">
        <?php
        $res = $conn->query("SELECT * FROM alumni ORDER BY id DESC LIMIT 8");
        while ($row = $res->fetch_assoc()) { ?>
            <div class="premium-alumni-card moving-card">
                <div class="avatar-container" style="margin-bottom: 25px; position: relative; display: inline-block;">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['name']) ?>&background=ff4d4d&color=fff&size=200&bold=true" class="alumni-avatar">
                </div>
                <h3 style="font-weight: 850; font-size: 24px; margin-bottom: 8px; color: var(--text-rich); letter-spacing: -0.5px;"><?= $row['name'] ?></h3>
                <div class="company-badge" style="background: #fff5f5; display: inline-block; padding: 6px 16px; border-radius: 12px; margin-bottom: 15px; transition: 0.3s;">
                    <p style="color: var(--primary); font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; margin: 0;"><?= $row['company'] ?></p>
                </div>
                <p style="color: var(--text-gray); font-size: 14px; line-height: 1.6; font-weight: 500; margin: 0;">Pioneering excellence and leadership in the global tech ecosystem.</p>
                <div style="position: absolute; top: 20px; right: 25px; opacity: 0.05; font-size: 40px; pointer-events: none;"><i class="fas fa-award"></i></div>
            </div>
        <?php } ?>
    </div>
</section>

<section class="container-fluid" style="background: rgba(255, 255, 255, 0.98); box-shadow: 0 24px 80px rgba(15, 23, 42, 0.05); border-radius: 40px;">
    <div class="row g-lg-5"> 
        <div class="col-lg-8" style="padding-bottom: 40px;">
            <div class="section-label reveal" style="margin-bottom: 50px;">
                <div class="label-line" style="width: 80px; height: 6px; background: linear-gradient(90deg, var(--primary), transparent); border-radius: 10px; margin-bottom: 20px;"></div>
                <h2 class="section-title" style="font-size: clamp(45px, 6vw, 75px); font-weight: 900; letter-spacing: -3px; text-transform: uppercase; line-height: 0.9; margin: 0;">Global <br><span style="color: var(--primary);">Summits</span></h2>
            </div>
            <div class="bento-grid">
                <?php
                $res = $conn->query("SELECT * FROM events ORDER BY event_date ASC LIMIT 3");
                $count = 0;
                while ($row = $res->fetch_assoc()) {
                    $count++;
                    $isLarge = ($count == 1) ? 'grid-column: span 2;' : '';
                ?>
                    <div class="reveal sexy-event-card" style="<?= $isLarge ?> position: relative; min-height: <?= ($count == 1) ? '440px' : '340px' ?>;">
                        <img src="https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=800" class="event-img" style="width:100%; height:100%; object-fit:cover; opacity:0.65; transition: 0.8s ease;">
                        <div style="position:absolute; inset:0; background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.4) 50%, transparent 100%);"></div>
                        <div style="position: absolute; top: 25px; left: 25px; background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.25); padding: 12px 20px; border-radius: 22px; text-align: center;">
                            <span style="display:block; font-weight:900; color:#fff; font-size:24px; line-height:1;"><?= date('d', strtotime($row['event_date'])) ?></span>
                            <span style="display:block; font-weight:700; color:var(--primary); font-size:11px; text-transform:uppercase; margin-top:2px;"><?= date('M', strtotime($row['event_date'])) ?></span>
                        </div>
                        <div style="position: absolute; bottom: 35px; left: 35px; right: 35px; z-index: 5;">
                            <h3 style="font-size: <?= ($count == 1) ? '36px' : '26px' ?>; font-weight: 850; color: #fff; margin: 0; line-height: 1.1;"><?= $row['title'] ?></h3>
                            <p style="color: rgba(255,255,255,0.8); font-size: 15px; margin-top: 15px; font-weight: 500;"><i class="fas fa-location-dot" style="color:var(--primary); margin-right: 8px;"></i><?= $row['location'] ?></p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="col-lg-4" style="padding-left: 50px; border-left: 1px solid var(--border-light);">
            <div class="section-label reveal" style="margin-bottom: 50px;">
                <div class="label-line" style="width: 60px; height: 6px; background: linear-gradient(90deg, var(--primary), transparent); border-radius: 10px; margin-bottom: 20px;"></div>
                <h2 class="section-title" style="font-size: clamp(35px, 4vw, 55px); font-weight: 900; letter-spacing: -2px; text-transform: uppercase; line-height: 0.9; margin: 0;">Latest <br><span style="color: var(--primary);">Feed</span></h2>
            </div>
            <div class="timeline-container">
                <div class="sexy-post reveal">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 18px;">
                        <img src="https://i.pravatar.cc/100?img=12" style="width: 55px; height: 55px; border-radius: 18px; border: 2px solid var(--bg-soft);">
                        <div>
                            <h4 style="font-weight: 800; margin: 0; font-size: 17px;">Rahul Sharma</h4>
                            <small style="color: var(--text-gray); font-size: 11px;">2 hours ago</small>
                        </div>
                    </div>
                    <p style="color: #4b5563; font-size: 15px; line-height: 1.6;">Placed at <strong style="color:var(--text-rich)">Microsoft</strong>! Huge thanks to the network. 🚀</p>
                </div>
                <div class="sexy-post reveal">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 18px;">
                        <img src="https://i.pravatar.cc/100?img=5" style="width: 55px; height: 55px; border-radius: 18px; border: 2px solid var(--bg-soft);">
                        <div>
                            <h4 style="font-weight: 800; margin: 0; font-size: 17px;">Priya Verma</h4>
                            <small style="color: var(--text-gray); font-size: 11px;">Yesterday</small>
                        </div>
                    </div>
                    <p style="color: #4b5563; font-size: 15px; line-height: 1.6;">Great session on <strong style="color:var(--text-rich)">AI Ethics</strong> today. 👋</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container-fluid" style="padding-top: 50px; background: transparent;">
    <div class="section-label reveal" style="margin-bottom: 60px;">
        <div class="label-line" style="width: 100px; height: 8px; background: var(--primary); border-radius: 10px; margin-bottom: 15px;"></div>
        <h2 class="section-title" style="font-size: clamp(45px, 6vw, 85px); font-weight: 900; letter-spacing: -4px; text-transform: uppercase;">Career <span style="color: var(--primary);">Board</span></h2>
    </div>
    <div class="job-list">
        <?php
        $res = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC LIMIT 5");
        while ($row = $res->fetch_assoc()) { ?>
            <div class="job-strip reveal">
                <div style="display: flex; align-items: center; gap: 30px;">
                    <div class="job-icon-box"><i class="fas fa-briefcase"></i></div>
                    <div>
                        <h3 style="font-weight: 850; font-size: 22px; margin: 0; color: var(--text-rich);"><?= $row['title'] ?></h3>
                        <p style="color: var(--text-gray); font-weight: 600; margin-top: 5px; font-size: 15px;"><?= $row['company'] ?> • <span style="color:var(--primary)"><?= $row['location'] ?: 'Remote' ?></span></p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 40px;">
                    <span style="font-weight: 800; color: var(--text-gray); font-size: 12px; text-transform: uppercase; letter-spacing: 2px;">Full-Time</span>
                    <a href="jobs.php" class="job-btn">Apply Now</a>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

<script>
    gsap.registerPlugin(ScrollTrigger);
    
    // Reveal Animations
    document.querySelectorAll('.reveal').forEach(el => {
        gsap.to(el, {
            scrollTrigger: { trigger: el, start: "top 90%" },
            opacity: 1, y: 0, duration: 1, ease: "expo.out"
        });
    });

    // Final Touch: Smooth Auto-Pilot Scroll for Hall of Fame
    const track = document.getElementById('alumniTrack');
    let scrollSpeed = 0.6;
    let isHovering = false;

    function autoScroll() {
        if (!isHovering) {
            track.scrollLeft += scrollSpeed;
            if (track.scrollLeft >= track.scrollWidth - track.clientWidth) {
                track.scrollLeft = 0;
            }
        }
        requestAnimationFrame(autoScroll);
    }
    
    track.addEventListener('mouseenter', () => isHovering = true);
    track.addEventListener('mouseleave', () => isHovering = false);
    
    // Kickstart auto-scroll
    autoScroll();

    // Horizontal Drag logic
    let isDown = false; let startX; let scrollLeft;
    track.addEventListener('mousedown', (e) => { isDown = true; startX = e.pageX - track.offsetLeft; scrollLeft = track.scrollLeft; });
    track.addEventListener('mouseleave', () => { isDown = false; });
    track.addEventListener('mouseup', () => { isDown = false; });
    track.addEventListener('mousemove', (e) => {
        if(!isDown) return;
        e.preventDefault();
        const x = e.pageX - track.offsetLeft;
        const walk = (x - startX) * 2;
        track.scrollLeft = scrollLeft - walk;
    });
</script>


<?php include("includes/footer.php"); ?>