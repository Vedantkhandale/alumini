<?php
// 1. DATABASE CONNECTION LAYER
// include("../config/db_connect.php"); 

// Fallback high-fidelity Alumni Data Layer 
$db_active = false; 
$stats = [
    "total_alumni" => 1420,
    "placement_rate" => 88,
    "active_jobs" => 42,
    "pending_approvals" => 15
];

$employment_pool = ["total" => 1420, "employed" => 1150, "entrepreneur" => 180, "higher_studies" => 90];
$monthly_registrations = [35, 48, 65, 52, 80, 95, 110, 142]; // Alumni signups trend
$recent_jobs = [
    ["job_id" => 101, "title" => "SDE-1 (Backend)", "company" => "TCS / Nagpur", "trend" => [20, 45, 90, 65]],
    ["job_id" => 102, "title" => "Frontend Engineer", "company" => "InfoCepts", "trend" => [35, 70, 55, 80]],
    ["job_id" => 103, "title" => "Data Analyst Role", "company" => "Persistent", "trend" => [50, 30, 65, 85]]
];

// --- Live Production Query Example for AlumniX ---
/*
try {
    if (isset($conn)) {
        $db_active = true;
        $stats["total_alumni"] = (int)$conn->query("SELECT COUNT(*) FROM alumni WHERE status='verified'")->fetch_row()[0];
        $stats["active_jobs"] = (int)$conn->query("SELECT COUNT(*) FROM job_postings WHERE expiry_date >= NOW()")->fetch_row()[0];
        
        // Fetch Job Posts with Application Trends
        $job_query = $conn->query("SELECT id, job_title, company_name, metrics_string FROM job_postings ORDER BY created_at DESC LIMIT 3");
        $recent_jobs = [];
        while($row = $job_query->fetch_assoc()) {
            $recent_jobs[] = [
                "job_id" => $row['id'],
                "title" => $row['job_title'],
                "company" => $row['company_name'],
                "trend" => explode(',', $row['metrics_string']) // e.g. "20,40,60,80"
            ];
        }
    }
} catch (Exception $e) {
    // Fail-safe fallback logic execution
}
*/

