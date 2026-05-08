<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: admin_login.php");
    exit();
}
include("../includes/db.php"); 

// --- ⚡ SHARP LOGIC ENGINE ---
if(isset($_GET['action']) && isset($_GET['id'])){
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $type = $_GET['type'];
    $act = $_GET['action'];

    if($type == 'user' && $act == 'approve') $conn->query("UPDATE users SET status='active' WHERE id='$id'");
    if($type == 'job' && $act == 'approve') $conn->query("UPDATE jobs SET status='approved' WHERE id='$id'");
    if($act == 'delete'){
        $table = ($type == 'user') ? 'users' : (($type == 'job') ? 'jobs' : 'events');
        $conn->query("DELETE FROM $table WHERE id='$id'");
    }
    header("Location: admin_dashboard.php?msg=success");
}

// Stats fetch
$pendingU = $conn->query("SELECT id FROM users WHERE status='pending'")->num_rows;
$pendingJ = $conn->query("SELECT id FROM jobs WHERE status='pending'")->num_rows;
$totalE = $conn->query("SELECT id FROM events")->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin OS | AlumniX Pro</title>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;500;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>

    <style>
        :root {
            --primary: #ff3e3e;
            --dark: #0f172a;
            --white: #ffffff;
            --bg: #f8fafc;
            --border: #e2e8f0;
            --sidebar-width: 260px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: var(--bg); color: var(--dark); display: flex; overflow-x: hidden; }

        /* --- 🛸 PRO SIDEBAR --- */
        .sidebar {
            width: var(--sidebar-width); height: 100vh; background: var(--white);
            border-right: 1px solid var(--border); position: fixed; padding: 40px 20px;
            display: flex; flex-direction: column; z-index: 100;
        }
        .brand { font-size: 1.4rem; font-weight: 800; letter-spacing: -1px; margin-bottom: 50px; }
        .brand span { color: var(--primary); }

        .nav-link {
            padding: 14px 18px; border-radius: 12px; text-decoration: none; color: #64748b;
            font-weight: 600; display: flex; align-items: center; gap: 15px; margin-bottom: 8px;
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .nav-link:hover, .nav-link.active { background: #f1f5f9; color: var(--dark); transform: translateX(5px); }
        .nav-link.active { border-left: 4px solid var(--primary); background: #fff1f1; color: var(--primary); }

        /* --- 🏛️ MAIN VIEWPORT --- */
        .main-content { margin-left: var(--sidebar-width); width: calc(100% - var(--sidebar-width)); padding: 40px 50px; }
        
        .glass-header { 
            display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; 
            background: rgba(255,255,255,0.7); backdrop-filter: blur(10px); padding: 20px; border-radius: 20px;
            border: 1px solid var(--border);
        }

        /* --- 📊 BENTO GRID STATS --- */
        .bento-stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 40px; }
        .stat-box { 
            background: var(--white); padding: 30px; border-radius: 24px; border: 1px solid var(--border);
            box-shadow: 0 4px 20px rgba(0,0,0,0.02); transition: 0.3s;
        }
        .stat-box:hover { border-color: var(--primary); transform: translateY(-5px); }
        .stat-box label { font-size: 0.75rem; font-weight: 800; color: #94a3b8; text-transform: uppercase; }
        .stat-box h2 { font-size: 2.2rem; font-weight: 800; margin-top: 10px; }

        /* --- 📋 CONTROL TABS --- */
        .data-panel { background: var(--white); border-radius: 30px; border: 1px solid var(--border); padding: 35px; box-shadow: 0 10px 40px rgba(0,0,0,0.03); }
        .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        
        table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
        th { text-align: left; padding: 10px 15px; font-size: 0.75rem; color: #94a3b8; text-transform: uppercase; }
        td { padding: 20px 15px; background: #fafafa; border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); }
        td:first-child { border-left: 1px solid var(--border); border-radius: 15px 0 0 15px; }
        td:last-child { border-right: 1px solid var(--border); border-radius: 0 15px 15px 0; }

        .action-btn {
            width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;
            text-decoration: none; color: var(--dark); border: 1px solid var(--border); transition: 0.3s;
        }
        .action-btn.approve:hover { background: #10b981; color: white; border-color: #10b981; }
        .action-btn.delete:hover { background: var(--primary); color: white; border-color: var(--primary); }

        @media (max-width: 1000px) { .sidebar { width: 80px; } .sidebar span { display: none; } .main-content { margin-left: 80px; } .bento-stats { grid-template-columns: 1fr 1fr; } }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="brand">ALUMNI<span>X</span></div>
    <nav>
        <a href="admin_dashboard.php" class="nav-link active"><i class="fas fa-home"></i> <span>Dashboard</span></a>
        <a href="alumni_list.php" class="nav-link"><i class="fas fa-users"></i> <span>Manage Alumni</span></a>
        <a href="jobs.php" class="nav-link"><i class="fas fa-briefcase"></i> <span>Job Requests</span></a>
        <a href="event.php" class="nav-link"><i class="fas fa-calendar"></i> <span>Events Control</span></a>
        <a href="helpers.php" class="nav-link"><i class="fas fa-magic"></i> <span>System Tools</span></a>
    </nav>
    <a href="logout.php" class="nav-link" style="margin-top:auto; color: var(--primary);"><i class="fas fa-power-off"></i> <span>Logout</span></a>
</aside>

<main class="main-content">
    <header class="glass-header">
        <div>
            <h1 style="font-weight: 800; font-size: 1.5rem;">System Console</h1>
            <p style="color: #64748b; font-size: 0.9rem;">Welcome back, Master Admin</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <div style="text-align: right; margin-right: 10px;">
                <p style="font-weight: 700; font-size: 0.85rem;">Admin User</p>
                <span style="color: #10b981; font-size: 0.7rem;"><i class="fas fa-circle"></i> Live Server</span>
            </div>
            <div style="width: 45px; height: 45px; border-radius: 12px; background: var(--dark);"></div>
        </div>
    </header>

    <div class="bento-stats">
        <div class="stat-box">
            <label>Pending Alumni</label>
            <h2 style="color: var(--primary);"><?= $pendingU ?></h2>
        </div>
        <div class="stat-box">
            <label>Job Approvals</label>
            <h2><?= $pendingJ ?></h2>
        </div>
        <div class="stat-box">
            <label>Total Events</label>
            <h2><?= $totalE ?></h2>
        </div>
        <div class="stat-box">
            <label>System Health</label>
            <h2 style="color: #10b981;">98%</h2>
        </div>
    </div>

    <div class="data-panel">
        <div class="panel-header">
            <h3 style="font-weight: 800;">Recent Verification Requests</h3>
            <a href="alumni_list.php" style="text-decoration: none; color: var(--primary); font-size: 0.8rem; font-weight: 700;">VIEW ALL DATABASE →</a>
        </div>

        <table>
            <thead><tr><th>Alumni Name</th><th>Batch</th><th>Email Address</th><th>Status</th><th>Control</th></tr></thead>
            <tbody>
                <?php
                $requests = $conn->query("SELECT * FROM users WHERE status='pending' LIMIT 5");
                while($row = $requests->fetch_assoc()): ?>
                <tr>
                    <td><b><?= $row['full_name'] ?></b></td>
                    <td><span style="background: #f1f5f9; padding: 5px 12px; border-radius: 8px; font-weight: 700; font-size: 0.8rem;"><?= $row['batch'] ?></span></td>
                    <td style="color: #64748b;"><?= $row['email'] ?></td>
                    <td><span style="color: orange; font-weight: 800; font-size: 0.7rem;">● WAITING</span></td>
                    <td>
                        <div style="display: flex; gap: 8px;">
                            <a href="#" onclick="confirmAct('?type=user&action=approve&id=<?= $row['id'] ?>')" class="action-btn approve"><i class="fas fa-check"></i></a>
                            <a href="#" onclick="confirmAct('?type=user&action=delete&id=<?= $row['id'] ?>')" class="action-btn delete"><i class="fas fa-trash-alt"></i></a>
                        </div>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
    // Animations
    gsap.from(".stat-box", { opacity: 0, y: 30, duration: 0.8, stagger: 0.1 });
    gsap.from(".data-panel", { opacity: 0, scale: 0.95, delay: 0.5, duration: 1 });

    function confirmAct(url) {
        Swal.fire({
            title: 'Proceed with Action?',
            text: "This will modify the live system data.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0f172a',
            cancelButtonColor: '#ff3e3e',
            confirmButtonText: 'Yes, Execute'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = url;
        })
    }

    // Success Toast
    const urlParams = new URLSearchParams(window.location.search);
    if(urlParams.get('msg') === 'success') {
        Swal.fire({ icon: 'success', title: 'Action Successful', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
    }
</script>
</body>
</html>