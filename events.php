<?php 
include(__DIR__ . "/includes/header.php"); 
include(__DIR__ . "/includes/db.php"); 
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<style>
/* 🌑 DEEP DARK THEME */
:root {
    --primary: #ff3b3b;
    --bg-dark: #050505;
    --card-bg: rgba(255, 255, 255, 0.03);
    --border: rgba(255, 255, 255, 0.08);
}

.page {
    padding: 160px 8% 100px;
    background: var(--bg-dark);
    min-height: 100vh;
    color: #fff;
    overflow-x: hidden;
}

/* 🎭 MESH GRADIENT BACKGROUND */
.mesh-bg {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: radial-gradient(circle at 10% 20%, rgba(255, 59, 59, 0.05) 0%, transparent 50%),
                radial-gradient(circle at 90% 80%, rgba(255, 59, 59, 0.05) 0%, transparent 50%);
    z-index: 0;
    pointer-events: none;
}

/* 🔥 ULTRA TITLE */
.page-header {
    text-align: center;
    margin-bottom: 100px;
    z-index: 1;
    position: relative;
}

.hero-tag {
    text-transform: uppercase;
    letter-spacing: 5px;
    font-size: 12px;
    color: var(--primary);
    font-weight: 800;
    margin-bottom: 15px;
    display: block;
}

.page-title {
    font-size: clamp(45px, 10vw, 100px);
    font-weight: 950;
    line-height: 0.8;
    letter-spacing: -6px;
    text-transform: uppercase;
}

.page-title span {
    display: block;
    color: transparent;
    -webkit-text-stroke: 1.5px rgba(255, 255, 255, 0.2);
}

/* 💎 GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 50px;
    position: relative;
    z-index: 1;
}

/* 🚀 THE "ELITE" EVENT CARD */
.event-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 50px;
    padding: 20px;
    backdrop-filter: blur(20px);
    transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
}

.event-card:hover {
    transform: translateY(-20px) scale(1.02);
    border-color: var(--primary);
    box-shadow: 0 30px 60px rgba(255, 59, 59, 0.15);
}

/* IMAGE CONTAINER */
.img-wrap {
    width: 100%;
    height: 320px;
    border-radius: 40px;
    overflow: hidden;
    position: relative;
}

.img-wrap img {
    width: 100%; height: 100%;
    object-fit: cover;
    filter: grayscale(40%);
    transition: 0.8s;
}

.event-card:hover .img-wrap img {
    filter: grayscale(0%);
    transform: scale(1.1);
}

/* 📅 NEON DATE */
.neon-date {
    position: absolute;
    bottom: 25px;
    right: 25px;
    background: var(--primary);
    color: #fff;
    padding: 15px 25px;
    border-radius: 25px;
    font-weight: 900;
    box-shadow: 0 10px 30px rgba(255, 59, 59, 0.4);
}

/* CONTENT */
.content-area {
    padding: 30px 15px 15px;
}

.event-card h3 {
    font-size: 32px;
    font-weight: 800;
    margin-bottom: 15px;
    letter-spacing: -1px;
}

.meta-info {
    display: flex;
    gap: 20px;
    color: #888;
    font-size: 14px;
    font-weight: 600;
}

.meta-info i { color: var(--primary); }

/* 🔘 GLOW BUTTON */
.btn-glow {
    margin-top: 35px;
    display: inline-block;
    padding: 20px 45px;
    background: #fff;
    color: #000;
    text-decoration: none;
    border-radius: 50px;
    font-weight: 900;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 2px;
    transition: 0.4s;
    width: 100%;
    text-align: center;
}

.btn-glow:hover {
    background: var(--primary);
    color: #fff;
    box-shadow: 0 0 40px rgba(255, 59, 59, 0.6);
}

/* MOBILE */
@media(max-width:768px){
    .grid { grid-template-columns: 1fr; }
    .page-title { font-size: 60px; }
}
</style>

<div class="page">
    <div class="mesh-bg"></div>
    
    <div class="page-header">
        <span class="hero-tag">Exclusive Gatherings</span>
        <h1 class="page-title">
            <span>Summit</span>
            Nexus 2024
        </h1>
    </div>

    <div class="grid">
        <?php
        $res = $conn->query("SELECT * FROM events ORDER BY event_date ASC");

        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){ 
                $eDate = strtotime($row['event_date']);
                $time = isset($row['event_time']) ? date('h:i A', strtotime($row['event_time'])) : 'TBA';
                $loc = !empty($row['location']) ? $row['location'] : 'Nexus Hall';
                $imgUrl = !empty($row['image']) ? 'uploads/events/'.$row['image'] : 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?auto=format&fit=crop&w=800&q=80';
            ?>
                <div class="event-card reveal">
                    <div class="img-wrap">
                        <img src="<?= $imgUrl ?>" alt="Event">
                        <div class="neon-date">
                            <?= date('d M', $eDate) ?>
                        </div>
                    </div>

                    <div class="content-area">
                        <div class="meta-info">
                            <span><i class="fas fa-clock"></i> <?= $time ?></span>
                            <span><i class="fas fa-location-arrow"></i> <?= $loc ?></span>
                        </div>
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        
                        <a href="event_details.php?id=<?= $row['id'] ?>" class="btn-glow">
                            Join Experience
                        </a>
                    </div>
                </div>
            <?php }
        } else {
            echo "<div style='grid-column: 1/-1; text-align:center; padding:100px; color:#444; font-size: 24px; font-weight:800;'>COMING SOON...</div>";
        }
        ?>
    </div>
</div>

<script>
    // GSAP Entry Animation
    gsap.from(".reveal", {
        y: 100,
        opacity: 0,
        duration: 1.2,
        stagger: 0.2,
        ease: "power4.out"
    });

    gsap.from(".page-title", {
        scale: 0.8,
        opacity: 0,
        duration: 1.5,
        ease: "expo.out"
    });
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>