<?php
require_once __DIR__ . "/helpers.php";
adminOnly();

if (isset($_POST["add"])) {
    $title = trim((string) ($_POST["title"] ?? ""));
    $desc = trim((string) ($_POST["desc"] ?? ""));
    $date = trim((string) ($_POST["date"] ?? ""));
    $location = trim((string) ($_POST["location"] ?? "Campus Hall"));

    $stmt = $conn->prepare("INSERT INTO events (title, description, event_date, location) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssss", $title, $desc, $date, $location);
        $stmt->execute();
        $stmt->close();
    }

    adminSetFlash("success", "Event published successfully.");
    header("Location: event.php");
    exit();
}

if (isset($_GET["delete"])) {
    $id = (int) $_GET["delete"];
    $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }

    adminSetFlash("warning", "Event deleted.");
    header("Location: event.php");
    exit();
}

$flash = adminPullFlash();
$stats = [
    "total" => adminCount($conn, "SELECT COUNT(*) FROM events"),
    "upcoming" => adminCount($conn, "SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()"),
    "this_month" => adminCount($conn, "SELECT COUNT(*) FROM events WHERE YEAR(event_date) = YEAR(CURDATE()) AND MONTH(event_date) = MONTH(CURDATE())"),
    "locations" => adminCount($conn, "SELECT COUNT(DISTINCT location) FROM events WHERE location IS NOT NULL AND location <> ''"),
];

$events = adminRows($conn, "SELECT * FROM events ORDER BY event_date ASC, id DESC");

adminRenderPageStart([
    "page_title" => "Events Manager | AlumniX Admin",
    "hero_badge" => "Event operations",
    "hero_title" => "Publish and manage events in one polished workspace.",
    "hero_text" => "The creation form and live schedule now share the same admin shell, with tighter spacing, better date hierarchy, and cleaner delete actions.",
    "active" => "events",
    "actions" => [
        ["href" => "admin_dashboard.php", "icon" => "fas fa-table-cells-large", "label" => "Dashboard", "variant" => "secondary"],
        ["href" => "jobs.php", "icon" => "fas fa-briefcase", "label" => "Moderate Jobs", "variant" => "primary"],
    ],
]);
?>

<?php adminRenderFlash($flash); ?>

<section class="metric-grid">
    <article class="metric-card">
        <div class="metric-label">Total Events</div>
        <span class="metric-value"><?php echo number_format($stats["total"]); ?></span>
        <div class="metric-note">All events currently stored across the alumni calendar.</div>
        <div class="metric-icon"><i class="fas fa-calendar-days"></i></div>
    </article>
    <article class="metric-card">
        <div class="metric-label">Upcoming</div>
        <span class="metric-value"><?php echo number_format($stats["upcoming"]); ?></span>
        <div class="metric-note">Schedule blocks that still have future visibility for the audience.</div>
        <div class="metric-icon"><i class="fas fa-bolt"></i></div>
    </article>
    <article class="metric-card">
        <div class="metric-label">This Month</div>
        <span class="metric-value"><?php echo number_format($stats["this_month"]); ?></span>
        <div class="metric-note">Current-month activity ready for communication and promotion.</div>
        <div class="metric-icon"><i class="fas fa-calendar-week"></i></div>
    </article>
    <article class="metric-card">
        <div class="metric-label">Locations</div>
        <span class="metric-value"><?php echo number_format($stats["locations"]); ?></span>
        <div class="metric-note">Distinct venues represented across the live events collection.</div>
        <div class="metric-icon"><i class="fas fa-location-dot"></i></div>
    </article>
</section>

<section class="split-grid">
    <aside class="panel-card">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Create Event</h2>
                <p class="panel-copy">Push a new event to the public site with title, date, location, and a short description.</p>
            </div>
        </div>

        <form method="POST" class="form-stack">
            <div class="field">
                <label for="title">Event title</label>
                <input id="title" type="text" name="title" placeholder="Annual Alumni Meet 2026" required>
            </div>
            <div class="field">
                <label for="date">Event date</label>
                <input id="date" type="date" name="date" required>
            </div>
            <div class="field">
                <label for="location">Location</label>
                <input id="location" type="text" name="location" placeholder="Main Auditorium">
            </div>
            <div class="field">
                <label for="desc">Description</label>
                <textarea id="desc" name="desc" placeholder="What is this event about?" required></textarea>
            </div>
            <button type="submit" name="add" class="submit-btn">
                <i class="fas fa-paper-plane"></i> Publish Event
            </button>
        </form>
    </aside>

    <section class="panel-card">
        <div class="panel-head">
            <div>
                <h2 class="panel-title">Live Schedule</h2>
                <p class="panel-copy">Review what is already published and remove anything that should no longer be visible.</p>
            </div>
            <span class="panel-link"><?php echo number_format(count($events)); ?> event(s)</span>
        </div>

        <?php if ($events): ?>
            <div class="record-list">
                <?php foreach ($events as $event): ?>
                    <article class="record-card">
                        <div class="date-tile">
                            <div>
                                <strong><?php echo adminE(adminFormatDate((string) $event["event_date"], "d", "--")); ?></strong>
                                <span><?php echo adminE(adminFormatDate((string) $event["event_date"], "M", "TBA")); ?></span>
                            </div>
                        </div>
                        <div>
                            <span class="record-title"><?php echo adminE($event["title"]); ?></span>
                            <div class="record-meta">
                                <?php echo adminE(adminFormatDate((string) $event["event_date"], "d M Y", "Date TBA")); ?> · <?php echo adminE($event["location"] ?: "Location TBA"); ?><br>
                                <?php echo adminE($event["description"] ?: "No description available."); ?>
                            </div>
                        </div>
                        <a href="?delete=<?php echo (int) $event["id"]; ?>" class="action-pill delete" onclick="return confirm('Delete this event permanently?');">Delete</a>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-calendar-xmark"></i>
                <p>No events scheduled yet.</p>
            </div>
        <?php endif; ?>
    </section>
</section>

<?php adminRenderPageEnd(); ?>
