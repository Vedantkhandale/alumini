<?php
require_once __DIR__ . "/helpers.php";
adminOnly();

if (isset($_POST["add"])) {
    $title = mysqli_real_escape_string($conn, $_POST["title"]);
    $desc = mysqli_real_escape_string($conn, $_POST["desc"]);
    $date = mysqli_real_escape_string($conn, $_POST["date"]);

    if ($title !== "" && $date !== "") {
        $conn->query("INSERT INTO events (title, description, event_date) VALUES ('$title', '$desc', '$date')");
        header("Location: event.php?res=created");
        exit();
    }
}

if (isset($_GET["delete"])) {
    $id = (int) $_GET["delete"];
    $conn->query("DELETE FROM events WHERE id='$id'");
    header("Location: event.php?res=deleted");
    exit();
}

$events = adminRows($conn, "SELECT id, title, description, event_date FROM events ORDER BY event_date ASC, id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Events | AlumniX</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #fff9f3; --surface: #fff; --ink: #142338; --muted: #657489; --accent: #ff6b57; --line: rgba(20,35,56,.08); --shadow: 0 16px 34px rgba(20,35,56,.08); }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: "Manrope", sans-serif; background: linear-gradient(180deg, #fff9f3 0%, #f7f6f2 100%); color: var(--ink); }
        .shell { width: min(1180px, calc(100% - 32px)); margin: 0 auto; padding: 28px 0 40px; }
        .topbar, .panel, .event { background: rgba(255,255,255,.88); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,.86); box-shadow: var(--shadow); }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 18px; padding: 20px 24px; border-radius: 28px; }
        h1, h2 { font-family: "Space Grotesk", sans-serif; letter-spacing: -.05em; }
        h1 { font-size: 2rem; }
        .topbar p, .event p { color: var(--muted); margin-top: 6px; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 11px 16px; border-radius: 999px; font-weight: 800; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, var(--accent), #ff8b65); color: #fff; }
        .btn-soft { background: #fff; color: var(--ink); border: 1px solid var(--line); }
        .grid { display: grid; grid-template-columns: 420px minmax(0, 1fr); gap: 18px; margin-top: 18px; }
        .panel { padding: 24px; border-radius: 28px; }
        .panel p { color: var(--muted); margin-top: 8px; }
        .field { margin-top: 16px; }
        .field label { display: block; font-size: .82rem; font-weight: 800; text-transform: uppercase; letter-spacing: .12em; color: var(--muted); margin-bottom: 8px; }
        .field input, .field textarea { width: 100%; padding: 14px 16px; border-radius: 18px; border: 1px solid var(--line); background: #fff; }
        .field textarea { min-height: 120px; resize: vertical; }
        .list { display: grid; gap: 16px; }
        .event { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 16px; padding: 22px; border-radius: 28px; }
        .date { display: inline-flex; align-items: center; padding: 8px 12px; border-radius: 999px; background: rgba(255,107,87,.12); color: var(--accent); font-size: .78rem; font-weight: 800; text-transform: uppercase; letter-spacing: .12em; }
        .event h2 { margin-top: 10px; font-size: 1.4rem; }
        @media (max-width: 900px) { .grid, .event { grid-template-columns: 1fr; } .topbar, .actions { flex-direction: column; align-items: stretch; } .shell { width: min(100% - 18px, 1180px); } }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div>
                <h1>Manage Events</h1>
                <p>Create new events and keep the public event page updated.</p>
            </div>
            <div class="actions">
                <a href="admin_dashboard.php" class="btn btn-soft">Dashboard</a>
                <a href="jobs.php" class="btn btn-soft">Job Queue</a>
                <a href="logout.php" class="btn btn-primary">Logout</a>
            </div>
        </header>

        <section class="grid">
            <article class="panel">
                <h2>Add new event</h2>
                <p>Publish a meetup, reunion, or networking session to the public portal.</p>
                <form method="POST">
                    <div class="field">
                        <label>Title</label>
                        <input type="text" name="title" placeholder="Annual alumni meetup" required>
                    </div>
                    <div class="field">
                        <label>Description</label>
                        <textarea name="desc" placeholder="Share what the event is about."></textarea>
                    </div>
                    <div class="field">
                        <label>Date</label>
                        <input type="date" name="date" required>
                    </div>
                    <div class="field">
                        <button class="btn btn-primary" type="submit" name="add">Publish event</button>
                    </div>
                </form>
            </article>

            <article class="list">
                <?php if ($events): ?>
                    <?php foreach ($events as $event): ?>
                        <div class="event">
                            <div>
                                <span class="date"><?php echo !empty($event["event_date"]) ? adminE(date("d M Y", strtotime($event["event_date"]))) : "TBD"; ?></span>
                                <h2><?php echo adminE($event["title"]); ?></h2>
                                <p><?php echo adminE($event["description"] ?: "No description added for this event yet."); ?></p>
                            </div>
                            <div class="actions">
                                <a href="?delete=<?php echo (int) $event["id"]; ?>" class="btn btn-soft" onclick="return confirm('Delete this event?');">Delete</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="event">
                        <div>
                            <span class="date">Empty</span>
                            <h2>No events listed</h2>
                            <p>Create the first event from the form on the left.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </article>
        </section>
    </div>
</body>
</html>
