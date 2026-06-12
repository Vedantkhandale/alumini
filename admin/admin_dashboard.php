<?php
require_once __DIR__ . "/helpers.php";
adminOnly();

$adminName = (string) ($_SESSION["admin"] ?? "Admin");
$initials = strtoupper(substr(trim($adminName), 0, 1) ?: "A");

$stats = [
    "verified_alumni" => adminCount($conn, "SELECT COUNT(*) FROM alumni_users WHERE role='alumni' AND status IN ('approved', 'active')"),
    "pending_alumni" => adminCount($conn, "SELECT COUNT(*) FROM alumni_users WHERE role='alumni' AND status='pending'"),
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
     LEFT JOIN alumni_users u ON j.alumni_id = u.id
     LEFT JOIN job_applications ja ON ja.job_id = j.id
     GROUP BY j.id, j.title, j.company, j.location, j.status, u.full_name
     ORDER BY
        CASE j.status WHEN 'pending' THEN 0 WHEN 'approved' THEN 1 ELSE 2 END,
        j.id DESC
     LIMIT 6"
);

// 🛠️ Yahan 'alumini_users' ko sahi karke 'alumni_users' kar diya hai:
$pendingAlumni = adminRows(
    $conn,
    "SELECT id, full_name, email, student_id, batch
     FROM alumni_users
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
     JOIN alumni_users u ON ja.alumni_id = u.id
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
            /* Premium High-Contrast Red, Black & White Theme */
            --accent: #e60000; /* Vibrant Red */
            --accent-dark: #b30000;
            --accent-soft: rgba(230, 0, 0, 0.08);
            --ink: #000000; /* Pure Black */
            --muted: #666666;
            --line: #000000; /* Bold Black Lines */
            --panel: #ffffff; /* Pure White */
            --page: #ffffff; /* Pure White Background */
            --good: #000000;
            --warn: #e60000;
            --bad: #e60000;
            --shadow: 4px 4px 0px rgba(0, 0, 0, 1); /* Neobrutalism sharp shadow */
            --radius: 8px; /* Sharper edges for a modern premium feel */
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh;
            background: var(--page);
            color: var(--ink);
            font-family: "Plus Jakarta Sans", sans-serif;
            background-image: radial-gradient(rgba(0, 0, 0, 0.05) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .layout {
            display: grid;
            grid-template-columns: 280px minmax(0, 1fr);
            min-height: 100vh;
        }

        .sidebar {
            position: sticky;
            top: 0;
            height: 100vh;
            background: var(--ink); /* Deep black sidebar */
            color: #ffffff;
            padding: 30px 20px;
            display: flex;
            flex-direction: column;
            gap: 25px;
            border-right: 2px solid var(--accent);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 0 8px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .brand-mark,
        .avatar {
            display: grid;
            place-items: center;
            flex-shrink: 0;
            font-weight: 800;
        }

        .brand-mark {
            width: 45px;
            height: 45px;
            border-radius: var(--radius);
            background: var(--accent);
            color: #fff;
            font-size: 18px;
            box-shadow: 0 0 15px rgba(230, 0, 0, 0.4);
        }

        .brand-text {
            font-family: "Space Grotesk", sans-serif;
            font-size: 26px;
            font-weight: 700;
            letter-spacing: -0.03em;
        }

        .brand-text span { color: var(--accent); }

        .admin-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px;
            border-radius: var(--radius);
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .avatar {
            width: 45px;
            height: 45px;
            border-radius: var(--radius);
            background: #fff;
            color: var(--ink);
            font-size: 16px;
        }

        .admin-name {
            font-size: 15px;
            font-weight: 800;
            line-height: 1.2;
        }

        .admin-role {
            margin-top: 4px;
            font-size: 11px;
            color: #a1a1aa;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .nav {
            display: grid;
            gap: 8px;
        }

        .nav a {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            border-radius: var(--radius);
            color: #a1a1aa;
            text-decoration: none;
            font-size: 15px;
            font-weight: 700;
            transition: var(--transition);
            border: 1px solid transparent;
        }

        .nav a:hover,
        .nav a.active {
            background: rgba(230, 0, 0, 0.1);
            color: #ffffff;
            border-color: var(--accent);
            transform: translateX(4px);
        }

        .nav a.active i { color: var(--accent); }

        .nav a.logout {
            margin-top: auto;
            color: #ff4d4d;
        }

        .nav a.logout:hover {
            background: #ff4d4d;
            color: #000;
        }

        .main {
            min-width: 0;
            padding: 35px 40px;
        }

        .topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-bottom: 30px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--accent);
            font-size: 13px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            background: var(--accent-soft);
            padding: 6px 12px;
            border-radius: 20px;
            border: 1px solid var(--accent);
        }

        h1 {
            margin-top: 12px;
            font-family: "Space Grotesk", sans-serif;
            font-size: clamp(32px, 4vw, 54px);
            line-height: 1;
            letter-spacing: -0.04em;
            color: var(--ink);
        }

        .topbar p {
            margin-top: 12px;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.6;
            max-width: 600px;
            font-weight: 500;
        }

        .top-actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-end;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 46px;
            padding: 0 20px;
            border-radius: var(--radius);
            border: 2px solid var(--ink);
            background: var(--panel);
            color: var(--ink);
            text-decoration: none;
            font-size: 14px;
            font-weight: 800;
            white-space: nowrap;
            transition: var(--transition);
            box-shadow: 3px 3px 0px var(--ink);
        }

        .btn:hover {
            transform: translate(-2px, -2px);
            box-shadow: 5px 5px 0px var(--ink);
        }

        .btn.primary {
            border-color: var(--accent);
            background: var(--accent);
            color: #ffffff;
            box-shadow: 3px 3px 0px var(--ink);
        }
        
        .btn.primary:hover {
            box-shadow: 5px 5px 0px var(--ink);
        }

        .alert-strip {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 20px 24px;
            margin-bottom: 24px;
            border-radius: var(--radius);
            border: 2px solid var(--ink);
            background: var(--panel);
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .alert-strip::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 6px;
            background: var(--accent);
        }

        .alert-strip strong {
            display: block;
            font-size: 16px;
            font-weight: 800;
        }

        .alert-strip span {
            display: block;
            margin-top: 4px;
            color: var(--muted);
            font-size: 14px;
            font-weight: 600;
        }

        .metrics {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 24px;
        }

        .metric,
        .panel {
            background: var(--panel);
            border: 2px solid var(--ink);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: var(--transition);
        }
        
        .metric:hover {
            transform: translateY(-4px);
            box-shadow: 6px 6px 0px var(--ink);
        }

        .metric {
            padding: 24px;
            min-height: 140px;
            display: flex;
            justify-content: space-between;
            gap: 14px;
            position: relative;
        }

        .metric-label {
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .metric-value {
            margin-top: 12px;
            font-family: "Space Grotesk", sans-serif;
            font-size: 42px;
            font-weight: 700;
            line-height: 1;
            color: var(--ink);
        }

        .metric-note {
            margin-top: 12px;
            color: var(--accent);
            font-size: 13px;
            font-weight: 700;
        }

        .metric-icon {
            width: 50px;
            height: 50px;
            display: grid;
            place-items: center;
            border-radius: var(--radius);
            color: #fff;
            background: var(--ink);
            font-size: 20px;
            flex-shrink: 0;
            border: 2px solid var(--ink);
            box-shadow: 2px 2px 0px var(--accent);
        }

        .grid {
            display: grid;
            grid-template-columns: minmax(0, 1.5fr) minmax(340px, 0.9fr);
            gap: 24px;
            align-items: start;
        }

        .stack {
            display: grid;
            gap: 24px;
        }

        .panel {
            padding: 24px;
        }

        .panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--ink);
            margin-bottom: 20px;
        }

        .panel-title {
            font-size: 18px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .panel-link {
            color: var(--accent);
            font-size: 13px;
            font-weight: 800;
            text-decoration: none;
            text-transform: uppercase;
            border-bottom: 2px solid transparent;
            transition: 0.2s;
        }

        .panel-link:hover { border-color: var(--accent); }

        .health {
            display: grid;
            grid-template-columns: 180px minmax(0, 1fr);
            gap: 24px;
            align-items: center;
        }

        .ring {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: conic-gradient(var(--accent) <?php echo $approvalPercent; ?>%, var(--ink) 0);
            box-shadow: 0 0 0 2px var(--ink);
        }

        .ring-inner {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            display: grid;
            place-items: center;
            background: var(--panel);
            text-align: center;
            border: 2px solid var(--ink);
        }

        .ring-inner strong {
            font-family: "Space Grotesk", sans-serif;
            font-size: 34px;
            line-height: 1;
            color: var(--ink);
        }

        .ring-inner span {
            margin-top: 4px;
            color: var(--accent);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .health-list {
            display: grid;
            gap: 14px;
        }

        .health-row {
            display: flex;
            justify-content: space-between;
            gap: 12px;
            padding: 14px 16px;
            border: 2px solid var(--ink);
            border-radius: var(--radius);
            background: #ffffff;
            font-size: 14px;
            font-weight: 800;
            transition: var(--transition);
        }

        .health-row:hover {
            transform: translateX(4px);
            border-color: var(--accent);
        }

        .health-row span {
            color: var(--muted);
            font-weight: 700;
        }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            min-width: 720px;
            border-collapse: separate;
            border-spacing: 0 8px;
        }

        th {
            color: var(--ink);
            text-align: left;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 0 16px 8px;
            border-bottom: 2px solid var(--ink);
        }

        td {
            padding: 16px;
            vertical-align: middle;
            font-size: 14px;
            background: #ffffff;
            border-top: 2px solid var(--ink);
            border-bottom: 2px solid var(--ink);
        }

        td:first-child { border-left: 2px solid var(--ink); border-radius: var(--radius) 0 0 var(--radius); }
        td:last-child { border-right: 2px solid var(--ink); border-radius: 0 var(--radius) var(--radius) 0; }

        .title-cell {
            font-weight: 800;
            color: var(--ink);
            font-size: 15px;
        }

        .subtext {
            margin-top: 6px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 700;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 80px;
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            border: 2px solid var(--ink);
        }

        .is-approved { background: #ffffff; color: var(--ink); border-color: var(--ink); box-shadow: 2px 2px 0px var(--ink); }
        .is-pending { background: var(--accent); color: #ffffff; border-color: var(--ink); box-shadow: 2px 2px 0px var(--ink); }
        .is-rejected { background: #000000; color: #ffffff; border-color: var(--ink); }

        .mini-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .icon-btn {
            width: 36px;
            height: 36px;
            display: inline-grid;
            place-items: center;
            border-radius: var(--radius);
            color: var(--ink);
            border: 2px solid var(--ink);
            background: #fff;
            text-decoration: none;
            transition: var(--transition);
            box-shadow: 2px 2px 0px var(--ink);
        }

        .icon-btn:hover {
            background: var(--accent);
            color: #fff;
            transform: translate(-2px, -2px);
            box-shadow: 4px 4px 0px var(--ink);
        }

        .list {
            display: grid;
            gap: 14px;
        }

        .person,
        .event,
        .activity {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            padding: 16px;
            border: 2px solid var(--ink);
            border-radius: var(--radius);
            background: #ffffff;
            transition: var(--transition);
        }

        .person:hover,
        .event:hover,
        .activity:hover {
            border-color: var(--accent);
            transform: translateX(4px);
            box-shadow: 4px 4px 0px rgba(0,0,0,0.1);
        }

        .list-icon {
            width: 42px;
            height: 42px;
            border-radius: var(--radius);
            display: grid;
            place-items: center;
            color: #ffffff;
            background: var(--ink);
            border: 2px solid var(--ink);
            flex-shrink: 0;
            font-size: 16px;
            box-shadow: 2px 2px 0px var(--accent);
        }

        .list-body {
            min-width: 0;
            flex: 1;
        }

        .list-title {
            font-size: 14px;
            font-weight: 800;
            overflow-wrap: anywhere;
            color: var(--ink);
        }

        .list-meta {
            margin-top: 6px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
            line-height: 1.5;
        }

        .empty {
            padding: 30px;
            border: 2px dashed var(--ink);
            border-radius: var(--radius);
            color: var(--ink);
            text-align: center;
            font-size: 14px;
            font-weight: 800;
            background: rgba(0,0,0,0.02);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        @media (max-width: 1160px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar {
                position: relative;
                height: auto;
                border-right: none;
                border-bottom: 2px solid var(--accent);
            }
            .nav {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
            .nav a.logout { margin-top: 0; }
            .metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 720px) {
            .main { padding: 20px; }
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