<?php
$pageTitle = "AlumniX | Home";
include(__DIR__ . "/includes/header.php");
include(__DIR__ . "/includes/db.php");

function homeEsc($value) {
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function homeCount(mysqli $conn, string $sql): int {
    $result = $conn->query($sql);
    if ($result && ($row = $result->fetch_assoc()) && isset($row["total"])) {
        return (int) $row["total"];
    }
    return 0;
}

$alumniCount = homeCount($conn, "SELECT COUNT(*) AS total FROM alumni");
$eventCount = homeCount($conn, "SELECT COUNT(*) AS total FROM events");
$jobCount = homeCount($conn, "SELECT COUNT(*) AS total FROM jobs WHERE status='approved'");

$latestAlumni = $conn->query("SELECT id, name, company, course, image FROM alumni ORDER BY id DESC LIMIT 8");
$upcomingEvents = $conn->query("SELECT id, title, event_date, location, image FROM events ORDER BY event_date ASC LIMIT 4");
$recentJobs = $conn->query("SELECT id, title, company, location, description FROM jobs WHERE status='approved' ORDER BY id DESC LIMIT 5");
?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

<style>
    :root {
        --home-bg: #06080f;
        --home-surface: rgba(21, 27, 43, 0.72);
        --home-surface-soft: rgba(255, 255, 255, 0.04);
        --home-line: rgba(255, 255, 255, 0.12);
        --home-text: #f8fafc;
        --home-muted: #9ba8c1;
        --home-accent: #ff4d4d;
        --home-accent-soft: rgba(255, 77, 77, 0.16);
    }

    body {
        background:
            radial-gradient(circle at 12% 15%, rgba(255, 77, 77, 0.17), transparent 36%),
            radial-gradient(circle at 88% 72%, rgba(49, 130, 246, 0.2), transparent 38%),
            var(--home-bg);
        color: var(--home-text);
    }

    .home-shell {
        overflow: hidden;
    }

    .hero {
        position: relative;
        min-height: 100vh;
        display: grid;
        grid-template-columns: 1.15fr 0.85fr;
        gap: 30px;
        align-items: end;
        padding: 170px 7% 70px;
    }

    .hero-video {
        position: absolute;
        inset: 0;
        z-index: 0;
        overflow: hidden;
    }

    .hero-video video {
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.36) contrast(1.1);
        transform: scale(1.06);
    }

    .hero-layer {
        position: absolute;
        inset: 0;
        z-index: 1;
        background:
            linear-gradient(180deg, rgba(2, 6, 16, 0.18) 0%, rgba(2, 6, 16, 0.78) 70%, rgba(2, 6, 16, 0.95) 100%),
            radial-gradient(circle at 18% 8%, rgba(255, 77, 77, 0.22), transparent 34%);
    }

    .hero-copy,
    .hero-panel {
        position: relative;
        z-index: 2;
    }

    .hero-kicker {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        margin-bottom: 18px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.16);
        font-size: 12px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #f2f5fb;
        font-weight: 600;
    }

    .hero-copy h1 {
        margin: 0;
        max-width: 800px;
        font-size: clamp(2.3rem, 5.6vw, 5.6rem);
        line-height: 0.94;
        letter-spacing: -0.04em;
    }

    .hero-copy h1 .accent {
        color: var(--home-accent);
    }

    .hero-copy p {
        margin-top: 20px;
        max-width: 680px;
        color: #ced7e9;
        font-size: clamp(1rem, 1.5vw, 1.2rem);
        line-height: 1.75;
    }

    .hero-actions {
        margin-top: 30px;
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
    }

    .btn-main,
    .btn-soft {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        text-decoration: none;
        border-radius: 999px;
        padding: 13px 24px;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        transition: transform 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
    }

    .btn-main {
        color: #fff;
        background: linear-gradient(120deg, #ff4d4d, #ff7a45);
        box-shadow: 0 14px 28px rgba(255, 77, 77, 0.35);
    }

    .btn-main:hover {
        transform: translateY(-2px);
        box-shadow: 0 18px 34px rgba(255, 77, 77, 0.42);
    }

    .btn-soft {
        color: #f4f7ff;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .btn-soft:hover {
        transform: translateY(-2px);
        background: rgba(255, 255, 255, 0.17);
    }

    .hero-panel {
        margin-left: auto;
        width: min(390px, 100%);
        border-radius: 26px;
        padding: 24px;
        background: var(--home-surface);
        border: 1px solid var(--home-line);
        backdrop-filter: blur(14px);
        box-shadow: 0 30px 50px rgba(0, 0, 0, 0.35);
    }

    .hero-panel h3 {
        margin: 0 0 18px;
        font-size: 1.1rem;
        letter-spacing: -0.01em;
    }

    .hero-list {
        list-style: none;
        display: grid;
        gap: 10px;
    }

    .hero-list li {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 11px 12px;
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }

    .hero-list li span {
        color: #d8e1f2;
        font-size: 13px;
    }

    .hero-list li strong {
        color: #fff;
        font-size: 14px;
    }

    .hero-note {
        margin-top: 15px;
        color: var(--home-muted);
        font-size: 12px;
        line-height: 1.6;
    }

    .section-wrap {
        padding: 25px 7% 0;
    }

    .section {
        margin-bottom: 36px;
        border-radius: 26px;
        border: 1px solid var(--home-line);
        background: linear-gradient(150deg, rgba(17, 24, 39, 0.74), rgba(11, 14, 26, 0.88));
        backdrop-filter: blur(12px);
        padding: 26px;
    }

    .section-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 24px;
    }

    .section-kicker {
        font-size: 12px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #ff9a7c;
        font-weight: 700;
    }

    .section-title {
        margin-top: 8px;
        font-size: clamp(1.7rem, 3.2vw, 2.9rem);
        letter-spacing: -0.03em;
        line-height: 1.05;
    }

    .section-copy {
        max-width: 620px;
        color: var(--home-muted);
        line-height: 1.7;
        font-size: 14px;
    }

    .section-link {
        text-decoration: none;
        color: #fff;
        font-size: 13px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 10px 16px;
        border-radius: 999px;
        background: var(--home-accent-soft);
        border: 1px solid rgba(255, 77, 77, 0.24);
        white-space: nowrap;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 14px;
    }

    .stat-card {
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: rgba(255, 255, 255, 0.04);
        padding: 20px 16px;
    }

    .stat-card strong {
        display: block;
        font-size: clamp(1.8rem, 3.8vw, 2.8rem);
        letter-spacing: -0.04em;
    }

    .stat-card span {
        display: block;
        margin-top: 8px;
        color: var(--home-muted);
        font-size: 13px;
    }

    .alumni-track {
        display: grid;
        grid-auto-flow: column;
        grid-auto-columns: minmax(240px, 1fr);
        gap: 14px;
        overflow-x: auto;
        padding-bottom: 6px;
        scrollbar-width: thin;
    }

    .alumni-track::-webkit-scrollbar {
        height: 8px;
    }

    .alumni-track::-webkit-scrollbar-thumb {
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.18);
    }

    .alumni-card {
        border-radius: 20px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.04);
        padding: 18px;
        transition: transform 0.28s ease, border-color 0.28s ease;
    }

    .alumni-card:hover {
        transform: translateY(-4px);
        border-color: rgba(255, 77, 77, 0.5);
    }

    .alumni-top {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .alumni-avatar {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        object-fit: cover;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .alumni-card h4 {
        margin: 0;
        font-size: 1rem;
    }

    .alumni-card p {
        margin: 5px 0 0;
        color: #d3dcf0;
        font-size: 13px;
    }

    .alumni-badge {
        margin-top: 14px;
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 7px 12px;
        background: rgba(255, 255, 255, 0.06);
        color: var(--home-muted);
        font-size: 11px;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }

    .event-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .event-card {
        position: relative;
        border-radius: 20px;
        min-height: 260px;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.13);
        background: rgba(255, 255, 255, 0.06);
    }

    .event-card img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        filter: brightness(0.65);
        transition: transform 0.45s ease;
    }

    .event-card:hover img {
        transform: scale(1.06);
    }

    .event-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(4, 7, 14, 0.12), rgba(4, 7, 14, 0.9));
    }

    .event-content {
        position: absolute;
        left: 16px;
        right: 16px;
        bottom: 16px;
        z-index: 2;
    }

    .event-date {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 10px;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(255, 77, 77, 0.82);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .event-content h4 {
        margin: 0;
        font-size: 1.2rem;
        line-height: 1.28;
    }

    .event-content p {
        margin-top: 8px;
        color: #d0d9ed;
        font-size: 13px;
    }

    .job-stack {
        display: grid;
        gap: 12px;
    }

    .job-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, 0.11);
        background: rgba(255, 255, 255, 0.04);
        padding: 16px;
    }

    .job-main h4 {
        margin: 0;
        font-size: 1.06rem;
    }

    .job-meta {
        margin-top: 6px;
        color: #cad3e5;
        font-size: 13px;
    }

    .job-desc {
        margin-top: 8px;
        color: var(--home-muted);
        font-size: 13px;
        line-height: 1.6;
        max-width: 660px;
    }

    .job-link {
        text-decoration: none;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 11px 16px;
        border-radius: 999px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        background: rgba(255, 255, 255, 0.08);
        white-space: nowrap;
    }

    .empty-state {
        border-radius: 18px;
        border: 1px dashed rgba(255, 255, 255, 0.24);
        background: rgba(255, 255, 255, 0.03);
        padding: 24px;
        color: var(--home-muted);
        text-align: center;
    }

    .reveal {
        opacity: 0;
        transform: translateY(26px);
    }

    @media (max-width: 1100px) {
        .hero {
            grid-template-columns: 1fr;
            align-items: center;
            padding-bottom: 52px;
        }

        .hero-panel {
            margin: 0;
            width: 100%;
        }

        .stats-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .hero {
            padding: 135px 5% 48px;
        }

        .section-wrap {
            padding: 10px 5% 0;
        }

        .section {
            padding: 20px;
        }

        .section-head {
            flex-direction: column;
            align-items: flex-start;
        }

        .event-grid {
            grid-template-columns: 1fr;
        }

        .job-card {
            flex-direction: column;
            align-items: flex-start;
        }

        .job-link {
            width: 100%;
            text-align: center;
        }
    }

    @media (max-width: 540px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .alumni-track {
            grid-auto-columns: minmax(220px, 84%);
        }
    }
