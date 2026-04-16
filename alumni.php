<?php 
// Footer errors fix karne ke liye path check kar lena
include(__DIR__ . "/includes/header.php"); 
include(__DIR__ . "/includes/db.php"); 
?>

<style>
/* 🎨 THEME VARIABLES */
:root {
    --primary: #ff3b3b;
    --primary-glow: rgba(255, 59, 59, 0.15);
    --card-shadow: 0 20px 40px rgba(0,0,0,0.06);
    --card-hover-shadow: 0 40px 80px rgba(255, 59, 59, 0.12);
}

.page {
    padding: 100px 8%;
    background: #f8fafc; /* Ultra clean background */
    min-height: 100vh;
}

/* 🏢 ELITE GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 35px;
}

/* 🤵 PREMIUM ALUMNI CARD */
.card {
    background: #ffffff;
    padding: 50px 30px;
    border-radius: 35px;
    border: 1px solid rgba(0,0,0,0.03);
    box-shadow: var(--card-shadow);
    text-align: center;
    transition: all 0.6s cubic-bezier(0.23, 1, 0.32, 1);
    position: relative;
    overflow: hidden;
    z-index: 1;
}

/* Glassy Border Glow Effect */
.card::after {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 35px;
    padding: 2px; /* Border thickness */
    background: linear-gradient(135deg, transparent, transparent);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
    transition: 0.5s;
    opacity: 0;
}

.card:hover {
    transform: translateY(-20px) scale(1.02);
    box-shadow: var(--card-hover-shadow);
    border-color: transparent;
}

.card:hover::after {
    background: linear-gradient(135deg, var(--primary), #ff9b9b);
    opacity: 1;
}

/* 🎨 SQUIRCLE AVATAR WITH NEON GLOW */
.avatar-wrapper {
    position: relative;
    width: 100px;
    height: 100px;
    margin: 0 auto 25px;
}

.avatar {
    width: 100%;
    height: 100%;
    border-radius: 32px; /* Premium Squircle */
    background: linear-gradient(135deg, #111 0%, #333 100%);
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 36px;
    font-weight: 800;
    position: relative;
    z-index: 2;
    transition: 0.5s;
}

.avatar-glow {
    position: absolute;
    inset: -5px;
    background: var(--primary);
    border-radius: 35px;
    filter: blur(15px);
    opacity: 0;
    transition: 0.5s;
}

.card:hover .avatar-glow {
    opacity: 0.4;
}

.card:hover .avatar {
    background: var(--primary);
    transform: rotate(-5deg);
}

/* TEXT STYLING */
.card h3 {
    color: #0f172a;
    font-size: 1.5rem;
    font-weight: 850;
    letter-spacing: -0.5px;
    margin-bottom: 10px;
}

.course-tag {
    display: inline-block;
    padding: 6px 14px;
    background: var(--primary-glow);
    color: var(--primary);
    border-radius: 12px;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 15px;
}

.company-box {
    color: #64748b;
    font-size: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 15px;
    padding-top: 15px;
    border-top: 1px solid #f1f5f9;
}

/* 🏅 VERIFIED BADGE */
.verified-icon {
    position: absolute;
    top: 25px;
    right: 25px;
    color: #10b981;
    font-size: 18px;
    background: #ecfdf5;
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}
</style>

<div class="page">
    <div class="page-header reveal" style="text-align:center; margin-bottom: 70px;">
        <span style="color:var(--primary); font-weight:800; text-transform:uppercase; letter-spacing:2px; font-size:12px;">Global Directory</span>
        <h2 style="font-size: 3.5rem; font-weight: 900; letter-spacing:-3px; color:#0f172a;">Elite <span>Network.</span></h2>
    </div>

    <div class="grid">
        <?php
        $res = $conn->query("SELECT * FROM alumni ORDER BY id DESC");
        while($row = $res->fetch_assoc()){
            $initial = strtoupper(substr($row['name'], 0, 1));
        ?>
            <div class="card">
                <div class="verified-icon" title="Verified Member">
                    <i class="fas fa-check-circle"></i>
                </div>

                <div class="avatar-wrapper">
                    <div class="avatar-glow"></div>
                    <div class="avatar"><?= $initial ?></div>
                </div>

                <span class="course-tag"><?= htmlspecialchars($row['course']) ?> Member</span>
                
                <h3><?= htmlspecialchars($row['name']) ?></h3>
                
                <div class="company-box">
                    <i class="fas fa-building" style="color:#cbd5e1;"></i> 
                    <span><?= htmlspecialchars($row['company']) ?></span>
                </div>

                <a href="profile.php?id=<?= $row['id'] ?>" style="margin-top:25px; display:inline-block; text-decoration:none; font-weight:800; font-size:12px; color:var(--primary); text-transform:uppercase; letter-spacing:1px;">
                    View Experience <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        <?php } ?>
    </div>
</div>

<?php include(__DIR__ . "/includes/footer.php"); ?>