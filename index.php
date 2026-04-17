<?php
include("includes/header.php");
include("includes/db.php");
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<style>
    :root {
        --primary: #ff3b3b;
        --primary-glow: rgba(255, 59, 59, 0.4);
        --dark-bg: #050505;
        --card-glass: rgba(255, 255, 255, 0.03);
        --border-glass: rgba(255, 255, 255, 0.08);
        --text-gray: #94a3b8;
    }

    body {
        background: var(--dark-bg);
        color: #fff;
        font-family: 'Plus Jakarta Sans', sans-serif;
        overflow-x: hidden;
        margin: 0;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: var(--dark-bg); }
    ::-webkit-scrollbar-thumb { background: #333; border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--primary); }

    /* --- HERO SECTION --- */
    .hero-section {
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        padding: 0 20px;
    }

    .hero-video-wrap {
        position: absolute;
        inset: 0;
        z-index: 1;
        opacity: 0.5;
    }
    .hero-video-wrap video { width: 100%; height: 100%; object-fit: cover; filter: brightness(0.4); }

    .hero-content {
        z-index: 10;
        text-align: center;
        max-width: 1000px;
    }

    .hero-content h1 {
        font-size: clamp(45px, 12vw, 110px);
        font-weight: 800;
        letter-spacing: -4px;
        line-height: 0.9;
        margin-bottom: 25px;
        text-transform: uppercase;
    }

    .hero-content h1 span {
        color: var(--primary);
        background: linear-gradient(to bottom, #ff3b3b, #8b0000);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        filter: drop-shadow(0 0 20px var(--primary-glow));
    }

    .btn-neon {
        padding: 18px 40px;
        background: var(--primary);
        color: #fff;
        border-radius: 100px;
        text-decoration: none;
        font-weight: 800;
        display: inline-block;
        transition: 0.3s;
        box-shadow: 0 10px 30px var(--primary-glow);
    }
    .btn-neon:hover { transform: scale(1.05); box-shadow: 0 15px 40px var(--primary-glow); }

    /* --- COMMON LAYOUT --- */
    .container-fluid { padding: 80px 8%; }
    .section-label { margin-bottom: 50px; }
    .label-line { width: 50px; height: 4px; background: var(--primary); margin-bottom: 15px; }
    .section-title { font-size: clamp(30px, 5vw, 55px); font-weight: 800; letter-spacing: -2px; }

    /* --- ALUMNI HORIZONTAL SCROLL --- */
    .horizontal-scroll-container {
        overflow-x: auto;
        white-space: nowrap;
        padding: 20px 0;
        scrollbar-width: none; /* Firefox */
    }
    .horizontal-scroll-container::-webkit-scrollbar { display: none; }

    .premium-alumni-card {
        display: inline-block;
        width: 300px;
        background: var(--card-glass);
        border: 1px solid var(--border-glass);
        border-radius: 30px;
        padding: 35px;
        margin-right: 25px;
        vertical-align: top;
        transition: 0.4s;
        white-space: normal;
    }
    .premium-alumni-card:hover { border-color: var(--primary); background: rgba(255,255,255,0.05); transform: translateY(-10px); }

    .alumni-avatar { width: 100px; height: 100px; border-radius: 25px; object-fit: cover; margin-bottom: 20px; border: 2px solid var(--border-glass); }

    /* --- BENTO EVENTS GRID --- */
    .bento-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 25px;
    }
    .bento-item {
        min-height: 350px;
        border-radius: 35px;
        padding: 40px;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        border: 1px solid var(--border-glass);
        transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .bento-item.large { grid-column: span 2; background: var(--primary); }
    .event-overlay { position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.9), transparent); z-index: 1; }
    .event-img { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; opacity: 0.4; }

    /* --- LATEST POSTS / FEED --- */
    .post-card {
        background: var(--card-glass);
        border: 1px solid var(--border-glass);
        border-radius: 25px;
        padding: 25px;
        margin-bottom: 25px;
    }
    .post-header { display: flex; align-items: center; gap: 15px; margin-bottom: 15px; }
    .post-user-img { width: 45px; height: 45px; border-radius: 50%; }

    /* --- JOB BOARD --- */
    .job-strip {
        background: var(--card-glass);
        border: 1px solid var(--border-glass);
        padding: 20px 35px;
        border-radius: 20px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 20px;
        transition: 0.3s;
    }
    .job-strip:hover { background: #fff; color: #000; }
    .job-strip:hover .job-btn { background: var(--primary); color: #fff; }

    /* Responsive Fixes */
    @media (max-width: 768px) {
        .bento-item.large { grid-column: span 1; }
        .job-strip { text-align: center; justify-content: center; }
        .container-fluid { padding: 60px 5%; }
    }

    .reveal { opacity: 0; transform: translateY(30px); }
</style>

<section class="hero-section">
    <div class="hero-video-wrap">
        <video autoplay muted loop playsinline>
            <source src="images/hero.mp4" type="video/mp4">
        </video>
    </div>
    <div class="hero-content">
        <span class="reveal" style="color: var(--primary); letter-spacing: 4px; font-weight: 800; text-transform: uppercase; font-size: 14px;">The Official Alumni Network</span>
        <h1 id="mainTitle">Nexus <span>Elite</span></h1>
        <p class="reveal" style="font-size: 18px; color: var(--text-gray); margin-bottom: 30px;">Where legacy meets opportunity. Connect with the legends of G H Raisoni.</p>
        <div class="reveal">
            <a href="registration.php" class="btn-neon">JOIN THE ELITE</a>
        </div>
    </div>
</section>

<section class="container-fluid">
    <div class="section-label reveal">
        <div class="label-line"></div>
        <h2 class="section-title">Hall of <span style="color: var(--primary);">Fame</span></h2>
    </div>
    
    <div class="horizontal-scroll-container">
        <?php
        $res = $conn->query("SELECT * FROM alumni ORDER BY id DESC LIMIT 8");
        while ($row = $res->fetch_assoc()) { ?>
            <div class="premium-alumni-card reveal">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['name']) ?>&background=ff3b3b&color=fff&size=200&bold=true" class="alumni-avatar">
                <h3 style="font-size: 22px; font-weight: 800; margin-bottom: 5px;"><?= $row['name'] ?></h3>
                <p style="color: var(--primary); font-weight: 700; font-size: 13px; text-transform: uppercase;"><?= $row['company'] ?></p>
                <p style="color: var(--text-gray); font-size: 14px; margin-top: 15px;">Transforming the industry through leadership and innovation.</p>
            </div>
        <?php } ?>
    </div>
</section>

<section class="container-fluid" style="background: rgba(255,255,255,0.02);">
    <div class="row">
        <div class="col-lg-8">
            <div class="section-label reveal">
                <div class="label-line"></div>
                <h2 class="section-title">Upcoming <span style="color: var(--primary);">Summits</span></h2>
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
                        <div style="position: relative; z-index: 5;">
                            <span style="font-weight: 800; font-size: 32px;"><?= date('d M', strtotime($row['event_date'])) ?></span>
                            <h3 style="font-size: 24px; font-weight: 800; margin: 10px 0;"><?= $row['title'] ?></h3>
                            <p style="opacity: 0.9; font-size: 14px;"><i class="fas fa-map-marker-alt"></i> <?= $row['location'] ?></p>
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
            <div class="feed-container">
                <div class="post-card reveal">
                    <div class="post-header">
                        <img src="https://i.pravatar.cc/100?img=12" class="post-user-img">
                        <div>
                            <h4 style="font-size: 16px; font-weight: 700; margin: 0;">Rahul Sharma</h4>
                            <small style="color: var(--text-gray);">2 hours ago</small>
                        </div>
                    </div>
                    <p style="font-size: 14px; color: #ddd;">Just joined as a Senior Dev at Google! Grateful for the Raisoni network. 🚀</p>
                </div>
                
                <div class="post-card reveal">
                    <div class="post-header">
                        <img src="https://i.pravatar.cc/100?img=5" class="post-user-img">
                        <div>
                            <h4 style="font-size: 16px; font-weight: 700; margin: 0;">Priya Verma</h4>
                            <small style="color: var(--text-gray);">Yesterday</small>
                        </div>
                    </div>
                    <p style="font-size: 14px; color: #ddd;">Anyone attending the Tech Summit next month? Let's connect!</p>
                </div>
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
                <div style="display: flex; align-items: center; gap: 20px; flex: 1;">
                    <div style="background: var(--primary); width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 12px; color: #fff;">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div>
                        <h3 style="font-weight: 800; font-size: 20px; margin: 0;"><?= $row['title'] ?></h3>
                        <p style="color: var(--text-gray); font-size: 14px; margin: 0;"><?= $row['company'] ?> • <?= $row['location'] ?: 'Remote' ?></p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 20px;">
                    <span style="font-weight: 600; font-size: 14px; opacity: 0.7;"><i class="far fa-clock"></i> Full-time</span>
                    <a href="jobs.php" class="job-btn" style="padding: 10px 25px; border-radius: 10px; background: rgba(255,255,255,0.05); text-decoration: none; font-weight: 700; color: inherit;">APPLY</a>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

<script>
    gsap.registerPlugin(ScrollTrigger);

    // Hero Animations
    gsap.from("#mainTitle", { duration: 1.5, y: 100, opacity: 0, ease: "power4.out" });
    
    // Reveal Observer for all sections
    const revealItems = document.querySelectorAll('.reveal');
    revealItems.forEach((el) => {
        gsap.to(el, {
            scrollTrigger: {
                trigger: el,
                start: "top 85%",
                toggleActions: "play none none none"
            },
            opacity: 1,
            y: 0,
            duration: 1,
            ease: "power3.out"
        });
    });

    // Premium Scroll Effect for Alumni Cards
    gsap.to(".horizontal-scroll-container", {
        scrollTrigger: {
            trigger: ".horizontal-scroll-container",
            start: "top bottom",
            end: "bottom top",
            scrub: 1
        },
        xPercent: -10,
        ease: "none"
    });
</script>

<?php include("includes/footer.php"); ?>