</style>

<main class="home-shell">
    <section class="hero">
        <div class="hero-video">
            <video autoplay muted loop playsinline>
                <source src="images/hero.mp4" type="video/mp4">
            </video>
        </div>
        <div class="hero-layer"></div>

        <div class="hero-copy reveal">
            <span class="hero-kicker">
                <i class="fa-solid fa-bolt"></i>
                G H Raisoni Alumni Network
            </span>
            <h1>Meet alumni. Build <span class="accent">real opportunities</span>. Move faster together.</h1>
            <p>
                AlumniX brings mentors, hiring partners, founders, and ambitious grads into one focused community.
                Discover events, crack referrals, and grow with people who already know the path.
            </p>
            <div class="hero-actions">
                <a href="registration.php" class="btn-main">Join The Network</a>
                <a href="events.php" class="btn-soft">See Upcoming Events</a>
            </div>
        </div>

        <aside class="hero-panel reveal">
            <h3>Network Pulse</h3>
            <ul class="hero-list">
                <li>
                    <span>Active Alumni</span>
                    <strong><?= number_format($alumniCount) ?>+</strong>
                </li>
                <li>
                    <span>Upcoming Events</span>
                    <strong><?= number_format($eventCount) ?></strong>
                </li>
                <li>
                    <span>Open Jobs</span>
                    <strong><?= number_format($jobCount) ?></strong>
                </li>
            </ul>
            <p class="hero-note">
                Use this network as your unfair advantage: mentorship, events, and opportunities curated for alumni.
            </p>
        </aside>
    </section>

    <div class="section-wrap">
        <section class="section reveal">
            <div class="stats-grid">
                <article class="stat-card">
                    <strong><?= number_format($alumniCount) ?>+</strong>
                    <span>Verified Alumni Profiles</span>
                </article>
                <article class="stat-card">
                    <strong><?= number_format($eventCount) ?></strong>
                    <span>Community Events Planned</span>
                </article>
                <article class="stat-card">
                    <strong><?= number_format($jobCount) ?></strong>
                    <span>Approved Job Listings</span>
                </article>
                <article class="stat-card">
                    <strong>24x7</strong>
                    <span>Access To Network Resources</span>
                </article>
            </div>
        </section>

        <section class="section">
            <div class="section-head reveal">
                <div>
                    <span class="section-kicker">Featured Alumni</span>
                    <h2 class="section-title">People You Can Learn From Right Now</h2>
                    <p class="section-copy">
                        Find seniors across tech, product, business, and leadership. Reach out, learn fast, and build
                        genuine connections.
                    </p>
                </div>
                <a href="alumni.php" class="section-link">View Directory</a>
            </div>

            <div class="alumni-track reveal">
                <?php if ($latestAlumni && $latestAlumni->num_rows > 0): ?>
                    <?php while ($alumni = $latestAlumni->fetch_assoc()): ?>
                        <?php
                        $name = homeEsc($alumni["name"] ?? "Alumni");
                        $company = homeEsc($alumni["company"] ?? "Independent");
                        $course = homeEsc($alumni["course"] ?? "Community Member");
                        $avatar = "";
                        if (!empty($alumni["image"])) {
                            $raw = (string) $alumni["image"];
                            $localPath = __DIR__ . "/uploads/profiles/" . $raw;
                            if (file_exists($localPath)) {
                                $avatar = "uploads/profiles/" . rawurlencode($raw);
                            }
                        }
                        if ($avatar === "") {
                            $avatar = "https://ui-avatars.com/api/?name=" . urlencode((string) $alumni["name"]) . "&background=1f2937&color=fff&size=160";
                        }
                        ?>
                        <article class="alumni-card">
                            <div class="alumni-top">
                                <img class="alumni-avatar" src="<?= homeEsc($avatar) ?>" alt="<?= $name ?>">
                                <div>
                                    <h4><?= $name ?></h4>
                                    <p><?= $company ?></p>
                                </div>
                            </div>
                            <span class="alumni-badge"><?= $course ?></span>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">No alumni profiles available yet.</div>
                <?php endif; ?>
            </div>
        </section>

        <section class="section">
            <div class="section-head reveal">
                <div>
                    <span class="section-kicker">Upcoming Events</span>
                    <h2 class="section-title">Meetups, Summits, and Career Rooms</h2>
                    <p class="section-copy">
                        Join discussions with peers and industry leaders. Attend the next event and turn connections
                        into momentum.
                    </p>
                </div>
                <a href="events.php" class="section-link">Explore Events</a>
            </div>

            <div class="event-grid reveal">
                <?php if ($upcomingEvents && $upcomingEvents->num_rows > 0): ?>
                    <?php while ($event = $upcomingEvents->fetch_assoc()): ?>
                        <?php
                        $eventTitle = homeEsc($event["title"] ?? "Community Event");
                        $eventLocation = homeEsc($event["location"] ?: "Campus Venue");
                        $eventDateRaw = $event["event_date"] ?? "";
                        $eventDate = $eventDateRaw ? date("d M Y", strtotime((string) $eventDateRaw)) : "Date TBA";
                        $eventImage = "https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=1200&auto=format&fit=crop";
                        if (!empty($event["image"])) {
                            $raw = (string) $event["image"];
                            $localPath = __DIR__ . "/uploads/events/" . $raw;
                            if (file_exists($localPath)) {
                                $eventImage = "uploads/events/" . rawurlencode($raw);
                            }
                        }
                        ?>
                        <article class="event-card">
                            <img src="<?= homeEsc($eventImage) ?>" alt="<?= $eventTitle ?>">
                            <div class="event-overlay"></div>
                            <div class="event-content">
                                <span class="event-date"><i class="fa-regular fa-calendar"></i> <?= homeEsc($eventDate) ?></span>
                                <h4><?= $eventTitle ?></h4>
                                <p><i class="fa-solid fa-location-dot"></i> <?= $eventLocation ?></p>
                            </div>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">No events scheduled right now. Check back soon.</div>
                <?php endif; ?>
            </div>
        </section>

        <section class="section">
            <div class="section-head reveal">
                <div>
                    <span class="section-kicker">Career Board</span>
                    <h2 class="section-title">Latest Roles Shared By The Community</h2>
                    <p class="section-copy">
                        Alumni-posted openings from trusted companies. Use this board for faster hiring loops and
                        stronger referrals.
                    </p>
                </div>
                <a href="jobs.php" class="section-link">Open Jobs</a>
            </div>

            <div class="job-stack reveal">
                <?php if ($recentJobs && $recentJobs->num_rows > 0): ?>
                    <?php while ($job = $recentJobs->fetch_assoc()): ?>
                        <?php
                        $jobTitle = homeEsc($job["title"] ?? "Untitled Role");
                        $jobCompany = homeEsc($job["company"] ?? "Unknown Company");
                        $jobLocation = homeEsc($job["location"] ?: "Remote");
                        $rawDesc = trim((string) ($job["description"] ?? ""));
                        if ($rawDesc === "") {
                            $rawDesc = "Role details are available on the job board.";
                        }
                        if (strlen($rawDesc) > 170) {
                            $rawDesc = substr($rawDesc, 0, 167) . "...";
                        }
                        $jobDesc = homeEsc($rawDesc);
                        ?>
                        <article class="job-card">
                            <div class="job-main">
                                <h4><?= $jobTitle ?></h4>
                                <p class="job-meta"><?= $jobCompany ?> . <?= $jobLocation ?></p>
                                <p class="job-desc"><?= $jobDesc ?></p>
                            </div>
                            <a class="job-link" href="jobs.php">Apply Now</a>
                        </article>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">No active openings yet. New roles will appear here.</div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>

<script>
    (function () {
        const reveals = document.querySelectorAll(".reveal");
        const showAll = () => {
            reveals.forEach((node) => {
                node.style.opacity = "1";
                node.style.transform = "translateY(0)";
            });
        };

        if (!window.gsap || !window.ScrollTrigger) {
            showAll();
            return;
        }

        const gsap = window.gsap;
        gsap.registerPlugin(window.ScrollTrigger);

        gsap.from(".hero-copy", {
            y: 32,
            opacity: 0,
            duration: 0.9,
            ease: "power3.out"
        });

        gsap.from(".hero-panel", {
            y: 36,
            opacity: 0,
            duration: 1.05,
            delay: 0.15,
            ease: "power3.out"
        });

        reveals.forEach((node, index) => {
            gsap.to(node, {
                opacity: 1,
                y: 0,
                duration: 0.85,
                ease: "power2.out",
                delay: index % 4 === 0 ? 0 : 0.05,
                scrollTrigger: {
                    trigger: node,
                    start: "top 88%",
                    once: true
                }
            });
        });
    })();
</script>

<?php include(__DIR__ . "/includes/footer.php"); ?>
