<?php

require_once __DIR__ . '/helpers.php'; // Path check karo ki helpers.php isi folder mein hai ya nahi
adminOnly(); // Ye confirm karta hai ki helpers load hua hai


require_once __DIR__ . '/helpers.php';
adminOnly();

if (isset($_GET["approve"])) {
    $id = (int)$_GET["approve"];
    $result = alumnixApproveUserEngine($conn, $id);
    adminSetFlash($result["ok"] ? "success" : "error", $result["message"]);
    header("Location: alumni_list.php");
    exit();
}

if (isset($_GET["reject"])) {
    $id = (int) $_GET["reject"];
    $conn->query("UPDATE users SET status='rejected' WHERE id='{$id}'");
    adminSetFlash("warning", "Member request marked as rejected.");
    header("Location: alumni_list.php");
    exit();
}

if (isset($_GET["delete"])) {
    $id = (int) $_GET["delete"];
    $conn->query("DELETE FROM users WHERE id='{$id}'");
    adminSetFlash("success", "Member record deleted.");
    header("Location: alumni_list.php");
    exit();
}

$flash = adminPullFlash();
$stats = [
    "pending" => adminCount($conn, "SELECT COUNT(*) FROM users WHERE role='alumni' AND status='pending'"),
    "approved" => adminCount($conn, "SELECT COUNT(*) FROM users WHERE role='alumni' AND status IN ('approved', 'active')"),
    "rejected" => adminCount($conn, "SELECT COUNT(*) FROM users WHERE role='alumni' AND status='rejected'"),
];

