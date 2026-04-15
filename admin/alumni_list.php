<?php
require_once __DIR__ . "/helpers.php";
adminOnly();

if (isset($_GET["approve"])) {
    $id = (int) $_GET["approve"];
    $conn->query("UPDATE users SET status='approved' WHERE id='$id'");
    header("Location: alumni_list.php?res=approved");
    exit();
}

$alumniUsers = adminRows($conn, "SELECT id, full_name, email, student_id, batch, graduation_year, status FROM users WHERE role='alumni' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Directory | AlumniX Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <style>
        :root { --bg: #fff9f3; --surface: #fff; --ink: #142338; --muted: #657489; --accent: #ff6b57; --line: rgba(20,35,56,.08); --shadow: 0 16px 34px rgba(20,35,56,.08); }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: "Manrope", sans-serif; background: linear-gradient(180deg, #fff9f3 0%, #f7f6f2 100%); color: var(--ink); }
        .shell { width: min(1180px, calc(100% - 32px)); margin: 0 auto; padding: 28px 0 40px; }
        .topbar, .row { background: rgba(255,255,255,.88); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,.86); box-shadow: var(--shadow); }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 18px; padding: 20px 24px; border-radius: 28px; }
        h1 { font-family: "Space Grotesk", sans-serif; font-size: 2rem; letter-spacing: -.05em; }
        .topbar p { color: var(--muted); margin-top: 6px; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 11px 16px; border-radius: 999px; font-weight: 800; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, var(--accent), #ff8b65); color: #fff; }
        .btn-soft { background: #fff; color: var(--ink); border: 1px solid var(--line); }
        .list { display: grid; gap: 16px; margin-top: 18px; }
        .row { display: grid; grid-template-columns: minmax(0, 1fr) auto; gap: 16px; padding: 22px; border-radius: 28px; }
        .meta { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 14px; }
        .chip { display: inline-flex; align-items: center; gap: 8px; padding: 8px 12px; border-radius: 999px; background: #f8fafc; border: 1px solid var(--line); color: var(--muted); font-size: .84rem; font-weight: 700; }
        .status { text-transform: uppercase; letter-spacing: .12em; font-size: .75rem; font-weight: 800; color: var(--accent); }
        .row h2 { font-family: "Space Grotesk", sans-serif; font-size: 1.45rem; letter-spacing: -.04em; }
        @media (max-width: 860px) { .topbar, .row { grid-template-columns: 1fr; } .topbar, .actions { flex-direction: column; align-items: stretch; } .shell { width: min(100% - 18px, 1180px); } }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div>
                <h1>Alumni Directory</h1>
                <p>Review all alumni accounts with their current approval status and key profile details.</p>
            </div>
            <div class="actions">
                <a href="admin_dashboard.php" class="btn btn-soft">Dashboard</a>
                <a href="jobs.php" class="btn btn-soft">Jobs</a>
                <a href="event.php" class="btn btn-primary">Events</a>
            </div>
        </header>

        <section class="list">
            <?php if ($alumniUsers): ?>
                <?php foreach ($alumniUsers as $user): ?>
                    <article class="row">
                        <div>
                            <span class="status"><?php echo adminE($user["status"] ?: "unknown"); ?></span>
                            <h2><?php echo adminE($user["full_name"]); ?></h2>
                            <div class="meta">
                                <span class="chip"><?php echo adminE($user["email"]); ?></span>
                                <span class="chip"><?php echo adminE($user["student_id"] ?: "No student ID"); ?></span>
                                <span class="chip"><?php echo adminE($user["batch"] ?: "Batch not listed"); ?></span>
                                <span class="chip"><?php echo adminE($user["graduation_year"] ?: "Year not listed"); ?></span>
                            </div>
                        </div>
                        <div class="actions">
                            <?php if (($user["status"] ?? "") === "pending"): ?>
                                <a href="?approve=<?php echo (int) $user["id"]; ?>" class="btn btn-primary">Approve</a>
                            <?php else: ?>
                                <span class="chip">Active</span>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <article class="row">
                    <div>
                        <span class="status">Empty</span>
                        <h2>No alumni accounts found</h2>
                        <p>The directory will fill up as new members register.</p>
                    </div>
                </article>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
