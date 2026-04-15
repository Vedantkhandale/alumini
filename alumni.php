<?php require __DIR__ . "/includes/page_alumni.php"; return; ?>
<?php include("includes/header.php"); ?>

<style>
/* 🌍 PAGE WRAPPER */
.page {
    padding: 80px 10%;
    background: #f4f7fa; 
    min-height: 100vh;
}

/* 🔥 DYNAMIC PAGE TITLE */
.page-header {
    text-align: center;
    margin-bottom: 60px;
}

.page-title {
    font-size: 3rem;
    font-weight: 800;
    color: #0f172a;
    letter-spacing: -2px;
    margin-bottom: 10px;
}

.page-subtitle {
    color: #64748b;
    font-size: 1.1rem;
    font-weight: 500;
}

/* 🏢 ALUMNI GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 30px;
}

/* 🤵 ELITE ALUMNI CARD */
.card {
    background: #ffffff;
    padding: 40px 30px;
    border-radius: 30px;
    border: 1px solid rgba(226, 232, 240, 0.8);
    box-shadow: 0 10px 25px rgba(0,0,0,0.02);
    text-align: center;
    transition: all 0.5s cubic-bezier(0.165, 0.84, 0.44, 1);
    position: relative;
    overflow: hidden;
}

/* Subtle background pattern for card */
.card::before {
    content: '';
    position: absolute;
    top: -50px; right: -50px;
    width: 100px; height: 100px;
    background: rgba(255, 59, 59, 0.03);
    border-radius: 50%;
    transition: 0.5s;
}

.card:hover {
    transform: translateY(-15px);
    box-shadow: 0 30px 60px rgba(0,0,0,0.08);
    border-color: rgba(255, 59, 59, 0.2);
}

.card:hover::before {
    transform: scale(3);
    background: rgba(255, 59, 59, 0.05);
}

/* 🎨 PREMIUM AVATAR */
.avatar {
    width: 90px;
    height: 90px;
    border-radius: 25px; /* Squircle shape */
    background: linear-gradient(135deg, #ff3b3b 0%, #ff7b7b 100%);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: 800;
    margin: 0 auto 20px;
    box-shadow: 0 10px 20px rgba(255, 59, 59, 0.3);
    transform: rotate(-5deg);
    transition: 0.4s;
}

.card:hover .avatar {
    transform: rotate(0deg) scale(1.1);
}

/* TEXT STYLING */
.card h3 {
    color: #1e293b;
    font-size: 1.4rem;
    font-weight: 700;
    margin-bottom: 8px;
}

.course {
    color: #ff3b3b;
    font-weight: 700;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 5px;
}

.company {
    color: #64748b;
    font-size: 0.95rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
}

/* 🎖️ ALUMNI STATUS BADGE */
.badge {
    display: inline-block;
    margin-top: 25px;
    padding: 8px 20px;
    background: #f1f5f9;
    color: #475569;
    border-radius: 100px;
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.5px;
    transition: 0.3s;
}

.card:hover .badge {
    background: #0f172a;
    color: #ffffff;
}

/* 📱 MOBILE FIX */
@media(max-width:768px){
    .page { padding: 50px 20px; }
    .page-title { font-size: 2.2rem; }
}
</style>

<div class="page">
    <div class="page-header reveal">
        <h2 class="page-title">👨‍🎓 Alumni Network</h2>
        <p class="page-subtitle">Connecting past achievers with future leaders</p>
    </div>

    <div class="grid">
        <?php
        include("includes/db.php");
        $res = $conn->query("SELECT * FROM alumni ORDER BY id DESC");

        if($res->num_rows > 0){
            while($row = $res->fetch_assoc()){
                $initial = strtoupper(substr($row['name'], 0, 1));
                ?>
                <div class="card reveal">
                    <div class="avatar"><?= $initial ?></div>
                    <p class="course"><?= htmlspecialchars($row['course']) ?></p>
                    <h3><?= htmlspecialchars($row['name']) ?></h3>
                    <p class="company">
                        <i class="fas fa-briefcase" style="font-size: 12px;"></i> 
                        <?= htmlspecialchars($row['company']) ?>
                    </p>
                    <div class="badge">PRO ALUMNI</div>
                </div>
            <?php }
        } else {
            echo "<p style='grid-column: 1/-1; text-align:center;'>No alumni members listed yet.</p>";
        }
        ?>
    </div>
</div>

<?php include("includes/footer.php"); ?>
