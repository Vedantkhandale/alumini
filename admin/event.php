<?php
require_once __DIR__ . "/helpers.php";
adminOnly();

if (isset($_POST["add"])) {
    $title = mysqli_real_escape_string($conn, $_POST["title"]);
    $desc = mysqli_real_escape_string($conn, $_POST["desc"]);
    $date = mysqli_real_escape_string($conn, $_POST["date"]);
    $location = mysqli_real_escape_string($conn, $_POST["location"] ?? "Campus Hall");

    $conn->query("INSERT INTO events (title, description, event_date, location) VALUES ('$title', '$desc', '$date', '$location')");
    adminSetFlash("success", "Event published successfully.");
    header("Location: event.php");
    exit();
}

if (isset($_GET["delete"])) {
    $id = (int) $_GET["delete"];
    $conn->query("DELETE FROM events WHERE id={$id}");
    adminSetFlash("warning", "Event deleted.");
    header("Location: event.php");
    exit();
}

$flash = adminPullFlash();
$stats = [
    "total" => adminCount($conn, "SELECT COUNT(*) FROM events"),
    "upcoming" => adminCount($conn, "SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()"),
    "this_month" => adminCount($conn, "SELECT COUNT(*) FROM events WHERE YEAR(event_date) = YEAR(CURDATE()) AND MONTH(event_date) = MONTH(CURDATE())"),
];

