<?php
require_once __DIR__ . "/helpers.php";
adminOnly();

if (isset($_GET["member_action"], $_GET["id"])) {
    $memberId = (int) $_GET["id"];
    $memberAction = $_GET["member_action"];

    if ($memberAction === "approve") {
        $result = alumnixApproveUser($conn, $memberId);
        adminSetFlash($result["ok"] ? ($result["mail_sent"] ? "success" : "warning") : "error", $result["message"], $result["ok"] ? [
            "credential_name" => $result["name"] ?? "",
            "credential_email" => $result["email"] ?? "",
            "credential_password" => $result["mail_sent"] ? "" : ($result["password"] ?? ""),
        ] : []);
    } elseif ($memberAction === "reject") {
        $conn->query("UPDATE users SET status='rejected' WHERE id={$memberId}");
        adminSetFlash("warning", "Member request marked as rejected.");
    } elseif ($memberAction === "delete") {
        $conn->query("DELETE FROM users WHERE id={$memberId}");
        adminSetFlash("success", "Member record deleted.");
    }

    header("Location: admin_dashboard.php");
    exit();
}

$flash = adminPullFlash();

$stats = [
    "pending_alumni" => adminCount($conn, "SELECT COUNT(*) FROM users WHERE role='alumni' AND status='pending'"),
    "active_alumni" => adminCount($conn, "SELECT COUNT(*) FROM users WHERE role='alumni' AND status IN ('approved', 'active')"),
    "pending_jobs" => adminCount($conn, "SELECT COUNT(*) FROM jobs WHERE status='pending'"),
    "live_events" => adminCount($conn, "SELECT COUNT(*) FROM events"),
    "applications" => adminCount($conn, "SELECT COUNT(*) FROM job_applications"),
];

