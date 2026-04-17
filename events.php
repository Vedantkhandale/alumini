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

/* Fix: Padding-bottom ko 0 kiya aur min-height ko auto taaki footer chipak jaye */
.page {
    padding: 160px 8% 0px; /* Bottom padding 0 */
    background: var(--bg-dark);
    min-height: 100vh;
    color: #fff;
    overflow-x: hidden;
    position: relative;
    display: flex;
    flex-direction: column;
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

/* 💎 GRID - Margin bottom nikal diya */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
    gap: 40px;
    position: relative;
    z-index: 2;
    margin-bottom: 60px; /* Isse control kar sakte ho cards aur footer ka gap */
}

.event-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 50px;
    padding: 20px;
    backdrop-filter: blur(20px);
    transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
    visibility: visible;
}

.event-card:hover {
    transform: translateY(-15px) scale(1.02);
    border-color: var(--primary);
    box-shadow: 0 30px 60px rgba(255, 59, 59, 0.15);
}

.img-wrap {
    width: 100%;
    height: 320px;
    border-radius: 40px;
    overflow: hidden;
    position: relative;
    background: #111;
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

.neon-date {
    position: absolute;
    bottom: 25px;
    right: 25px;
    background: var(--primary);
    color: #fff;
    padding: 12px 22px;
    border-radius: 20px;
    font-weight: 900;
    box-shadow: 0 10px 30px rgba(255, 59, 59, 0.4);
    z-index: 3;
}

.content-area {
    padding: 25px 15px 10px;
}

.event-card h3 {
    font-size: 28px;
    font-weight: 800;
    margin-bottom: 12px;
    letter-spacing: -1px;
    color: #fff;
}

.meta-info {
    display: flex; gap: 15px;
    color: #94a3b8; font-size: 13px; font-weight: 600; margin-bottom: 15px;
}

.meta-info i { color: var(--primary); }

.btn-glow {
    margin-top: 20px;
    display: block;
    padding: 18px;
    background: #fff;
    color: #000;
    text-decoration: none;
    border-radius: 25px;
    font-weight: 900;
    font-size: 13px;
    text-transform: uppercase;
    letter-spacing: 2px;
    transition: 0.4s;
    text-align: center;
}

.btn-glow:hover {
    background: var(--primary);
    color: #fff;
    box-shadow: 0 0 30px rgba(255, 59, 59, 0.5);
}

@media(max-width:768px){
    .grid { grid-template-columns: 1fr; }
    .page-title { font-size: 55px; }
    .page { padding: 120px 5% 0px; }
}

/* Footer ko exact niche chipkane ke liye */
footer {
    margin-top: 0 !important;
}
</style>

<div class="page">
    <div class="mesh-bg"></div>
    
    <div class="page-header">
        <span class="hero-tag">Elite Gatherings</span>
        <h1 class="page-title">
            <span>Summit</span>
            Nexus 2026
        </h1>
    </div>

    <div class="grid">
        <?php
        $res = $conn->query("SELECT * FROM events ORDER BY event_date ASC");

        if($res && $res->num_rows > 0){
            while($row = $res->fetch_assoc()){ 
                $eDate = strtotime($row['event_date']);
                $time = isset($row['event_time']) ? date('h:i A', strtotime($row['event_time'])) : 'TBA';
                $loc = !empty($row['location']) ? $row['location'] : 'Nexus Hall';
                $imgUrl = !empty($row['image']) ? 'uploads/events/'.$row['image'] : 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?auto=format&fit=crop&w=800&q=80';
            ?>
                <div class="event-card reveal">
                    <div class="img-wrap">
                        <img src="<?= $imgUrl ?>" alt="Event">
                        <div class="neon-date"><?= date('d M', $eDate) ?></div>
                    </div>
                    <div class="content-area">
                        <div class="meta-info">
                            <span><i class="fas fa-clock"></i> <?= $time ?></span>
                            <span><i class="fas fa-location-arrow"></i> <?= htmlspecialchars($loc) ?></span>
                        </div>
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        <a href="event_details.php?id=<?= $row['id'] ?>" class="btn-glow">Join Experience</a>
                    </div>
                </div>
            <?php }
        } else {
            echo "<div style='grid-column: 1/-1; text-align:center; padding:100px; z-index:10; position:relative;'>
                    <h2 style='color:#333; letter-spacing:2px;'>NO ACTIVE SUMMITS</h2>
                  </div>";
        }
        ?>
    </div>
</div>

<script>
    gsap.set(".reveal", { opacity: 0, y: 50 });
    window.onload = () => {
        gsap.to(".reveal", {
            y: 0, opacity: 1, duration: 1, stagger: 0.15, ease: "power4.out"
        });
        gsap.from(".page-title", {
            scale: 0.9, opacity: 0, duration: 1.2, ease: "expo.out"
        });
    };
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>