$events = adminRows($conn, "SELECT * FROM events ORDER BY event_date ASC, id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events Manager | AlumniX Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #ff4d4d;
            --ink: #0f172a;
            --muted: #64748b;
            --surface: rgba(255, 255, 255, 0.92);
            --line: rgba(148, 163, 184, 0.18);
            --bg: #f8fafc;
            --shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background:
                radial-gradient(circle at top left, rgba(255, 77, 77, 0.08), transparent 28%),
                radial-gradient(circle at bottom right, rgba(15, 23, 42, 0.06), transparent 24%),
                var(--bg);
            color: var(--ink);
            min-height: 100vh;
        }

        .shell {
            width: min(1240px, calc(100% - 36px));
            margin: 0 auto;
            padding: 28px 0 36px;
        }

        .topbar,
        .stat-card,
        .panel,
        .event-card,
        .flash {
            background: var(--surface);
            backdrop-filter: blur(18px);
            border: 1px solid rgba(255, 255, 255, 0.88);
            box-shadow: var(--shadow);
        }

        .topbar {
            border-radius: 32px;
            padding: 24px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(255, 77, 77, 0.12);
            color: var(--accent);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .topbar h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(30px, 4vw, 44px);
            letter-spacing: -0.05em;
            line-height: 0.96;
        }

        .topbar p {
            margin-top: 10px;
            color: var(--muted);
            line-height: 1.7;
            max-width: 620px;
        }

        .nav {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 18px;
            border-radius: 999px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
            border: 1px solid transparent;
            transition: transform 0.25s ease;
        }

        .btn-primary { color: #fff; background: linear-gradient(135deg, var(--accent), #ff8b65); }
        .btn-soft { color: var(--ink); background: #fff; border-color: var(--line); }
        .btn:hover { transform: translateY(-3px); }

        .flash {
            margin-top: 18px;
            padding: 18px 20px;
            border-radius: 22px;
        }

        .flash.success { background: rgba(236, 253, 245, 0.96); border-color: rgba(16, 185, 129, 0.22); }
        .flash.warning { background: rgba(255, 251, 235, 0.96); border-color: rgba(245, 158, 11, 0.22); }
        .flash h3 { font-size: 15px; font-weight: 800; }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin-top: 22px;
        }

        .stat-card {
            border-radius: 28px;
            padding: 22px;
        }

        .stat-label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .stat-value {
            display: block;
            margin-top: 8px;
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(34px, 4vw, 46px);
            letter-spacing: -0.05em;
        }

        .layout {
            display: grid;
            grid-template-columns: 400px minmax(0, 1fr);
            gap: 18px;
            margin-top: 22px;
        }

        .panel {
            border-radius: 30px;
            padding: 24px;
        }

        .panel h2 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px;
            letter-spacing: -0.04em;
            margin-bottom: 6px;
        }

        .panel p {
            color: var(--muted);
            line-height: 1.7;
            font-size: 13px;
        }

        .form-stack {
            display: grid;
            gap: 14px;
            margin-top: 18px;
        }

        .input,
        textarea {
            width: 100%;
            border-radius: 16px;
            border: 1px solid var(--line);
            padding: 13px 15px;
            font: inherit;
            outline: none;
            background: rgba(255, 255, 255, 0.96);
        }

        textarea { min-height: 140px; resize: vertical; }
        .input:focus,
        textarea:focus { border-color: rgba(255, 77, 77, 0.4); }

        .submit-btn {
            width: 100%;
            border: none;
            border-radius: 16px;
            padding: 14px 16px;
            color: #fff;
            font-size: 13px;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent), #ff8b65);
            cursor: pointer;
        }

        .event-list {
            display: grid;
            gap: 16px;
            margin-top: 18px;
        }

        .event-card {
            border-radius: 28px;
            padding: 20px;
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: 16px;
            align-items: center;
        }

        .date-box {
            min-width: 84px;
            min-height: 84px;
            border-radius: 24px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, rgba(255, 77, 77, 0.12), rgba(255, 139, 101, 0.18));
            border: 1px solid rgba(255, 77, 77, 0.12);
            color: var(--accent);
            text-align: center;
        }

        .date-box strong {
            display: block;
            font-size: 28px;
            font-family: 'Space Grotesk', sans-serif;
            line-height: 1;
        }

        .date-box span {
            display: block;
            margin-top: 6px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .event-card h3 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 22px;
            letter-spacing: -0.04em;
        }

        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 12px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid var(--line);
        }

        .event-card p {
            margin-top: 12px;
            color: #334155;
            line-height: 1.7;
            font-size: 13px;
        }

        .delete-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 11px 14px;
            border-radius: 14px;
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
            background: linear-gradient(135deg, #ef4444, #f87171);
        }

        .empty {
            padding: 40px 24px;
            text-align: center;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.84);
            color: var(--muted);
            border: 1px dashed var(--line);
        }

        @media (max-width: 1080px) {
            .stats { grid-template-columns: 1fr; }
            .layout { grid-template-columns: 1fr; }
            .event-card { grid-template-columns: 1fr; }
        }

        @media (max-width: 760px) {
            .shell { width: calc(100% - 20px); }
            .topbar { flex-direction: column; align-items: flex-start; }
            .nav { width: 100%; justify-content: flex-start; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div>
                <div class="eyebrow"><i class="fas fa-calendar-days"></i> Event Operations</div>
                <h1>Launch events with less friction and more control.</h1>
                <p>Create fresh events from one side and manage the live schedule from the other without leaving the admin workspace.</p>
            </div>
            <nav class="nav">
                <a href="admin_dashboard.php" class="btn btn-soft"><i class="fas fa-grid-2"></i> Dashboard</a>
                <a href="alumni_list.php" class="btn btn-soft"><i class="fas fa-users"></i> Alumni</a>
                <a href="jobs.php" class="btn btn-soft"><i class="fas fa-briefcase"></i> Jobs</a>
                <a href="logout.php" class="btn btn-primary"><i class="fas fa-power-off"></i> Logout</a>
            </nav>
        </header>

        <?php if ($flash): ?>
            <section class="flash <?php echo adminE($flash["type"] ?? "success"); ?>">
                <h3><?php echo adminE($flash["message"] ?? "Update complete."); ?></h3>
            </section>
        <?php endif; ?>

        <section class="stats">
            <article class="stat-card">
                <span class="stat-label">Total Events</span>
                <span class="stat-value" style="color: var(--accent);"><?php echo number_format($stats["total"]); ?></span>
            </article>
            <article class="stat-card">
                <span class="stat-label">Upcoming</span>
                <span class="stat-value"><?php echo number_format($stats["upcoming"]); ?></span>
            </article>
            <article class="stat-card">
                <span class="stat-label">This Month</span>
                <span class="stat-value"><?php echo number_format($stats["this_month"]); ?></span>
            </article>
        </section>

        <section class="layout">
            <aside class="panel">
                <h2>Create Event</h2>
                <p>Push a new event to the public site with title, date, location, and description.</p>
                <form method="POST" class="form-stack">
                    <input type="text" class="input" name="title" placeholder="Annual Alumni Meet 2026" required>
                    <input type="date" class="input" name="date" required>
                    <input type="text" class="input" name="location" placeholder="Main Auditorium">
                    <textarea name="desc" placeholder="What is this event about?" required></textarea>
                    <button type="submit" name="add" class="submit-btn">Publish Event</button>
                </form>
            </aside>

            <main class="panel">
                <h2>Live Schedule</h2>
                <p>Review what is already published and clear out any event that should no longer be visible.</p>
                <div class="event-list">
                    <?php if ($events): ?>
                        <?php foreach ($events as $event): ?>
                            <?php $timestamp = strtotime((string) $event["event_date"]); ?>
                            <article class="event-card">
                                <div class="date-box">
                                    <div>
                                        <strong><?php echo date('d', $timestamp); ?></strong>
                                        <span><?php echo date('M', $timestamp); ?></span>
                                    </div>
                                </div>
                                <div>
                                    <h3><?php echo adminE($event["title"]); ?></h3>
                                    <div class="event-meta">
                                        <span class="chip"><i class="fas fa-calendar"></i> <?php echo adminE(date('d M Y', $timestamp)); ?></span>
                                        <span class="chip"><i class="fas fa-location-dot"></i> <?php echo adminE($event["location"] ?: "TBA"); ?></span>
                                    </div>
                                    <p><?php echo adminE($event["description"] ?: "No description available."); ?></p>
                                </div>
                                <a href="?delete=<?php echo (int) $event["id"]; ?>" class="delete-btn" onclick="return confirm('Delete this event permanently?');">Delete</a>
                            </article>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty">
                            <i class="far fa-calendar-times" style="font-size: 36px; margin-bottom: 12px;"></i>
                            <p>No events scheduled yet.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </section>
    </div>
</body>
</html>