// Math Matrix for SVG Arc Circles
$employed_deg = ($employment_pool["employed"] / $employment_pool["total"]) * 440;
$entrepreneur_deg = ($employment_pool["entrepreneur"] / $employment_pool["total"]) * 440;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AlumniX Core Engine</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --crimson: #e11d48;
            --crimson-dark: #be123c;
            --crimson-light: #fff1f2;
            --pitch-black: #09090b;
            --slate-gray: #71717a;
            --border-line: #e4e4e7;
            --pure-white: #ffffff;
            --zebra-tint: #fafafa;
            --radius-sharp: 4px;
            --bezier-fast: cubic-bezier(0.25, 1, 0.5, 1);
            --bezier-fluid: cubic-bezier(0.4, 0, 0.2, 1);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--pitch-black);
            background-color: var(--pure-white);
            display: flex;
            min-height: 100vh;
            letter-spacing: -0.02em;
            overflow-x: hidden;
        }

        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes drawStroke { to { stroke-dashoffset: 0; } }
        @keyframes growHeight { from { height: 0; } }

        /* Left Navigation Console Frame */
        .sidebar-console {
            width: 260px;
            background: var(--pure-white);
            border-right: 2px solid var(--pitch-black);
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            z-index: 100;
        }

        .sidebar-brand-block {
            padding: 24px; display: flex; align-items: center; gap: 12px; border-bottom: 1px solid var(--border-line);
        }
        .brand-logo-icon {
            width: 32px; height: 32px; background: var(--pitch-black); display: flex;
            align-items: center; justify-content: center; color: var(--pure-white); font-weight: 800; border-radius: var(--radius-sharp);
        }
        .brand-title { font-size: 20px; font-weight: 800; text-transform: uppercase; letter-spacing: -0.03em; }

        .user-profile-anchor {
            padding: 24px; background: var(--zebra-tint); border-bottom: 1px solid var(--border-line); display: flex; flex-direction: column; align-items: center; text-align: center;
        }
        .avatar-frame {
            width: 64px; height: 64px; border: 2px solid var(--pitch-black); border-radius: var(--radius-sharp); margin-bottom: 12px;
            background: #e2e8f0; display: flex; align-items: center; justify-content: center; font-size: 24px; color: var(--slate-gray); overflow: hidden;
        }
        .user-profile-anchor .profile-name { font-size: 14px; font-weight: 700; color: var(--pitch-black); }
        .user-profile-anchor .profile-role { font-size: 11px; font-weight: 600; color: var(--slate-gray); text-transform: uppercase; margin-top: 2px; }

        .sidebar-nav-container { flex: 1; overflow-y: auto; padding: 16px 12px; display: flex; flex-direction: column; gap: 4px; }
        .nav-node-link {
            display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; font-size: 13px; font-weight: 700;
            text-decoration: none; color: var(--pitch-black); border-radius: var(--radius-sharp); transition: var(--bezier-fluid); cursor: pointer;
        }
        .nav-node-link:hover, .nav-node-link.active { background: var(--pitch-black); color: var(--pure-white); }
        .nav-node-link .link-label-group { display: flex; align-items: center; gap: 12px; }
        .nav-node-link i { width: 16px; text-align: center; font-size: 14px; }
        
        .badge-tag-new { background: var(--crimson); color: var(--pure-white); font-size: 9px; font-weight: 800; padding: 2px 6px; border-radius: 2px; text-transform: uppercase; }

        .submenu-stack { padding-left: 42px; display: flex; flex-direction: column; gap: 2px; margin-bottom: 4px; }
        .submenu-item { padding: 8px 12px; font-size: 12px; font-weight: 600; color: var(--slate-gray); text-decoration: none; border-radius: var(--radius-sharp); transition: var(--bezier-fluid); }
        .submenu-item:hover, .submenu-item.active { color: var(--crimson); background: var(--crimson-light); }

        /* Central Work Arena */
        .main-workspace-shell { flex: 1; display: flex; flex-direction: column; min-width: 0; }
        .top-global-header { height: 70px; background: var(--pure-white); border-bottom: 1px solid var(--border-line); display: flex; align-items: center; justify-content: space-between; padding: 0 32px; }
        .search-container { position: relative; width: 340px; }
        .search-container input {
            width: 100%; padding: 10px 14px 10px 38px; border-radius: var(--radius-sharp); border: 1px solid var(--border-line);
            font-size: 13px; font-weight: 600; background: var(--zebra-tint); transition: var(--bezier-fluid);
        }
        .search-container input:focus { outline: none; border-color: var(--pitch-black); background: var(--pure-white); }
        .search-container i { position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: var(--slate-gray); }

        .header-toolbar-group { display: flex; align-items: center; gap: 20px; }
        .action-icon-trigger { background: transparent; border: none; font-size: 16px; color: var(--pitch-black); cursor: pointer; position: relative; transition: var(--bezier-fluid); }
        .action-icon-trigger:hover { color: var(--crimson); transform: scale(1.05); }
        .notification-marker { position: absolute; top: -4px; right: -4px; width: 6px; height: 6px; background: var(--crimson); border-radius: 50%; }

        .workspace-scroller { flex: 1; padding: 32px; overflow-y: auto; max-width: 1400px; width: 100%; margin: 0 auto; }
        .breadcrumb-navbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; }
        .breadcrumb-navbar h2 { font-size: 28px; font-weight: 800; letter-spacing: -0.04em; text-transform: uppercase; }
        .route-path-log { font-size: 12px; font-weight: 700; color: var(--slate-gray); }
        .route-path-log span { color: var(--pitch-black); }

        /* KPI Interface Layout Matrix */
        .kpi-row-layout { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 20px; margin-bottom: 32px; }
        .kpi-metric-card {
            background: var(--pure-white); border: 1px solid var(--border-line); padding: 24px; border-radius: var(--radius-sharp);
            display: flex; justify-content: space-between; align-items: flex-start; transition: var(--bezier-fast); animation: slideInUp 0.5s var(--bezier-fast) both;
        }
        .kpi-metric-card:hover { border-color: var(--pitch-black); transform: translateY(-4px); box-shadow: 0 12px 24px rgba(0,0,0,0.03); }
        .kpi-metric-card .num-value { font-size: 34px; font-weight: 800; color: var(--pitch-black); line-height: 1; margin-bottom: 6px; }
        .kpi-metric-card .metric-label { font-size: 12px; font-weight: 700; color: var(--slate-gray); text-transform: uppercase; letter-spacing: 0.02em; }
        .kpi-metric-card .icon-shell {
            width: 42px; height: 42px; border: 1px solid var(--border-line); display: flex; align-items: center; justify-content: center;
            font-size: 16px; color: var(--pitch-black); border-radius: var(--radius-sharp); transition: var(--bezier-fluid);
        }
        .kpi-metric-card:hover .icon-shell { background: var(--pitch-black); color: var(--pure-white); border-color: var(--pitch-black); }

        /* Chart Tri-Section Panel CSS */
        .analytics-tri-grid { display: grid; grid-template-columns: 0.9fr 1.2fr 1fr; gap: 24px; margin-bottom: 32px; }
        .panel-widget-node { background: var(--pure-white); border: 1px solid var(--border-line); border-radius: var(--radius-sharp); padding: 24px; display: flex; flex-direction: column; animation: slideInUp 0.6s var(--bezier-fast) both; }
        .panel-widget-node:hover { border-color: var(--pitch-black); }
        .widget-header-title { font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.02em; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border-line); }

        .chart-canvas-wrapper { flex: 1; display: flex; align-items: center; justify-content: center; position: relative; min-height: 200px; }
        
        .svg-donut-circle { transform: rotate(-90deg); transform-origin: 50% 50%; stroke-dasharray: 440; transition: stroke-dashoffset 1s var(--bezier-fast); }
        
        .bar-chart-flex-container { display: flex; align-items: flex-end; justify-content: space-between; width: 100%; height: 180px; padding-top: 10px; }
        .bar-column-node { width: 14px; background: var(--pitch-black); border-radius: var(--radius-sharp) var(--radius-sharp) 0 0; position: relative; animation: growHeight 0.8s var(--bezier-fast) both; transition: var(--bezier-fluid); }
        .bar-column-node:hover { background: var(--crimson); }
        .bar-column-node:hover::after {
            content: attr(data-value); position: absolute; top: -24px; left: 50%; transform: translateX(-50%);
            font-size: 10px; font-weight: 800; background: var(--pitch-black); color: var(--pure-white); padding: 2px 6px; border-radius: 2px; white-space: nowrap; z-index: 10;
        }

        .analytics-dual-split { display: grid; grid-template-columns: 1.4fr 1fr; gap: 24px; }

        /* Dynamic Table Grid Engine */
        .custom-data-table { width: 100%; border-collapse: collapse; text-align: left; }
        .custom-data-table th { font-size: 11px; font-weight: 800; text-transform: uppercase; color: var(--slate-gray); padding: 12px 16px; border-bottom: 2px solid var(--pitch-black); }
        .custom-data-table td { padding: 14px 16px; font-size: 13px; font-weight: 600; border-bottom: 1px solid var(--border-line); color: var(--pitch-black); vertical-align: middle; }
        .custom-data-table tr:hover td { background: var(--zebra-tint); }

        .sparkline-micro-flex { display: flex; align-items: flex-end; gap: 3px; height: 20px; width: 60px; }
        .sparkline-bar { flex: 1; background: var(--border-line); height: 40%; transition: var(--bezier-fluid); }
        .custom-data-table tr:hover .sparkline-bar { background: var(--pitch-black); }
        .custom-data-table tr:hover .sparkline-bar.high-point { background: var(--crimson); }

        .row-action-btn-cluster { display: flex; gap: 4px; }
        .action-tool-btn {
            border: 1px solid var(--border-line); background: var(--pure-white); color: var(--pitch-black); width: 28px; height: 28px;
            display: inline-flex; align-items: center; justify-content: center; font-size: 11px; cursor: pointer; border-radius: var(--radius-sharp); transition: var(--bezier-fluid);
            text-decoration: none;
        }
        .action-tool-btn:hover { border-color: var(--pitch-black); background: var(--pitch-black); color: var(--pure-white); }
        .action-tool-btn.btn-delete-alert:hover { background: var(--crimson); color: var(--pure-white); border-color: var(--crimson); }

        @media (max-width: 1200px) {
            .kpi-row-layout { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .analytics-tri-grid, .analytics-dual-split { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <aside class="sidebar-console">
        <div class="sidebar-brand-block">
            <div class="brand-logo-icon"><i class="fas fa-user-graduate"></i></div>
            <div class="brand-title">AlumniX</div>
        </div>
        
        <div class="user-profile-anchor">
            <div class="avatar-frame"><i class="fas fa-shield-alt"></i></div>
            <div class="profile-name">Vedant Admin</div>
            <div class="profile-role">Core Controller</div>
        </div>

        <nav class="sidebar-nav-container">
            <a href="alumni_dashboard.php" class="nav-node-link active">
                <div class="link-label-group"><i class="fas fa-chart-line"></i><span>Control Panel</span></div>
                <i class="fas fa-chevron-down" style="font-size: 10px;"></i>
            </a>
            <div class="submenu-stack">
                <a href="alumni_dashboard.php" class="submenu-item active">Core Metrics</a>
                <a href="alumni_directory.php" class="submenu-item">Alumni Directory</a>
                <a href="verifications.php" class="submenu-item">Pending Approvals</a>
            </div>

            <a href="job_board.php" class="nav-node-link">
                <div class="link-label-group"><i class="fas fa-briefcase"></i><span>Job Board</span></div>
                <span class="badge-tag-new">Live</span>
            </a>
            <a href="events_manager.php" class="nav-node-link">
                <div class="link-label-group"><i class="fas fa-handshake"></i><span>Chapters & Events</span></div>
            </a>
            <a href="donations.php" class="nav-node-link">
                <div class="link-label-group"><i class="fas fa-donate"></i><span>Fund Tracking</span></div>
            </a>
            <a href="system_settings.php" class="nav-node-link">
                <div class="link-label-group"><i class="fas fa-sliders-h"></i><span>Config Systems</span></div>
            </a>
        </nav>
    </aside>

    <main class="main-workspace-shell">
        <header class="top-global-header">
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search across alumni network nodes...">
            </div>
            <div class="header-toolbar-group">
                <button class="action-icon-trigger"><i class="far fa-bell"></i><span class="notification-marker"></span></button>
                <button class="action-icon-trigger"><i class="far fa-envelope"></i></button>
                <a href="admin_profile.php" class="action-icon-trigger" style="font-size: 18px;"><i class="far fa-user-circle"></i></a>
            </div>
        </header>

        <div class="workspace-scroller">
            <div class="breadcrumb-navbar">
                <div>
                    <h2>Alumni Core Engine</h2>
                </div>
                <div class="route-path-log">Network &nbsp;/&nbsp; <span>Dashboard Console</span></div>
            </div>

            <section class="kpi-row-layout">
                <div class="kpi-metric-card" style="animation-delay: 0.05s;">
                    <div>
                        <div class="num-value"><?php echo number_format($stats["total_alumni"]); ?></div>
                        <div class="metric-label">Verified Alumni</div>
                    </div>
                    <div class="icon-shell"><i class="fas fa-users"></i></div>
                </div>
                <div class="kpi-metric-card" style="animation-delay: 0.1s;">
                    <div>
                        <div class="num-value"><?php echo $stats["placement_rate"]; ?>%</div>
                        <div class="metric-label">Employment Rate</div>
                    </div>
                    <div class="icon-shell"><i class="fas fa-award"></i></div>
                </div>
                <div class="kpi-metric-card" style="animation-delay: 0.15s;">
                    <div>
                        <div class="num-value"><?php echo $stats["active_jobs"]; ?></div>
                        <div class="metric-label">Active Job Posts</div>
                    </div>
                    <div class="icon-shell"><i class="fas fa-business-time"></i></div>
                </div>
                <div class="kpi-metric-card" style="animation-delay: 0.2s;">
                    <div>
                        <div class="num-value" style="color: var(--crimson);"><?php echo $stats["pending_approvals"]; ?></div>
                        <div class="metric-label">Pending Reviews</div>
                    </div>
                    <div class="icon-shell" style="color: var(--crimson);"><i class="fas fa-user-clock"></i></div>
                </div>
            </section>

            <section class="analytics-tri-grid">
                <article class="panel-widget-node" style="animation-delay: 0.25s;">
                    <h3 class="widget-header-title">Alumni Profile Metrics</h3>
                    <div class="chart-canvas-wrapper">
                        <svg width="160" height="160" viewBox="0 0 160 160">
                            <circle cx="80" cy="80" r="70" fill="transparent" stroke="#e4e4e7" stroke-width="12"/>
                            <circle class="svg-donut-circle" cx="80" cy="80" r="70" fill="transparent" stroke="#09090b" stroke-width="12" stroke-dashoffset="<?php echo (440 - $employed_deg); ?>"/>
                            <circle class="svg-donut-circle" cx="80" cy="80" r="70" fill="transparent" stroke="#e11d48" stroke-width="12" stroke-dashoffset="<?php echo (440 - $entrepreneur_deg); ?>"/>
                        </svg>
                        <div style="position: absolute; text-align: center;">
                            <div style="font-size: 22px; font-weight: 800; color: var(--pitch-black);"><?php echo $employment_pool["total"]; ?></div>
                            <div style="font-size: 10px; font-weight: 700; color: var(--slate-gray); text-transform: uppercase;">Total Pool</div>
                        </div>
                    </div>
                </article>

                <article class="panel-widget-node" style="animation-delay: 0.3s;">
                    <h3 class="widget-header-title">Monthly Alumni Signups</h3>
                    <div class="bar-chart-flex-container">
                        <?php foreach ($monthly_registrations as $index => $count): ?>
                            <div class="bar-column-node" data-value="<?php echo $count; ?> Users" style="height: <?php echo ($count / max($monthly_registrations)) * 100; ?>%; animation-delay: <?php echo ($index * 0.05); ?>s;"></div>
                        <?php endforeach; ?>
                    </div>
                </article>

                <article class="panel-widget-node" style="animation-delay: 0.35s;">
                    <h3 class="widget-header-title">Job Board Metrics</h3>
                    <div class="chart-canvas-wrapper">
                        <svg width="100%" height="150" viewBox="0 0 300 150" preserveAspectRatio="none">
                            <path d="M0,130 Q60,40 120,100 T240,30 T300,5" fill="none" stroke="#e11d48" stroke-width="3" style="stroke-dasharray: 1000; stroke-dashoffset: 1000; animation: drawStroke 1.5s var(--bezier-fast) forwards;"/>
                            <path d="M0,145 Q60,80 120,120 T240,60 T300,20" fill="none" stroke="#09090b" stroke-width="2" style="stroke-dasharray: 1000; stroke-dashoffset: 1000; animation: drawStroke 1.5s var(--bezier-fast) 0.2s forwards;"/>
                        </svg>
                    </div>
                </article>
            </section>

            <section class="analytics-dual-split">
                <article class="panel-widget-node" style="animation-delay: 0.4s;">
                    <h3 class="widget-header-title">Chapter Engagement Trend</h3>
                    <div class="chart-canvas-wrapper" style="min-height: 220px;">
                        <svg width="100%" height="180" viewBox="0 0 500 180" preserveAspectRatio="none">
                            <path d="M0,140 C120,120 180,50 280,80 C380,100 420,20 500,10" fill="none" stroke="#09090b" stroke-width="3"/>
                            <path d="M0,165 C120,145 180,95 280,105 C380,115 420,50 500,35" fill="none" stroke="#e11d48" stroke-width="2" stroke-dasharray="4,4"/>
                        </svg>
                    </div>
                </article>

                <article class="panel-widget-node" style="animation-delay: 0.45s; padding: 24px 16px;">
                    <h3 class="widget-header-title" style="margin-left: 8px;">Active Job Board Analytics</h3>
                    <table class="custom-data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Role Title / Node</th>
                                <th>App Trend</th>
                                <th style="text-align: right;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_jobs as $job): 
                                $max_trend = max($job["trend"]);
                            ?>
                                <tr>
                                    <td>#<?php echo $job["job_id"]; ?></td>
                                    <td>
                                        <strong><?php echo htmlspecialchars($job["title"]); ?></strong>
                                        <div style="font-size: 11px; color: var(--slate-gray); font-weight: 500; margin-top: 2px;"><?php echo htmlspecialchars($job["company"]); ?></div>
                                    </td>
                                    <td>
                                        <div class="sparkline-micro-flex">
                                            <?php foreach ($job["trend"] as $point): ?>
                                                <div class="sparkline-bar <?php echo ($point == $max_trend) ? 'high-point' : ''; ?>" style="height: <?php echo $point; ?>%;"></div>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                    <td style="text-align: right;">
                                        <div class="row-action-btn-cluster" style="justify-content: flex-end;">
                                            <a href="job_edit.php?id=<?php echo $job["job_id"]; ?>" class="action-tool-btn"><i class="fas fa-sliders-h"></i></a>
                                            <a href="job_delete.php?id=<?php echo $job["job_id"]; ?>" class="action-tool-btn btn-delete-alert"><i class="fas fa-trash-alt"></i></a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </article>
            </section>
        </div>
    </main>
</body>
</html>