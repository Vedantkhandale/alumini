<?php 
include(__DIR__ . "/includes/header.php"); 
include(__DIR__ . "/includes/db.php"); 
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<style>
/* ⚪ PREMIUM HIGH-VISIBILITY THEME */
:root {
    --primary: #ff3b3b;
    --bg-white: #ffffff;
    --card-bg: #ffffff;
    --text-main: #000000;
    --text-muted: #555555;
    --border: #eeeeee;
}

body {
    background-color: var(--bg-white);
    margin: 0;
    padding: 0;
    font-family: 'Plus Jakarta Sans', sans-serif;
    overflow-x: hidden;
}

.page {
    padding: 100px 6% 60px;
    position: relative;
    z-index: 10;
}

.mesh-bg {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: radial-gradient(circle at 10% 10%, rgba(255, 59, 59, 0.05) 0%, transparent 40%);
    z-index: -1;
    pointer-events: none;
}

.page-header { text-align: center; margin-bottom: 40px; }

.page-title {
    font-size: clamp(36px, 6vw, 72px);
    font-weight: 800;
    color: var(--text-main);
    text-transform: uppercase;
    letter-spacing: -1.5px;
}

.page-title span { color: var(--primary); }

/* 💎 GRID SYSTEM */
.grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 22px;
}

.event-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 24px;
    padding: 14px;
    box-shadow: 0 10px 24px rgba(0,0,0,0.06);
    transition: border-color 0.4s ease, transform 0.3s ease, box-shadow 0.3s ease;
    will-change: transform, opacity;
}

.event-card:hover {
    border-color: var(--primary);
    transform: translateY(-4px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.08);
}

.img-wrap {
    width: 100%;
    aspect-ratio: 16 / 10;
    border-radius: 18px;
    overflow: hidden;
    position: relative;
    background: #f0f0f0;
}

.img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    display: block;
    transition: transform 0.8s cubic-bezier(0.2, 1, 0.3, 1);
}

.event-card:hover .img-wrap img {
    transform: scale(1.08);
}

.date-badge {
    position: absolute;
    top: 14px;
    right: 14px;
    background: var(--primary);
    color: #fff;
    padding: 8px 14px;
    border-radius: 14px;
    font-weight: 800;
    font-size: 12px;
    z-index: 5;
}

.content-area {
    padding: 16px 16px 18px;
}

.meta-info {
    font-size: 12px;
    font-weight: 700;
    color: var(--text-muted);
    margin-bottom: 10px;
    text-transform: uppercase;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.meta-info i { color: var(--primary); }

.event-card h3 {
    font-size: 22px;
    font-weight: 700;
    color: var(--text-main);
    margin: 0 0 14px;
    line-height: 1.2;
    min-height: 48px;
}

.btn-premium {
    display: block;
    width: 100%;
    padding: 14px;
    background: #000;
    color: #fff;
    text-align: center;
    text-decoration: none;
    border-radius: 14px;
    font-weight: 700;
    font-size: 13px;
    text-transform: uppercase;
    transition: all 0.3s ease;
}

.btn-premium:hover {
    background: var(--primary);
    letter-spacing: 0.5px;
}

@media(max-width:1100px){ .grid { grid-template-columns: repeat(2, 1fr); } }
@media(max-width:768px){ .grid { grid-template-columns: 1fr; } }
</style>

<div class="page">
    <div class="mesh-bg"></div>
    
    <div class="page-header">
        <h1 class="page-title reveal-header">Summit <span>2026</span></h1>
    </div>

    <div class="grid">
        <?php
        $res = $conn->query("SELECT * FROM events ORDER BY event_date ASC");

        if($res && $res->num_rows > 0){
            while($row = $res->fetch_assoc()){ 
                $eDate = strtotime($row['event_date']);
                $time = isset($row['event_time']) ? date('h:i A', strtotime($row['event_time'])) : 'TBA';
                $loc = !empty($row['location']) ? $row['location'] : 'Nexus Hall';
                $defaultImages = [
                    'https://images.unsplash.com/photo-1515169067865-5387ec356754?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=800&q=80',
                    'https://images.unsplash.com/photo-1496307042754-b4aa456c4a2d?auto=format&fit=crop&w=800&q=80'
                ];
                $imgUrl = !empty($row['image'])
                    ? 'uploads/events/' . $row['image']
                    : $defaultImages[$row['id'] % count($defaultImages)];
            ?>
                <div class="event-card reveal-card">
                    <div class="img-wrap">
                        <img src="<?= $imgUrl ?>" alt="Event" loading="lazy">
                        <div class="date-badge"><?= date('d M', $eDate) ?></div>
                    </div>
                    <div class="content-area">
                        <div class="meta-info">
                            <span><i class="fas fa-clock"></i> <?= $time ?></span>
                            <span style="margin-left:10px;"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($loc) ?></span>
                        </div>
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        <a href="event_details.php?id=<?= $row['id'] ?>" class="btn-premium">View Event</a>
                    </div>
                </div>
            <?php }
        } else {
            echo "<h2 style='grid-column:1/-1; text-align:center; color:#999;'>No Events Found</h2>";
        }
        ?>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        gsap.registerPlugin(ScrollTrigger);

        // 1. Header Animation
        gsap.from(".reveal-header", {
            y: -50,
            opacity: 0,
            duration: 1,
            ease: "back.out(1.7)"
        });

        // 2. Cards Entrance & Scroll Animation
        gsap.from(".reveal-card", {
            scrollTrigger: {
                trigger: ".grid",
                start: "top 85%", // Jab grid ka top 85% screen par ho
                toggleActions: "play none none none"
            },
            y: 60,
            opacity: 0,
            duration: 1,
            stagger: 0.15, // Ek ke baad ek aayenge
            ease: "power4.out"
        });

        // 3. Sexy Hover Effect using GSAP (Tilt effect)
        const cards = document.querySelectorAll(".event-card");
        cards.forEach(card => {
            card.addEventListener("mouseenter", () => {
                gsap.to(card, { 
                    y: -10, 
                    scale: 1.02, 
                    boxShadow: "0 30px 60px rgba(255, 59, 59, 0.15)",
                    duration: 0.4, 
                    ease: "power2.out" 
                });
            });
            card.addEventListener("mouseleave", () => {
                gsap.to(card, { 
                    y: 0, 
                    scale: 1, 
                    boxShadow: "0 10px 30px rgba(0,0,0,0.05)",
                    duration: 0.4, 
                    ease: "power2.out" 
                });
            });
        });
    });
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>