<?php include("includes/header.php"); ?>

<style>
/* PAGE WRAPPER */
.page {
    padding: 80px 10%;
    background: #f8fafc; /* Premium light bluish-grey bg */
    min-height: 100vh;
}

/* SEXY TITLE */
.page-header {
    margin-bottom: 50px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -1.5px;
    position: relative;
}

.page-title::after {
    content: '';
    position: absolute;
    bottom: -8px; left: 0;
    width: 60px; height: 5px;
    background: #ff3b3b;
    border-radius: 10px;
}

/* GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
}

/* 💼 PREMIUM JOB CARD */
.card {
    background: #ffffff;
    padding: 30px;
    border-radius: 24px;
    border: 1px solid #edf2f7;
    box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    display: flex;
    flex-direction: column;
    position: relative;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-12px);
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
    border-color: rgba(255, 59, 59, 0.2);
}

/* Company Avatar Style */
.job-icon {
    width: 50px;
    height: 50px;
    background: #fff1f1;
    color: #ff3b3b;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.2rem;
    margin-bottom: 20px;
}

.card h3 {
    font-size: 1.4rem;
    color: #1e293b;
    font-weight: 700;
    margin-bottom: 5px;
}

.company {
    color: #ff3b3b;
    font-weight: 600;
    font-size: 1rem;
    margin-bottom: 15px;
}

.location-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f1f5f9;
    padding: 5px 12px;
    border-radius: 8px;
    color: #64748b;
    font-size: 0.85rem;
    font-weight: 500;
    margin-bottom: 25px;
}

/* APPLY BUTTON UPGRADE */
.apply-btn {
    margin-top: auto;
    width: 100%;
    padding: 14px;
    background: #0f172a; /* Dark button for contrast */
    color: white;
    text-align: center;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 700;
    font-size: 0.95rem;
    transition: all 0.3s ease;
}

.apply-btn:hover {
    background: #ff3b3b;
    box-shadow: 0 10px 20px rgba(255, 59, 59, 0.3);
}

/* MOBILE RESPONSIVE */
@media(max-width:768px){
    .page { padding: 40px 20px; }
    .page-header { flex-direction: column; text-align: center; }
    .page-title::after { left: 50%; transform: translateX(-50%); }
}
</style>

<div class="page">
    <div class="page-header">
        <h2 class="page-title">💼 Available Opportunities</h2>
    </div>

    <div class="grid">
        <?php
        include("includes/db.php");
        $res = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC");

        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){ 
                // Getting the first letter of the company for the icon
                $icon = substr($row['company'], 0, 1);
                ?>
                <div class="card reveal">
                    <div class="job-icon"><?= strtoupper($icon) ?></div>
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <p class="company"><?= htmlspecialchars($row['company']) ?></p>
                    
                    <div class="location-tag">
                        📍 <?= htmlspecialchars($row['location']) ?>
                    </div>
                    
                    <a href="#" class="apply-btn">View Details & Apply</a>
                </div>
            <?php }
        } else {
            echo "<div style='grid-column: 1/-1; text-align:center; padding: 100px; color: #94a3b8;'>
                    <img src='assets/img/no-jobs.svg' style='width: 200px; opacity:0.5;'><br>
                    <p style='margin-top:20px;'>No job opportunities posted yet. Check back later!</p>
                  </div>";
        }
        ?>
    </div>
</div>

<?php include("includes/footer.php"); ?>