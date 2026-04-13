<?php include("includes/header.php"); ?>

<style>
/* PAGE WRAPPER */
.page {
    padding: 80px 10%;
    background: #fdfdfd; /* Light subtle grey bg */
    min-height: 100vh;
}

/* SEXY TITLE */
.page-title {
    text-align: left;
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 50px;
    color: #0f172a;
    position: relative;
    letter-spacing: -1px;
}

.page-title::after {
    content: '';
    position: absolute;
    bottom: -10px; left: 0;
    width: 50px; height: 6px;
    background: var(--primary, #ff3b3b);
    border-radius: 10px;
}

/* GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
}

/* HIGH-END EVENT CARD */
.card {
    background: #ffffff;
    padding: 30px;
    border-radius: 20px;
    border: 1px solid #f1f5f9;
    box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

/* Left-Side Red Accent */
.card::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0;
    width: 0;
    background: #ff3b3b;
    transition: 0.3s ease;
}

/* HOVER EFFECTS */
.card:hover {
    transform: translateY(-10px);
    box-shadow: 0 20px 40px rgba(255, 59, 59, 0.1);
    border-color: rgba(255, 59, 59, 0.2);
}

.card:hover::before {
    width: 6px; /* Hover par red border bar nikal ke aayega */
}

.card h3 {
    color: #1e293b;
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 12px;
}

.card p {
    color: #64748b;
    font-size: 0.95rem;
    margin-bottom: 20px;
}

/* DATE BADGE UPGRADE */
.date-container {
    display: flex;
    align-items: center;
    gap: 10px;
}

.date-badge {
    display: inline-flex;
    align-items: center;
    padding: 8px 16px;
    background: #fff1f1;
    color: #ff3b3b;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.85rem;
    border: 1px solid rgba(255, 59, 59, 0.1);
}

/* EMPTY STATE */
.no-data {
    text-align: center;
    grid-column: 1 / -1;
    padding: 50px;
    color: #94a3b8;
    font-size: 1.2rem;
}

/* MOBILE RESPONSIVE */
@media(max-width:768px){
    .page { padding: 40px 20px; }
    .page-title { font-size: 2rem; text-align: center; }
    .page-title::after { left: 50%; transform: translateX(-50%); }
}
</style>

<div class="page">
    <h2 class="page-title animate-hero">📅 Upcoming Events</h2>

    <div class="grid">
        <?php
        include("includes/db.php");
        $res = $conn->query("SELECT * FROM events ORDER BY event_date DESC");

        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){ ?>
                <div class="card reveal">
                    <div>
                        <h3><?= htmlspecialchars($row['title']) ?></h3>
                        <p>Join us for an exclusive networking session and industry insights.</p>
                    </div>
                    
                    <div class="date-container">
                        <span class="date-badge">
                            <i class="far fa-calendar-alt" style="margin-right:8px;"></i>
                            <?= date('d M, Y', strtotime($row['event_date'])) ?>
                        </span>
                    </div>
                </div>
            <?php }
        } else {
            echo "<div class='no-data'>No upcoming events at the moment. Stay tuned!</div>";
        }
        ?>
    </div>
</div>

<?php include("includes/footer.php"); ?>