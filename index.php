<?php 
include("includes/header.php"); 
include("includes/db.php"); 
?>

<style>
    :root {
        --primary: #ff3b3b;
        --dark: #0f172a;
        --bg: #f8fafc;
        --text-muted: #64748b;
    }

    /* Hero Section */
    .hero {
        position: relative;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        background: #000;
        overflow: hidden;
        color: #fff;
    }
    .hero-video {
        position: absolute;
        width: 100%; height: 100%;
        object-fit: cover;
        opacity: 0.5;
    }
    .hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(to bottom, transparent, rgba(15, 23, 42, 0.9));
    }
    .hero-content { position: relative; z-index: 10; max-width: 800px; padding: 0 20px; }
    .hero-badge {
        background: rgba(255, 59, 59, 0.1);
        color: var(--primary);
        padding: 8px 20px;
        border-radius: 100px;
        font-weight: 700;
        border: 1px solid var(--primary);
        display: inline-block;
        margin-bottom: 20px;
    }
    .hero-content h1 { font-size: clamp(3rem, 8vw, 5.5rem); font-weight: 800; line-height: 1; margin-bottom: 20px; letter-spacing: -2px; }
    .hero-content h1 span { color: var(--primary); }
    .btn-main {
        background: var(--primary);
        color: #fff;
        padding: 16px 40px;
        border-radius: 50px;
        text-decoration: none;
        font-weight: 700;
        display: inline-block;
        transition: 0.3s;
    }

    /* Grid & Cards */
    .section { padding: 80px 8%; background: var(--bg); }
    .title { font-size: 2.5rem; font-weight: 800; margin-bottom: 10px; }
    .title span { color: var(--primary); }
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }

    /* Elite Card Design */
    .elite-card {
        background: #fff;
        border-radius: 24px;
        padding: 30px;
        text-decoration: none;
        color: inherit;
        display: block;
        border: 1px solid rgba(0,0,0,0.05);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .elite-card:hover {
        transform: translateY(-12px);
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.1);
        border-color: var(--primary);
    }
    .alumni-img-wrapper img {
        width: 80px; height: 80px;
        border-radius: 50%;
        margin-bottom: 20px;
        border: 3px solid #fff;
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }
    .company-tag {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        background: rgba(255, 59, 59, 0.05);
        color: var(--primary);
        border-radius: 12px;
        font-weight: 700;
        font-size: 0.85rem;
        margin-top: 15px;
    }
</style>

<div class="hero">
    <video autoplay muted loop playsinline class="hero-video">
        <source src="images/hero.mp4" type="video/mp4">
    </video>
    <div class="hero-overlay"></div>
    <div class="hero-content">
        <span class="hero-badge animate-hero">Nagpur's Elite Network</span>
        <h1 class="animate-hero">Alumni <span>Connect</span></h1>
        <p class="animate-hero" style="font-size: 1.2rem; opacity: 0.8; margin-bottom: 30px;">Fueling the next generation of global leaders and local innovators.</p>
        <div class="hero-btns animate-hero">
            <a href="registration.php" class="btn-main">Join the Community</a>
        </div>
    </div>
</div>

<div class="section">
    <h2 class="title">Notable <span>Alumni</span></h2>
    <p style="color: var(--text-muted);">The trailblazers leading the global industry.</p>
    
    <div class="grid">
        <?php
        $res = $conn->query("SELECT * FROM alumni ORDER BY id DESC LIMIT 4");
        while($row = $res->fetch_assoc()){ ?>
            <a href="profile.php?id=<?= $row['id'] ?>" class="elite-card">
                <div class="alumni-img-wrapper">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['name']) ?>&background=ff3b3b&color=fff&bold=true" alt="User">
                </div>
                <h3 style="font-weight: 700;"><?= htmlspecialchars($row['name']) ?></h3>
                <p style="color: var(--text-muted); font-size: 0.9rem;"><?= htmlspecialchars($row['course']) ?> • <?= htmlspecialchars($row['batch']) ?></p>
                <div class="company-tag">
                    <i class="fas fa-building"></i> <?= htmlspecialchars($row['company']) ?>
                </div>
            </a>
        <?php } ?>
    </div>
</div>

<div class="section" style="background: #fff; border-top: 1px solid #eee;">
    <h2 class="title">Active <span>Openings</span></h2>
    <div class="grid">
        <?php
        $res = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC LIMIT 3");
        while($row = $res->fetch_assoc()){ ?>
            <a href="job_details.php?id=<?= $row['id'] ?>" class="elite-card" style="background: var(--bg);">
                <i class="fas fa-bolt" style="color: var(--primary); font-size: 1.5rem; margin-bottom: 15px;"></i>
                <h3 style="font-weight: 700;"><?= htmlspecialchars($row['title']) ?></h3>
                <p style="font-weight: 600; color: var(--dark);"><?= htmlspecialchars($row['company']) ?></p>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 5px;"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($row['location']) ?></p>
            </a>
        <?php } ?>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
    gsap.from(".animate-hero", {
        opacity: 0, y: 30, duration: 1, stagger: 0.2, ease: "power3.out"
    });
</script>

<?php include("includes/footer.php"); ?>