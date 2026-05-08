<?php
require_once __DIR__ . "/helpers.php";
adminOnly();

// --- ⚡ ADMIN CORE ACTIONS ---
if (isset($_GET["approve_user"])) {
    $id = (int) $_GET["approve_user"];
    $conn->query("UPDATE users SET status='approved' WHERE id='$id'");
    header("Location: admin_dashboard.php?res=approved");
    exit();
}

// Stats Collection
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
    <title>Console Pro | AlumniX Admin</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;500;700;800&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <style>
        :root {
            --primary: #ff3e3e;
            --secondary: #0f172a;
            --bg: #fafafa;
            --white: #ffffff;
            --border: rgba(15, 23, 42, 0.08);
            --text-main: #1e293b;
            --text-dim: #64748b;
            --grad: linear-gradient(135deg, #ff3e3e, #ff8144);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background-color: var(--bg); color: var(--text-main); min-height: 100vh; overflow-x: hidden; }

        /* --- 🛸 MODERN BACKGROUND --- */
        .bg-mesh {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: -1;
            background-image: 
                radial-gradient(at 0% 0%, rgba(255, 62, 62, 0.05) 0px, transparent 50%),
                radial-gradient(at 100% 100%, rgba(15, 23, 42, 0.05) 0px, transparent 50%);
        }

        .shell { width: min(1280px, calc(100% - 40px)); margin: 0 auto; padding: 40px 0; }

        /* --- 🏛️ GLASS HEADER --- */
        header.top-panel {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid var(--white);
            border-radius: 32px;
            padding: 30px 40px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 20px 40px rgba(15, 23, 42, 0.05);
            margin-bottom: 30px;
        }

        h1 { font-family: 'Space Grotesk', sans-serif; font-size: 2.2rem; font-weight: 700; letter-spacing: -2px; }
        h1 span { color: var(--primary); }
        .header-meta p { color: var(--text-dim); font-size: 0.95rem; font-weight: 500; }

        /* --- ⚡ QUICK ACTIONS --- */
        .btn {
            padding: 12px 24px; border-radius: 16px; font-weight: 800; font-size: 0.85rem;
            text-decoration: none; display: inline-flex; align-items: center; gap: 8px;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .btn-main { background: var(--secondary); color: var(--white); }
        .btn-main:hover { transform: translateY(-5px); background: var(--primary); box-shadow: 0 10px 20px rgba(255, 62, 62, 0.2); }
        .btn-outline { background: var(--white); color: var(--secondary); border: 1px solid var(--border); }
        .btn-outline:hover { background: #f8fafc; border-color: var(--secondary); }

        /* --- 📈 STATS BENTO --- */
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
        .stat-card {
            background: var(--white); border-radius: 28px; padding: 30px;
            border: 1px solid var(--border); transition: 0.3s;
        }
        .stat-card:hover { border-color: var(--primary); transform: translateY(-3px); }
        .stat-card label { font-size: 0.75rem; font-weight: 800; color: var(--text-dim); text-transform: uppercase; letter-spacing: 1px; }
        .stat-card .val { display: block; font-size: 2.5rem; font-weight: 800; margin-top: 5px; font-family: 'Space Grotesk', sans-serif; }

        /* --- 📑 MAIN CONTENT GRID --- */
        .content-grid { display: grid; grid-template-columns: 1.4fr 1fr; gap: 30px; }
        .panel {
            background: var(--white); border-radius: 35px; padding: 35px;
            border: 1px solid var(--border); box-shadow: 0 10px 30px rgba(0,0,0,0.02);
        }
        .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
        .panel-header h2 { font-family: 'Space Grotesk', sans-serif; font-size: 1.6rem; letter-spacing: -1px; }

        /* --- 📝 LIST ITEMS --- */
        .data-list { display: flex; flex-direction: column; gap: 15px; }
        .data-item {
            padding: 20px; border-radius: 24px; background: #fcfcfd;
            border: 1px solid #f1f5f9; display: flex; justify-content: space-between; align-items: center;
            transition: 0.3s;
        }
        .data-item:hover { background: #fff; border-color: var(--primary); transform: scale(1.02); }
        
        .user-info strong { display: block; font-size: 1.05rem; }
        .user-info span { font-size: 0.85rem; color: var(--text-dim); font-weight: 600; }

        .status-pill {
            padding: 6px 14px; border-radius: 99px; font-size: 0.7rem; font-weight: 800;
            text-transform: uppercase; background: #fff1f1; color: var(--primary);
        }

        /* Responsive */
        @media (max-width: 1024px) { .stats-grid { grid-template-columns: 1fr 1fr; } .content-grid { grid-template-columns: 1fr; } }
        @media (max-width: 640px) { header.top-panel { flex-direction: column; gap: 20px; text-align: center; } .stats-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="bg-mesh"></div>

<div class="shell">
    <header class="top-panel">
        <div class="header-meta">
            <h1>Alumni<span>X</span>.admin</h1>
            <p>System Online: Welcome back to the command center.</p>
        </div>
        <div class="actions">
            <a href="alumni_list.php" class="btn btn-outline"><i class="fas fa-users"></i> Directory</a>
            <a href="logout.php" class="btn btn-main"><i class="fas fa-power-off"></i> Logout</a>
        </div>
    </header>

    <section class="stats-grid">
        <div class="stat-card">
            <label>Verify Requests</label>
            <span class="val" style="color: var(--primary);"><?= $stats['pending_alumni'] ?></span>
        </div>
        <div class="stat-card">
            <label>Active Members</label>
            <span class="val"><?= $stats['active_alumni'] ?></span>
        </div>
        <div class="stat-card">
            <label>Pending Jobs</label>
            <span class="val"><?= $stats['pending_jobs'] ?></span>
        </div>
        <div class="stat-card">
            <label>Live Events</label>
            <span class="val"><?= $stats['live_events'] ?></span>
        </div>
    </section>

    <div class="content-grid">
        <!-- Approval Queue -->
        <article class="panel">
            <div class="panel-header">
                <h2>Approval Queue</h2>
                <a href="alumni_list.php" style="color: var(--primary); font-weight: 800; font-size: 0.8rem; text-decoration: none;">VIEW ALL →</a>
            </div>
            <div class="data-list">
                <?php if ($pendingUsers): ?>
                    <?php foreach ($pendingUsers as $user): ?>
                        <div class="data-item">
                            <div class="user-info">
                                <strong><?= adminE($user["full_name"]) ?></strong>
                                <span><?= adminE($user["email"]) ?> • Batch <?= adminE($user["batch"]) ?></span>
                            </div>
                            <a href="?approve_user=<?= (int) $user["id"] ?>" class="btn btn-main" style="padding: 10px 20px; border-radius: 12px;">Approve</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px; color: var(--text-dim);">
                        <i class="fas fa-check-circle" style="font-size: 2rem; margin-bottom: 10px;"></i>
                        <p>No pending approvals. Enjoy the silence!</p>
                    </div>
                <?php endif; ?>
            </div>
        </article>

        <!-- Job Feed -->
        <article class="panel">
            <div class="panel-header">
                <h2>Job Activity</h2>
                <a href="jobs.php" style="color: var(--secondary); font-weight: 800; font-size: 0.8rem; text-decoration: none;">MODERATE →</a>
            </div>
            <div class="data-list">
                <?php if ($recentJobs): ?>
                    <?php foreach ($recentJobs as $job): ?>
                        <div class="data-item">
                            <div class="user-info">
                                <strong><?= adminE($job["title"]) ?></strong>
                                <span><?= adminE($job["company"]) ?></span>
                            </div>
                            <span class="status-pill"><?= adminE($job["status"]) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="text-align: center; padding: 40px;">
                        <p>No job activity found.</p>
                    </div>
                <?php endif; ?>
            </div>
        </article>
    </div>

    <!-- Quick Navigation Pad -->
    <section class="panel" style="margin-top: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 20px;">
        <div>
            <h2 style="font-family: 'Space Grotesk';">Management Hub</h2>
            <p style="color: var(--text-dim);">Quickly jump to specialized moderation pages.</p>
        </div>
        <div class="actions">
            <a href="jobs.php" class="btn btn-outline"><i class="fas fa-briefcase"></i> Job Queue</a>
            <a href="event.php" class="btn btn-outline"><i class="fas fa-calendar-alt"></i> Events</a>
            <a href="alumni_list.php" class="btn btn-main">Member Directory</a>
        </div>
    </section>
</div>

<script>
    // --- 🎭 SMOOTH GSAP ANIMATIONS ---
    gsap.from(".stat-card", { opacity: 0, y: 20, stagger: 0.1, duration: 0.8, ease: "power4.out" });
    gsap.from(".panel", { opacity: 0, y: 30, duration: 1, delay: 0.4, ease: "power4.out" });
    gsap.from("header.top-panel", { opacity: 0, scale: 0.9, duration: 1, ease: "elastic.out(1, 0.7)" });
</script>

</body>
</html>