$pendingUsers = adminRows($conn, "SELECT id, full_name, email, batch, graduation_year, company FROM users WHERE role='alumni' AND status='pending' ORDER BY id DESC LIMIT 6");
$recentJobs = adminRows($conn, "SELECT title, company, status, created_at FROM jobs ORDER BY id DESC LIMIT 5");
$recentEvents = adminRows($conn, "SELECT title, event_date, location FROM events ORDER BY event_date ASC LIMIT 4");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | AlumniX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --accent: #ff4d4d;
            --accent-soft: rgba(255, 77, 77, 0.12);
            --ink: #0f172a;
            --muted: #64748b;
            --surface: rgba(255, 255, 255, 0.92);
            --surface-strong: #ffffff;
            --line: rgba(148, 163, 184, 0.18);
            --bg: #f8fafc;
            --shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
            --radius: 28px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(255, 77, 77, 0.08), transparent 26%),
                radial-gradient(circle at bottom right, rgba(15, 23, 42, 0.06), transparent 24%),
                var(--bg);
            min-height: 100vh;
        }

        .shell {
            width: min(1280px, calc(100% - 36px));
            margin: 0 auto;
            padding: 28px 0 36px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 18px;
            padding: 26px 28px;
            border-radius: 32px;
            background: rgba(255, 255, 255, 0.78);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.86);
            box-shadow: var(--shadow);
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 14px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .topbar h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(30px, 4vw, 46px);
            letter-spacing: -0.05em;
            line-height: 0.96;
        }

        .topbar p {
            margin-top: 10px;
            color: var(--muted);
            max-width: 650px;
            line-height: 1.7;
        }

        .top-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 10px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            border-radius: 999px;
            padding: 12px 18px;
            text-decoration: none;
            font-weight: 800;
            font-size: 13px;
            transition: transform 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
            border: 1px solid transparent;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), #ff8b65);
            color: #fff;
            box-shadow: 0 18px 35px rgba(255, 77, 77, 0.18);
        }

        .btn-primary:hover { transform: translateY(-3px); }

        .btn-soft {
            background: var(--surface-strong);
            color: var(--ink);
            border-color: var(--line);
        }

        .btn-soft:hover { transform: translateY(-3px); }

        .flash {
            margin-top: 18px;
            padding: 18px 20px;
            border-radius: 22px;
            border: 1px solid var(--line);
            background: var(--surface);
            box-shadow: 0 14px 35px rgba(15, 23, 42, 0.04);
        }

        .flash.success { border-color: rgba(16, 185, 129, 0.22); background: rgba(236, 253, 245, 0.96); }
        .flash.warning { border-color: rgba(245, 158, 11, 0.22); background: rgba(255, 251, 235, 0.96); }
        .flash.error { border-color: rgba(239, 68, 68, 0.22); background: rgba(254, 242, 242, 0.96); }

        .flash h3 { font-size: 15px; font-weight: 800; margin-bottom: 8px; }
        .flash p { color: #334155; line-height: 1.7; font-size: 13px; }
        .flash code {
            display: inline-block;
            margin-top: 10px;
            padding: 8px 12px;
            border-radius: 10px;
            background: rgba(15, 23, 42, 0.06);
            font-size: 12px;
            font-weight: 700;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .stat-card,
        .panel {
            background: var(--surface);
            backdrop-filter: blur(18px);
            border-radius: var(--radius);
            border: 1px solid rgba(255, 255, 255, 0.88);
            box-shadow: var(--shadow);
        }

        .stat-card {
            padding: 22px;
            min-height: 160px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .stat-label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        .stat-value {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(32px, 4vw, 44px);
            letter-spacing: -0.05em;
        }

        .stat-foot {
            color: #334155;
            font-size: 13px;
            line-height: 1.6;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1.25fr 0.9fr;
            gap: 18px;
            margin-top: 24px;
        }

        .panel {
            padding: 24px;
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }

        .panel-head h2 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 24px;
            letter-spacing: -0.04em;
        }

        .panel-head p {
            color: var(--muted);
            font-size: 13px;
            margin-top: 6px;
        }

        .panel-link {
            color: var(--accent);
            text-decoration: none;
            font-weight: 800;
            font-size: 13px;
        }

        .queue {
            display: grid;
            gap: 14px;
        }

        .queue-item,
        .feed-item,
        .event-item {
            border-radius: 24px;
            padding: 18px;
            background: rgba(248, 250, 252, 0.94);
            border: 1px solid var(--line);
        }

        .queue-item {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 16px;
            align-items: center;
        }

        .queue-name {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .queue-meta,
        .feed-meta,
        .event-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 12px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid var(--line);
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: flex-end;
        }

        .action-btn {
            border: none;
            border-radius: 14px;
            padding: 10px 14px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            color: #fff;
            text-decoration: none;
        }

        .action-btn.approve { background: linear-gradient(135deg, #10b981, #34d399); }
        .action-btn.reject { background: linear-gradient(135deg, #f59e0b, #f97316); }
        .action-btn.delete { background: linear-gradient(135deg, #ef4444, #f87171); }

        .mini-grid {
            display: grid;
            gap: 12px;
        }

        .feed-item h3,
        .event-item h3 {
            font-size: 18px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .feed-item p,
        .event-item p {
            color: #334155;
            line-height: 1.7;
            font-size: 13px;
        }

        .empty {
            padding: 30px 20px;
            border-radius: 24px;
            text-align: center;
            color: var(--muted);
            background: rgba(248, 250, 252, 0.9);
            border: 1px dashed var(--line);
        }

        @media (max-width: 1180px) {
            .stats-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .content-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 760px) {
            .shell { width: calc(100% - 20px); }
            .topbar { padding: 22px; }
            .topbar, .top-actions, .panel-head, .queue-item { grid-template-columns: 1fr; }
            .topbar, .panel-head { flex-direction: column; align-items: flex-start; }
            .top-actions, .actions { width: 100%; justify-content: flex-start; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div>
                <div class="eyebrow"><i class="fas fa-shield-heart"></i> Admin Command Center</div>
                <h1>Run AlumniX with clarity and control.</h1>
                <p>Approve alumni accounts, watch job moderation, and keep campus events moving without jumping across messy screens.</p>
            </div>
            <div class="top-actions">
                <a href="alumni_list.php" class="btn btn-soft"><i class="fas fa-users"></i> Alumni</a>
                <a href="jobs.php" class="btn btn-soft"><i class="fas fa-briefcase"></i> Jobs</a>
                <a href="event.php" class="btn btn-soft"><i class="fas fa-calendar-days"></i> Events</a>
                <a href="view_applications.php" class="btn btn-soft"><i class="fas fa-list-check"></i> Applications</a>
                <a href="logout.php" class="btn btn-primary"><i class="fas fa-power-off"></i> Logout</a>
            </div>
        </header>

        <?php if ($flash): ?>
            <section class="flash <?php echo adminE($flash["type"] ?? "success"); ?>">
                <h3><?php echo adminE($flash["message"] ?? "Update complete."); ?></h3>
                <?php if (!empty($flash["credential_password"])): ?>
                    <p>Email delivery failed, so share these credentials manually if needed.</p>
                    <code>Login ID: <?php echo adminE($flash["credential_email"] ?? ""); ?></code>
                    <code>Password: <?php echo adminE($flash["credential_password"] ?? ""); ?></code>
                <?php elseif (!empty($flash["credential_email"])): ?>
                    <p>Login ID was sent to <strong><?php echo adminE($flash["credential_email"]); ?></strong>.</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>

        <section class="stats-grid">
            <article class="stat-card">
                <span class="stat-label">Pending Alumni</span>
                <span class="stat-value" style="color: var(--accent);"><?php echo number_format($stats["pending_alumni"]); ?></span>
                <p class="stat-foot">Fresh registrations waiting for admin approval and auto-generated credentials.</p>
            </article>
            <article class="stat-card">
                <span class="stat-label">Approved Members</span>
                <span class="stat-value"><?php echo number_format($stats["active_alumni"]); ?></span>
                <p class="stat-foot">Members currently cleared to log in with emailed credentials.</p>
            </article>
            <article class="stat-card">
                <span class="stat-label">Pending Jobs</span>
                <span class="stat-value"><?php echo number_format($stats["pending_jobs"]); ?></span>
                <p class="stat-foot">Open roles still waiting for moderation before they go live.</p>
            </article>
            <article class="stat-card">
                <span class="stat-label">Live Events</span>
                <span class="stat-value"><?php echo number_format($stats["live_events"]); ?></span>
                <p class="stat-foot">Upcoming events already published on the public portal.</p>
            </article>
            <article class="stat-card">
                <span class="stat-label">Applications</span>
                <span class="stat-value"><?php echo number_format($stats["applications"]); ?></span>
                <p class="stat-foot">Total job applications tracked inside the admin workspace.</p>
            </article>
        </section>

        <section class="content-grid">
            <article class="panel">
                <div class="panel-head">
                    <div>
                        <h2>Approval Queue</h2>
                        <p>Approve members and instantly email their login ID plus generated password.</p>
                    </div>
                    <a href="alumni_list.php" class="panel-link">Open full directory</a>
                </div>

                <div class="queue">
                    <?php if ($pendingUsers): ?>
                        <?php foreach ($pendingUsers as $user): ?>
                            <div class="queue-item">
                                <div>
                                    <div class="queue-name"><?php echo adminE($user["full_name"]); ?></div>
                                    <div class="queue-meta">
                                        <span class="pill"><i class="fas fa-envelope"></i> <?php echo adminE($user["email"]); ?></span>
                                        <span class="pill"><i class="fas fa-graduation-cap"></i> <?php echo adminE($user["batch"] ?: "N/A"); ?></span>
                                        <span class="pill"><i class="fas fa-calendar"></i> <?php echo adminE($user["graduation_year"] ?: "N/A"); ?></span>
                                        <span class="pill"><i class="fas fa-building"></i> <?php echo adminE($user["company"] ?: "Not added"); ?></span>
                                    </div>
                                </div>
                                <div class="actions">
                                    <a href="?member_action=approve&id=<?php echo (int) $user["id"]; ?>" class="action-btn approve" onclick="return confirm('Approve this member and send login credentials by email?');">Approve</a>
                                    <a href="?member_action=reject&id=<?php echo (int) $user["id"]; ?>" class="action-btn reject" onclick="return confirm('Reject this member request?');">Reject</a>
                                    <a href="?member_action=delete&id=<?php echo (int) $user["id"]; ?>" class="action-btn delete" onclick="return confirm('Delete this member record permanently?');">Delete</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty">
                            <i class="fas fa-check-circle" style="font-size: 32px; margin-bottom: 10px;"></i>
                            <p>No pending alumni approvals right now.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </article>

            <div class="mini-grid">
                <article class="panel">
                    <div class="panel-head">
                        <div>
                            <h2>Job Activity</h2>
                            <p>Latest posts moving through moderation.</p>
                        </div>
                        <a href="jobs.php" class="panel-link">Moderate jobs</a>
                    </div>
                    <div class="mini-grid">
                        <?php if ($recentJobs): ?>
                            <?php foreach ($recentJobs as $job): ?>
                                <div class="feed-item">
                                    <h3><?php echo adminE($job["title"]); ?></h3>
                                    <div class="feed-meta">
                                        <span class="pill"><?php echo adminE($job["company"] ?: "Unknown company"); ?></span>
                                        <span class="pill"><?php echo adminE(strtoupper((string) ($job["status"] ?: "pending"))); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty"><p>No recent job activity yet.</p></div>
                        <?php endif; ?>
                    </div>
                </article>

                <article class="panel">
                    <div class="panel-head">
                        <div>
                            <h2>Event Radar</h2>
                            <p>Closest upcoming events in the system.</p>
                        </div>
                        <a href="event.php" class="panel-link">Manage events</a>
                    </div>
                    <div class="mini-grid">
                        <?php if ($recentEvents): ?>
                            <?php foreach ($recentEvents as $event): ?>
                                <div class="event-item">
                                    <h3><?php echo adminE($event["title"]); ?></h3>
                                    <div class="event-meta">
                                        <span class="pill"><i class="fas fa-calendar"></i> <?php echo adminE(date('d M Y', strtotime((string) $event["event_date"]))); ?></span>
                                        <span class="pill"><i class="fas fa-location-dot"></i> <?php echo adminE($event["location"] ?: "TBA"); ?></span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="empty"><p>No events scheduled yet.</p></div>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
        </section>
    </div>
</body>
</html>
