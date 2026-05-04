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
    }

    .hero-content {
        z-index: 10;
        text-align: center;
        padding: 0 5%;
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
        padding: 100px 8%;
        max-width: 1600px;
        margin: 0 auto;
    }

    .section-label {
        margin-bottom: 40px;
    }

    .label-line {
        width: 60px;
        height: 6px;
        background: linear-gradient(90deg, var(--primary), transparent);
        margin-bottom: 15px;
        border-radius: 10px;
    }

    .section-title {
        font-family: 'Inter', sans-serif;
        font-size: clamp(35px, 5vw, 65px);
        font-weight: 900;
        letter-spacing: -2px;
        line-height: 1;
        color: var(--text-rich);
        text-transform: uppercase;
    }

    /* --- HORIZONTAL SCROLL FIX --- */
    .horizontal-scroll-container {
        display: flex;
        gap: 25px;
        overflow-x: auto;
        padding: 10px 0 50px;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .horizontal-scroll-container::-webkit-scrollbar {
        display: none;
    }

    .premium-alumni-card {
        flex: 0 0 300px;
        background: var(--white);
        border: 1px solid var(--border-light);
        border-radius: 30px;
        padding: 40px 25px;
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
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 15px;
        border: 4px solid var(--bg-soft);
    }

    /* --- BENTO GRID --- */
    .bento-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .sexy-event-card {
        position: relative;
        border-radius: 25px;
        overflow: hidden;
        background: #000;
        transition: 0.4s ease;
    }

    /* --- LATEST FEED FIX --- */
    .timeline-container {
        position: relative;
        padding-left: 20px;
        width: 100%;
        box-sizing: border-box;
    }

    .sexy-post {
        background: var(--white) !important;
        border: 1px solid var(--border-light) !important;
        border-radius: 20px !important;
        padding: 20px !important;
        margin-bottom: 20px !important;
        transition: 0.4s cubic-bezier(0.165, 0.84, 0.44, 1) !important;
        width: 100% !important;
        box-sizing: border-box;
    }

    .sexy-post:hover {
        transform: translateX(10px) !important;
        border-color: var(--primary) !important;
        box-shadow: var(--shadow-hover) !important;
    }

    /* --- JOBS STRIPS --- */
    .job-strip {
        background: var(--white);
        border: 1px solid var(--border-light);
        padding: 25px 35px;
        border-radius: 20px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: 0.3s ease;
        box-shadow: var(--shadow-clean);
    }

    .job-strip:hover {
        border-color: var(--primary);
        transform: scale(1.01) translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .job-icon-box {
        width: 55px;
        height: 55px;
        background: #fff5f5;
        color: var(--primary);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .job-btn {
        background: var(--text-rich);
        color: #fff;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 800;
        text-decoration: none;
        font-size: 13px;
        transition: 0.3s;
    }

    .reveal {
        opacity: 0;
        transform: translateY(30px);
    }

    @media (max-width: 992px) {
        .container-fluid { padding: 60px 6%; }
        .bento-grid { grid-template-columns: 1fr !important; }
        .job-strip { flex-direction: column; text-align: center; gap: 20px; }
        .timeline-container { padding-left: 0; }
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

<section class="container-fluid" style="padding: 100px 8%; background: var(--bg-soft); overflow: hidden;">
    <div class="section-label reveal" style="margin-bottom: 60px;">
        <div class="label-line" style="width: 80px; height: 6px; background: linear-gradient(90deg, var(--primary), transparent); border-radius: 10px; margin-bottom: 15px;"></div>
        <h2 class="section-title" style="font-size: clamp(45px, 6vw, 80px); font-weight: 900; letter-spacing: -3px; text-transform: uppercase; line-height: 0.9; margin: 0;">
            Hall Of <span style="color: var(--primary);">Fame</span>
        </h2>
    </div>

    <div class="horizontal-scroll-container reveal" id="alumniTrack" style="display: flex; gap: 30px; overflow-x: auto; padding: 40px 10px 80px; scrollbar-width: none; -ms-overflow-style: none; cursor: grab; perspective: 1000px;">
        <?php
        $res = $conn->query("SELECT * FROM alumni ORDER BY id DESC LIMIT 8");
        while ($row = $res->fetch_assoc()) { ?>
            <div class="premium-alumni-card moving-card" style="flex: 0 0 320px; background: var(--white); border: 1px solid var(--border-light); border-radius: 35px; padding: 45px 30px; text-align: center; transition: transform 0.4s ease, box-shadow 0.4s ease, border-color 0.4s ease; box-shadow: var(--shadow-clean); position: relative; z-index: 1;">
                
                <div class="avatar-container" style="margin-bottom: 25px; position: relative; display: inline-block;">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['name']) ?>&background=ff4d4d&color=fff&size=200&bold=true" 
                         class="alumni-avatar" 
                         style="width: 115px; height: 115px; border-radius: 35px; object-fit: cover; border: 4px solid #fff; box-shadow: 0 10px 25px rgba(0,0,0,0.08); transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                </div>

                <h3 style="font-weight: 850; font-size: 24px; margin-bottom: 8px; color: var(--text-rich); letter-spacing: -0.5px;"><?= $row['name'] ?></h3>
                
                <div class="company-badge" style="background: #fff5f5; display: inline-block; padding: 6px 16px; border-radius: 12px; margin-bottom: 15px; transition: 0.3s;">
                    <p style="color: var(--primary); font-weight: 800; font-size: 11px; text-transform: uppercase; letter-spacing: 1.5px; margin: 0;"><?= $row['company'] ?></p>
                </div>

                <p style="color: var(--text-gray); font-size: 14px; line-height: 1.6; font-weight: 500; margin: 0;">Pioneering excellence and leadership in the global tech ecosystem.</p>
                
                <div style="position: absolute; top: 20px; right: 25px; opacity: 0.05; font-size: 40px; pointer-events: none;">
                    <i class="fas fa-award"></i>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

<style>
    /* Premium Hover States */
    .moving-card:hover {
        transform: translateY(-20px) rotateX(5deg) !important;
        border-color: var(--primary);
        box-shadow: 0 40px 70px rgba(255, 77, 77, 0.12);
        z-index: 10;
    }

    .moving-card:hover .alumni-avatar {
        transform: scale(1.1) rotate(5deg);
        border-radius: 50%; /* Smooth transition to circle on hover */
        border-color: var(--primary);
    }

    .moving-card:hover .company-badge {
        background: var(--primary);
    }

    .moving-card:hover .company-badge p {
        color: #fff;
    }

    /* Hide scrollbar */
    .horizontal-scroll-container::-webkit-scrollbar {
        display: none;
    }
</style>

<script>
    // GSAP Floating Animation for Cards
    gsap.fromTo(".moving-card", 
        { y: 0 }, 
        { 
            y: -15, 
            duration: 2, 
            repeat: -1, 
            yoyo: true, 
            ease: "sine.inOut",
            stagger: {
                amount: 1,
                from: "random"
            }
        }
    );

    // Smooth Drag-to-Scroll Functionality
    const slider = document.getElementById('alumniTrack');
    let isDown = false;
    let startX;
    let scrollLeft;

    slider.addEventListener('mousedown', (e) => {
        isDown = true;
        slider.style.cursor = 'grabbing';
        startX = e.pageX - slider.offsetLeft;
        scrollLeft = slider.scrollLeft;
    });
    slider.addEventListener('mouseleave', () => {
        isDown = false;
        slider.style.cursor = 'grab';
    });
    slider.addEventListener('mouseup', () => {
        isDown = false;
        slider.style.cursor = 'grab';
    });
    slider.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - slider.offsetLeft;
        const walk = (x - startX) * 2; 
        slider.scrollLeft = scrollLeft - walk;
    });
</script>

<section class="container-fluid" style="background: var(--white); border-top: 1px solid var(--border-light); border-bottom: 1px solid var(--border-light); padding: 120px 8%;">
    <div class="row g-lg-5"> <div class="col-lg-8" style="padding-bottom: 40px;">
            <div class="section-label reveal" style="margin-bottom: 50px;">
                <div class="label-line" style="width: 80px; height: 6px; background: linear-gradient(90deg, var(--primary), transparent); border-radius: 10px; margin-bottom: 20px;"></div>
                <h2 class="section-title" style="font-size: clamp(45px, 6vw, 75px); font-weight: 900; letter-spacing: -3px; text-transform: uppercase; line-height: 0.9; margin: 0;">Global <br><span style="color: var(--primary);">Summits</span></h2>
            </div>

            <div class="bento-grid" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 25px;">
                <?php
                $res = $conn->query("SELECT * FROM events ORDER BY event_date ASC LIMIT 3");
                $count = 0;
                while ($row = $res->fetch_assoc()) {
                    $count++;
                    $isLarge = ($count == 1) ? 'grid-column: span 2;' : '';
                ?>
                    <div class="reveal sexy-event-card" style="<?= $isLarge ?> position: relative; border-radius: 35px; overflow: hidden; min-height: <?= ($count == 1) ? '440px' : '340px' ?>; background: #000; box-shadow: var(--shadow-clean); transition: transform 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);">
                        <img src="https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=800" class="event-img" style="width:100%; height:100%; object-fit:cover; opacity:0.65; transition: 0.8s ease;">
                        <div style="position:absolute; inset:0; background: linear-gradient(to top, rgba(0,0,0,0.9) 0%, rgba(0,0,0,0.4) 50%, transparent 100%);"></div>
                        
                        <div style="position: absolute; top: 25px; left: 25px; background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.25); padding: 12px 20px; border-radius: 22px; text-align: center; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
                            <span style="display:block; font-weight:900; color:#fff; font-size:24px; line-height:1;"><?= date('d', strtotime($row['event_date'])) ?></span>
                            <span style="display:block; font-weight:700; color:var(--primary); font-size:11px; text-transform:uppercase; margin-top:2px; letter-spacing: 1px;"><?= date('M', strtotime($row['event_date'])) ?></span>
                        </div>

                        <div style="position: absolute; bottom: 35px; left: 35px; right: 35px; z-index: 5;">
                            <h3 style="font-size: <?= ($count == 1) ? '36px' : '26px' ?>; font-weight: 850; color: #fff; margin: 0; line-height: 1.1; letter-spacing: -1px;"><?= $row['title'] ?></h3>
                            <p style="color: rgba(255,255,255,0.8); font-size: 15px; margin-top: 15px; font-weight: 500; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-location-dot" style="color:var(--primary);"></i> <?= $row['location'] ?>
                            </p>
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

            <div class="timeline-container" style="position: relative; padding-top: 5px;">
                <div class="sexy-post reveal" style="background: var(--white); border: 1px solid var(--border-light); border-radius: 28px; padding: 25px; margin-bottom: 30px; transition: 0.4s; box-shadow: var(--shadow-clean); position: relative; overflow: hidden;">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 18px;">
                        <img src="https://i.pravatar.cc/100?img=12" style="width: 55px; height: 55px; border-radius: 18px; border: 2px solid var(--bg-soft); object-fit: cover;">
                        <div>
                            <h4 style="font-weight: 800; margin: 0; font-size: 17px; color: var(--text-rich); letter-spacing: -0.3px;">Rahul Sharma</h4>
                            <small style="color: var(--text-gray); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;"><i class="far fa-clock" style="margin-right: 4px;"></i> 2 hours ago</small>
                        </div>
                    </div>
                    <p style="color: #4b5563; font-size: 15px; line-height: 1.6; font-weight: 500; margin: 0;">Placed at <strong style="color:var(--text-rich); font-weight: 800; border-bottom: 2px solid rgba(255, 77, 77, 0.2);">Microsoft</strong>! Huge thanks to the network. 🚀</p>
                </div>

                <div class="sexy-post reveal" style="background: var(--white); border: 1px solid var(--border-light); border-radius: 28px; padding: 25px; margin-bottom: 30px; transition: 0.4s; box-shadow: var(--shadow-clean);">
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 18px;">
                        <img src="https://i.pravatar.cc/100?img=5" style="width: 55px; height: 55px; border-radius: 18px; border: 2px solid var(--bg-soft); object-fit: cover;">
                        <div>
                            <h4 style="font-weight: 800; margin: 0; font-size: 17px; color: var(--text-rich); letter-spacing: -0.3px;">Priya Verma</h4>
                            <small style="color: var(--text-gray); font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px;"><i class="far fa-clock" style="margin-right: 4px;"></i> Yesterday</small>
                        </div>
                    </div>
                    <p style="color: #4b5563; font-size: 15px; line-height: 1.6; font-weight: 500; margin: 0;">Great session on <strong style="color:var(--text-rich); font-weight: 800; border-bottom: 2px solid rgba(255, 77, 77, 0.2);">AI Ethics</strong> today. Good to see everyone! 👋</p>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Desktop Hover Effects */
    .sexy-event-card:hover .event-img { transform: scale(1.1); opacity: 0.8; }
    .sexy-event-card:hover { transform: translateY(-10px); }
    .sexy-post:hover { transform: translateX(12px); border-color: var(--primary) !important; box-shadow: var(--shadow-hover); }
    
    /* Responsive Fix for Gap and Divider */
    @media (max-width: 991px) {
        .col-lg-4 { 
            padding-left: 15px !important; 
            border-left: none !important; 
            margin-top: 60px;
        }
    }
</style>

<section class="container-fluid" style="padding-top: 50px;">
    <div class="section-label reveal" style="margin-bottom: 60px;">
        <div class="label-line" style="width: 100px; height: 8px; background: var(--primary); border-radius: 10px; margin-bottom: 15px;"></div>
        <h2 class="section-title" style="font-size: clamp(45px, 6vw, 85px); font-weight: 900; letter-spacing: -4px; text-transform: uppercase;">Career <span style="color: var(--primary);">Board</span></h2>
    </div>
    
    <div class="job-list">
        <?php
        $res = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC LIMIT 5");
        while ($row = $res->fetch_assoc()) { ?>
            <div class="job-strip reveal" style="background: #fff; border: 1px solid var(--border-light); padding: 30px 40px; border-radius: 25px; margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; transition: 0.4s; box-shadow: var(--shadow-clean);">
                <div style="display: flex; align-items: center; gap: 30px;">
                    <div class="job-icon-box" style="width: 65px; height: 65px; background: #fff5f5; color: var(--primary); border-radius: 20px; display: flex; align-items: center; justify-content: center; font-size: 24px;"><i class="fas fa-briefcase"></i></div>
                    <div>
                        <h3 style="font-weight: 850; font-size: 22px; margin: 0; color: var(--text-rich);"><?= $row['title'] ?></h3>
                        <p style="color: var(--text-gray); font-weight: 600; margin-top: 5px; font-size: 15px;"><?= $row['company'] ?> • <span style="color:var(--primary)"><?= $row['location'] ?: 'Remote' ?></span></p>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 40px;">
                    <span style="font-weight: 800; color: var(--text-gray); font-size: 12px; text-transform: uppercase; letter-spacing: 2px; background: var(--bg-soft); padding: 8px 15px; border-radius: 10px;">Full-Time</span>
                    <a href="jobs.php" class="job-btn" style="background: var(--text-rich); color: #fff; padding: 15px 35px; border-radius: 15px; font-weight: 800; text-decoration: none; font-size: 14px; transition: 0.3s; text-transform: uppercase;">Apply Now</a>
                </div>
            </div>
        <?php } ?>
    </div>
</section>

<script>
    gsap.registerPlugin(ScrollTrigger);
    document.querySelectorAll('.reveal').forEach(el => {
        gsap.to(el, {
            scrollTrigger: { trigger: el, start: "top 90%" },
            opacity: 1, y: 0, duration: 1, ease: "expo.out"
        });
    });
</script>

<?php include("includes/footer.php"); ?>