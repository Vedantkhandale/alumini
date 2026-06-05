<?php
require_once __DIR__ . "/helpers.php";
adminOnly();

if (isset($_GET["approve"])) {
    $id = (int) $_GET["approve"];
    $job = null;
    $stmt = $conn->prepare(
        "SELECT jobs.title, jobs.company, users.full_name, users.email
         FROM jobs
         LEFT JOIN users ON jobs.alumni_id = users.id
         WHERE jobs.id = ?
         LIMIT 1"
    );
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $job = $result ? $result->fetch_assoc() : null;
        $stmt->close();
    }

    $conn->query("UPDATE jobs SET status='approved' WHERE id={$id}");
    $message = "Job approved and visible to alumni members.";
    if ($job && !empty($job["email"])) {
        $mailSent = alumnixSendJobApprovalNotice($job["full_name"], $job["email"], $job["title"], $job["company"]);
        $message = $mailSent
            ? "Job approved and submitter notified by email."
            : "Job approved, but notification email failed. " . alumnixLastMailError();
    }
    adminSetFlash(strpos($message, "failed") !== false ? "warning" : "success", $message);
    header("Location: jobs.php");
    exit();
}

if (isset($_GET["reject"])) {
    $id = (int) $_GET["reject"];
    $conn->query("UPDATE jobs SET status='rejected' WHERE id={$id}");
    adminSetFlash("warning", "Job marked as rejected.");
    header("Location: jobs.php");
    exit();
}

if (isset($_GET["delete"])) {
    $id = (int) $_GET["delete"];
    $conn->query("DELETE FROM jobs WHERE id={$id}");
    adminSetFlash("success", "Job deleted permanently.");
    header("Location: jobs.php");
    exit();
}

$flash = adminPullFlash();
$stats = [
    "pending" => adminCount($conn, "SELECT COUNT(*) FROM jobs WHERE status='pending'"),
    "approved" => adminCount($conn, "SELECT COUNT(*) FROM jobs WHERE status='approved'"),
    "rejected" => adminCount($conn, "SELECT COUNT(*) FROM jobs WHERE status='rejected'"),
];

$jobs = adminRows(
    $conn,
    "SELECT jobs.*, users.full_name, users.email
     FROM jobs
     LEFT JOIN users ON jobs.alumni_id = users.id
     ORDER BY
        CASE jobs.status
            WHEN 'pending' THEN 0
            WHEN 'approved' THEN 1
            WHEN 'rejected' THEN 2
            ELSE 3
        END,
        jobs.id DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Moderation | AlumniX Admin</title>
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
        .job-card,
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

        .job-list {
            display: grid;
            gap: 16px;
            margin-top: 22px;
        }

        .job-card {
            border-radius: 30px;
            padding: 22px;
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 18px;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .status-pending { color: #b45309; background: #fef3c7; }
        .status-approved { color: #15803d; background: #dcfce7; }
        .status-rejected { color: #b91c1c; background: #fee2e2; }

        .job-card h2 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px;
            letter-spacing: -0.04em;
        }

        .job-card p {
            margin-top: 12px;
            color: #334155;
            line-height: 1.7;
            font-size: 13px;
        }

        .meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 14px;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 8px 12px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid var(--line);
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .action-stack {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            align-content: flex-start;
            gap: 8px;
        }

        .action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 11px 14px;
            border-radius: 14px;
            color: #fff;
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
        }

        .approve { background: linear-gradient(135deg, #10b981, #34d399); }
        .reject { background: linear-gradient(135deg, #f59e0b, #f97316); }
        .delete { background: linear-gradient(135deg, #ef4444, #f87171); }

        .empty {
            padding: 40px 24px;
            text-align: center;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.84);
            color: var(--muted);
            border: 1px dashed var(--line);
        }

        @media (max-width: 980px) {
            .stats { grid-template-columns: 1fr; }
            .job-card { grid-template-columns: 1fr; }
            .action-stack { justify-content: flex-start; }
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
                <div class="eyebrow"><i class="fas fa-briefcase"></i> Job Moderation</div>
                <h1>Keep every opportunity polished before it goes live.</h1>
                <p>Review pending roles, approve the strong ones, reject weak submissions, or delete noisy posts from the pipeline.</p>
            </div>
            <nav class="nav">
                <a href="admin_dashboard.php" class="btn btn-soft"><i class="fas fa-grid-2"></i> Dashboard</a>
                <a href="alumni_list.php" class="btn btn-soft"><i class="fas fa-users"></i> Alumni</a>
                <a href="event.php" class="btn btn-soft"><i class="fas fa-calendar-days"></i> Events</a>
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
                <span class="stat-label">Pending</span>
                <span class="stat-value" style="color: var(--accent);"><?php echo number_format($stats["pending"]); ?></span>
            </article>
            <article class="stat-card">
                <span class="stat-label">Approved</span>
                <span class="stat-value"><?php echo number_format($stats["approved"]); ?></span>
            </article>
            <article class="stat-card">
                <span class="stat-label">Rejected</span>
                <span class="stat-value"><?php echo number_format($stats["rejected"]); ?></span>
            </article>
        </section>

        <section class="job-list">
            <?php if ($jobs): ?>
                <?php foreach ($jobs as $job): ?>
                    <?php
                    $status = strtolower((string) ($job["status"] ?: "pending"));
                    $statusClass = $status === "approved"
                        ? "status-approved"
                        : ($status === "rejected" ? "status-rejected" : "status-pending");
                    $owner = $job["full_name"] ?: ($job["email"] ?: "Unknown alumni");
                    ?>
                    <article class="job-card">
                        <div>
                            <span class="status-pill <?php echo $statusClass; ?>"><?php echo adminE($status); ?></span>
                            <h2><?php echo adminE($job["title"]); ?></h2>
                            <div class="meta">
                                <span class="chip"><i class="fas fa-building"></i> <?php echo adminE($job["company"] ?: "Unknown company"); ?></span>
                                <span class="chip"><i class="fas fa-location-dot"></i> <?php echo adminE($job["location"] ?: "Flexible"); ?></span>
                                <span class="chip"><i class="fas fa-user"></i> <?php echo adminE($owner); ?></span>
                            </div>
                            <p><?php echo adminE($job["description"] ?: "No description provided for this job post."); ?></p>
                            <?php if (!empty($job["apply_link"])): ?>
                                <p><a href="<?php echo adminE($job["apply_link"]); ?>" target="_blank" rel="noopener noreferrer">Open apply link</a></p>
                            <?php endif; ?>
                        </div>
                        <div class="action-stack">
                            <?php if ($status !== "approved"): ?>
                                <a href="?approve=<?php echo (int) $job["id"]; ?>" class="action approve" onclick="return confirm('Approve this job post?');">Approve</a>
                            <?php endif; ?>
                            <?php if ($status !== "rejected"): ?>
                                <a href="?reject=<?php echo (int) $job["id"]; ?>" class="action reject" onclick="return confirm('Reject this job post?');">Reject</a>
                            <?php endif; ?>
                            <a href="?delete=<?php echo (int) $job["id"]; ?>" class="action delete" onclick="return confirm('Delete this job permanently?');">Delete</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty">
                    <i class="fas fa-briefcase" style="font-size: 36px; margin-bottom: 12px;"></i>
                    <p>No jobs have been submitted yet.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
