<?php 
include(__DIR__ . "/includes/header.php"); 
include(__DIR__ . "/includes/db.php"); 
?>

<style>
/* 🌍 GLOBAL PAGE STYLING */
.page {
    padding: 120px 8% 80px;
    background: #f8fafc;
    min-height: 100vh;
}

/* 🔥 PREMIUM TITLE DESIGN */
.page-header {
    margin-bottom: 60px;
    border-left: 8px solid #ff3b3b;
    padding-left: 25px;
}

.page-title {
    font-size: clamp(32px, 5vw, 48px);
    font-weight: 900;
    color: #0f172a;
    letter-spacing: -2px;
}

/* 🏢 GRID SYSTEM */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
    gap: 35px;
}

/* 💎 ELITE EVENT CARD */
.card {
    background: #ffffff;
    border-radius: 35px;
    border: 1px solid rgba(0,0,0,0.03);
    box-shadow: 0 15px 35px rgba(0,0,0,0.03);
    transition: all 0.5s cubic-bezier(0.2, 1, 0.3, 1);
    position: relative;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* 🖼️ IMAGE SECTION */
.card-img {
    width: 100%;
    height: 200px;
    position: relative;
    overflow: hidden;
}

.card-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: 0.6s ease;
}

.card:hover .card-img img {
    transform: scale(1.1);
}

/* 📅 FLOATING DATE OVERLAY */
.date-badge {
    position: absolute;
    top: 20px;
    left: 20px;
    background: rgba(15, 23, 42, 0.9);
    backdrop-filter: blur(10px);
    color: #fff;
    padding: 12px;
    border-radius: 20px;
    text-align: center;
    min-width: 55px;
    z-index: 2;
}

.date-badge span { display: block; font-size: 20px; font-weight: 900; line-height: 1; }
.date-badge small { font-size: 10px; text-transform: uppercase; font-weight: 700; color: #ff3b3b; }

/* CARD CONTENT */
.card-body {
    padding: 30px;
    display: flex;
    flex-direction: column;
    flex-grow: 1;
}

.card h3 {
    color: #0f172a;
    font-size: 1.5rem;
    font-weight: 850;
    margin-bottom: 12px;
    letter-spacing: -0.5px;
}

/* 📍 INFO CHIPS */
.info-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 15px;
}

.chip {
    padding: 6px 14px;
    background: #f1f5f9;
    color: #475569;
    border-radius: 50px;
    font-size: 12px;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 6px;
}

.chip i { color: #ff3b3b; }

.card:hover {
    transform: translateY(-15px);
    box-shadow: 0 30px 60px rgba(255, 59, 59, 0.15);
}

/* 📱 RESPONSIVE */
@media(max-width:768px){
    .page { padding: 100px 20px 50px; }
    .grid { grid-template-columns: 1fr; }
}
</style>

<div class="page">
    <div class="page-header">
        <h2 class="page-title">Upcoming <span>Events</span></h2>
        <p style="color:#64748b; font-weight:500;">Premium gatherings for the AlumniX elite.</p>
    </div>

    <div class="grid">
        <?php
        $res = $conn->query("SELECT * FROM events ORDER BY event_date ASC");

        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){ 
                $eDate = strtotime($row['event_date']);
                $time = isset($row['event_time']) ? date('h:i A', strtotime($row['event_time'])) : 'TBA';
                $loc = !empty($row['location']) ? $row['location'] : 'Nagpur, India';
                
                // Image Handling: Agar DB me image h to wo, warna random high-quality image
                $imgUrl = !empty($row['image']) ? 'uploads/events/'.$row['image'] : 'https://images.unsplash.com/photo-1540575861501-7cf05a4b125a?auto=format&fit=crop&w=800&q=80';
            ?>
                <div class="card reveal">
                    <div class="card-img">
                        <div class="date-badge">
                            <span><?= date('d', $eDate) ?></span>
                            <small><?= date('M', $eDate) ?></small>
                        </div>
                        <img src="<?= $imgUrl ?>" alt="Event Banner">
                    </div>

                    <div class="card-body">
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        <p style="font-size:14px; color:#64748b;">Connect with industry leaders and old friends in this exclusive session.</p>
                        
                        <div class="info-row">
                            <div class="chip"><i class="fas fa-clock"></i> <?= $time ?></div>
                            <div class="chip"><i class="fas fa-location-dot"></i> <?= htmlspecialchars($loc) ?></div>
                        </div>

                        <a href="event_details.php?id=<?= $row['id'] ?>" style="margin-top:25px; font-weight:800; color:#ff3b3b; text-decoration:none; font-size:13px; text-transform:uppercase; letter-spacing:1px; display:inline-flex; align-items:center; gap:8px;">
                            View Details <i class="fas fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            <?php }
        } else {
            echo "<div style='grid-column: 1/-1; text-align:center; padding:100px; color:#94a3b8;'>No events planned yet.</div>";
        }
        ?>
    </div>
</div>

<?php include(__DIR__ . "/includes/footer.php"); ?>