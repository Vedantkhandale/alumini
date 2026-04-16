<?php require __DIR__ . "/includes/page_jobs.php"; return; ?>
<?php include("includes/header.php"); ?>

<style>
/* 🎨 GLOBAL REFINEMENT */
:root {
    --primary: #ff3b3b;
    --dark: #0f172a;
    --slate: #64748b;
    --card-shadow: 0 10px 40px -10px rgba(0,0,0,0.04);
}

.page {
    padding: 120px 8% 80px;
    background: #f8fafc; 
    min-height: 100vh;
}

/* ⚡ HEADER SECTION */
.page-header {
    margin-bottom: 50px;
    border-left: 4px solid var(--primary);
    padding-left: 25px;
}

.page-title {
    font-size: 3rem;
    font-weight: 850;
    color: var(--dark);
    letter-spacing: -2px;
    margin: 0;
}

.page-subtitle {
    color: var(--slate);
    font-size: 1.1rem;
    margin-top: 8px;
    font-weight: 500;
}

/* 🏢 ELITE GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
    gap: 35px;
}

/* 💼 THE PRO CARD */
.card {
    background: #ffffff;
    padding: 35px;
    border-radius: 30px;
    border: 1px solid rgba(15, 23, 42, 0.04);
    box-shadow: var(--card-shadow);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    display: flex;
    flex-direction: column;
    position: relative;
}

.card:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 40px 70px -15px rgba(0, 0, 0, 0.1);
    border-color: rgba(255, 59, 59, 0.1);
}

/* BRANDING STRIP */
.brand-strip {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
}

.company-logo-box {
    width: 65px; height: 65px;
    background: var(--dark);
    color: #fff;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.6rem;
    box-shadow: 0 10px 20px rgba(15, 23, 42, 0.15);
}

.live-badge {
    padding: 6px 14px;
    background: #ecfdf5;
    color: #10b981;
    border-radius: 50px;
    font-size: 0.7rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    border: 1px solid #d1fae5;
}

/* CONTENT INFO */
.job-h3 {
    font-size: 1.6rem;
    color: var(--dark);
    font-weight: 800;
    margin-bottom: 6px;
    letter-spacing: -0.5px;
}

.company-name {
    color: var(--primary);
    font-weight: 700;
    font-size: 1rem;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}

/* META CHIPS - Glass Style */
.meta-wrapper {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 30px;
}

.chip {
    padding: 8px 16px;
    background: #f1f5f9;
    color: #475569;
    border-radius: 14px;
    font-size: 0.85rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #e2e8f0;
}

/* CTA BUTTON */
.btn-apply {
    margin-top: auto;
    width: 100%;
    padding: 18px;
    background: var(--dark);
    color: white;
    text-align: center;
    border-radius: 20px;
    text-decoration: none;
    font-weight: 800;
    font-size: 1rem;
    transition: 0.3s;
    border: 2px solid var(--dark);
}

.btn-apply:hover {
    background: var(--primary);
    border-color: var(--primary);
    box-shadow: 0 15px 30px rgba(255, 59, 59, 0.3);
}

/* RESPONSIVE */
@media(max-width:768px){
    .page { padding: 100px 20px 40px; }
    .page-title { font-size: 2.2rem; }
}
</style>

<div class="page">
    <header class="page-header reveal">
        <h2 class="page-title">Career Hub</h2>
        <p class="page-subtitle">Premium roles handpicked for our alumni network.</p>
    </header>

    <div class="grid">
        <?php
        include("includes/db.php");
        $res = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC");

        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){ 
                $initial = substr($row['company'], 0, 1);
                ?>
                <div class="card reveal">
                    <div class="brand-strip">
                        <div class="company-logo-box"><?= strtoupper($initial) ?></div>
                        <span class="live-badge">Verified</span>
                    </div>
                    
                    <h3 class="job-h3"><?= htmlspecialchars($row['title']) ?></h3>
                    <div class="company-name">
                        <i class="fas fa-check-circle" style="font-size: 12px;"></i> 
                        <?= htmlspecialchars($row['company']) ?>
                    </div>
                    
                    <div class="meta-wrapper">
                        <div class="chip"><i class="fas fa-location-dot" style="color:var(--primary)"></i> <?= htmlspecialchars($row['location']) ?></div>
                        <div class="chip"><i class="fas fa-clock"></i> Full Time</div>
                        <div class="chip"><i class="fas fa-wallet"></i> Market Std.</div>
                    </div>
                    
                    <a href="job_details.php?id=<?= $row['id'] ?>" class="btn-apply">View Full Details</a>
                </div>
            <?php }
        } else {
            echo "<div style='grid-column: 1/-1; text-align:center; padding: 100px;'>
                    <img src='https://cdn-icons-png.flaticon.com/512/7486/7486744.png' width='80' style='opacity:0.2; margin-bottom:20px;'>
                    <p style='color: #94a3b8; font-size: 1.2rem; font-weight:600;'>No opportunities found right now.</p>
                  </div>";
        }
        ?>
    </div>
</div>

<?php include("includes/footer.php"); ?>