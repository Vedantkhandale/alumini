<?php
session_start();

// Database aur helpers (Untouched logic)
include("../includes/db.php"); 
require_once __DIR__ . "/helpers.php";
adminOnly();

if (isset($_GET["member_action"], $_GET["id"])) {
    $memberId = (int) $_GET["id"];
    $memberAction = $_GET["member_action"];
    $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

    if ($memberAction === "approve") {
        ob_start();
        error_reporting(0);
        ini_set('display_errors', 0);

        $result = alumnixApproveUserEngine($conn, $memberId);
        
        if ($isAjax) {
            ob_end_clean();
            header('Content-Type: application/json');
            echo json_encode($result);
            exit();
        }
        ob_end_clean();

        adminSetFlash($result["ok"] ? ($result["mail_sent"] ? "success" : "warning") : "error", $result["message"], $result["ok"] ? [
            "credential_name" => $result["name"] ?? "",
            "credential_email" => $result["email"] ?? "",
            "credential_password" => $result["mail_sent"] ? "" : ($result["password"] ?? ""),
        ] : []);
    } elseif ($memberAction === "reject") {
        $status = $conn->query("UPDATE users SET status='rejected' WHERE id={$memberId}");
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(["ok" => $status, "message" => "Member request marked as rejected."]);
            exit();
        }
        adminSetFlash("warning", "Member request marked as rejected.");
    } elseif ($memberAction === "delete") {
        $status = $conn->query("DELETE FROM users WHERE id={$memberId}");
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(["ok" => $status, "message" => "Member record deleted."]);
            exit();
        }
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
    <title>Premium Admin Workspace | AlumniX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        :root {
            --accent: #dc2626; /* Premium Crimson Red */
            --accent-hover: #991b1b;
            --accent-soft: rgba(220, 38, 38, 0.06);
            --ink: #0f172a; /* Solid Slate Black */
            --muted: #64748b;
            --surface: #ffffff; /* Clean White Background Preferred */
            --line: #e2e8f0;
            --bg: #ffffff; 
            --bg-alt: #f8fafc;
            --shadow: 0 12px 40px rgba(0, 0, 0, 0.03);
            --radius: 16px;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: var(--ink);
            background-color: var(--bg);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        .shell {
            width: min(1340px, calc(100% - 40px));
            margin: 0 auto;
            padding: 40px 0 60px;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            padding: 32px;
            border-radius: var(--radius);
            background: var(--surface);
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
            margin-bottom: 32px;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 14px;
            border-radius: 999px;
            background: var(--accent-soft);
            color: var(--accent);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            margin-bottom: 12px;
        }

        .topbar h1 {
            font-family: 'Space Grotesk', sans-serif;
            font-size: clamp(26px, 3vw, 36px);
            letter-spacing: -0.03em;
            font-weight: 700;
            color: var(--ink);
        }

        .topbar p {
            margin-top: 6px;
            color: var(--muted);
            font-size: 14px;
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
            gap: 8px;
            border-radius: 12px;
            padding: 10px 18px;
            text-decoration: none;
            font-weight: 700;
            font-size: 13px;
            transition: all 0.2s ease;
            border: 1px solid transparent;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--ink);
            color: #fff;
        }

        .btn-primary:hover {
            background: #1e293b;
            transform: translateY(-1px);
        }

        .btn-soft {
            background: #fff;
            color: var(--ink);
            border-color: var(--line);
        }

        .btn-soft:hover {
            background: var(--bg-alt);
            border-color: var(--ink);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card, .panel {
            background: var(--surface);
            border-radius: var(--radius);
            border: 1px solid var(--line);
            box-shadow: var(--shadow);
        }

        .stat-card {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            transition: border-color 0.2s ease;
        }

        .stat-card:hover { border-color: var(--ink); }

        .stat-label {
            color: var(--muted);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        .stat-value {
            font-family: 'Space Grotesk', sans-serif;
            font-size: 32px;
            font-weight: 700;
            color: var(--ink);
            line-height: 1;
        }

        .stat-foot { color: var(--muted); font-size: 12px; }

        /* Content Grid */
        .content-grid {
            display: grid;
            grid-template-columns: 1.4fr 0.85fr;
            gap: 24px;
        }

        .panel { padding: 32px; }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 24px;
            border-bottom: 1px solid var(--line);
            padding-bottom: 20px;
        }

        /* Sexy Custom Tab System */
        .tab-container {
            display: flex;
            gap: 8px;
            background: var(--bg-alt);
            padding: 4px;
            border-radius: 10px;
            border: 1px solid var(--line);
        }

        .tab-btn {
            background: transparent;
            border: none;
            padding: 8px 16px;
            font-size: 12px;
            font-weight: 700;
            color: var(--muted);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .tab-btn.active {
            background: var(--surface);
            color: var(--ink);
            box-shadow: 0 4px 12px rgba(0,0,0,0.04);
        }

        .tab-badge {
            font-size: 10px;
            padding: 2px 6px;
            border-radius: 999px;
            background: var(--ink);
            color: #fff;
        }
        .tab-badge.red-alert { background: var(--accent); }

        .search-wrapper {
            position: relative;
            width: 220px;
        }

        .search-wrapper i {
            position: absolute; left: 14px; top: 50%; transform: translateY(-50%);
            color: var(--muted); font-size: 13px;
        }

        .search-input {
            width: 100%; padding: 10px 14px 10px 36px;
            border-radius: 10px; border: 1px solid var(--line);
            font-family: inherit; font-size: 13px; font-weight: 600;
            background: var(--bg-alt); transition: all 0.2s ease;
        }

        .search-input:focus {
            outline: none; border-color: var(--ink); background: #fff;
        }

        /* Items Styles */
        .queue-pane { display: none; }
        .queue-pane.active { display: grid; gap: 14px; }

        .card-node {
            border-radius: 14px; padding: 18px;
            background: var(--bg-alt); border: 1px solid var(--line);
            display: grid; grid-template-columns: 1fr auto;
            align-items: center; gap: 16px;
            transition: all 0.2s ease;
        }

        .card-node:hover {
            background: #fff; border-color: var(--ink);
            box-shadow: 0 8px 24px rgba(0,0,0,0.02);
        }

        .node-title { font-size: 16px; font-weight: 700; margin-bottom: 6px; }
        
        .meta-flex { display: flex; flex-wrap: wrap; gap: 6px; }
        
        .badge-pill {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 10px; border-radius: 6px; background: #fff;
            border: 1px solid var(--line); font-size: 11px; font-weight: 600; color: var(--muted);
        }

        .actions-flex { display: flex; gap: 6px; }

        /* Action Buttons */
        .action-control {
            border: 1px solid var(--line); background: #fff;
            border-radius: 8px; padding: 8px 14px; font-size: 12px;
            font-weight: 700; cursor: pointer; transition: all 0.15s ease;
        }

        .action-control.cmd-approve { background: var(--ink); color: #fff; border: none; }
        .action-control.cmd-approve:hover { background: #1e293b; }

        .action-control.cmd-reject:hover { color: var(--accent); border-color: var(--accent); background: var(--accent-soft); }
        .action-control.cmd-delete:hover { color: #000; border-color: #000; background: #f1f5f9; }

        .action-control.disabled { opacity: 0.3; pointer-events: none; }

        /* Verified Row Badge */
        .verified-stamp {
            background: #f0fdf4; color: #16a34a; border-color: #bbf7d0;
            font-weight: 700; text-transform: uppercase; font-size: 10px; letter-spacing: 0.05em;
        }

        .panel-link {
            color: var(--accent); text-decoration: none; font-weight: 700; font-size: 13px;
        }

        .empty-state {
            padding: 40px 20px; text-align: center; color: var(--muted);
            border: 1px dashed var(--line); border-radius: 14px; font-size: 13px;
        }

        @media (max-width: 1240px) {
            .stats-grid { grid-template-columns: repeat(3, minmax(0, 1fr)); }
            .content-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            .topbar, .panel-head, .card-node { flex-direction: column; align-items: flex-start; }
            .stats-grid { grid-template-columns: 1fr; }
            .search-wrapper, .actions-flex { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div>
                <div class="eyebrow"><i class="fas fa-bolt"></i> Workspace Control</div>
                <h1>AlumniX Executive Engine</h1>
                <p>Monitor instant applications, verify credentials, and manage live campus parameters efficiently.</p>
            </div>
            <div class="top-actions">
                <a href="alumni_list.php" class="btn btn-soft">Alumni</a>
                <a href="jobs.php" class="btn btn-soft">Jobs</a>
                <a href="event.php" class="btn btn-soft">Events</a>
                <a href="logout.php" class="btn btn-primary"><i class="fas fa-power-off"></i></a>
            </div>
        </header>

        <?php if ($flash): ?>
            <div style="margin-bottom:24px; padding:16px; border-radius:12px; background:var(--bg-alt); border-left:4px solid var(--accent); font-size:13px; font-weight:600;">
                <?php echo adminE($flash["message"]); ?>
            </div>
        <?php endif; ?>

        <section class="stats-grid">
            <div class="stat-card">
                <span class="stat-label">Verification Holds</span>
                <span class="stat-value" style="color: var(--accent);"><?php echo number_format($stats["pending_alumni"]); ?></span>
                <p class="stat-foot">Profiles requiring action.</p>
            </div>
            <div class="stat-card">
                <span class="stat-label">Active Alumni</span>
                <span class="stat-value"><?php echo number_format($stats["active_alumni"]); ?></span>
                <p class="stat-foot">Verified portal accounts.</p>
            </div>
            <div class="stat-card">
                <span class="stat-label">Job Queue</span>
                <span class="stat-value"><?php echo number_format($stats["pending_jobs"]); ?></span>
                <p class="stat-foot">Pending moderation checks.</p>
            </div>
            <div class="stat-card">
                <span class="stat-label">Ecosystem Events</span>
                <span class="stat-value"><?php echo number_format($stats["live_events"]); ?></span>
                <p class="stat-foot">Scheduled items active.</p>
            </div>
            <div class="stat-card">
                <span class="stat-label">Submissions</span>
                <span class="stat-value"><?php echo number_format($stats["applications"]); ?></span>
                <p class="stat-foot">Aggregated job metrics.</p>
            </div>
        </section>

        <section class="content-grid">
            <article class="panel">
                <div class="panel-head">
                    <div class="tab-container">
                        <button class="tab-btn active" onclick="toggleDashboardTab('pending')">
                            Pending Requests <span class="tab-badge red-alert" id="badge-pending-count"><?php echo count($pendingUsers); ?></span>
                        </button>
                        <button class="tab-btn" onclick="toggleDashboardTab('approved')">
                            Approved Logs <span class="tab-badge" id="badge-approved-count">0</span>
                        </button>
                    </div>
                    
                    <div class="search-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" id="liveSearchNode" class="search-input" placeholder="Filter current view...">
                    </div>
                </div>

                <!-- Tab 1: Pending Queue -->
                <div id="pane-pending" class="queue-pane active">
                    <?php if ($pendingUsers): ?>
                        <?php foreach ($pendingUsers as $user): ?>
                            <div class="card-node" id="user-node-<?php echo (int)$user["id"]; ?>">
                                <div>
                                    <div class="node-title search-target-name"><?php echo adminE($user["full_name"]); ?></div>
                                    <div class="meta-flex">
                                        <span class="badge-pill"><i class="fas fa-envelope"></i> <?php echo adminE($user["email"]); ?></span>
                                        <span class="badge-pill"><i class="fas fa-graduation-cap"></i> Yr: <?php echo adminE($user["graduation_year"] ?: 'N/A'); ?></span>
                                        <span class="badge-pill search-target-company"><i class="fas fa-building"></i> <?php echo adminE($user["company"] ?: 'Independent'); ?></span>
                                    </div>
                                </div>
                                <div class="actions-flex">
                                    <button onclick="triggerEngineAction(<?php echo (int)$user['id']; ?>, 'approve', this)" class="action-control cmd-approve">Approve</button>
                                    <button onclick="triggerEngineAction(<?php echo (int)$user['id']; ?>, 'reject', this)" class="action-control cmd-reject">Reject</button>
                                    <button onclick="triggerEngineAction(<?php echo (int)$user['id']; ?>, 'delete', this)" class="action-control cmd-delete">Delete</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">No records awaiting administrative verification.</div>
                    <?php endif; ?>
                </div>

                <!-- Tab 2: Approved Logs (Live Feedback System) -->
                <div id="pane-approved" class="queue-pane">
                    <div class="empty-state" id="approved-empty-notice">No profiles approved during this session loop.</div>
                </div>
            </article>

            <!-- Sidebar Items -->
            <div style="display:grid; gap:24px; height:fit-content;">
                <article class="panel" style="padding:24px;">
                    <div class="panel-head" style="margin-bottom:16px; padding-bottom:12px;">
                        <div>
                            <h2 style="font-size:18px;">Job Feed</h2>
                        </div>
                        <a href="jobs.php" class="panel-link">Browse</a>
                    </div>
                    <div style="display:grid; gap:10px;">
                        <?php if ($recentJobs): foreach ($recentJobs as $job): ?>
                            <div style="padding:12px; background:var(--bg-alt); border-radius:10px; border:1px solid var(--line);">
                                <div style="font-size:14px; font-weight:700;"><?php echo adminE($job["title"]); ?></div>
                                <div style="font-size:11px; color:var(--muted); margin-top:4px;"><?php echo adminE($job["company"]); ?></div>
                            </div>
                        <?php endforeach; else: ?>
                            <div class="empty-state">No job streams.</div>
                        <?php endif; ?>
                    </div>
                </article>

                <article class="panel" style="padding:24px;">
                    <div class="panel-head" style="margin-bottom:16px; padding-bottom:12px;">
                        <div>
                            <h2 style="font-size:18px;">Radar</h2>
                        </div>
                    </div>
                    <div style="display:grid; gap:10px;">
                        <?php if ($recentEvents): foreach ($recentEvents as $ev): ?>
                            <div style="padding:12px; background:var(--bg-alt); border-radius:10px; border:1px solid var(--line);">
                                <div style="font-size:14px; font-weight:700;"><?php echo adminE($ev["title"]); ?></div>
                                <div style="font-size:11px; color:var(--muted); margin-top:4px;"><i class="fa fa-map-marker-alt"></i> <?php echo adminE($ev["location"]); ?></div>
                            </div>
                        <?php endforeach; else: ?>
                            <div class="empty-state">No events.</div>
                        <?php endif; ?>
                    </div>
                </article>
            </div>
        </section>
    </div>

    <script>
    let sessionApprovedCount = 0;

    function toggleDashboardTab(target) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelectorAll('.queue-pane').forEach(p => p.classList.remove('active'));
        
        const currentBtn = event.currentTarget;
        currentBtn.classList.add('active');
        document.getElementById(`pane-${target}`).classList.add('active');
    }

    document.getElementById('liveSearchNode').addEventListener('input', function(e) {
        const val = e.target.value.toLowerCase().trim();
        const activePane = document.querySelector('.queue-pane.active');
        const cards = activePane.querySelectorAll('.card-node');

        cards.forEach(c => {
            const name = c.querySelector('.search-target-name').textContent.toLowerCase();
            const compNode = c.querySelector('.search-target-company');
            const company = compNode ? compNode.textContent.toLowerCase() : '';
            
            if (name.includes(val) || company.includes(val)) {
                c.style.display = 'grid';
            } else {
                c.style.display = 'none';
            }
        });
    });

    async function triggerEngineAction(id, action, btn) {
        const row = document.getElementById(`user-node-${id}`);
        const actionButtons = row.querySelectorAll('.action-control');
        
        const confirmation = await Swal.fire({
            title: 'Confirm Operation',
            text: `Execute ${action} procedure on target record?`,
            icon: action === 'approve' ? 'info' : 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Yes, execute'
        });

        if (!confirmation.isConfirmed) return;

        actionButtons.forEach(b => b.classList.add('disabled'));
        const originalText = btn.innerHTML;
        btn.innerHTML = `<i class="fas fa-circle-notch fa-spin"></i>`;

        try {
            const res = await fetch(`?member_action=${action}&id=${id}`, {
                headers: { 'X-REQUESTED-WITH': 'XMLHttpRequest' }
            });
            const data = await res.json();

            if (action === 'approve' && data.ok) {
                Swal.fire({ title: 'Approved!', text: 'Account shifted to Active directory status.', icon: 'success', timer: 1500, showConfirmButton: false });
                
                // --- COOL PART: Shift Row dynamically to Approved Tab ---
                moveRowToApprovedLogs(row, id);
            } else if (data.ok || action !== 'approve') {
                Swal.fire({ title: 'Success', text: 'Operation completed successfully.', icon: 'success', timer: 1200, showConfirmButton: false });
                removeRowWithAnimation(row, 'pending');
            } else {
                Swal.fire('Failed', data.message || 'Operation halted by engine.', 'error');
                actionButtons.forEach(b => b.classList.remove('disabled'));
                btn.innerHTML = originalText;
            }
        } catch (e) {
            Swal.fire('Critical Error', 'Invalid response stream.', 'error');
            actionButtons.forEach(b => b.classList.remove('disabled'));
            btn.innerHTML = originalText;
        }
    }

    function moveRowToApprovedLogs(row, id) {
        // Remove Action buttons and add premium "Verified Stamp"
        const actionsContainer = row.querySelector('.actions-flex');
        if (actionsContainer) {
            actionsContainer.innerHTML = `<span class="badge-pill verified-stamp"><i class="fas fa-check-double"></i> Approved Live</span>`;
        }

        // Drop down structural opacity animation
        row.style.opacity = '0';
        row.style.transform = 'translateY(-10px)';

        setTimeout(() => {
            const approvedPane = document.getElementById('pane-approved');
            const emptyNotice = document.getElementById('approved-empty-notice');
            if (emptyNotice) emptyNotice.remove();

            // Append to approved container & fade in back elegantly
            approvedPane.appendChild(row);
            row.style.opacity = '1';
            row.style.transform = 'translateY(0)';

            // Update tab badges counter metrics
            sessionApprovedCount++;
            document.getElementById('badge-approved-count').textContent = sessionApprovedCount;
            
            const pendingContainer = document.getElementById('pane-pending');
            const totalPendingLeft = pendingContainer.querySelectorAll('.card-node').length;
            document.getElementById('badge-pending-count').textContent = totalPendingLeft;

            if (totalPendingLeft === 0) {
                pendingContainer.innerHTML = `<div class="empty-state">No records awaiting administrative verification.</div>`;
            }
        }, 250);
    }

    function removeRowWithAnimation(row, type) {
        row.style.opacity = '0';
        setTimeout(() => {
            row.remove();
            const container = document.getElementById(`pane-${type}`);
            const leftCount = container.querySelectorAll('.card-node').length;
            
            if(type === 'pending') {
                document.getElementById('badge-pending-count').textContent = leftCount;
                if (leftCount === 0) container.innerHTML = `<div class="empty-state">No records awaiting administrative verification.</div>`;
            }
        }, 250);
    }
    </script>
</body>
</html>