<?php
$pageTitle = "AlumniX | Home";
$bodyClass = "home-page";
include(__DIR__ . "/header.php");
include(__DIR__ . "/db.php");
require_once(__DIR__ . "/public_helpers.php");

$featuredJobs = fetchRows($conn, "SELECT id, title, company, location, apply_link, description FROM jobs WHERE status='approved' ORDER BY id DESC LIMIT 6");
$upcomingEvents = fetchRows($conn, "SELECT id, title, description, event_date FROM events ORDER BY event_date ASC LIMIT 4");
$featuredAlumni = fetchRows($conn, "SELECT id, name, course, batch, company FROM alumni ORDER BY id DESC LIMIT 4");

$stats = [
    ["value" => fetchCount($conn, "SELECT COUNT(*) FROM alumni"), "label" => "alumni stories"],
    ["value" => fetchCount($conn, "SELECT COUNT(*) FROM jobs WHERE status='approved'"), "label" => "live opportunities"],
    ["value" => fetchCount($conn, "SELECT COUNT(*) FROM events"), "label" => "community events"],
    ["value" => fetchCount($conn, "SELECT COUNT(DISTINCT location) FROM jobs WHERE status='approved' AND location <> ''"), "label" => "hiring hubs"],
];
?>

<section class="hero">
    <video autoplay muted loop playsinline class="hero-video">
        <source src="images/hero.mp4" type="video/mp4">
    </video>
    <div class="overlay"></div>
    <div class="hero-content">
        <h1 class="animate-hero">Alumni Connect</h1>
        <p class="animate-hero">Bridging the gap between Nagpur's talent and global success.</p>
        <a href="registration.php" class="btn-main animate-hero">Join the Elite Community</a>
    </div>
</section>

<div class="public-shell home-after-hero">
    <div class="stats-grid reveal">
        <?php foreach ($stats as $stat): ?>
            <div class="stat-card">
                <strong><?php echo number_format($stat["value"]); ?></strong>
                <span><?php echo e($stat["label"]); ?></span>
            </div>
        <?php endforeach; ?>
    </div>

    <section class="section">
        <div class="section-head">
            <div>
                <span class="section-kicker">Career Board</span>
                <h2 class="section-title">Roles worth forwarding.</h2>
                <p>Approved openings from the alumni community, ready to explore right now.</p>
            </div>
            <a href="jobs.php" class="section-link">See all jobs</a>
        </div>

        <div class="card-grid">
            <?php if ($featuredJobs): ?>
                <?php foreach ($featuredJobs as $job): ?>
                    <?php
                    $jobLink = !empty($job["apply_link"]) ? $job["apply_link"] : "jobs.php#job-" . (int) $job["id"];
                    $external = (bool) preg_match('/^(https?:\/\/|mailto:)/i', $jobLink);
                    ?>
                    <article class="card-panel reveal" id="job-<?php echo (int) $job["id"]; ?>">
                        <div class="panel-head">
                            <div class="icon-chip"><i class="fas fa-briefcase"></i></div>
                            <span class="tag">Approved</span>
                        </div>

                        <div>
                            <h3 class="card-title"><?php echo e($job["title"]); ?></h3>
                            <p class="card-subtitle"><?php echo e($job["company"]); ?></p>
                        </div>

                        <div class="meta-row">
                            <span class="meta-chip"><i class="fas fa-location-dot"></i> <?php echo e($job["location"] ?: "Flexible"); ?></span>
                        </div>

                        <p class="card-copy"><?php echo e($job["description"] ?: "Shared by an alumni member for the community."); ?></p>

                        <div class="action-row">
                            <a href="<?php echo e($jobLink); ?>" class="card-link" <?php echo $external ? 'target="_blank" rel="noopener noreferrer"' : ""; ?>><?php echo $external ? "Apply now" : "View listing"; ?></a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state reveal">No approved jobs are live yet. The moment alumni posts start flowing in, they will appear here.</div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <div>
                <span class="section-kicker">Meetups</span>
                <h2 class="section-title">Moments to reconnect.</h2>
                <p>Campus conversations, city meetups, and alumni gatherings that keep the circle active.</p>
            </div>
            <a href="events.php" class="section-link">View events</a>
        </div>

        <div class="card-grid">
            <?php if ($upcomingEvents): ?>
                <?php foreach ($upcomingEvents as $event): ?>
                    <?php $eventDate = !empty($event["event_date"]) ? strtotime($event["event_date"]) : false; ?>
                    <article class="card-panel reveal">
                        <div class="panel-head">
                            <div class="date-chip">
                                <strong><?php echo $eventDate ? date("d", $eventDate) : "--"; ?></strong>
                                <span><?php echo $eventDate ? date("M", $eventDate) : "TBD"; ?></span>
                            </div>
                            <span class="tag">Open</span>
                        </div>

                        <div>
                            <h3 class="card-title"><?php echo e($event["title"]); ?></h3>
                            <p class="card-subtitle"><?php echo $eventDate ? e(date("d M Y", $eventDate)) : "Date to be announced"; ?></p>
                        </div>

                        <p class="card-copy"><?php echo e($event["description"] ?: "Meet the network, swap stories, and stay close to what the community is building."); ?></p>

                        <div class="action-row">
                            <a href="events.php#event-<?php echo (int) $event["id"]; ?>" class="card-link">See event details</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state reveal">No events are listed right now. Add the next reunion or meetup from the admin side and it will show up here.</div>
            <?php endif; ?>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <div>
                <span class="section-kicker">Spotlight</span>
                <h2 class="section-title">Faces behind the momentum.</h2>
                <p>Real alumni journeys that help the next student imagine a bigger path.</p>
            </div>
            <a href="alumni.php" class="section-link">Browse alumni</a>
        </div>

        <div class="card-grid">
            <?php if ($featuredAlumni): ?>
                <?php foreach ($featuredAlumni as $alumnus): ?>
                    <?php $initial = strtoupper(substr((string) ($alumnus["name"] ?? "A"), 0, 1)); ?>
                    <article class="card-panel reveal">
                        <div class="panel-head">
                            <div class="avatar-chip"><?php echo e($initial); ?></div>
                            <span class="tag"><?php echo e($alumnus["batch"] ?: "Alumni"); ?></span>
                        </div>

                        <div>
                            <h3 class="card-title"><?php echo e($alumnus["name"]); ?></h3>
                            <p class="card-subtitle"><?php echo e($alumnus["course"] ?: "Community member"); ?></p>
                        </div>

                        <div class="meta-row">
                            <span class="meta-chip"><i class="fas fa-building"></i> <?php echo e($alumnus["company"] ?: "Company not listed"); ?></span>
                        </div>

                        <div class="action-row">
                            <a href="alumni.php#alumni-<?php echo (int) $alumnus["id"]; ?>" class="card-link">View profile card</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state reveal">No alumni profiles are published yet. Once the directory grows, the spotlight section will update automatically.</div>
            <?php endif; ?>
        </div>
    </section>

    <section class="cta-strip reveal">
        <div>
            <span class="section-kicker">Ready to join in</span>
            <h3>Make the portal feel alive with your profile, opportunities, and updates.</h3>
            <p>Register once, then start posting jobs, following events, and staying visible inside the community.</p>
        </div>
        <div class="action-row">
            <a href="registration.php" class="btn-main">Create account</a>
            <a href="login.php" class="btn-soft">Login</a>
        </div>
    </section>
</div>

<?php include(__DIR__ . "/footer.php"); ?>
