<?php 
include(__DIR__ . "/includes/header.php"); 
include(__DIR__ . "/includes/db.php"); 
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<style>
/* ⚪ PREMIUM LIGHT & RED DIRECTORY THEME */
:root {
    --primary: #ff3b3b;
    --primary-glow: rgba(255, 59, 59, 0.15);
    --bg-light: #ffffff; 
    --card-bg: #ffffff;
    --border: #f0f0f0; 
    --text-main: #111111;
    --text-dim: #666666;
}

html, body {
    background-color: var(--bg-light);
    margin: 0; padding: 0;
    font-family: 'Plus Jakarta Sans', sans-serif;
}

.page {
    padding: 160px 8% 80px; 
    background: var(--bg-light);
    min-height: 100vh;
    position: relative;
    overflow-x: hidden;
    display: flex;
    flex-direction: column;
}

/* Texture Pattern */
.mesh-bg {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: radial-gradient(circle at 10% 10%, rgba(255, 59, 59, 0.03) 0%, transparent 40%),
                radial-gradient(circle at 90% 90%, rgba(255, 59, 59, 0.02) 0%, transparent 40%);
    z-index: 0;
    pointer-events: none;
}

.page-header { text-align: center; margin-bottom: 80px; z-index: 2; }

.subpage-title {
    font-size: clamp(45px, 8vw, 90px);
    font-weight: 800;
    line-height: 0.9;
    letter-spacing: -3px;
    text-transform: uppercase;
    color: var(--text-main);
}

.subpage-title span { display: block; color: var(--primary); }

/* 💎 GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr); 
    gap: 35px;
    position: relative;
    z-index: 5; /* Z-index high rakha hai visibility ke liye */
}

/* 🤵 ALUMNI CARD - Bulletproof Visibility */
.card {
    background: var(--card-bg);
    padding: 45px 30px;
    border-radius: 40px;
    border: 1px solid var(--border);
    text-align: center;
    transition: transform 0.5s cubic-bezier(0.19, 1, 0.22, 1), box-shadow 0.5s ease;
    box-shadow: 0 15px 40px rgba(0,0,0,0.03);
    opacity: 1 !important; /* Default visible if JS fails */
    visibility: visible !important;
}

.card:hover {
    transform: translateY(-15px);
    border-color: var(--primary);
    box-shadow: 0 30px 60px -12px rgba(255, 59, 59, 0.15);
}

.avatar-wrapper { width: 120px; height: 120px; margin: 0 auto 25px; }

.avatar {
    width: 100%; height: 100%;
    border-radius: 40px;
    background: #f9f9f9;
    display: flex; align-items: center; justify-content: center;
    font-size: 42px; font-weight: 900;
    color: var(--primary);
    border: 1px solid var(--border);
    transition: 0.6s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    overflow: hidden;
}

.avatar img { width: 100%; height: 100%; object-fit: cover; }

.card:hover .avatar { transform: scale(1.1) rotate(-5deg); border-color: var(--primary); }

.course-tag {
    display: inline-block;
    padding: 8px 16px;
    background: rgba(255, 59, 59, 0.05);
    color: var(--primary);
    border-radius: 14px;
    font-size: 11px; font-weight: 800;
    text-transform: uppercase; margin-bottom: 20px;
}

.card h3 { font-size: 26px; font-weight: 800; margin-bottom: 10px; color: var(--text-main); }

.company-info { color: var(--text-dim); font-weight: 700; font-size: 13px; text-transform: uppercase; }

.btn-profile {
    margin-top: 30px;
    display: block;
    padding: 18px;
    background: #111;
    color: #fff;
    text-decoration: none;
    border-radius: 20px;
    font-weight: 800;
    font-size: 12px;
    text-transform: uppercase;
    transition: 0.4s;
}

.btn-profile:hover { background: var(--primary); box-shadow: 0 10px 25px var(--primary-glow); }

.verified-icon { position: absolute; top: 25px; right: 30px; color: var(--primary); font-size: 20px; }

footer { margin-top: auto !important; background: #fff !important; border-top: 1px solid var(--border); }

@media(max-width:1100px){ .grid { grid-template-columns: repeat(2, 1fr); } }
@media(max-width:768px){ .grid { grid-template-columns: 1fr; } .subpage-title { font-size: 50px; } }
</style>

<div class="page">
    <div class="mesh-bg"></div>
    
    <div class="page-header">
        <span class="reveal" style="color:var(--primary); font-weight:800; text-transform:uppercase; letter-spacing:5px; font-size:11px; display:block; margin-bottom:15px;">Global Directory</span>
        <h1 class="subpage-title reveal"><span>Alumni</span> Network</h1>
    </div>

    <div class="grid">
        <?php
        $res = $conn->query("SELECT * FROM alumni ORDER BY id DESC");
        if($res && $res->num_rows > 0) {
            while($row = $res->fetch_assoc()){
                $initial = strtoupper(substr($row['name'] ?? 'A', 0, 1));
                $imgUrl = !empty($row['image']) ? "uploads/profiles/".$row['image'] : ""; 
            ?>
                <div class="card reveal">
                    <div class="verified-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="avatar-wrapper">
                        <div class="avatar">
                            <?php if($imgUrl && file_exists(__DIR__ . "/" . $imgUrl)): ?>
                                <img src="<?= $imgUrl ?>" alt="Profile">
                            <?php else: ?>
                                <?= $initial ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <span class="course-tag"><?= htmlspecialchars($row['course'] ?? 'Alumni') ?> Member</span>
                    <h3><?= htmlspecialchars($row['name']) ?></h3>
                    <p class="company-info"><i class="fas fa-building"></i> <?= htmlspecialchars($row['company'] ?? 'Pioneering Future') ?></p>
                    <a href="profile.php?id=<?= $row['id'] ?>" class="btn-profile">View Experience</a>
                </div>
            <?php } 
        } else {
            echo "<div style='grid-column:1/-1; text-align:center;'><h3>No Alumni members found.</h3></div>";
        }
        ?>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Force state set then animate
        gsap.from(".reveal", { 
            y: 50, 
            opacity: 0, 
            duration: 1, 
            stagger: 0.1, 
            ease: "power4.out",
            clearProps: "all" // Animation ke baad visibility issues clear karega
        });
    });
</script>
<?php include(__DIR__ . "/includes/footer.php"); ?>

