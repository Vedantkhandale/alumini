<?php include("includes/header.php"); ?>
<?php include("includes/db.php"); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
    <div class="section-header">
        <h2 class="title">💼 Career Openings</h2>
        <p class="subtitle">Premium opportunities for our elite network</p>
    </div>
    <div class="grid">
        <?php
        $res = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC LIMIT 6");
        while($row = $res->fetch_assoc()){ ?>
            <div class="card job-card reveal">
                <div class="card-glow"></div>
                <span class="badge">Trending</span>
                <div class="job-icon"><i class="fas fa-briefcase"></i></div>
                <h3><?= $row['title'] ?></h3>
                <p class="company"><?= $row['company'] ?></p>
                <div class="card-meta">
                    <span><i class="fas fa-map-marker-alt"></i> <?= $row['location'] ?></span>
                    <a href="#" class="btn-text">Apply Now <i class="fas fa-chevron-right"></i></a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div class="section alt">
    <div class="section-header">
        <h2 class="title">📅 Upcoming Meets</h2>
        <p class="subtitle">Reconnect with your roots</p>
    </div>
    <div class="grid events-grid">
        <?php
        $res = $conn->query("SELECT * FROM events ORDER BY id DESC LIMIT 3");
        while($row = $res->fetch_assoc()){ ?>
            <div class="card event-card reveal">
                <div class="event-date">
                    <span class="day"><?= date('d', strtotime($row['event_date'])) ?></span>
                    <span class="month"><?= date('M', strtotime($row['event_date'])) ?></span>
                </div>
                <div class="event-content">
                    <h3><?= $row['title'] ?></h3>
                    <p><i class="far fa-clock"></i> Limited seats available</p>
                    <a href="#" class="btn-small">Secure Spot</a>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<div class="section">
    <div class="section-header">
        <h2 class="title">👨‍🎓 Notable Alumni</h2>
        <p class="subtitle">Inspiration for the next generation</p>
    </div>
    <div class="grid alumni-grid">
        <?php
        $res = $conn->query("SELECT * FROM alumni ORDER BY id DESC LIMIT 4");
        while($row = $res->fetch_assoc()){ ?>
            <div class="card alumni-card reveal">
                <div class="alumni-img">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['name']) ?>&background=ff3b3b&color=fff&bold=true" alt="User">
                </div>
                <div class="alumni-info">
                    <h3><?= $row['name'] ?></h3>
                    <p class="course"><?= $row['course'] ?> • <?= $row['batch'] ?></p>
                    <div class="work-tag">
                        <i class="fas fa-external-link-alt"></i> <?= $row['company'] ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
<script src="assets/js/script.js"></script>

<?php include("includes/footer.php"); ?>