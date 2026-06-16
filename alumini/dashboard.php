<?php
session_start();
error_reporting(E_ALL);
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
    die("Error: 'db.php' file nahi mili.");
}

// --- 2. SESSION CHECK ---
if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit();
}
$user = $_SESSION['user'];
$alumni_id = $user['id'];

$jobs_count = 0;
$events_count = 0;
$applied_count = 0;
$jobs_res = $conn->query("SELECT COUNT(*) AS cnt FROM jobs WHERE status='approved'");
if ($jobs_res) {
    $jobs_count = (int) $jobs_res->fetch_assoc()['cnt'];
}
$events_res = $conn->query("SELECT COUNT(*) AS cnt FROM events WHERE event_date >= CURDATE()");
if ($events_res) {
    $events_count = (int) $events_res->fetch_assoc()['cnt'];
}
$applied_res = $conn->query("SELECT COUNT(*) AS cnt FROM event_applications WHERE alumni_id='$alumni_id'");
if ($applied_res) {
    $applied_count = (int) $applied_res->fetch_assoc()['cnt'];
}

$upcoming_events = $conn->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC LIMIT 5");

// --- 3. EVENT APPLY LOGIC ---
if (isset($_GET['apply_event']) && !empty($_GET['apply_event'])) {
    $event_id = mysqli_real_escape_string($conn, $_GET['apply_event']);
    $check = $conn->query("SELECT * FROM event_applications WHERE event_id='$event_id' AND alumni_id='$alumni_id'");
    if ($check && $check->num_rows == 0) {
        $conn->query("INSERT INTO event_applications (event_id, alumni_id) VALUES ('$event_id', '$alumni_id')");
        header("Location: dashboard.php?msg=applied");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Dashboard | AlumniX</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>

    <style>
        :root {
            --accent: #e11d48;
            --dark: #0f172a;
            --white: #ffffff;
            --bg: #f8fafc;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background: var(--bg);
            color: var(--dark);
            overflow-x: hidden;
        }

        .dashboard-container {
            display: flex;
            min-height: 100vh;
            padding: 28px;
            gap: 24px;
            position: relative;
            overflow: hidden;
        }

        .dashboard-container::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 15% 10%, rgba(236, 72, 153, 0.16), transparent 20%),
                        radial-gradient(circle at 90% 10%, rgba(59, 130, 246, 0.12), transparent 15%);
            pointer-events: none;
        }

        /* Sidebar */
        .sidebar {
            width: 300px;
            background: rgba(15, 23, 42, 0.95);
            border-radius: 32px;
            padding: 40px 24px;
            color: white;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 1;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 30px 60px rgba(15, 23, 42, 0.12);
        }

        .logo {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 44px;
            text-align: center;
            letter-spacing: -0.04em;
        }

        .logo span {
            color: var(--accent);
        }

        .nav-item {
            padding: 16px 20px;
            border-radius: 18px;
            text-decoration: none;
            color: #cbd5e1;
            font-weight: 600;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: 0.3s;
        }

        .nav-item:hover,
        .nav-item.active {
            background: rgba(255, 255, 255, 0.08);
            color: white;
        }

        .nav-item i {
            color: var(--accent);
            width: 22px;
            text-align: center;
        }

        .main-feed {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 24px;
            position: relative;
            z-index: 1;
        }

        .top-nav {
            background: rgba(255, 255, 255, 0.96);
            padding: 28px 32px;
            border-radius: 30px;
            display: flex;
            flex-direction: column;
            gap: 18px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
        }

        .hero-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 24px;
            flex-wrap: wrap;
        }

        .hero-copy {
            max-width: 720px;
        }

        .hero-tag {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--primary-soft);
            color: var(--primary);
            padding: 10px 16px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.12em;
        }

        .hero-title {
            margin: 14px 0 10px;
            font-size: clamp(32px, 4vw, 48px);
            line-height: 1.05;
            font-weight: 800;
        }

        .hero-text {
            color: #475569;
            font-size: 15px;
            line-height: 1.8;
            max-width: 620px;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .hero-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 24px;
            border-radius: 18px;
            font-weight: 700;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .hero-btn.primary {
            background: linear-gradient(135deg, var(--accent), #f97316);
            color: white;
            border: 1px solid transparent;
        }

        .hero-btn.secondary {
            background: white;
            color: var(--dark);
            border: 1px solid #e2e8f0;
        }

        .hero-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 30px rgba(15, 23, 42, 0.12);
        }

        .top-nav .notification-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: rgba(236, 72, 153, 0.12);
            color: var(--accent);
            padding: 12px 18px;
            border-radius: 18px;
            font-weight: 700;
            border: 1px solid rgba(236, 72, 153, 0.18);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .stat-card {
            background: linear-gradient(180deg, rgba(255,255,255,0.95), rgba(248,250,252,0.95));
            border-radius: 28px;
            padding: 26px 26px 24px;
            border: 1px solid rgba(15, 23, 42, 0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
        }

        .stat-info {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .stat-title {
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            font-weight: 700;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 800;
            color: #0f172a;
        }

        .stat-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            background: linear-gradient(135deg, #ec4899, #f97316);
            font-size: 20px;
            box-shadow: 0 14px 30px rgba(236, 72, 153, 0.22);
        }

        .bento-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        .card {
            background: rgba(255, 255, 255, 0.96);
            border-radius: 32px;
            padding: 32px;
            border: 1px solid rgba(15, 23, 42, 0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 54px rgba(15, 23, 42, 0.12);
        }

        .job-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 22px;
            border-radius: 24px;
            background: rgba(241, 245, 249, 0.95);
            margin-bottom: 16px;
        }

        .job-item h4 {
            font-size: 17px;
            margin-bottom: 6px;
        }

        .job-item p {
            color: #64748b;
            font-size: 13px;
        }

        .btn-red {
            background: linear-gradient(135deg, #ec4899, #f97316);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease, opacity 0.2s ease;
            box-shadow: 0 14px 24px rgba(236, 72, 153, 0.24);
        }

        .btn-red:hover {
            opacity: 0.95;
            transform: translateY(-1px);
        }

        .profile-card {
            text-align: center;
            color: white;
            background: linear-gradient(180deg, rgba(236, 72, 153, 0.95), rgba(249, 115, 22, 0.95));
        }

        .profile-card h4 {
            margin-top: 10px;
            font-size: 20px;
        }

        .profile-card p {
            font-size: 13px;
            opacity: 0.85;
        }

        .profile-avatar {
            width: 76px;
            height: 76px;
            background: rgba(255, 255, 255, 0.18);
            border-radius: 24px;
            margin: 0 auto 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: 800;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            gap: 16px;
            margin-bottom: 22px;
        }

        .section-header h3 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
        }

        .section-header .section-subtitle {
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
        }

        .event-list .event-item,
        .profile-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 20px;
            border-radius: 22px;
            background: rgba(241, 245, 249, 0.95);
            border: 1px solid rgba(15, 23, 42, 0.06);
            gap: 12px;
        }

        .event-list .event-item h4,
        .profile-list .profile-label {
            margin: 0;
        }

        .event-list .event-item h4 {
            font-size: 16px;
            font-weight: 700;
        }

        .event-meta,
        .profile-value {
            color: #475569;
            font-size: 13px;
        }

        .profile-item {
            flex-direction: column;
            align-items: flex-start;
            text-align: left;
        }

        .profile-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: grid;
            gap: 12px;
        }

        .profile-label {
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            font-weight: 700;
            margin-bottom: 6px;
        }

        .profile-value {
            font-size: 15px;
            font-weight: 700;
            color: #0f172a;
        }

        .button-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            background: rgba(236, 72, 153, 0.12);
            color: #ec4899;
            padding: 12px 20px;
            border-radius: 16px;
            font-weight: 700;
            text-decoration: none;
            border: 1px solid rgba(236, 72, 153, 0.16);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .button-secondary:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 24px rgba(236, 72, 153, 0.14);
        }

        .profile-link-button {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(135deg, var(--accent), #fb7185);
            color: #fff;
            padding: 12px 18px;
            border-radius: 14px;
            font-weight: 700;
            text-decoration: none;
            transition: transform .2s ease, box-shadow .2s ease;
            box-shadow: 0 10px 24px rgba(225,29,72,0.18);
        }

        .profile-link-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(225,29,72,0.22);
        }

        @media (max-width: 1050px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }

            .bento-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 760px) {
            .dashboard-container {
                padding: 18px;
            }

            .sidebar {
                display: none;
            }

            .top-nav {
                flex-direction: column;
                align-items: flex-start;
                gap: 18px;
            }
        }
    </style>