$alumniUsers = adminRows(
    $conn,
    "SELECT id, full_name, email, student_id, batch, graduation_year, company, status
     FROM users
     WHERE role='alumni'
     ORDER BY
        CASE status
            WHEN 'pending' THEN 0
            WHEN 'approved' THEN 1
            WHEN 'active' THEN 1
            WHEN 'rejected' THEN 2
            ELSE 3
        END,
        id DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Directory | AlumniX Admin</title>
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
            color: var(--ink);
            background:
                radial-gradient(circle at top left, rgba(255, 77, 77, 0.08), transparent 28%),
                radial-gradient(circle at bottom right, rgba(15, 23, 42, 0.06), transparent 26%),
                var(--bg);
            min-height: 100vh;
        }

        .shell {
            width: min(1240px, calc(100% - 36px));
            margin: 0 auto;
            padding: 28px 0 36px;
        }

        .topbar,
        .stat-card,
        .member-card,
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

        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, var(--accent), #ff8b65);
        }

        .btn-soft {
            color: var(--ink);
            background: #fff;
            border-color: var(--line);
        }

        .btn:hover { transform: translateY(-3px); }

        .flash {
            margin-top: 18px;
            padding: 18px 20px;
            border-radius: 22px;
        }

        .flash.success { background: rgba(236, 253, 245, 0.96); border-color: rgba(16, 185, 129, 0.22); }
        .flash.warning { background: rgba(255, 251, 235, 0.96); border-color: rgba(245, 158, 11, 0.22); }
        .flash.error { background: rgba(254, 242, 242, 0.96); border-color: rgba(239, 68, 68, 0.22); }
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

        .directory {
            display: grid;
            gap: 16px;
            margin-top: 22px;
        }

        .member-card {
            border-radius: 30px;
            padding: 22px;
            display: grid;
            grid-template-columns: auto minmax(0, 1fr) auto;
            gap: 18px;
            align-items: center;
        }

        .avatar {
            width: 72px;
            height: 72px;
            border-radius: 22px;
            background: linear-gradient(135deg, rgba(255, 77, 77, 0.12), rgba(255, 139, 101, 0.18));
            display: grid;
            place-items: center;
            color: var(--accent);
            font-size: 24px;
            font-weight: 800;
            border: 1px solid rgba(255, 77, 77, 0.12);
        }

        .member-name {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 21px;
            font-weight: 800;
            margin-bottom: 10px;
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
        }

        .status-pending { color: #b45309; background: #fef3c7; }
        .status-approved { color: #15803d; background: #dcfce7; }
        .status-rejected { color: #b91c1c; background: #fee2e2; }

        .meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
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

        @media (max-width: 1000px) {
            .stats { grid-template-columns: 1fr; }
            .member-card { grid-template-columns: 1fr; }
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
                <div class="eyebrow"><i class="fas fa-user-group"></i> Alumni Moderation</div>
                <h1>Review every member with one clean workflow.</h1>
                <p>Pending members can be approved with auto-generated credentials, rejected for review, or removed completely if needed.</p>
            </div>
            <nav class="nav">
                <a href="admin_dashboard.php" class="btn btn-soft"><i class="fas fa-grid-2"></i> Dashboard</a>
                <a href="jobs.php" class="btn btn-soft"><i class="fas fa-briefcase"></i> Jobs</a>
                <a href="event.php" class="btn btn-soft"><i class="fas fa-calendar-days"></i> Events</a>
                <a href="logout.php" class="btn btn-primary"><i class="fas fa-power-off"></i> Logout</a>
            </nav>
        </header>

        <?php if ($flash): ?>
            <section class="flash <?php echo adminE($flash["type"] ?? "success"); ?>">
                <h3><?php echo adminE($flash["message"] ?? "Update complete."); ?></h3>
                <?php if (!empty($flash["credential_password"])): ?>
                    <p>Email delivery failed, so these credentials are shown once for manual sharing.</p>
                    <code>Login ID: <?php echo adminE($flash["credential_email"] ?? ""); ?></code>
                    <code>Password: <?php echo adminE($flash["credential_password"] ?? ""); ?></code>
                <?php endif; ?>
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

        <section class="directory">
            <?php if ($alumniUsers): ?>
                <?php foreach ($alumniUsers as $user): ?>
                    <?php
                    $status = strtolower((string) ($user["status"] ?: "pending"));
                    $statusClass = $status === "approved" || $status === "active"
                        ? "status-approved"
                        : ($status === "rejected" ? "status-rejected" : "status-pending");
                    ?>
                    <article class="member-card">
                        <div class="avatar"><?php echo adminE(strtoupper(substr((string) $user["full_name"], 0, 1))); ?></div>
                        <div>
                            <div class="member-name">
                                <span><?php echo adminE($user["full_name"]); ?></span>
                                <span class="status-pill <?php echo $statusClass; ?>"><?php echo adminE($status); ?></span>
                            </div>
                            <div class="meta">
                                <span class="chip"><i class="fas fa-envelope"></i> <?php echo adminE($user["email"]); ?></span>
                                <span class="chip"><i class="fas fa-id-card"></i> <?php echo adminE($user["student_id"] ?: "N/A"); ?></span>
                                <span class="chip"><i class="fas fa-graduation-cap"></i> <?php echo adminE($user["batch"] ?: "N/A"); ?></span>
                                <span class="chip"><i class="fas fa-calendar"></i> <?php echo adminE($user["graduation_year"] ?: "N/A"); ?></span>
                                <span class="chip"><i class="fas fa-building"></i> <?php echo adminE($user["company"] ?: "Not added"); ?></span>
                            </div>
                        </div>
                        <div class="action-stack">
                            <?php if (!in_array($status, ["approved", "active"], true)): ?>
                                <a href="?approve=<?php echo (int) $user["id"]; ?>" class="action approve" onclick="return confirm('Approve this member and send credentials by email?');">Approve</a>
                            <?php endif; ?>
                            <?php if ($status !== "rejected"): ?>
                                <a href="?reject=<?php echo (int) $user["id"]; ?>" class="action reject" onclick="return confirm('Mark this member request as rejected?');">Reject</a>
                            <?php endif; ?>
                            <a href="?delete=<?php echo (int) $user["id"]; ?>" class="action delete" onclick="return confirm('Delete this member permanently?');">Delete</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty">
                    <i class="fas fa-users-slash" style="font-size: 36px; margin-bottom: 12px;"></i>
                    <p>No alumni members found yet.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
