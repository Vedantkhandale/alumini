<?php 
include(__DIR__ . "/includes/header.php"); 
include(__DIR__ . "/includes/db.php"); 
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

<style>
/* 🔴 PREMIUM RED-BLACK-WHITE THEME */
:root {
    --primary: #ff3b3b;
    --primary-glow: rgba(255, 59, 59, 0.3);
    --bg-dark: #0a0a0a; /* Rich Charcoal Black */
    --card-bg: rgba(255, 255, 255, 0.04);
    --border: rgba(255, 255, 255, 0.12); /* Increased visibility */
    --text-main: #ffffff;
    --text-dim: #a1a1aa; /* Soft Gray */
}

/* Fix: Extra spacing and White Gap removal */
html, body {
    background-color: var(--bg-dark);
    margin: 0;
    padding: 0;
}

.page {
    padding: 140px 8% 40px; /* Reduced bottom padding */
    background: var(--bg-dark);
    min-height: auto; /* Changed from 100vh to avoid stretching */
    color: var(--text-main);
    overflow-x: hidden;
    position: relative;
    display: flex;
    flex-direction: column;
}

.mesh-bg {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: radial-gradient(circle at 10% 10%, rgba(255, 59, 59, 0.07) 0%, transparent 40%),
                radial-gradient(circle at 90% 90%, rgba(255, 59, 59, 0.05) 0%, transparent 40%);
    z-index: 0;
    pointer-events: none;
}

.page-header {
    text-align: center;
    margin-bottom: 70px;
    z-index: 2;
}

.hero-tag {
    text-transform: uppercase;
    letter-spacing: 4px;
    font-size: 12px;
    color: var(--primary);
    font-weight: 800;
    margin-bottom: 15px;
    display: block;
}

.page-title {
    font-size: clamp(40px, 8vw, 85px);
    font-weight: 950;
    line-height: 0.9;
    letter-spacing: -3px;
    text-transform: uppercase;
}

.page-title span {
    display: block;
    color: transparent;
    -webkit-text-stroke: 1.2px rgba(255, 255, 255, 0.3); /* Better visibility */
}

/* 💎 3-COLUMN PREMIUM GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr); 
    gap: 30px;
    position: relative;
    z-index: 2;
    margin-bottom: 40px; /* Controlled gap before footer */
}

.event-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 30px;
    padding: 18px;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    transition: all 0.5s cubic-bezier(0.19, 1, 0.22, 1);
}

.event-card:hover {
    transform: translateY(-12px);
    border-color: var(--primary);
    background: rgba(255, 255, 255, 0.07);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7), 
                0 0 15px var(--primary-glow);
}

.img-wrap {
    width: 100%;
    height: 250px;
    border-radius: 22px;
    overflow: hidden;
    position: relative;
    background: #111;
}

.img-wrap img {
    width: 100%; height: 100%;
    object-fit: cover;
    filter: brightness(0.8);
    transition: 0.8s ease;
}

.event-card:hover .img-wrap img {
    filter: brightness(1);
    transform: scale(1.05);
}

.neon-date {
    position: absolute;
    top: 15px;
    right: 15px;
    background: var(--primary);
    color: #fff;
    padding: 10px 18px;
    border-radius: 15px;
    font-weight: 900;
    font-size: 13px;
    box-shadow: 0 8px 20px rgba(255, 59, 59, 0.4);
}

.content-area {
    padding: 22px 10px 5px;
}

.meta-info {
    display: flex; gap: 15px;
    color: var(--text-dim); 
    font-size: 11px; 
    font-weight: 700; 
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.meta-info i { color: var(--primary); }

.event-card h3 {
    font-size: 24px;
    font-weight: 800;
    margin-bottom: 22px;
    color: #fff;
    min-height: 58px;
    line-height: 1.2;
}

.btn-glow {
    display: block;
    padding: 16px;
    background: #fff; /* High Contrast White */
    color: #000;
    text-decoration: none;
    border-radius: 18px;
    font-weight: 800;
    font-size: 12px;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: 0.4s;
    text-align: center;
}

.btn-glow:hover {
    background: var(--primary);
    color: #fff;
    box-shadow: 0 10px 25px var(--primary-glow);
}

/* 🛑 FOOTER ALIGNMENT FIX */
footer {
    margin-top: 0 !important;
    background: var(--bg-dark) !important;
    border-top: 1px solid var(--border);
}

@media(max-width:1100px){
    .grid { grid-template-columns: repeat(2, 1fr); }
}

@media(max-width:768px){
    .grid { grid-template-columns: 1fr; }
    .page { padding: 120px 6% 30px; }
}
</style>

<div class="page">
    <div class="mesh-bg"></div>
    
    <div class="page-header">
        <span class="hero-tag">Premium Experience</span>
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
            echo "<div style='grid-column: 1/-1; text-align:center; padding:100px; z-index:10;'>
                    <h2 style='color:var(--text-dim); letter-spacing:2px;'>NO ACTIVE SUMMITS</h2>
                  </div>";
        }
        ?>
    </div>
</div>

<script>
    gsap.set(".reveal", { opacity: 0, y: 30 });
    window.onload = () => {
        gsap.to(".reveal", {
            y: 0, opacity: 1, duration: 1.2, stagger: 0.1, ease: "power4.out"
        });
    };
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>