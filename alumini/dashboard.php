<?php
    session_start();
    error_reporting(E_ALL); // Error reporting on taaki real error dikhe
    ini_set('display_errors', 1);

    // --- 1. SECURE DB CONNECTION ---
    $db_paths = ["includes/db.php", "../includes/db.php", "../../includes/db.php", "db.php"];
    $connected = false;
    foreach ($db_paths as $path) {
        if (file_exists($path)) {
            include($path);
            $connected = true;
            break;
        }
    }

    if (!$connected) {
        die("Error: 'db.php' file nahi mili. Path check kar bhai!");
    }

    // --- 2. SESSION CHECK ---
    if (!isset($_SESSION['user'])) {
        header("Location: ../login.php");
        exit();
    }
    $user = $_SESSION['user'];
    $alumni_id = $user['id'];

    // --- 3. EVENT APPLY LOGIC (Fixed Warning) ---
    if (isset($_GET['apply_event']) && !empty($_GET['apply_event'])) {
        $event_id = mysqli_real_escape_string($conn, $_GET['apply_event']);
        $check = $conn->query("SELECT * FROM event_applications WHERE event_id='$event_id' AND alumni_id='$alumni_id'");
        if ($check && $check->num_rows == 0) {
            $conn->query("INSERT INTO event_applications (event_id, alumni_id) VALUES ('$event_id', '$alumni_id')");
            header("Location: dashboard.php?msg=applied"); // Redirect to avoid re-submit
            exit();
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRO Dashboard | AlumniX</title>
    
    <!-- Premium Fonts & Icons -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --accent: #e11d48; /* Premium Red */
            --dark: #0f172a;   /* Rich Black */
            --white: #ffffff;
            --bg: #f8fafc;
            --border: #e2e8f0;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }

        body { background: var(--bg); color: var(--dark); overflow-x: hidden; }

        .dashboard-container { display: flex; min-height: 100vh; padding: 20px; gap: 20px; }

        /* --- GLASS SIDEBAR --- */
        .sidebar {
            width: 280px; background: var(--dark); border-radius: 30px;
            padding: 40px 20px; color: white; display: flex; flex-direction: column;
            transition: 0.3s;
        }
        .logo { font-size: 24px; font-weight: 800; margin-bottom: 50px; text-align: center; }
        .logo span { color: var(--accent); }

        .nav-item {
            padding: 15px 20px; border-radius: 15px; text-decoration: none;
            color: #94a3b8; font-weight: 600; margin-bottom: 10px; display: flex; align-items: center; gap: 12px;
            transition: 0.3s;
        }
        .nav-item:hover, .nav-item.active { background: rgba(255,255,255,0.1); color: white; }
        .nav-item i { color: var(--accent); }

        /* --- MAIN FEED --- */
        .main-feed { flex: 1; display: flex; flex-direction: column; gap: 25px; }

        .top-nav {
            background: white; padding: 20px 30px; border-radius: 25px;
            display: flex; justify-content: space-between; align-items: center;
            border: 1px solid var(--border);
        }

        /* --- BENTO GRID --- */
        .bento-grid {
            display: grid; grid-template-columns: 2fr 1fr; gap: 25px;
        }

        .card {
            background: white; border-radius: 30px; padding: 30px;
            border: 1px solid var(--border); transition: 0.3s;
        }
        .card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.05); }

        .job-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 20px; border-radius: 20px; background: #f1f5f9; margin-bottom: 15px;
        }
        .btn-red {
            background: var(--accent); color: white; border: none; padding: 10px 20px;
            border-radius: 12px; font-weight: 700; cursor: pointer; transition: 0.3s;
        }
        .btn-red:hover { background: var(--dark); }

        /* Floating Button */
        .fab {
            position: fixed; bottom: 30px; right: 30px; width: 60px; height: 60px;
            background: var(--accent); color: white; border-radius: 20px;
            display: flex; align-items: center; justify-content: center; font-size: 24px;
            cursor: pointer; box-shadow: 0 10px 20px rgba(225, 29, 72, 0.3);
        }

        @media (max-width: 900px) { .sidebar { display: none; } .bento-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="logo">Alumni<span>X</span></div>
        <a href="jobs.php" class="nav-item active"><i class="fas fa-th-large"></i> Overview</a>
        <a href="#" class="nav-item"><i class="fas fa-briefcase"></i> Jobs</a>
        <a href="events.php" class="nav-item"><i class="fas fa-calendar"></i> Events</a>
        <a href="logout.php" class="nav-item" style="margin-top: auto; color: #ff4d4d;"><i class="fas fa-power-off"></i> Logout</a>
    </aside>

    <!-- Content -->
    <div class="main-feed">
        <div class="top-nav">
            <div>
                <h2 style="font-weight: 800;">Hello, <?php echo htmlspecialchars($user['full_name']); ?>!</h2>
                <p style="color: #64748b; font-size: 14px;">Welcome to your professional space.</p>
            </div>
            <div style="background: var(--bg); padding: 5px 15px; border-radius: 12px; border: 1px solid var(--border);">
                <i class="fas fa-bell"></i>
            </div>
        </div>

        <div class="bento-grid">
            <!-- Job List -->
            <div class="card">
                <h3 style="margin-bottom: 20px; font-weight: 800;">New Career Opportunities</h3>
                <?php
                    $result = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC LIMIT 5");
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="job-item">
                                    <div>
                                        <h4 style="font-size: 16px;">'.htmlspecialchars($row['title']).'</h4>
                                        <p style="font-size: 12px; color: #64748b;">'.htmlspecialchars($row['company']).'</p>
                                    </div>
                                    <a href="'.htmlspecialchars($row['apply_link']).'" target="_blank" class="btn-red" style="text-decoration:none; font-size:12px;">Apply</a>
                                  </div>';
                        }
                    } else {
                        echo "<p style='color:#64748b; text-align:center;'>No jobs available yet.</p>";
                    }
                ?>
            </div>

            <!-- Profile/Stats -->
            <div style="display:flex; flex-direction:column; gap:20px;">
                <div class="card" style="text-align:center; background: var(--dark); color: white;">
                    <div style="width:70px; height:70px; background:var(--accent); border-radius:20px; margin:0 auto 15px; display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:800;">
                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                    </div>
                    <h4><?php echo htmlspecialchars($user['full_name']); ?></h4>
                    <p style="font-size: 12px; opacity: 0.7;">Verified Alumni</p>
                </div>

                <div class="card">
                    <h4 style="margin-bottom:15px; font-weight:800;">Next Event</h4>
                    <?php
                        $ev = $conn->query("SELECT * FROM events ORDER BY event_date ASC LIMIT 1");
                        if ($ev && $ev->num_rows > 0) {
                            $event = $ev->fetch_assoc();
                            echo "<p style='font-size:13px; font-weight:700; color:var(--accent);'>".date('d M, Y', strtotime($event['event_date']))."</p>";
                            echo "<h5 style='margin:5px 0;'>".htmlspecialchars($event['event_name'] ?? $event['title'])."</h5>";
                            echo "<a href='?apply_event=".$event['id']."' class='btn-red' style='display:block; text-align:center; margin-top:10px; text-decoration:none; font-size:12px;'>Register</a>";
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="fab" onclick="showModal()">
    <i class="fas fa-plus"></i>
</div>

<script>
    function showModal() {
        Swal.fire({
            title: 'Post a Job',
            html: `
                <input id="swal-input1" class="swal2-input" placeholder="Job Title">
                <input id="swal-input2" class="swal2-input" placeholder="Company Name">
            `,
            confirmButtonText: 'Post Now',
            confirmButtonColor: '#e11d48'
        });
    }

    // Success Message from PHP
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'applied'): ?>
        Swal.fire('Success!', 'Event ke liye register ho gaya bhai!', 'success');
    <?php endif; ?>
</script>

</body>
</html>