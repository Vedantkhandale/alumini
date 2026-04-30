<?php 
include(__DIR__ . "/includes/header.php"); 
include(__DIR__ . "/includes/db.php"); 
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<style>
/* 🔴 LUXURY RED-BLACK-WHITE THEME */
:root {
    --primary: #ff3b3b;
    --primary-glow: rgba(255, 59, 59, 0.3);
    --bg-dark: #0a0a0a; /* Rich Charcoal */
    --card-bg: rgba(255, 255, 255, 0.04);
    --border: rgba(255, 255, 255, 0.12);
    --text-main: #ffffff;
    --text-dim: #a1a1aa;
}

/* 🌑 FULL PAGE SETUP - NO WHITE GAP */
html, body { background-color: var(--bg-dark); margin: 0; padding: 0; }

.page {
    padding: 140px 8% 40px; 
    background: var(--bg-dark);
    min-height: auto; 
    color: var(--text-main);
    position: relative;
    overflow-x: hidden;
    display: flex;
    flex-direction: column;
}

.mesh-bg {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: radial-gradient(circle at 15% 15%, rgba(255, 59, 59, 0.07) 0%, transparent 40%),
                radial-gradient(circle at 85% 85%, rgba(255, 59, 59, 0.05) 0%, transparent 40%);
    z-index: 0;
    pointer-events: none;
}

.page-header {
    text-align: center;
    margin-bottom: 70px;
    z-index: 2;
}

.subpage-title {
    font-size: clamp(40px, 8vw, 85px);
    font-weight: 950;
    line-height: 0.9;
    letter-spacing: -3px;
    text-transform: uppercase;
}

.subpage-title span {
    display: block;
    color: transparent;
    -webkit-text-stroke: 1.2px rgba(255, 255, 255, 0.25);
}

/* 💎 3-COLUMN GRID SYNC */
.grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr); 
    gap: 30px;
    position: relative;
    z-index: 2;
    margin-bottom: 40px;
}

/* 🤵 PREMIUM ALUMNI CARD */
.card {
    background: var(--card-bg);
    padding: 40px 25px;
    border-radius: 40px;
    border: 1px solid var(--border);
    backdrop-filter: blur(25px);
    -webkit-backdrop-filter: blur(25px);
    text-align: center;
    transition: all 0.5s cubic-bezier(0.19, 1, 0.22, 1);
    position: relative;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-12px);
    border-color: var(--primary);
    background: rgba(255, 255, 255, 0.07);
    box-shadow: 0 30px 60px -15px rgba(0,0,0,0.6), 0 0 15px var(--primary-glow);
}

/* AVATAR SQUIRCLE - MODERNIZED */
.avatar-wrapper {
    position: relative;
    width: 110px;
    height: 110px;
    margin: 0 auto 25px;
}

.avatar {
    width: 100%; height: 100%;
    border-radius: 35px;
    background: #111;
    display: flex; align-items: center; justify-content: center;
    font-size: 38px; font-weight: 900;
    color: #fff;
    border: 1px solid var(--border);
    transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    overflow: hidden;
}

.avatar img { 
    width: 100%; height: 100%; object-fit: cover; 
    filter: grayscale(100%);
    transition: 0.5s;
}

.card:hover .avatar {
    border-color: var(--primary);
    transform: scale(1.08) rotate(-3deg);
}

.card:hover .avatar img {
    filter: grayscale(0%);
}

.card h3 {
    font-size: 24px;
    font-weight: 800;
    margin-bottom: 8px;
    letter-spacing: -0.5px;
    color: #fff;
}

.course-tag {
    display: inline-block;
    padding: 6px 14px;
    background: rgba(255, 59, 59, 0.1);
    color: var(--primary);
    border-radius: 12px;
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: 20px;
    border: 1px solid rgba(255, 59, 59, 0.2);
}

.company-info {
    color: var(--text-dim);
    font-weight: 700;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.company-info i { color: var(--primary); margin-right: 5px; }

/* 🔘 PROFILE BUTTON - CLEAN WHITE CONTRAST */
.btn-profile {
    margin-top: 25px;
    display: block;
    padding: 15px;
    background: #fff;
    color: #000;
    text-decoration: none;
    border-radius: 18px;
    font-weight: 800;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    transition: 0.4s;
}

.btn-profile:hover {
    background: var(--primary);
    color: #fff;
    box-shadow: 0 10px 25px var(--primary-glow);
}

/* Verified Badge - Red Refinement */
.verified-icon {
    position: absolute;
    top: 20px; right: 25px;
    color: var(--primary);
    font-size: 18px;
    opacity: 0.8;
}

/* 🛑 FOOTER FIX */
footer { margin-top: 0 !important; border-top: 1px solid var(--border); }

@media(max-width:1100px){
    .grid { grid-template-columns: repeat(2, 1fr); }
}

@media(max-width:768px){
    .grid { grid-template-columns: 1fr; }
    .subpage-title { font-size: 50px; }
    .page { padding: 120px 6% 30px; }
}
</style>

<div class="page">
    <div class="mesh-bg"></div>
    
    <div class="page-header reveal">
        <span style="color:var(--primary); font-weight:800; text-transform:uppercase; letter-spacing:5px; font-size:11px; display:block; margin-bottom:15px;">Global Directory</span>
        <h1 class="subpage-title">
            <span>Alumni</span> Network
        </h1>
    </div>

    <div class="grid">
        <?php
        $res = $conn->query("SELECT * FROM alumni ORDER BY id DESC");
        while($row = $res->fetch_assoc()){
            $initial = strtoupper(substr($row['name'], 0, 1));
            $imgUrl = !empty($row['image']) ? "uploads/profiles/".$row['image'] : ""; 
        ?>
            <div class="card reveal">
                <div class="verified-icon"><i class="fas fa-check-circle"></i></div>
                
                <div class="avatar-wrapper">
                    <div class="avatar">
                        <?php if($imgUrl): ?>
                            <img src="<?= $imgUrl ?>" alt="Profile">
                        <?php else: ?>
                            <?= $initial ?>
                        <?php endif; ?>
                    </div>
                </div>

                <span class="course-tag"><?= htmlspecialchars($row['course']) ?> Member</span>
                <h3><?= htmlspecialchars($row['name']) ?></h3>
                <p class="company-info"><i class="fas fa-building"></i> <?= htmlspecialchars($row['company']) ?></p>

                <a href="profile.php?id=<?= $row['id'] ?>" class="btn-profile">
                    View Experience <i class="fas fa-arrow-right" style="margin-left:5px;"></i>
                </a>
            </div>
        <?php } ?>
    </div>
</div>

<script>
    gsap.set(".reveal", { opacity: 0, y: 30 });
    window.onload = () => {
        gsap.to(".reveal", { y: 0, opacity: 1, duration: 1.2, stagger: 0.1, ease: "power4.out" });
    };
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>