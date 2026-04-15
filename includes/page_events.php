<?php
$pageTitle = "AlumniX | Events";
include(__DIR__ . "/header.php");
include(__DIR__ . "/db.php");
require_once(__DIR__ . "/public_helpers.php");

$events = fetchRows($conn, "SELECT id, title, description, event_date FROM events ORDER BY event_date ASC");
?>

<div class="public-shell">
    <section class="subpage-hero">
        <span class="section-kicker animate-hero">Community Calendar</span>
        <h1 class="subpage-title animate-hero">Events that keep the alumni circle warm.</h1>
        <p class="animate-hero">From networking evenings to campus reunions, this page keeps upcoming moments visible and easy to follow.</p>

        <div class="pill-row subpage-meta animate-hero">
            <span class="pill"><?php echo number_format(count($events)); ?> events listed</span>
            <span class="pill">Community-led meetups</span>
            <span class="pill">Updated from admin panel</span>
        </div>
    </section>

    <section class="section">
        <div class="section-head">
            <div>
                <span class="section-kicker">Upcoming</span>
                <h2 class="section-title">Plan the next reconnect.</h2>
                <p>Every event below is shown with its latest date and summary so the alumni network stays in sync.</p>
            </div>
        </div>

        <div class="subpage-grid">
            <?php if ($events): ?>
                <?php foreach ($events as $event): ?>
                    <?php $eventDate = !empty($event["event_date"]) ? strtotime($event["event_date"]) : false; ?>
                    <article class="card-panel reveal" id="event-<?php echo (int) $event["id"]; ?>">
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

                        <p class="card-copy"><?php echo e($event["description"] ?: "Networking, conversations, and community updates wrapped into one alumni meetup."); ?></p>

                        <div class="action-row">
                            <a href="registration.php" class="card-link">Join the community</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state reveal">No events are scheduled right now. Add one from the admin panel and it will show up here instantly.</div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include(__DIR__ . "/footer.php"); ?>
