<?php
require_once __DIR__ . "/helpers.php";
adminOnly();

if (isset($_GET["approve"])) {
    $id = (int) $_GET["approve"];
    $conn->query("UPDATE jobs SET status='approved' WHERE id='$id'");
    header("Location: jobs.php?res=approved");
    exit();
}

if (isset($_GET["reject"])) {
    $id = (int) $_GET["reject"];
    $conn->query("UPDATE jobs SET status='rejected' WHERE id='$id'");
    header("Location: jobs.php?res=rejected");
    exit();
}

if (isset($_GET["delete"])) {
    $id = (int) $_GET["delete"];
    $conn->query("DELETE FROM jobs WHERE id='$id'");
    header("Location: jobs.php?res=deleted");
    exit();
}

$jobs = adminRows($conn, "SELECT jobs.*, users.full_name, users.email FROM jobs LEFT JOIN users ON jobs.alumni_id = users.id ORDER BY CASE jobs.status WHEN 'pending' THEN 0 WHEN 'approved' THEN 1 ELSE 2 END, jobs.id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Moderation | AlumniX</title>
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
        .row p { color: var(--muted); margin-top: 10px; line-height: 1.7; }
        .stack { display: flex; flex-wrap: wrap; gap: 10px; align-content: start; justify-content: flex-end; }
        @media (max-width: 860px) { .topbar, .row { grid-template-columns: 1fr; } .topbar, .actions, .stack { flex-direction: column; align-items: stretch; } .shell { width: min(100% - 18px, 1180px); } }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div>
                <h1>Job Moderation</h1>
                <p>Approve, reject, or clean up openings posted through the alumni portal.</p>
            </div>
            <div class="actions">
                <a href="admin_dashboard.php" class="btn btn-soft">Dashboard</a>
                <a href="event.php" class="btn btn-soft">Events</a>
                <a href="logout.php" class="btn btn-primary">Logout</a>
            </div>
        </header>

        <section class="list">
            <?php if ($jobs): ?>
                <?php foreach ($jobs as $job): ?>
                    <?php
                    $owner = $job["full_name"] ?: ($job["email"] ?: "Unknown alumni");
                    $applyLink = $job["apply_link"] ?? "";
                    ?>
                    <article class="row">
                        <div>
                            <span class="status"><?php echo adminE($job["status"] ?: "pending"); ?></span>
                            <h2><?php echo adminE($job["title"]); ?></h2>
                            <div class="meta">
                                <span class="chip"><?php echo adminE($job["company"]); ?></span>
                                <span class="chip"><?php echo adminE($job["location"] ?: "Flexible"); ?></span>
                                <span class="chip">By <?php echo adminE($owner); ?></span>
                            </div>
                            <p><?php echo adminE($job["description"] ?: "No description provided for this job post."); ?></p>
                            <?php if (!empty($applyLink)): ?>
                                <p><a href="<?php echo adminE($applyLink); ?>" target="_blank" rel="noopener noreferrer">Open apply link</a></p>
                            <?php endif; ?>
                        </div>
                        <div class="stack">
                            <?php if (($job["status"] ?? "") !== "approved"): ?>
                                <a href="?approve=<?php echo (int) $job["id"]; ?>" class="btn btn-primary">Approve</a>
                            <?php endif; ?>
                            <?php if (($job["status"] ?? "") !== "rejected"): ?>
                                <a href="?reject=<?php echo (int) $job["id"]; ?>" class="btn btn-soft">Reject</a>
                            <?php endif; ?>
                            <a href="?delete=<?php echo (int) $job["id"]; ?>" class="btn btn-soft" onclick="return confirm('Delete this job permanently?');">Delete</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <article class="row">
                    <div>
                        <span class="status">Empty</span>
                        <h2>No job posts yet</h2>
                        <p>Once alumni members submit roles, they will appear here for moderation.</p>
                    </div>
                </article>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
