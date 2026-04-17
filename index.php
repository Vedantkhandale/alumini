<?php
include("includes/header.php");
include("includes/db.php");
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap" rel="stylesheet">
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

    /* --- HERO SECTION (EXACTLY SAME, NO CHANGES) --- */
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

    /* ============================================================
       ABB SHURU HOTI HAI SEXY CSS - BAKI SAB CHIZON KE LIYE
       ============================================================ */

    /* General Layout Polish */
    .container-fluid { padding: 120px 8%; position: relative; z-index: 2; }

    /* Ultra Pro Section Headers */
    .section-label { margin-bottom: 80px; position: relative; }
    .label-line { 
        width: 80px; height: 6px; 
        background: var(--primary); 
        margin-bottom: 20px; 
        border-radius: 10px; 
        box-shadow: 0 0 20px var(--primary); /* Neon Line */
    }
    .section-title { 
        font-size: clamp(35px, 6vw, 65px); 
        font-weight: 800; 
        letter-spacing: -3px; 
        line-height: 1; 
        text-transform: uppercase; 
        background: linear-gradient(180deg, #fff 0%, #aaa 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* --- HALL OF FAME (Sexy Cards) --- */
    .horizontal-scroll-container {
        display: flex;
        gap: 30px;
        padding: 40px 0;
        overflow-x: auto;
        scrollbar-width: none; /* Firefox */
    }
    .horizontal-scroll-container::-webkit-scrollbar { display: none; } /* Chrome/Safari */

    .premium-alumni-card {
        flex: 0 0 320px; /* Fixed width for horizontal scroll */
        background: linear-gradient(145deg, rgba(255,255,255,0.05) 0%, rgba(0,0,0,0) 100%);
        border: 1px solid var(--border-glass);
        backdrop-filter: blur(15px);
        border-radius: 40px;
        padding: 50px 35px;
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
    }

    .premium-alumni-card:hover { 
        border-color: var(--primary); 
        transform: translateY(-20px) rotateX(5deg);
        box-shadow: 0 20px 50px rgba(255, 59, 59, 0.2);
    }

    .alumni-avatar { 
        width: 110px; height: 110px; 
        border-radius: 35px; 
        object-fit: cover; 
        margin-bottom: 30px; 
        border: 3px solid var(--border-glass);
        transition: 0.3s;
    }
    .premium-alumni-card:hover .alumni-avatar { border-color: var(--primary); transform: scale(1.1); }

    /* --- BENTO EVENTS GRID (Attractive & Sexy) --- */
    .bento-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
    }
    .bento-item {
        min-height: 400px;
        border-radius: 45px;
        padding: 40px;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        border: 1px solid var(--border-glass);
        transition: 0.6s cubic-bezier(0.165, 0.84, 0.44, 1);
    }
    .bento-item.large { grid-column: span 2; }
    
    .bento-item:hover { transform: scale(0.98); border-color: rgba(255,255,255,0.2); }

    .event-overlay { 
        position: absolute; inset: 0; 
        background: linear-gradient(to top, rgba(0,0,0,0.95) 10%, rgba(0,0,0,0.3) 50%, transparent 100%); 
        z-index: 2; 
    }
    .event-img { 
        position: absolute; inset: 0; width: 100%; height: 100%; 
        object-fit: cover; opacity: 0.4; 
        transition: 0.8s cubic-bezier(0.165, 0.84, 0.44, 1); 
        z-index: 1;
    }
    .bento-item:hover .event-img { transform: scale(1.1); opacity: 0.6; }

    /* Sexy Date Badge */
    .event-date-badge {
        position: absolute; top: 30px; right: 30px;
        background: var(--primary);
        color: #fff;
        padding: 10px 20px;
        border-radius: 50px;
        font-weight: 800;
        font-size: 14px;
        text-transform: uppercase;
        z-index: 5;
        box-shadow: 0 5px 15px var(--primary-glow);
    }

    /* --- LATEST POSTS FEED (Clean & Glassy) --- */
    .feed-container { display: flex; flex-direction: column; gap: 20px; }
    .post-card {
        background: rgba(255,255,255,0.02);
        border: 1px solid var(--border-glass);
        backdrop-filter: blur(10px);
        border-radius: 30px;
        padding: 30px;
        transition: 0.3s;
    }
    .post-card:hover { background: rgba(255,255,255,0.05); border-color: rgba(255,255,255,0.15); }
    .post-header { display: flex; align-items: center; gap: 15px; margin-bottom: 20px; }
    .post-user-img { width: 55px; height: 55px; border-radius: 18px; border: 2px solid var(--border-glass); }

    /* --- CAREER BOARD (Premium Job Strips) --- */
    .job-list { display: flex; flex-direction: column; gap: 15px; }
    .job-strip {
        background: var(--card-glass);
        border: 1px solid var(--border-glass);
        padding: 25px 40px;
        border-radius: 25px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: 0.4s cubic-bezier(0.075, 0.82, 0.165, 1);
        position: relative;
        overflow: hidden;
    }
    
    /* Hover Effect: Background turns white, text turns black */
    .job-strip:hover { 
        background: #fff; 
        transform: translateY(-5px) scale(1.01);
        box-shadow: 0 10px 30px rgba(255,255,255,0.1);
    }
    .job-strip:hover * { color: #000 !important; }
    
    /* Sexy Job Icon */
    .job-icon-box {
        background: rgba(255,59,59,0.1); 
        width: 60px; height: 60px; 
        display: flex; align-items: center; justify-content: center; 
        border-radius: 20px; color: var(--primary); 
        font-size: 22px;
        transition: 0.3s;
    }
    .job-strip:hover .job-icon-box { background: var(--primary); color: #fff !important; }

    .job-btn {
        padding: 12px 30px; 
        border-radius: 14px; 
        background: rgba(255,255,255,0.05); 
        text-decoration: none; 
        font-weight: 800; 
        color: #fff;
        font-size: 14px;
        transition: 0.3s;
        border: 1px solid var(--border-glass);
    }
    .job-strip:hover .job-btn { background: #000; color: #fff !important; border-color: #000; }

    /* UTILS & RESPONSIVE */
    .reveal { opacity: 0; transform: translateY(40px); }
    
    /* Background Sexy Glows */
    .sexy-bg-glow {
        position: absolute;
        width: 40vw; height: 40vw;
        background: radial-gradient(circle, rgba(255,59,59,0.07) 0%, rgba(5,5,5,0) 70%);
        border-radius: 50%;
        z-index: 1;
        pointer-events: none;
    }

    @media (max-width: 992px) {
        .bento-grid { grid-template-columns: 1fr; }
        .bento-item.large { grid-column: span 1; }
        .job-strip { flex-direction: column; text-align: center; gap: 20px; padding: 30px; }
        .container-fluid { padding: 80px 5%; }
    }
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
    <div class="sexy-bg-glow" style="top: 10%; left: -10%;"></div>
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
                <h3 style="font-size: 24px; font-weight: 800; margin-bottom: 8px; letter-spacing: -0.5px;"><?= $row['name'] ?></h3>
                <p style="color: var(--primary); font-weight: 700; font-size: 13px; text-transform: uppercase; letter-spacing: 1px;"><?= $row['company'] ?></p>
                <p style="color: var(--text-gray); font-size: 14px; margin-top: 20px; line-height: 1.6; font-weight: 500;">Transforming the industry through visionary leadership and innovation.</p>
            </div>
        <?php } ?>
    </div>
</section>

<section class="container-fluid" style="background: rgba(255,255,255,0.01);">
    <div class="sexy-bg-glow" style="bottom: 10%; right: -10%;"></div>
    <div class="row g-5">
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
                        <div class="event-date-badge"><?= date('d M', strtotime($row['event_date'])) ?></div>
                        <img src="https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=800" class="event-img">
                        <div class="event-overlay"></div>
                        <div style="position: relative; z-index: 5;">
                            <h3 style="font-size: clamp(22px, 3vw, 32px); font-weight: 800; margin: 0 0 15px; line-height: 1.1;"><?= $row['title'] ?></h3>
                            <p style="opacity: 0.8; font-size: 14px; font-weight: 600; color: #ddd; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-map-marker-alt" style="color: var(--primary);"></i> <?= $row['location'] ?>
                            </p>
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
                            <h4 style="font-size: 17px; font-weight: 800; margin: 0; letter-spacing: -0.3px;">Rahul Sharma</h4>
                            <small style="color: var(--primary); font-weight: 700;">2 hours ago</small>
                        </div>
                    </div>
                    <p style="font-size: 15px; color: var(--text-gray); line-height: 1.6; font-weight: 500;">Just joined as a Senior Dev at Google! Grateful for the Raisoni network. 🚀</p>
                </div>
                
                <div class="post-card reveal">
                    <div class="post-header">
                        <img src="https://i.pravatar.cc/100?img=5" class="post-user-img">
                        <div>
                            <h4 style="font-size: 17px; font-weight: 800; margin: 0; letter-spacing: -0.3px;">Priya Verma</h4>
                            <small style="color: var(--primary); font-weight: 700;">Yesterday</small>
                        </div>
                    </div>
                    <p style="font-size: 15px; color: var(--text-gray); line-height: 1.6; font-weight: 500;">Anyone attending the Tech Summit next month? Let's connect!</p>
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
                <div style="display: flex; align-items: center; gap: 30px; flex: 1;">
                    <div class="job-icon-box">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <div>
                        <h3 style="font-weight: 800; font-size: 24px; margin: 0; letter-spacing: -0.5px;"><?= $row['title'] ?></h3>
                        <p style="color: var(--text-gray); font-size: 16px; margin: 5px 0 0; font-weight: 600;">
                            <?= $row['company'] ?> <span style="opacity: 0.3; margin: 0 10px;">|</span> <?= $row['location'] ?: 'Remote' ?>
                        </p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 40px;">
                    <span style="font-weight: 700; font-size: 14px; color: #aaa; text-transform: uppercase; letter-spacing: 1px;">
                        <i class="far fa-clock" style="margin-right: 8px; color: var(--primary);"></i> Full-time
                    </span>
                    <a href="jobs.php" class="job-btn">APPLY NOW</a>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

<script>
    gsap.registerPlugin(ScrollTrigger);

    // Hero Animations (Same as before)
    gsap.from("#mainTitle", { duration: 1.5, y: 100, opacity: 0, ease: "power4.out" });
    
    // Sexy Smooth Reveal for all items
    const revealItems = document.querySelectorAll('.reveal');
    revealItems.forEach((el) => {
        gsap.to(el, {
            scrollTrigger: {
                trigger: el,
                start: "top 90%",
                toggleActions: "play none none none"
            },
            opacity: 1,
            y: 0,
            duration: 1.2,
            ease: "power4.out" // Smoother ease
        });
    });

    // Premium Momentum Scroll for Alumni cards
    gsap.to(".horizontal-scroll-container", {
        scrollTrigger: {
            trigger: ".horizontal-scroll-container",
            start: "top bottom",
            end: "bottom top",
            scrub: 1.5
        },
        xPercent: -10,
        ease: "none"
    });
</script>

<?php include("includes/footer.php"); ?>