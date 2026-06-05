<?php
require_once __DIR__ . "/helpers.php";
adminOnly();

$adminName = (string) ($_SESSION["admin"] ?? "Admin");
$initials = strtoupper(substr(trim($adminName), 0, 1) ?: "A");

$stats = [
    "verified_alumni" => adminCount($conn, "SELECT COUNT(*) FROM users WHERE role='alumni' AND status IN ('approved', 'active')"),
    "pending_alumni" => adminCount($conn, "SELECT COUNT(*) FROM users WHERE role='alumni' AND status='pending'"),
    "active_jobs" => adminCount($conn, "SELECT COUNT(*) FROM jobs WHERE status='approved'"),
    "pending_jobs" => adminCount($conn, "SELECT COUNT(*) FROM jobs WHERE status='pending'"),
    "upcoming_events" => adminCount($conn, "SELECT COUNT(*) FROM events WHERE event_date >= CURDATE()"),
    "applications" => adminCount($conn, "SELECT COUNT(*) FROM job_applications"),
];

$totalAlumni = max(1, $stats["verified_alumni"] + $stats["pending_alumni"]);
$approvalPercent = round(($stats["verified_alumni"] / $totalAlumni) * 100);
$pendingWork = $stats["pending_alumni"] + $stats["pending_jobs"];

$recentJobs = adminRows(
    $conn,
    "SELECT
        j.id,
        j.title,
        j.company,
        j.location,
        j.status,
        u.full_name AS owner_name,
        COUNT(ja.id) AS applications
     FROM jobs j
     LEFT JOIN users u ON j.alumni_id = u.id
     LEFT JOIN job_applications ja ON ja.job_id = j.id
     GROUP BY j.id, j.title, j.company, j.location, j.status, u.full_name
     ORDER BY
        CASE j.status WHEN 'pending' THEN 0 WHEN 'approved' THEN 1 ELSE 2 END,
        j.id DESC
     LIMIT 6"
);

$pendingAlumni = adminRows(
    $conn,
    "SELECT id, full_name, email, student_id, batch
     FROM users
     WHERE role='alumni' AND status='pending'
     ORDER BY id DESC
     LIMIT 5"
);

$upcomingEvents = adminRows(
    $conn,
    "SELECT id, title, event_date, location
     FROM events
     WHERE event_date >= CURDATE()
     ORDER BY event_date ASC, id DESC
     LIMIT 4"
);

$recentApplications = adminRows(
    $conn,
    "SELECT
        ja.apply_time,
        u.full_name AS alumni_name,
        j.title AS job_title,
        j.company
     FROM job_applications ja
     JOIN users u ON ja.alumni_id = u.id
     JOIN jobs j ON ja.job_id = j.id
     ORDER BY ja.apply_time DESC
     LIMIT 5"
);

