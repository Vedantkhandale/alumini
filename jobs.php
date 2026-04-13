<?php include("includes/header.php"); ?>

<style>
/* PAGE WRAPPER */
.page {
    padding: 80px 10%;
    background: #f4f7fa; 
    min-height: 100vh;
}

/* SEARCH & HEADER SECTION */
.page-header {
    margin-bottom: 60px;
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    flex-wrap: wrap;
    gap: 20px;
}

.title-area h2 {
    font-size: 2.8rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -1.5px;
    margin: 0;
}

.title-area p {
    color: #64748b;
    font-size: 1.1rem;
    margin-top: 5px;
}

/* JOB GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
    gap: 30px;
}

/* 💼 ELITE JOB CARD */
.card {
    background: #ffffff;
    padding: 35px;
    border-radius: 28px;
    border: 1px solid rgba(226, 232, 240, 0.7);
    box-shadow: 0 15px 35px rgba(0,0,0,0.03);
    transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Top Glow Line */
.card::after {
    content: '';
    position: absolute;
    top: 0; left: 0; width: 100%; height: 4px;
    background: linear-gradient(90deg, transparent, var(--primary, #ff3b3b), transparent);
    opacity: 0;
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-12px);
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.08);
    border-color: rgba(255, 59, 59, 0.15);
}

.card:hover::after { opacity: 1; }

/* JOB HEADER (Icon + Badge) */
.job-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 25px;
}

.job-icon {
    width: 60px; height: 60px;
    background: linear-gradient(135deg, #fff1f1 0%, #ffe4e4 100%);
    color: #ff3b3b;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.4rem;
    box-shadow: inset 0 0 10px rgba(255, 59, 59, 0.05);
}

.job-type {
    padding: 6px 14px;
    background: #eff6ff;
    color: #3b82f6;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
}

/* CONTENT */
.card h3 {
    font-size: 1.5rem;
    color: #1e293b;
    font-weight: 700;
    margin-bottom: 8px;
    line-height: 1.2;
}

.company {
    color: #64748b;
    font-weight: 500;
    font-size: 1.05rem;
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
}

.company::before {
    content: '';
    width: 6px; height: 6px;
    background: #ff3b3b;
    border-radius: 50%;
}

.job-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 30px;
}

.meta-tag {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #f8fafc;
    padding: 8px 14px;
    border-radius: 12px;
    color: #475569;
    font-size: 0.85rem;
    font-weight: 600;
    border: 1px solid #f1f5f9;
}

/* APPLY BUTTON */
.apply-btn {
    margin-top: auto;
    width: 100%;
    padding: 16px;
    background: #0f172a;
    color: white;
    text-align: center;
    border-radius: 16px;
    text-decoration: none;
    font-weight: 700;
    font-size: 1rem;
    transition: all 0.4s;
}

.apply-btn:hover {
    background: #ff3b3b;
    transform: scale(1.02);
    box-shadow: 0 10px 25px rgba(255, 59, 59, 0.3);
}

/* RESPONSIVE */
@media(max-width:768px){
    .page { padding: 40px 20px; }
    .page-header { text-align: center; justify-content: center; }
}
</style>

<div class="page">
    <div class="page-header reveal">
        <div class="title-area">
            <h2 class="page-title">💼 Job Board</h2>
            <p>Exclusive opportunities for our Alumni network</p>
        </div>
    </div>

    <div class="grid">
        <?php
        include("includes/db.php");
        $res = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC");

        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){ 
                $icon = substr($row['company'], 0, 1);
                ?>
                <div class="card reveal">
                    <div class="job-card-header">
                        <div class="job-icon"><?= strtoupper($icon) ?></div>
                        <span class="job-type">Full Time</span>
                    </div>
                    
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <div class="company"><?= htmlspecialchars($row['company']) ?></div>
                    
                    <div class="job-meta">
                        <div class="meta-tag">📍 <?= htmlspecialchars($row['location']) ?></div>
                        <div class="meta-tag">💰 Competitive Pay</div>
                    </div>
                    
                    <a href="#" class="apply-btn">View Opportunity</a>
                </div>
            <?php }
        } else {
            echo "<div style='grid-column: 1/-1; text-align:center; padding: 100px;'>
                    <p style='color: #94a3b8; font-size: 1.2rem;'>No opportunities available right now. Check back soon!</p>
                  </div>";
        }
        ?>
    </div>
</div>

<?php include("includes/footer.php"); ?>