</head>

<body>

    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo">Alumni<span>X</span></div>
            <a href="#" class="nav-item active"><i class="fas fa-th-large"></i> Overview</a>
            <a href="careers.php" class="nav-item"><i class="fas fa-briefcase"></i> Jobs</a>
            <a href="events.php" class="nav-item"><i class="fas fa-calendar"></i> Events</a>
            <a href="logout.php" class="nav-item" style="margin-top: auto; color: #ff4d4d;"><i class="fas fa-power-off"></i> Logout</a>
        </aside>

        <div class="main-feed">
            <div class="top-nav">
            <div class="hero-section">
                <div class="hero-copy">
                    <span class="hero-tag"><i class="fas fa-star"></i> Alumni HQ</span>
                    <h1 class="hero-title">Welcome back, <?php echo htmlspecialchars(explode(' ', $user['full_name'])[0]); ?>.</h1>
                    <p class="hero-text">This is your professional alumni workspace. Track open opportunities, upcoming events, and your profile at a glance. Everything is built for alumni-first access.</p>
                </div>
                <div class="hero-actions">
                    <a href="profile.php" class="hero-btn primary"><i class="fas fa-user-circle"></i> View Profile</a>
                    <a href="my_jobs.php" class="hero-btn secondary"><i class="fas fa-clipboard-list"></i> Manage My Posts</a>
                </div>
            </div>
            <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:12px;">
                <div style="min-width:220px;"><p style="margin:0;color:#6b7280;font-size:14px;">Stay updated with the latest jobs and events curated by your alumni network.</p></div>
                <div class="notification-pill">
                    <i class="fas fa-bell"></i>
                    Alumni updates
                </div>
            </div>
        </div>

            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-info">
                        <span class="stat-title">Open Jobs</span>
                        <span class="stat-value"><?php echo $jobs_count; ?></span>
                    </div>
                    <div class="stat-icon"><i class="fas fa-briefcase"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <span class="stat-title">Upcoming Events</span>
                        <span class="stat-value"><?php echo $events_count; ?></span>
                    </div>
                    <div class="stat-icon"><i class="fas fa-calendar-week"></i></div>
                </div>
                <div class="stat-card">
                    <div class="stat-info">
                        <span class="stat-title">Your Applications</span>
                        <span class="stat-value"><?php echo $applied_count; ?></span>
                    </div>
                    <div class="stat-icon"><i class="fas fa-paper-plane"></i></div>
                </div>
            </div>

            <div class="bento-grid">
                <div class="card">
                    <div class="section-header">
                        <h3>Latest Career Opportunities</h3>
                        <span class="section-subtitle">Updated in real time</span>
                    </div>
                    <?php
                    $result = $conn->query("SELECT * FROM jobs WHERE status='approved' ORDER BY id DESC LIMIT 6");
                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="job-item">
                                    <div>
                                        <h4>' . htmlspecialchars($row['title']) . '</h4>
                                        <p class="event-meta">' . htmlspecialchars($row['company']) . ' • ' . htmlspecialchars($row['location'] ?? 'Remote / Onsite') . '</p>
                                    </div>
                                    <a href="go_to_job.php?job_id=' . $row['id'] . '" target="_blank" class="btn-red">Apply</a>
                                  </div>';
                        }
                    } else {
                        echo "<p style='color:#64748b; text-align:center;'>No jobs available yet.</p>";
                    }
                    ?>
                </div>

                <div class="card">
                    <div class="section-header">
                        <h3>Upcoming Events</h3>
                        <span class="section-subtitle"><?php echo $events_count; ?> events coming soon</span>
                    </div>
                    <div class="event-list">
                        <?php
                        if ($upcoming_events && $upcoming_events->num_rows > 0) {
                            while ($event = $upcoming_events->fetch_assoc()) {
                                $e_title = $event['event_name'] ?? ($event['title'] ?? 'Untitled Event');
                                echo '<div class="event-item">
                                        <div>
                                            <h4>' . htmlspecialchars($e_title) . '</h4>
                                            <span class="event-meta">' . date('d M, Y', strtotime($event['event_date'])) . ' • ' . htmlspecialchars($event['location'] ?? 'Online') . '</span>
                                        </div>
                                        <a href="?apply_event=' . $event['id'] . '" class="button-secondary">RSVP</a>
                                      </div>';
                            }
                        } else {
                            echo "<p style='color:#64748b; text-align:center;'>No upcoming events yet.</p>";
                        }
                        ?>
                    </div>
                </div>

                <div style="display:flex; flex-direction:column; gap:20px;">
                    <div class="card">
                        <div class="section-header">
                            <h3>Quick Actions</h3>
                            <span class="section-subtitle">Access everything fast</span>
                        </div>
                        <a href="profile.php" class="button-secondary"><i class="fas fa-user"></i> View Profile</a>
                        <a href="careers.php" class="button-secondary"><i class="fas fa-briefcase"></i> Browse Jobs</a>
                        <a href="my_jobs.php" class="button-secondary"><i class="fas fa-clipboard-list"></i> My Job Posts</a>
                        <a href="events.php" class="button-secondary"><i class="fas fa-calendar-check"></i> Explore Events</a>
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
                title: 'Post a New Job',
                html: `
            <input id="job-title" class="swal2-input" placeholder="Job Title (e.g. Web Developer)">
            <input id="job-company" class="swal2-input" placeholder="Company Name">
            <input id="job-location" class="swal2-input" placeholder="Location (e.g. Nagpur/Remote)">
            <input id="job-link" class="swal2-input" placeholder="Application Link (URL)">
            <textarea id="job-desc" class="swal2-textarea" placeholder="Short Description..."></textarea>
        `,
                confirmButtonText: 'Post Now',
                confirmButtonColor: '#e11d48',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    const title = document.getElementById('job-title').value;
                    const company = document.getElementById('job-company').value;
                    const location = document.getElementById('job-location').value;
                    const link = document.getElementById('job-link').value;
                    const desc = document.getElementById('job-desc').value;

                    if (!title || !company || !link) {
                        Swal.showValidationMessage('Title, Company and Application Link are required!');
                        return;
                    }

                    return fetch('post_job_action.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `title=${encodeURIComponent(title)}&company=${encodeURIComponent(company)}&location=${encodeURIComponent(location)}&apply_link=${encodeURIComponent(link)}&description=${encodeURIComponent(desc)}`
                    }).then(response => response.json());
                }
            }).then((result) => {
                if (result.isConfirmed && result.value.status === 'success') {
                    Swal.fire('Done!', 'Your job post was submitted successfully. Awaiting approval.', 'success').then(() => location.reload());
                }
            });
        }

        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'applied'): ?>
            Swal.fire('Success!', 'You have successfully registered for the event!', 'success');
        <?php endif; ?>
        // Animations: count-up and reveal
        document.addEventListener('DOMContentLoaded', function(){
            if(window.gsap) gsap.registerPlugin(ScrollTrigger);

            // Count up for stats
            const counters = document.querySelectorAll('.stat-value');
            counters.forEach((el)=>{
                const target = parseInt(el.textContent) || 0;
                let cur = 0;
                const step = Math.max(1, Math.floor(target / 60));
                const id = setInterval(()=>{
                    cur += step;
                    if(cur >= target){ el.textContent = target; clearInterval(id); }
                    else el.textContent = cur;
                }, 12);
            });

            // Reveal cards
            if(window.gsap){
                gsap.utils.toArray('.card').forEach((card,i)=>{
                    gsap.from(card,{y:20,opacity:0,duration:0.7,delay:0.08*i,scrollTrigger:{trigger:card,start:'top 90%'}});
                });
            }
        });
    </script>

</body>

</html>