function dashboardStatusClass($status): string
{
    $status = strtolower((string) $status);
    if ($status === "approved" || $status === "active") {
        return "is-approved";
    }
    if ($status === "rejected") {
        return "is-rejected";
    }
    return "is-pending";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | AlumniX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent: #ef4444;
            --accent-dark: #dc2626;
            --accent-soft: #fee2e2;
            --ink: #111827;
            --muted: #64748b;
            --line: #e5e7eb;
            --panel: #ffffff;
            --page: #f8fafc;
            --good: #16a34a;
            --warn: #d97706;
            --bad: #dc2626;
            --shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
            --radius: 18px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            background: var(--page);
            color: var(--ink);
            font-family: "Plus Jakarta Sans", sans-serif;
        }

        .layout {
            display: grid;
            grid-template-columns: 270px minmax(0, 1fr);
            min-height: 100vh;
        }

        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            background: #0f172a;
            color: #fff;
            padding: 24px 18px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 8px 18px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        }

        .brand-mark,
        .avatar {
            display: grid;
            place-items: center;
            flex-shrink: 0;
            font-weight: 800;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: var(--accent);
        }

        .brand-text {
            font-family: "Space Grotesk", sans-serif;
            font-size: 22px;
            font-weight: 700;
            letter-spacing: -0.03em;
        }

        .brand-text span { color: #fca5a5; }

        .admin-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .avatar {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            background: #fff;
            color: #0f172a;
        }

        .admin-name {
            font-size: 14px;
            font-weight: 800;
            line-height: 1.2;
        }

        .admin-role {
            margin-top: 3px;
            font-size: 11px;
            color: #cbd5e1;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .nav {
            display: grid;
            gap: 8px;
        }

        .nav a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            color: #cbd5e1;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            transition: 0.2s ease;
        }

        .nav a:hover,
        .nav a.active {
            background: rgba(239, 68, 68, 0.18);
            color: #fff;
        }

        .nav a.logout {
            margin-top: 12px;
            color: #fecaca;
        }

        .main {
            min-width: 0;
            padding: 28px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 22px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--accent);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        h1 {
            margin-top: 8px;
            font-family: "Space Grotesk", sans-serif;
            font-size: clamp(30px, 4vw, 48px);
            line-height: 0.98;
            letter-spacing: -0.05em;
        }

        .topbar p {
            margin-top: 10px;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.7;
            max-width: 680px;
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
            min-height: 42px;
            padding: 11px 16px;
            border-radius: 12px;
            border: 1px solid var(--line);
            background: #fff;
            color: var(--ink);
            text-decoration: none;
            font-size: 13px;
            font-weight: 800;
            white-space: nowrap;
        }

        .btn.primary {
            border-color: var(--accent);
            background: var(--accent);
            color: #fff;
        }

        .alert-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 16px 18px;
            margin-bottom: 18px;
            border-radius: var(--radius);
            border: 1px solid rgba(239, 68, 68, 0.2);
            background: #fff;
            box-shadow: var(--shadow);
        }

        .alert-strip strong {
            display: block;
            font-size: 14px;
        }

        .alert-strip span {
            display: block;
            margin-top: 3px;
            color: var(--muted);
            font-size: 13px;
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px;
            margin-bottom: 18px;
        }

        .metric,
        .panel {
            background: var(--panel);
            border: 1px solid var(--line);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .metric {
            padding: 20px;
            min-height: 132px;
            display: flex;
            justify-content: space-between;
            gap: 14px;
            overflow: hidden;
            position: relative;
        }

        .metric::before {
            content: "";
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: var(--accent);
        }

        .metric-label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .metric-value {
            margin-top: 11px;
            font-family: "Space Grotesk", sans-serif;
            font-size: 38px;
            font-weight: 700;
            line-height: 1;
            letter-spacing: -0.05em;
        }

        .metric-note {
            margin-top: 10px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .metric-icon {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 14px;
            color: var(--accent);
            background: var(--accent-soft);
            flex-shrink: 0;
        }

        .grid {
            display: grid;
            grid-template-columns: minmax(0, 1.4fr) minmax(320px, 0.8fr);
            gap: 18px;
            align-items: start;
        }

        .stack {
            display: grid;
            gap: 18px;
        }

        .panel {
            padding: 20px;
        }

        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 14px;
            border-bottom: 1px solid var(--line);
            margin-bottom: 14px;
        }

        .panel-title {
            font-size: 15px;
            font-weight: 800;
        }

        .panel-link {
            color: var(--accent);
            font-size: 12px;
            font-weight: 800;
            text-decoration: none;
        }

        .health {
            display: grid;
            grid-template-columns: 180px minmax(0, 1fr);
            gap: 20px;
            align-items: center;
        }

        .ring {
            width: 154px;
            height: 154px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: conic-gradient(var(--accent) <?php echo $approvalPercent; ?>%, #e5e7eb 0);
        }

        .ring-inner {
            width: 116px;
            height: 116px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: #fff;
            text-align: center;
        }

        .ring-inner strong {
            font-family: "Space Grotesk", sans-serif;
            font-size: 32px;
            line-height: 1;
        }

        .ring-inner span {
            margin-top: 4px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .health-list {
            display: grid;
            gap: 12px;
        }

        .health-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 13px 14px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #f8fafc;
            font-size: 13px;
            font-weight: 800;
        }

        .health-row span {
            color: var(--muted);
            font-weight: 700;
        }

        .table-wrap {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 720px;
            border-collapse: collapse;
        }

        th {
            color: var(--muted);
            text-align: left;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            padding: 0 12px 12px;
        }

        td {
            border-top: 1px solid var(--line);
            padding: 15px 12px;
            vertical-align: middle;
            font-size: 13px;
        }

        .title-cell {
            font-weight: 800;
            color: var(--ink);
        }

        .subtext {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 76px;
            padding: 7px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .is-approved { background: #dcfce7; color: var(--good); }
        .is-pending { background: #fef3c7; color: var(--warn); }
        .is-rejected { background: #fee2e2; color: var(--bad); }

        .mini-actions {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
        }

        .icon-btn {
            width: 34px;
            height: 34px;
            display: inline-grid;
            place-items: center;
            border-radius: 10px;
            color: var(--ink);
            border: 1px solid var(--line);
            background: #fff;
            text-decoration: none;
        }

        .icon-btn:hover {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }

        .list {
            display: grid;
            gap: 12px;
        }

        .person,
        .event,
        .activity {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding: 14px;
            border: 1px solid var(--line);
            border-radius: 14px;
            background: #f8fafc;
        }

        .list-icon {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            display: grid;
            place-items: center;
            color: var(--accent);
            background: #fff;
            border: 1px solid var(--line);
            flex-shrink: 0;
        }

        .list-body {
            min-width: 0;
            flex: 1;
        }

        .list-title {
            font-size: 13px;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .list-meta {
            margin-top: 4px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
            line-height: 1.5;
        }

        .empty {
            padding: 24px;
            border: 1px dashed #cbd5e1;
            border-radius: 14px;
            color: var(--muted);
            text-align: center;
            font-size: 13px;
            font-weight: 700;
            background: #f8fafc;
        }

        @media (max-width: 1160px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar {
                position: relative;
                height: auto;
            }
            .nav {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
            .nav a.logout { margin-top: 0; }
            .metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 720px) {
            .main { padding: 18px; }
            .topbar,
            .alert-strip,
            .health {
                grid-template-columns: 1fr;
                display: grid;
            }
            .top-actions { justify-content: flex-start; }
            .metrics { grid-template-columns: 1fr; }
            .nav { grid-template-columns: 1fr; }
            .ring { margin: 0 auto; }
        }
    </style>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <div class="brand">
                <div class="brand-mark"><i class="fas fa-graduation-cap"></i></div>
                <div class="brand-text">Alumni<span>X</span></div>
            </div>

            <div class="admin-card">
                <div class="avatar"><?php echo adminE($initials); ?></div>
                <div>
                    <div class="admin-name"><?php echo adminE($adminName); ?></div>
                    <div class="admin-role">Administrator</div>
                </div>
            </div>

            <nav class="nav">
                <a class="active" href="admin_dashboard.php"><i class="fas fa-chart-line"></i> Dashboard</a>
                <a href="alumni_list.php"><i class="fas fa-users"></i> Alumni</a>
                <a href="jobs.php"><i class="fas fa-briefcase"></i> Jobs</a>
                <a href="view_applications.php"><i class="fas fa-file-lines"></i> Applications</a>
                <a href="event.php"><i class="fas fa-calendar-days"></i> Events</a>
                <a class="logout" href="logout.php"><i class="fas fa-power-off"></i> Logout</a>
            </nav>
        </aside>

        <main class="main">
            <header class="topbar">
                <div>
                    <div class="eyebrow"><i class="fas fa-circle-check"></i> Live Admin Workspace</div>
                    <h1>Dashboard</h1>
                    <p>Approve members, moderate jobs, track applications, and keep events visible from one focused control room.</p>
                </div>
                <div class="top-actions">
                    <a class="btn" href="alumni_list.php"><i class="fas fa-user-check"></i> Review Alumni</a>
                    <a class="btn primary" href="jobs.php"><i class="fas fa-briefcase"></i> Moderate Jobs</a>
                </div>
            </header>

            <?php if ($pendingWork > 0): ?>
                <section class="alert-strip">
                    <div>
                        <strong><?php echo number_format($pendingWork); ?> items need review</strong>
                        <span><?php echo number_format($stats["pending_alumni"]); ?> alumni request(s) and <?php echo number_format($stats["pending_jobs"]); ?> job post(s) are waiting.</span>
                    </div>
                    <a class="btn primary" href="<?php echo $stats["pending_alumni"] > 0 ? "alumni_list.php" : "jobs.php"; ?>">
                        <i class="fas fa-arrow-right"></i> Open Queue
                    </a>
                </section>
            <?php endif; ?>

            <section class="metrics">
                <article class="metric">
                    <div>
                        <div class="metric-label">Verified Alumni</div>
                        <div class="metric-value"><?php echo number_format($stats["verified_alumni"]); ?></div>
                        <div class="metric-note"><?php echo $approvalPercent; ?>% approval health</div>
                    </div>
                    <div class="metric-icon"><i class="fas fa-user-graduate"></i></div>
                </article>
                <article class="metric">
                    <div>
                        <div class="metric-label">Pending Review</div>
                        <div class="metric-value"><?php echo number_format($pendingWork); ?></div>
                        <div class="metric-note">Alumni and job approvals</div>
                    </div>
                    <div class="metric-icon"><i class="fas fa-clock"></i></div>
                </article>
                <article class="metric">
                    <div>
                        <div class="metric-label">Active Jobs</div>
                        <div class="metric-value"><?php echo number_format($stats["active_jobs"]); ?></div>
                        <div class="metric-note"><?php echo number_format($stats["applications"]); ?> total applications</div>
                    </div>
                    <div class="metric-icon"><i class="fas fa-briefcase"></i></div>
                </article>
                <article class="metric">
                    <div>
                        <div class="metric-label">Upcoming Events</div>
                        <div class="metric-value"><?php echo number_format($stats["upcoming_events"]); ?></div>
                        <div class="metric-note">Public event schedule</div>
                    </div>
                    <div class="metric-icon"><i class="fas fa-calendar-day"></i></div>
                </article>
            </section>

            <div class="grid">
                <div class="stack">
                    <section class="panel">
                        <div class="panel-head">
                            <h2 class="panel-title">Alumni Approval Health</h2>
                            <a class="panel-link" href="alumni_list.php">Manage alumni</a>
                        </div>
                        <div class="health">
                            <div class="ring">
                                <div class="ring-inner">
                                    <div>
                                        <strong><?php echo $approvalPercent; ?>%</strong>
                                        <span>Approved</span>
                                    </div>
                                </div>
                            </div>
                            <div class="health-list">
                                <div class="health-row">
                                    <span>Approved or active members</span>
                                    <strong><?php echo number_format($stats["verified_alumni"]); ?></strong>
                                </div>
                                <div class="health-row">
                                    <span>Waiting for approval email</span>
                                    <strong><?php echo number_format($stats["pending_alumni"]); ?></strong>
                                </div>
                                <div class="health-row">
                                    <span>Pending job moderation</span>
                                    <strong><?php echo number_format($stats["pending_jobs"]); ?></strong>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="panel">
                        <div class="panel-head">
                            <h2 class="panel-title">Job Pipeline</h2>
                            <a class="panel-link" href="jobs.php">View all jobs</a>
                        </div>
                        <?php if ($recentJobs): ?>
                            <div class="table-wrap">
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Role</th>
                                            <th>Owner</th>
                                            <th>Status</th>
                                            <th>Apps</th>
                                            <th style="text-align: right;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentJobs as $job): ?>
                                            <?php $status = strtolower((string) ($job["status"] ?: "pending")); ?>
                                            <tr>
                                                <td>
                                                    <div class="title-cell"><?php echo adminE($job["title"]); ?></div>
                                                    <div class="subtext"><?php echo adminE($job["company"] ?: "Unknown company"); ?> &middot; <?php echo adminE($job["location"] ?: "Flexible"); ?></div>
                                                </td>
                                                <td><?php echo adminE($job["owner_name"] ?: "Admin"); ?></td>
                                                <td><span class="badge <?php echo dashboardStatusClass($status); ?>"><?php echo adminE($status); ?></span></td>
                                                <td><?php echo number_format((int) $job["applications"]); ?></td>
                                                <td>
                                                    <div class="mini-actions">
                                                        <a class="icon-btn" href="jobs.php" title="Manage job"><i class="fas fa-sliders"></i></a>
                                                        <a class="icon-btn" href="view_applications.php?job_id=<?php echo (int) $job["id"]; ?>" title="View applications"><i class="fas fa-arrow-right"></i></a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="empty">No jobs have been posted yet.</div>
                        <?php endif; ?>
                    </section>
                </div>

                <aside class="stack">
                    <section class="panel">
                        <div class="panel-head">
                            <h2 class="panel-title">Pending Alumni</h2>
                            <a class="panel-link" href="alumni_list.php">Open list</a>
                        </div>
                        <?php if ($pendingAlumni): ?>
                            <div class="list">
                                <?php foreach ($pendingAlumni as $member): ?>
                                    <article class="person">
                                        <div class="list-icon"><i class="fas fa-user"></i></div>
                                        <div class="list-body">
                                            <div class="list-title"><?php echo adminE($member["full_name"]); ?></div>
                                            <div class="list-meta"><?php echo adminE($member["email"]); ?><br><?php echo adminE($member["student_id"] ?: "No student ID"); ?> &middot; <?php echo adminE($member["batch"] ?: "Batch N/A"); ?></div>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty">No pending alumni right now.</div>
                        <?php endif; ?>
                    </section>

                    <section class="panel">
                        <div class="panel-head">
                            <h2 class="panel-title">Upcoming Events</h2>
                            <a class="panel-link" href="event.php">Manage events</a>
                        </div>
                        <?php if ($upcomingEvents): ?>
                            <div class="list">
                                <?php foreach ($upcomingEvents as $event): ?>
                                    <?php $eventTime = !empty($event["event_date"]) ? strtotime((string) $event["event_date"]) : false; ?>
                                    <article class="event">
                                        <div class="list-icon"><i class="fas fa-calendar"></i></div>
                                        <div class="list-body">
                                            <div class="list-title"><?php echo adminE($event["title"]); ?></div>
                                            <div class="list-meta"><?php echo $eventTime ? adminE(date("d M Y", $eventTime)) : "Date TBA"; ?> &middot; <?php echo adminE($event["location"] ?: "Location TBA"); ?></div>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty">No upcoming events scheduled.</div>
                        <?php endif; ?>
                    </section>

                    <section class="panel">
                        <div class="panel-head">
                            <h2 class="panel-title">Recent Applications</h2>
                            <a class="panel-link" href="view_applications.php">View tracker</a>
                        </div>
                        <?php if ($recentApplications): ?>
                            <div class="list">
                                <?php foreach ($recentApplications as $application): ?>
                                    <?php $applyTime = !empty($application["apply_time"]) ? strtotime((string) $application["apply_time"]) : false; ?>
                                    <article class="activity">
                                        <div class="list-icon"><i class="fas fa-file-lines"></i></div>
                                        <div class="list-body">
                                            <div class="list-title"><?php echo adminE($application["alumni_name"]); ?></div>
                                            <div class="list-meta"><?php echo adminE($application["job_title"]); ?> at <?php echo adminE($application["company"]); ?><br><?php echo $applyTime ? adminE(date("d M Y, h:i A", $applyTime)) : "Recently"; ?></div>
                                        </div>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="empty">No applications submitted yet.</div>
                        <?php endif; ?>
                    </section>
                </aside>
            </div>
        </main>
    </div>
</body>
</html>
