<?php
include("includes/header.php");
include("includes/db.php");
?>

<style>
    :root {
        --primary: #ff3b3b;
        --primary-light: rgba(255, 59, 59, 0.1);
        --bg: #f8fafc;
        --card-bg: #ffffff;
        --text: #111;
        --muted: #64748b;
        --radius: 20px;
    }

    /* HERO (NO CHANGES) */
    .hero {
        position: relative;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-align: center;
        overflow: hidden;
    }

    .hero video {
        position: absolute;
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.6);
    }

    .hero::after {
        content: "";
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.6);
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero h1 {
        font-size: 60px;
        font-weight: 800;
    }

    .hero h1 span {
        color: var(--primary);
    }

    .hero p {
        margin: 15px 0 25px;
        opacity: 0.8;
    }

    .btn-main {
        background: var(--primary);
        padding: 14px 35px;
        border-radius: 40px;
        color: #fff;
        text-decoration: none;
        font-weight: 600;
    }

    /* SECTION HEADINGS */
    .section {
        padding: 100px 8%;
        background: var(--bg);
    }

    .section-head {
        margin-bottom: 50px;
        text-align: left;
    }

    .section-kicker {
        display: block;
        color: var(--primary);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 12px;
        letter-spacing: 2px;
        margin-bottom: 10px;
    }

    .title {
        font-size: 42px;
        font-weight: 800;
        letter-spacing: -1.5px;
        color: #111;
    }

    /* GRID & CARDS */
    .grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
    }

    .card {
        background: var(--card-bg);
        border-radius: var(--radius);
        padding: 30px;
        text-decoration: none;
        color: inherit;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0, 0, 0, 0.03);
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.08);
        border-color: var(--primary-light);
    }

    /* Event Specific Styling */
    .event-date-box {
        position: absolute;
        top: 20px;
        right: 20px;
        background: var(--primary-light);
        color: var(--primary);
        padding: 8px 12px;
        border-radius: 12px;
        text-align: center;
        min-width: 50px;
    }

    .event-date-box span {
        display: block;
        font-weight: 800;
        font-size: 18px;
        line-height: 1;
    }

    .event-date-box small {
        font-size: 10px;
        text-transform: uppercase;
        font-weight: 700;
    }

    .tag {
        display: inline-block;
        padding: 4px 12px;
        background: var(--primary-light);
        color: var(--primary);
        font-size: 11px;
        font-weight: 700;
        border-radius: 50px;
        width: fit-content;
    }

    .meta-info {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: var(--muted);
    }
</style>

<div class="hero">
    <video autoplay muted loop>
        <source src="images/hero.mp4">
    </video>
    <div class="hero-content">
        <h1>Alumni <span>Network</span></h1>
        <p>Connect • Grow • Explore</p>
        <a href="registration.php" class="btn-main">Join Now</a>
    </div>
</div>

<div class="section">
    <div class="section-head">
        <span class="section-kicker">Directory</span>
        <h2 class="title">Top <span>Alumni</span></h2>
    </div>

    <div class="grid">
        <?php
        $res = $conn->query("SELECT * FROM alumni ORDER BY id DESC LIMIT 4");
        while ($row = $res->fetch_assoc()) { ?>
            <a href="profile.php?id=<?= $row['id'] ?>" class="card">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($row['name']) ?>&background=ff3b3b&color=fff&bold=true" style="width:60px; height:60px; border-radius:15px; margin-bottom:10px;">
                <span class="tag"><?= $row['batch'] ?> Batch</span>
                <h3><?= $row['name'] ?></h3>
                <div class="meta-info"><i class="fas fa-building"></i> <?= $row['company'] ?></div>
            </a>
        <?php } ?>
    </div>
</div>

<div class="section" style="background:#fff; border-top: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9;">
    <div class="section-head">
        <span class="section-kicker">Upcoming</span>
        <h2 class="title">Community <span>Events</span></h2>
    </div>

    <div class="grid">
        <?php
        $res = $conn->query("SELECT * FROM events ORDER BY event_date ASC LIMIT 3");
        if ($res->num_rows > 0) {
            while ($row = $res->fetch_assoc()) {
                $eDate = strtotime($row['event_date']);
        ?>
                <a href="event_details.php?id=<?= $row['id'] ?>" class="card event-card">
                    <div class="event-date-box" style="
                    position: absolute; 
                    top: 25px; 
                    right: 25px; 
                    background: linear-gradient(135deg, #ff3b3b, #ff6b6b); 
                    color: #fff; 
                    padding: 10px; 
                    border-radius: 15px; 
                    text-align: center; 
                    min-width: 55px;
                    box-shadow: 0 8px 15px rgba(255, 59, 59, 0.2);
                ">
                        <span style="display: block; font-weight: 800; font-size: 20px; line-height: 1;"><?= date('d', $eDate) ?></span>
                        <small style="font-size: 10px; text-transform: uppercase; font-weight: 700; opacity: 0.9;"><?= date('M', $eDate) ?></small>
                    </div>

                    <span class="tag" style="background: var(--primary-light); color: var(--primary); margin-bottom: 10px;">
                        <i class="fas fa-calendar-check" style="margin-right: 5px; font-size: 10px;"></i> Alumni Meet
                    </span>

                    <h3 style="padding-right: 70px; font-size: 22px; font-weight: 800; color: #0f172a; margin-bottom: 15px;">
                        <?= htmlspecialchars($row['title']) ?>
                    </h3>

                    <div class="meta-wrap" style="display: flex; flex-direction: column; gap: 8px;">
                        <div class="meta-info" style="font-weight: 600; color: #475569;">
                            <i class="fas fa-clock" style="color: var(--primary); width: 20px;"></i>
                            <?= date('h:i A', strtotime($row['event_time'])) ?>
                        </div>
                        <div class="meta-info" style="font-weight: 600; color: #475569;">
                            <i class="fas fa-location-dot" style="color: var(--primary); width: 20px;"></i>
                            <?= htmlspecialchars($row['location']) ?>
                        </div>
                    </div>

                    <div style="margin-top: 20px; padding-top: 15px; border-top: 1px solid #f1f5f9; font-size: 13px; font-weight: 700; color: var(--primary); display: flex; align-items: center; gap: 5px;">
                        Get Invite <i class="fas fa-arrow-right" style="font-size: 11px;"></i>
                    </div>
                </a>
        <?php
            }
        } else {
            echo "<p style='color:var(--muted);'>No upcoming events found.</p>";
        }
        ?>
    </div>
</div>

<div class="section">
    <div class="section-head">
        <span class="section-kicker">Career</span>
        <h2 class="title">Latest <span>Jobs</span></h2>
    </div>

    <div class="grid">
        <?php
        $res = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC LIMIT 3");
        while ($row = $res->fetch_assoc()) { ?>
            <a href="job_details.php?id=<?= $row['id'] ?>" class="card">
                <div class="job-icon" style="width:50px; height:50px; background:#111; color:#fff; border-radius:15px; display:flex; align-items:center; justify-content:center; font-size:20px;">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h3><?= $row['title'] ?></h3>
                <div class="meta-info"><strong><?= $row['company'] ?></strong></div>
                <div class="meta-info"><i class="fas fa-location-dot"></i> <?= $row['location'] ?: 'Remote' ?></div>
                <div style="margin-top:10px; font-weight:700; font-size:12px; color:var(--primary);">
                    Apply Now <i class="fas fa-arrow-right"></i>
                </div>
            </a>
        <?php } ?>
    </div>
</div>

<?php include("includes/footer.php"); ?>