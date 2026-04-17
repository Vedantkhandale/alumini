<?php 
include(__DIR__ . "/includes/header.php"); 
include(__DIR__ . "/includes/db.php"); 
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<style>
/* 🌑 DEEP DARK THEME SYNC */
:root {
    --primary: #ff3b3b;
    --bg-dark: #050505;
    --card-bg: rgba(255, 255, 255, 0.03);
    --border: rgba(255, 255, 255, 0.08);
}

.page {
    padding: 160px 8% 60px;
    background: var(--bg-dark);
    min-height: 100vh;
    color: #fff;
    position: relative;
    overflow-x: hidden;
}

.mesh-bg {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: radial-gradient(circle at 10% 20%, rgba(255, 59, 59, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 90% 80%, rgba(255, 59, 59, 0.08) 0%, transparent 50%);
    z-index: 0;
    pointer-events: none;
}

.page-header {
    text-align: center;
    margin-bottom: 80px;
    z-index: 2;
    position: relative;
}

.subpage-title {
    font-size: clamp(45px, 10vw, 90px);
    font-weight: 950;
    line-height: 0.8;
    letter-spacing: -5px;
    text-transform: uppercase;
}

.subpage-title span {
    display: block;
    color: transparent;
    -webkit-text-stroke: 1.5px rgba(255, 255, 255, 0.2);
}

/* 💎 GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 40px;
    position: relative;
    z-index: 2;
}

/* 🤵 PREMIUM ALUMNI CARD */
.card {
    background: var(--card-bg);
    padding: 50px 30px;
    border-radius: 50px;
    border: 1px solid var(--border);
    backdrop-filter: blur(20px);
    text-align: center;
    transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
    position: relative;
}

.card:hover {
    transform: translateY(-15px);
    border-color: var(--primary);
    box-shadow: 0 30px 60px rgba(255, 59, 59, 0.15);
}

/* AVATAR SQUIRCLE */
.avatar-wrapper {
    position: relative;
    width: 120px;
    height: 120px;
    margin: 0 auto 25px;
}

.avatar {
    width: 100%; height: 100%;
    border-radius: 40px;
    background: #111;
    display: flex; align-items: center; justify-content: center;
    font-size: 42px; font-weight: 800;
    border: 1px solid var(--border);
    transition: 0.5s;
    overflow: hidden;
}

.avatar img { width: 100%; height: 100%; object-fit: cover; }

.card:hover .avatar {
    border-color: var(--primary);
    transform: scale(1.05) rotate(-5deg);
}

.card h3 {
    font-size: 26px;
    font-weight: 800;
    margin-bottom: 5px;
    letter-spacing: -1px;
}

.course-tag {
    display: inline-block;
    padding: 6px 14px;
    background: rgba(255, 59, 59, 0.1);
    color: var(--primary);
    border-radius: 12px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    margin-bottom: 20px;
}

.btn-profile {
    margin-top: 25px;
    display: block;
    padding: 15px;
    background: #fff;
    color: #000;
    text-decoration: none;
    border-radius: 20px;
    font-weight: 900;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: 0.4s;
}

.card:hover .btn-profile {
    background: var(--primary);
    color: #fff;
}

/* Verified Badge */
.verified-icon {
    position: absolute;
    top: 25px; right: 25px;
    color: #10b981;
    font-size: 20px;
}

footer { margin-top: 0 !important; }
</style>

<div class="page">
    <div class="mesh-bg"></div>
    
    <div class="page-header reveal">
        <span style="color:var(--primary); font-weight:800; text-transform:uppercase; letter-spacing:5px; font-size:12px;">Global Directory</span>
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
                <p style="color:#64748b; font-weight:600;"><i class="fas fa-building"></i> <?= htmlspecialchars($row['company']) ?></p>

                <a href="profile.php?id=<?= $row['id'] ?>" class="btn-profile">
                    View Experience <i class="fas fa-arrow-right" style="margin-left:5px;"></i>
                </a>
            </div>
        <?php } ?>
    </div>
</div>

<script>
    gsap.set(".reveal", { opacity: 0, y: 50 });
    window.onload = () => {
        gsap.to(".reveal", { y: 0, opacity: 1, duration: 1, stagger: 0.15, ease: "power4.out" });
    };
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>