<?php include("includes/header.php"); ?>
<?php include("includes/db.php"); ?>
<link rel="stylesheet" href="assets/css/style.css">


<div class="hero">
    <video autoplay muted loop playsinline class="hero-video">
        <source src="images/hero.mp4" type="video/mp4">
    </video>
    <div class="overlay"></div>
    <div class="hero-content">
        <h1 class="animate-hero">Alumni Connect</h1>
        <p class="animate-hero">Bridging the gap between Nagpur's talent and global success.</p>
        <a href="registration.php" class="btn-main animate-hero">Join the Elite Community</a>
    </div>
</div>

<div class="section">
    <h2 class="title">💼 Career Openings</h2>
    <div class="grid">
        <?php
        $res = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC LIMIT 6");
        while($row = $res->fetch_assoc()){ ?>
            <div class="card reveal">
                <span class="badge">Trending</span>
                <h3><?= $row['title'] ?></h3>
                <p><?= $row['company'] ?></p>
                <small style="color:#94a3b8;"><?= $row['location'] ?></small>
            </div>
        <?php } ?>
    </div>
</div>

<div class="section alt">
    <h2 class="title">📅 Upcoming Meets</h2>
    <div class="grid">
        <?php
        $res = $conn->query("SELECT * FROM events ORDER BY id DESC LIMIT 3");
        while($row = $res->fetch_assoc()){ ?>
            <div class="card reveal">
                <h3 style="color:var(--primary);"><?= date('M d', strtotime($row['event_date'])) ?></h3>
                <h3><?= $row['title'] ?></h3>
                <p>Register to secure your spot.</p>
            </div>
        <?php } ?>
    </div>
</div>

<div class="section">
    <h2 class="title">👨‍🎓 Notable Alumni</h2>
    <div class="grid">
        <?php
        $res = $conn->query("SELECT * FROM alumni ORDER BY id DESC LIMIT 4");
        while($row = $res->fetch_assoc()){ ?>
            <div class="card reveal">
                <h3><?= $row['name'] ?></h3>
                <p><?= $row['course'] ?> - <?= $row['batch'] ?></p>
                <small>Working at <b><?= $row['company'] ?></b></small>
            </div>
        <?php } ?>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script src="assets/js/script.js"></script>


<?php include("includes/footer.php"); ?>