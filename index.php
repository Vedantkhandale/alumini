<?php include("includes/header.php"); ?>

<style>

/* HERO */
.hero {
    position: relative;
    height: 80vh;
    display: flex;
    justify-content: center;
    align-items: center;
    text-align: center;
    overflow: hidden;
}

/* VIDEO BACKGROUND */
.hero-video {
    position: absolute;
    top: 50%;
    left: 50%;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transform: translate(-50%, -50%);
    z-index: -2;
}

/* DARK OVERLAY */
.overlay {
    position: absolute;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.45);
    z-index: -1;
}

/* HERO CONTENT */
.hero-content {
    color: #fff;
    z-index: 2;
}

.hero-content h1 {
    font-size: 50px;
}

.hero-content p {
    margin-top: 10px;
    color: #eee;
}

/* BUTTON */
.btn {
    display: inline-block;
    margin-top: 20px;
    padding: 12px 25px;
    background: #ff3b3b;
    color: white;
    text-decoration: none;
    border-radius: 25px;
    font-weight: 500;
    transition: 0.3s;
}

.btn:hover {
    background: #e60023;
    transform: translateY(-2px);
}

/* SECTION */
.section {
    padding: 60px 80px;
}

/* TITLE */
.title {
    text-align: center;
    margin-bottom: 40px;
    font-size: 26px;
    color: #222;
}

/* GRID */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 25px;
}

/* CARD */
.card {
    background: #ffffff;
    padding: 22px;
    border-radius: 14px;
    transition: 0.3s;
    border: 1px solid #eee;
    box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

.card h3 {
    color: #222;
    margin-bottom: 5px;
}

.card p {
    color: #666;
}

.card small {
    color: #999;
}

/* HOVER */
.card:hover {
    transform: translateY(-6px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
}

/* ALT BG */
.section.alt {
    background: #f9fbff;
}

/* MOBILE */
@media(max-width:768px){
    .section {
        padding: 40px 20px;
    }

    .hero-content h1 {
        font-size: 32px;
    }
}

</style>

<!-- 🔥 HERO -->
<div class="hero">

    <!-- VIDEO -->
    <video autoplay muted loop playsinline class="hero-video">
        <source src="images/hero.mp4" type="video/mp4">
    </video>

    <!-- OVERLAY -->
    <div class="overlay"></div>

    <!-- CONTENT -->
    <div class="hero-content">
        <h1>🎓 Alumni Network</h1>
        <p>Connect with seniors • Grow your career • Explore opportunities</p>
        <a href="registration.php" class="btn">Join Now</a>
    </div>

</div>

<!-- 💼 JOBS -->
<div class="section">
    <h2 class="title">💼 Latest Opportunities</h2>

    <div class="grid">
    <?php
    include("includes/db.php");
    $res = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC LIMIT 6");

    while($row = $res->fetch_assoc()){
        echo "<div class='card'>";
        echo "<h3>".$row['title']."</h3>";
        echo "<p>".$row['company']."</p>";
        echo "<small>".$row['location']."</small>";
        echo "</div>";
    }
    ?>
    </div>
</div>

<!-- 📅 EVENTS -->
<div class="section alt">
    <h2 class="title">📅 Upcoming Events</h2>

    <div class="grid">
    <?php
    $res = $conn->query("SELECT * FROM events ORDER BY id DESC LIMIT 3");

    while($row = $res->fetch_assoc()){
        echo "<div class='card'>";
        echo "<h3>".$row['title']."</h3>";
        echo "<p>".$row['event_date']."</p>";
        echo "</div>";
    }
    ?>
    </div>
</div>

<!-- 👥 ALUMNI -->
<div class="section">
    <h2 class="title">👨‍🎓 Our Alumni</h2>

    <div class="grid">
    <?php
    $res = $conn->query("SELECT * FROM alumni ORDER BY id DESC LIMIT 4");

    while($row = $res->fetch_assoc()){
        echo "<div class='card'>";
        echo "<h3>".$row['name']."</h3>";
        echo "<p>".$row['course']."</p>";
        echo "<small>".$row['company']."</small>";
        echo "</div>";
    }
    ?>
    </div>
</div>

<?php include("includes/footer.php"); ?>