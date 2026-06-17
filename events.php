<?php 
include(__DIR__ . "/includes/header.php"); 
include(__DIR__ . "/includes/db.php"); 
?>

<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<style>
/* ⚪ PREMIUM HIGH-VISIBILITY THEME */
:root {
    --primary: #ff3b3b;
    --bg-white: #ffffff;
    --card-bg: #ffffff;
    --text-main: #0c0f14;
    --text-muted: #64748b;
    --border: #e2e8f0;
}

body {
    background-color: var(--bg-white);
    margin: 0;
    padding: 0;
    font-family: 'Plus Jakarta Sans', sans-serif;
    overflow-x: hidden;
}

.page {
    padding: 100px 0 60px;
    position: relative;
    z-index: 10;
}

.mesh-bg {
    position: fixed;
    top: 0; left: 0; width: 100%; height: 100%;
    background: radial-gradient(circle at 10% 10%, rgba(255, 59, 59, 0.04) 0%, transparent 40%);
    z-index: -1;
    pointer-events: none;
}

.page-header { text-align: center; margin-bottom: 30px; }

.page-title {
    font-size: clamp(32px, 5vw, 52px);
    font-weight: 800;
    color: var(--text-main);
    text-transform: uppercase;
    letter-spacing: -1px;
}

.page-title span { color: var(--primary); }

/* 💎 FIXED 3-COLUMN LEFT-FLOATING COMPACT SYSTEM */
.reveal-container {
    display: flex;
    flex-wrap: wrap;
    justify-content: flex-start; /* Forcing items to align left */
}

.event-card-wrapper {
    width: 33.333% !important; /* Forces exactly 3 cards in one line */
    padding: 12px;
    display: flex;
}

.event-card {
    background: var(--card-bg);
    border: 1px solid var(--border);
    border-radius: 16px;
    padding: 10px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
    transition: border-color 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
    will-change: transform, opacity;
    display: flex;
    flex-direction: column;
    width: 100%;
}

.event-card:hover {
    border-color: rgba(255, 59, 59, 0.25);
}

.img-wrap {
    width: 100%;
    aspect-ratio: 16 / 9;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    background: #f1f5f9;
}

.img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
    display: block;
    transition: transform 0.6s cubic-bezier(0.16, 1, 0.3, 1);
}

.event-card:hover .img-wrap img {
    transform: scale(1.04);
}

.date-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    background: var(--primary);
    color: #fff;
    padding: 4px 10px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 10px;
    letter-spacing: 0.3px;
    z-index: 5;
    box-shadow: 0 4px 12px rgba(255, 59, 59, 0.2);
}

.content-area {
    padding: 10px 4px 2px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
    justify-content: space-between;
}

.meta-info {
    font-size: 11px;
    font-weight: 600;
    color: var(--text-muted);
    margin-bottom: 6px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.meta-info i { 
    color: var(--primary); 
    margin-right: 4px;
}

.event-card h3 {
    font-size: 16px;
    font-weight: 700;
    color: var(--text-main);
    margin: 0 0 12px;
    line-height: 1.35;
    letter-spacing: -0.2px;
}

.btn-premium {
    display: block;
    width: 100%;
    padding: 10px;
    background: #0f172a;
    color: #fff;
    text-align: center;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 12px;
    letter-spacing: 0.2px;
    transition: all 0.25s ease;
    margin-top: auto;
}

.btn-premium:hover {
    background: var(--primary);
    box-shadow: 0 6px 16px rgba(255, 59, 59, 0.2);
}

/* Responsive Scaling to keep alignment clean on smaller screens */
@media(max-width: 992px) {
    .event-card-wrapper { width: 50% !important; }
}
@media(max-width: 576px) {
    .event-card-wrapper { width: 100% !important; }
}
</style>

<div class="page">
    <div class="mesh-bg"></div>
    <div class="container">
        <div class="row align-items-center gy-4">
            <div class="col-12">
                <div class="page-header text-center text-lg-start">
                    <h1 class="page-title reveal-header">Summit <span>2026</span></h1>
                </div>
            </div>
        </div>

        <div class="reveal-container mt-2">
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
                <div class="event-card-wrapper">
                    <div class="event-card reveal-card">
                        <div class="img-wrap">
                            <img src="<?= $imgUrl ?>" alt="Event" loading="lazy">
                            <div class="date-badge"><?= date('d M', $eDate) ?></div>
                        </div>
                        <div class="content-area">
                            <div>
                                <div class="meta-info">
                                    <span><i class="fas fa-clock"></i><?= $time ?></span>
                                    <span><i class="fas fa-map-marker-alt"></i><?= htmlspecialchars($loc) ?></span>
                                </div>
                                <h3><?= htmlspecialchars($row['title']) ?></h3>
                            </div>
                            <a href="event_details.php?id=<?= $row['id'] ?>" class="btn-premium">View Event</a>
                        </div>
                    </div>
                </div>
            <?php }
            } else {
                echo '<div class="col-12"><h2 class="text-center text-muted">No Events Found</h2></div>';
            }
            ?>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        gsap.registerPlugin(ScrollTrigger);

        // 1. Header Animation
        gsap.from(".reveal-header", {
            y: -30,
            opacity: 0,
            duration: 0.8,
            ease: "power3.out"
        });

        // 2. Cards Entrance Animation
        gsap.from(".reveal-card", {
            scrollTrigger: {
                trigger: ".reveal-container",
                start: "top 90%", 
                toggleActions: "play none none none"
            },
            y: 40,
            opacity: 0,
            duration: 0.8,
            stagger: 0.1, 
            ease: "power3.out"
        });

        // 3. Smooth Micro-Hover Feedback
        const cards = document.querySelectorAll(".event-card");
        cards.forEach(card => {
            card.addEventListener("mouseenter", () => {
                gsap.to(card, { 
                    y: -6, 
                    boxShadow: "0 16px 32px rgba(255, 59, 59, 0.06)",
                    duration: 0.3, 
                    ease: "power2.out" 
                });
            });
            card.addEventListener("mouseleave", () => {
                gsap.to(card, { 
                    y: 0, 
                    boxShadow: "0 4px 18px rgba(0, 0, 0, 0.03)",
                    duration: 0.3, 
                    ease: "power2.out" 
                });
            });
        });
    });
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>