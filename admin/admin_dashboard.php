<?php require __DIR__ . "/page_dashboard.php"; return; ?>
<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
    exit();
}
include("../includes/db.php"); 

// --- DATABASE UPDATES ---
if(isset($_GET['approve_user'])){
    $id = $_GET['approve_user'];
    $conn->query("UPDATE users SET status='active' WHERE id='$id'");
    header("Location: dashboard.php?res=success");
}

if(isset($_GET['delete_job'])){
    $id = $_GET['delete_job'];
    $conn->query("DELETE FROM jobs WHERE id='$id'");
    header("Location: dashboard.php?res=deleted");
}

// --- FETCH QUICK STATS ---
$pendingJobs = $conn->query("SELECT id FROM jobs WHERE status='pending'")->num_rows;
$pendingAlumni = $conn->query("SELECT id FROM users WHERE status='pending'")->num_rows;
$totalAlumni = $conn->query("SELECT id FROM users WHERE status='active'")->num_rows;
$totalEvents = $conn->query("SELECT id FROM events")->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Elite | AlumniX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { 
            --primary: #ff3b3b; 
            --primary-soft: rgba(255, 59, 59, 0.05);
            --bg-light: #fdfdfd; 
            --white: #ffffff;
            --text-dark: #0f172a;
            --text-muted: #64748b;
            --border: rgba(0,0,0,0.04);
            --shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.03), 0 10px 10px -5px rgba(0, 0, 0, 0.02);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body { 
            background-color: var(--bg-light); 
            color: var(--text-dark); 
            min-height: 100vh; 
            padding: 40px 8%; 
            overflow-x: hidden;
        }

        /* 🔴 INDEX PAGE VIBE: BLOOM EFFECTS */
        .bg-glow-1 {
            position: fixed; top: -10%; right: -5%;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(255, 59, 59, 0.06) 0%, transparent 70%);
            z-index: -1; filter: blur(60px);
        }
        .bg-glow-2 {
            position: fixed; bottom: -10%; left: -5%;
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(255, 59, 59, 0.04) 0%, transparent 70%);
            z-index: -1; filter: blur(50px);
        }

        /* 🚀 HEADER */
        .header-pill {
            display: flex; justify-content: space-between; align-items: center;
            margin-bottom: 60px;
        }
        .brand h1 { font-size: 1.8rem; font-weight: 800; letter-spacing: -1.5px; }
        .brand span { color: var(--primary); }

        .logout-link {
            background: var(--text-dark);
            color: #fff;
            padding: 12px 28px;
            border-radius: 100px;
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .logout-link:hover { transform: translateY(-3px); background: var(--primary); box-shadow: 0 10px 20px rgba(255, 59, 59, 0.2); }

        /* 📊 FLOATING STATS */
        .stat-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); 
            gap: 25px; 
            margin-bottom: 50px; 
        }
        .stat-card { 
            background: var(--white); 
            padding: 35px 30px; 
            border-radius: 35px; 
            border: 1px solid var(--border); 
            transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            box-shadow: var(--shadow);
        }
        .stat-card:hover { transform: translateY(-10px); border-color: var(--primary); }
        .stat-card h4 { font-size: 0.7rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 10px; font-weight: 700; }
        .stat-card p { font-size: 2.5rem; font-weight: 800; color: var(--text-dark); letter-spacing: -1px; }
        .stat-card.alert p { color: var(--primary); }

        /* 🛠️ CONTENT PANELS */
        .content-grid { display: grid; grid-template-columns: 1.3fr 1fr; gap: 30px; }
        .panel { 
            background: var(--white); 
            border-radius: 40px; 
            padding: 40px; 
            border: 1px solid var(--border); 
            box-shadow: var(--shadow);
        }
        .panel h3 { font-size: 1.4rem; margin-bottom: 30px; display: flex; align-items: center; gap: 15px; }
        .panel h3 i { color: var(--primary); background: var(--primary-soft); width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; border-radius: 15px; }

        /* 📋 TABLES */
        .data-table { width: 100%; border-collapse: collapse; }
        .data-table th { text-align: left; padding: 0 15px 15px; color: var(--text-muted); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; }
        .data-table td { padding: 25px 15px; font-size: 0.95rem; border-top: 1px solid #f8fafc; }

        /* 🔘 BUTTONS */
        .btn { padding: 10px 22px; border-radius: 15px; font-size: 0.85rem; font-weight: 700; text-decoration: none; transition: 0.3s; display: inline-block; }
        .btn-check { background: #10b981; color: #fff; }
        .btn-check:hover { background: #059669; transform: scale(1.05); }
        .btn-del { background: var(--primary-soft); color: var(--primary); border: 1px solid rgba(255, 59, 59, 0.1); }
        .btn-del:hover { background: var(--primary); color: #fff; }

        /* ⚡ FOOTER BAR */
        .footer-actions {
            margin-top: 40px;
            background: #0f172a;
            padding: 45px;
            border-radius: 45px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
        }

        @media (max-width: 1100px) { .content-grid { grid-template-columns: 1fr; } .footer-actions { flex-direction: column; text-align: center; gap: 20px; } }
    </style>
</head>
<body>

<div class="bg-glow-1"></div>
<div class="bg-glow-2"></div>

<header class="header-pill">
    <div class="brand">
        <h1>Alumni<span>X.</span>Console</h1>
    </div>
    <a href="../logout.php" class="logout-link">
        <i class="fas fa-power-off"></i> &nbsp;Terminate Session
    </a>
</header>

<section class="stat-grid">
    <div class="stat-card"><h4>System Members</h4><p><?php echo $totalAlumni; ?></p></div>
    <div class="stat-card alert"><h4>Verification Queue</h4><p><?php echo $pendingAlumni; ?></p></div>
    <div class="stat-card"><h4>Live Jobs</h4><p><?php echo $pendingJobs; ?></p></div>
    <div class="stat-card"><h4>Upcoming Events</h4><p><?php echo $totalEvents; ?></p></div>
</section>

<main class="content-grid">
    <div class="panel">
        <h3><i class="fas fa-user-shield"></i> Identity Verification</h3>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr><th>Applicant</th><th>Batch</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php
                    $u_req = $conn->query("SELECT * FROM users WHERE status='pending' LIMIT 5");
                    if($u_req->num_rows > 0){
                        while($u = $u_req->fetch_assoc()){
                            echo "<tr>
                                <td><b style='font-size:1.1rem'>{$u['full_name']}</b><br><span style='color:var(--text-muted); font-size:0.8rem;'>{$u['email']}</span></td>
                                <td><span style='background:#f1f5f9; padding:5px 12px; border-radius:8px; font-weight:600'>{$u['batch']}</span></td>
                                <td><a href='?approve_user={$u['id']}' class='btn btn-check'>Approve</a></td>
                            </tr>";
                        }
                    } else { echo "<tr><td colspan='3' style='text-align:center; padding:40px; color:var(--text-muted)'>All clear! No pending requests.</td></tr>"; }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="panel">
        <h3><i class="fas fa-briefcase"></i> Job Management</h3>
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr><th>Post Details</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php
                    $j_req = $conn->query("SELECT * FROM jobs ORDER BY id DESC LIMIT 5");
                    if($j_req->num_rows > 0){
                        while($j = $j_req->fetch_assoc()){
                            echo "<tr>
                                <td><b style='color:var(--text-dark)'>{$j['title']}</b><br><small>{$j['company']}</small></td>
                                <td><a href='?delete_job={$j['id']}' class='btn btn-del' onclick='return confirm(\"Wipe this record?\")'><i class='fas fa-trash-alt'></i></a></td>
                            </tr>";
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<div class="footer-actions">
    <div>
        <h2 style="font-size: 1.8rem; margin-bottom: 8px;">Quick Launch Pad</h2>
        <p style="opacity: 0.5;">Manage the Nagpur Alumni ecosystem effortlessly.</p>
    </div>
    <div style="display: flex; gap: 20px;">
        <a href="manage_events.php" class="btn" style="background: var(--primary); color: #fff; padding: 15px 35px; border-radius: 20px;">+ New Event</a>
        <a href="alumni_list.php" class="btn" style="background: #fff; color: #000; padding: 15px 35px; border-radius: 20px;">Full Database</a>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
    gsap.from(".stat-card", { opacity: 0, y: 30, stagger: 0.1, duration: 0.8, ease: "back.out(1.7)" });
    gsap.from(".panel", { opacity: 0, x: -20, stagger: 0.2, duration: 1, delay: 0.4 });
    gsap.from(".footer-actions", { opacity: 0, y: 50, duration: 1, delay: 0.8 });
</script>

</body>
</html>
