<?php
require_once __DIR__ . "/helpers.php";
adminOnly();

$applications = adminRows(
    $conn,
    "SELECT
        ja.id,
        u.full_name AS alumni_name,
        u.email AS alumni_email,
        j.title AS job_role,
        j.company,
        ja.apply_time
     FROM job_applications ja
     JOIN users u ON ja.alumni_id = u.id
     JOIN jobs j ON ja.job_id = j.id
     ORDER BY ja.apply_time DESC"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Applications Tracker | AlumniX Admin</title>
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
        .table-wrap {
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

        .table-wrap {
            margin-top: 22px;
            border-radius: 30px;
            padding: 22px;
            overflow-x: auto;
        }

        table { width: 100%; border-collapse: separate; border-spacing: 0 12px; }
        th {
            text-align: left;
            padding: 0 16px 8px;
            color: var(--muted);
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }

        td {
            padding: 18px 16px;
            background: rgba(248, 250, 252, 0.92);
            border-top: 1px solid var(--line);
            border-bottom: 1px solid var(--line);
            font-size: 14px;
        }

        td:first-child {
            border-left: 1px solid var(--line);
            border-radius: 20px 0 0 20px;
        }

        td:last-child {
            border-right: 1px solid var(--line);
            border-radius: 0 20px 20px 0;
        }

        .user-name {
            display: block;
            font-weight: 800;
            margin-bottom: 4px;
        }

        .user-email {
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
        }

        .tag {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 77, 77, 0.12);
            color: var(--accent);
            font-size: 12px;
            font-weight: 800;
        }

        .empty {
            padding: 40px 24px;
            text-align: center;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.84);
            color: var(--muted);
            border: 1px dashed var(--line);
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
                <div class="eyebrow"><i class="fas fa-list-check"></i> Applications Tracker</div>
                <h1>See who is actually moving on the roles you publish.</h1>
                <p>Every application is listed with alumni identity, job title, company, and timestamp so the admin team stays informed.</p>
            </div>
            <nav class="nav">
                <a href="admin_dashboard.php" class="btn btn-soft"><i class="fas fa-grid-2"></i> Dashboard</a>
                <a href="jobs.php" class="btn btn-soft"><i class="fas fa-briefcase"></i> Jobs</a>
                <a href="alumni_list.php" class="btn btn-soft"><i class="fas fa-users"></i> Alumni</a>
                <a href="logout.php" class="btn btn-primary"><i class="fas fa-power-off"></i> Logout</a>
            </nav>
        </header>

        <section class="table-wrap">
            <?php if ($applications): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Alumni</th>
                            <th>Role</th>
                            <th>Company</th>
                            <th>Applied At</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $application): ?>
                            <tr>
                                <td>
                                    <span class="user-name"><?php echo adminE($application["alumni_name"]); ?></span>
                                    <span class="user-email"><?php echo adminE($application["alumni_email"]); ?></span>
                                </td>
                                <td><span class="tag"><?php echo adminE($application["job_role"]); ?></span></td>
                                <td><?php echo adminE($application["company"]); ?></td>
                                <td><?php echo adminE(date('d M Y, h:i A', strtotime((string) $application["apply_time"]))); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty">
                    <i class="fas fa-inbox" style="font-size: 36px; margin-bottom: 12px;"></i>
                    <p>No job applications have been submitted yet.</p>
                </div>
            <?php endif; ?>
        </section>
    </div>
</body>
</html>
