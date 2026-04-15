<?php
require_once __DIR__ . "/helpers.php";
adminOnly();

if (isset($_GET["approve_user"])) {
    $id = (int) $_GET["approve_user"];
    $conn->query("UPDATE users SET status='approved' WHERE id='$id'");
    header("Location: admin_dashboard.php?res=approved");
    exit();
}

if (isset($_GET["delete_job"])) {
    $id = (int) $_GET["delete_job"];
    $conn->query("DELETE FROM jobs WHERE id='$id'");
    header("Location: admin_dashboard.php?res=deleted");
    exit();
}

$stats = [
    "pending_alumni" => adminCount($conn, "SELECT COUNT(*) FROM users WHERE role='alumni' AND status='pending'"),
    "active_alumni" => adminCount($conn, "SELECT COUNT(*) FROM users WHERE role='alumni' AND status IN ('approved', 'active')"),
    "pending_jobs" => adminCount($conn, "SELECT COUNT(*) FROM jobs WHERE status='pending'"),
    "live_events" => adminCount($conn, "SELECT COUNT(*) FROM events"),
];

$pendingUsers = adminRows($conn, "SELECT id, full_name, email, batch FROM users WHERE role='alumni' AND status='pending' ORDER BY id DESC LIMIT 5");
$recentJobs = adminRows($conn, "SELECT id, title, company, status FROM jobs ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | AlumniX</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        :root { --bg: #f7f6f2; --surface: #ffffff; --ink: #142338; --muted: #657489; --accent: #ff6b57; --line: rgba(20, 35, 56, 0.08); --shadow: 0 18px 40px rgba(20, 35, 56, 0.08); }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: "Manrope", sans-serif; background: linear-gradient(180deg, #fff9f3 0%, var(--bg) 100%); color: var(--ink); min-height: 100vh; }
        .shell { width: min(1180px, calc(100% - 32px)); margin: 0 auto; padding: 28px 0 40px; }
        .topbar, .panel, .stat, .launch { background: rgba(255,255,255,0.88); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.86); box-shadow: var(--shadow); }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 18px; padding: 20px 24px; border-radius: 28px; }
        .title h1, .section-title, .launch h2 { font-family: "Space Grotesk", sans-serif; letter-spacing: -0.05em; }
        .title h1 { font-size: 2rem; }
        .title p { color: var(--muted); margin-top: 6px; }
        .actions { display: flex; flex-wrap: wrap; gap: 10px; }
        .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 12px 18px; border-radius: 999px; font-weight: 800; text-decoration: none; }
        .btn-primary { background: linear-gradient(135deg, var(--accent), #ff8d63); color: #fff; }
        .btn-soft { background: #fff; color: var(--ink); border: 1px solid var(--line); }
        .stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; margin-top: 18px; }
        .stat { padding: 22px; border-radius: 24px; }
        .stat span { color: var(--muted); font-size: 0.82rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.12em; }
        .stat strong { display: block; margin-top: 10px; font-family: "Space Grotesk", sans-serif; font-size: 2.3rem; }
        .grid { display: grid; grid-template-columns: 1.2fr 1fr; gap: 18px; margin-top: 18px; }
        .panel { padding: 24px; border-radius: 28px; }
        .section-title { font-size: 1.55rem; }
        .panel p { color: var(--muted); margin-top: 6px; }
        .list { display: grid; gap: 14px; margin-top: 18px; }
        .item { display: flex; justify-content: space-between; align-items: center; gap: 14px; padding: 16px 18px; border-radius: 22px; background: #fff; border: 1px solid var(--line); }
        .item strong { display: block; }
        .item small { color: var(--muted); }
        .pill { display: inline-flex; align-items: center; padding: 8px 12px; border-radius: 999px; background: rgba(255, 107, 87, 0.12); color: var(--accent); font-size: 0.78rem; font-weight: 800; text-transform: uppercase; }
        .launch { display: grid; grid-template-columns: minmax(0, 1fr) auto; align-items: center; gap: 18px; margin-top: 18px; padding: 24px; border-radius: 28px; }
        .launch p { color: var(--muted); margin-top: 8px; }
        @media (max-width: 980px) { .stats, .grid, .launch { grid-template-columns: 1fr 1fr; } .grid { grid-template-columns: 1fr; } .launch { grid-template-columns: 1fr; } }
        @media (max-width: 640px) { .shell { width: min(100% - 18px, 1180px); } .topbar, .actions { flex-direction: column; align-items: stretch; } .stats { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div class="title">
                <h1>AlumniX Admin</h1>
                <p>Moderate the network, approve members, and keep the public portal fresh.</p>
            </div>
            <div class="actions">
                <a href="event.php" class="btn btn-soft"><i class="fas fa-calendar-plus"></i> Manage Events</a>
                <a href="jobs.php" class="btn btn-soft"><i class="fas fa-briefcase"></i> Review Jobs</a>
                <a href="logout.php" class="btn btn-primary"><i class="fas fa-right-from-bracket"></i> Logout</a>
            </div>
        </header>

        <section class="stats">
            <div class="stat"><span>Pending Alumni</span><strong><?php echo number_format($stats["pending_alumni"]); ?></strong></div>
            <div class="stat"><span>Active Alumni</span><strong><?php echo number_format($stats["active_alumni"]); ?></strong></div>
            <div class="stat"><span>Pending Jobs</span><strong><?php echo number_format($stats["pending_jobs"]); ?></strong></div>
            <div class="stat"><span>Live Events</span><strong><?php echo number_format($stats["live_events"]); ?></strong></div>
        </section>

        <section class="grid">
            <article class="panel">
                <h2 class="section-title">Approval Queue</h2>
                <p>Recent alumni registrations waiting for review.</p>
                <div class="list">
                    <?php if ($pendingUsers): ?>
                        <?php foreach ($pendingUsers as $user): ?>
                            <div class="item">
                                <div>
                                    <strong><?php echo adminE($user["full_name"]); ?></strong>
                                    <small><?php echo adminE($user["email"]); ?><?php echo !empty($user["batch"]) ? " | Batch " . adminE($user["batch"]) : ""; ?></small>
                                </div>
                                <a href="?approve_user=<?php echo (int) $user["id"]; ?>" class="btn btn-primary">Approve</a>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="item"><div><strong>All clear</strong><small>No pending alumni approvals right now.</small></div><span class="pill">Stable</span></div>
                    <?php endif; ?>
                </div>
            </article>

            <article class="panel">
                <h2 class="section-title">Latest Job Activity</h2>
                <p>Quick glance at the newest job posts and their current status.</p>
                <div class="list">
                    <?php if ($recentJobs): ?>
                        <?php foreach ($recentJobs as $job): ?>
                            <div class="item">
                                <div>
                                    <strong><?php echo adminE($job["title"]); ?></strong>
                                    <small><?php echo adminE($job["company"]); ?></small>
                                </div>
                                <span class="pill"><?php echo adminE($job["status"]); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="item"><div><strong>No jobs yet</strong><small>Job moderation items will appear here once posted.</small></div><span class="pill">Empty</span></div>
                    <?php endif; ?>
                </div>
            </article>
        </section>

        <section class="launch">
            <div>
                <h2>Quick launch pad</h2>
                <p>Use the dedicated pages for deeper moderation and cleaner data management.</p>
            </div>
            <div class="actions">
                <a href="jobs.php" class="btn btn-soft">Job Queue</a>
                <a href="event.php" class="btn btn-soft">Events</a>
                <a href="alumni_list.php" class="btn btn-primary">Alumni Directory</a>
            </div>
        </section>
    </div>
</body>
</html>
