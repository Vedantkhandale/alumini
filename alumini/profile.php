<?php
session_start();
include(__DIR__ . "/../includes/db.php");

if (!isset($_SESSION['user'])) {
    header('Location: ../login.php');
    exit;
}

$userEmail = $_SESSION['user']['email'] ?? '';
$userId = (int) ($_SESSION['user']['id'] ?? 0);

$user = null;
$stmt = $conn->prepare("SELECT * FROM alumni WHERE email = ? LIMIT 1");
if ($stmt) {
    $stmt->bind_param('s', $userEmail);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if (!$user) {
    $user = [
        'name' => $_SESSION['user']['full_name'] ?? 'Alumni Member',
        'email' => $_SESSION['user']['email'] ?? '',
        'department' => $_SESSION['user']['department'] ?? 'Not set',
        'grad_year' => $_SESSION['user']['grad_year'] ?? 'N/A',
        'company' => $_SESSION['user']['company'] ?? 'Independent',
        'location' => $_SESSION['user']['location'] ?? 'Worldwide',
        'about' => $_SESSION['user']['about'] ?? 'No bio available yet.',
        'id' => $userId,
        'image' => $_SESSION['user']['image'] ?? null,
    ];
}

$profileImage = !empty($user['image']) && file_exists(__DIR__ . "/../uploads/profiles/" . $user['image'])
    ? "../uploads/profiles/" . $user['image']
    : "https://ui-avatars.com/api/?name=" . urlencode($user['name']) . "&background=3b82f6&color=fff&bold=true&size=240";

$jobsPosted = 0;
$eventsRsvped = 0;
$pendingJobs = 0;

if ($userId > 0) {
    $jobsPosted = (int) ($conn->query("SELECT COUNT(*) AS cnt FROM jobs WHERE alumni_id = '$userId'")?->fetch_assoc()['cnt'] ?? 0);
    $pendingJobs = (int) ($conn->query("SELECT COUNT(*) AS cnt FROM jobs WHERE alumni_id = '$userId' AND status = 'pending'")?->fetch_assoc()['cnt'] ?? 0);
    $eventsRsvped = (int) ($conn->query("SELECT COUNT(*) AS cnt FROM event_applications WHERE alumni_id = '$userId'")?->fetch_assoc()['cnt'] ?? 0);
}

function esc($value) {
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo esc($user['name']); ?> | Alumni Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
    <style>
        :root {
            --bg: #f8fbff;
            --surface: #ffffff;
            --surface-soft: #eef5ff;
            --primary: #2563eb;
            --primary-soft: #dbeafe;
            --text: #0f172a;
            --muted: #64748b;
            --shadow: 0 25px 60px rgba(15, 23, 42, 0.08);
        }

        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); color: var(--text); }
        a { color: inherit; text-decoration: none; }

        .page { padding: 40px 6%; max-width: 1180px; margin: 0 auto; }
        .topbar { display: flex; justify-content: space-between; align-items: center; gap: 16px; margin-bottom: 34px; }
        .topbar h1 { margin: 0; font-size: 34px; font-weight: 800; }
        .topbar .nav-links { display: flex; gap: 12px; flex-wrap: wrap; }
        .nav-links a { padding: 12px 18px; border-radius: 14px; background: var(--surface); border: 1px solid #e2e8f0; font-weight: 700; transition: transform .2s, box-shadow .2s; }
        .nav-links a:hover { transform: translateY(-2px); box-shadow: var(--shadow); }

        .profile-panel { display: grid; grid-template-columns: 360px 1fr; gap: 28px; align-items: start; }
        .card { background: var(--surface); border-radius: 28px; padding: 28px; box-shadow: var(--shadow); border: 1px solid #e2e8f0; }
        .hero-card { display: grid; gap: 18px; text-align: center; }

        .avatar { width: 180px; height: 180px; border-radius: 32px; overflow: hidden; border: 6px solid var(--primary-soft); margin: 0 auto; }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .user-name { margin: 0; font-size: 28px; font-weight: 800; }
        .user-role { color: var(--muted); font-size: 14px; letter-spacing: .08em; text-transform: uppercase; }
        .bio { margin-top: 14px; color: var(--muted); line-height: 1.8; }

        .stats-grid { display: grid; grid-template-columns: repeat(2,minmax(0,1fr)); gap: 16px; margin-top: 24px; }
        .stat-card { background: var(--surface-soft); border-radius: 20px; padding: 20px; }
        .stat-card label { display: block; font-size: 12px; color: var(--muted); margin-bottom: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; }
        .stat-card strong { font-size: 26px; display: block; margin-top: 2px; }

        .profile-details { display: grid; gap: 18px; }
        .detail-row { display: grid; grid-template-columns: 140px 1fr; gap: 12px; }
        .detail-key { color: var(--muted); font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; }
        .detail-value { color: var(--text); font-size: 15px; line-height: 1.7; }

        .action-links { display: grid; gap: 14px; margin-top: 26px; }
        .action-links a { display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 14px 18px; background: var(--primary); color: #fff; border-radius: 14px; font-weight: 700; }
        .action-links a.secondary { background: var(--surface); color: var(--text); border: 1px solid #e2e8f0; }

        .section { margin-top: 34px; }
        .section h2 { margin: 0 0 22px; font-size: 22px; font-weight: 800; }
        .activity-card { background: var(--surface); border-radius: 24px; padding: 24px; box-shadow: var(--shadow); border: 1px solid #e2e8f0; }
        .activity-card p { margin: 0; color: var(--muted); line-height: 1.75; }

        @media (max-width: 980px) {
            .profile-panel { grid-template-columns: 1fr; }
        }
        @media (max-width: 700px) {
            .page { padding: 24px 5%; }
            .topbar { flex-direction: column; align-items: flex-start; }
            .nav-links { width: 100%; justify-content: space-between; }
        }
    </style>
</head>
<body>
<div class="page">
    <div class="topbar">
        <div>
            <h1>My Alumni Profile</h1>
            <div style="color: var(--muted); margin-top: 6px;">Clean member profile with live job/post stats, approvals, and quick links.</div>
        </div>
        <div class="nav-links">
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
            <a href="careers.php"><i class="fas fa-briefcase"></i> Careers</a>
            <a href="my_jobs.php"><i class="fas fa-clipboard-list"></i> My Posts</a>
            <a href="events.php"><i class="fas fa-calendar-check"></i> Events</a>
        </div>
    </div>

    <div class="profile-panel">
        <div class="card hero-card">
            <div class="avatar"><img src="<?php echo esc($profileImage); ?>" alt="<?php echo esc($user['name']); ?>"></div>
            <h2 class="user-name"><?php echo esc($user['name']); ?></h2>
            <div class="user-role"><?php echo esc($user['department'] ?? 'Alumni'); ?> · Class of <?php echo esc($user['grad_year'] ?? 'N/A'); ?></div>
            <p class="bio"><?php echo esc($user['about'] ?: 'Your profile is your network identity. Keep it updated to stay visible to alumni, employers, and connections.'); ?></p>
            <div class="stats-grid">
                <div class="stat-card">
                    <label>Jobs Posted</label>
                    <strong><?php echo esc($jobsPosted); ?></strong>
                </div>
                <div class="stat-card">
                    <label>Pending Approval</label>
                    <strong><?php echo esc($pendingJobs); ?></strong>
                </div>
                <div class="stat-card">
                    <label>Events Joined</label>
                    <strong><?php echo esc($eventsRsvped); ?></strong>
                </div>
                <div class="stat-card">
                    <label>Member ID</label>
                    <strong>#ALX-<?php echo esc(str_pad($user['id'] ?: 0, 4, '0', STR_PAD_LEFT)); ?></strong>
                </div>
            </div>
            <div class="action-links">
                <a href="mailto:<?php echo esc($user['email']); ?>"><i class="fas fa-envelope"></i> Email Me</a>
                <a href="my_jobs.php" class="secondary"><i class="fas fa-pen"></i> Manage My Posts</a>
            </div>
        </div>

        <div>
            <div class="card profile-details">
                <div class="detail-row">
                    <div class="detail-key">Full Name</div>
                    <div class="detail-value"><?php echo esc($user['name']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-key">Email</div>
                    <div class="detail-value"><?php echo esc($user['email']); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-key">Department</div>
                    <div class="detail-value"><?php echo esc($user['department'] ?? 'Not specified'); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-key">Graduation</div>
                    <div class="detail-value"><?php echo esc($user['grad_year'] ?? 'N/A'); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-key">Current Company</div>
                    <div class="detail-value"><?php echo esc($user['company'] ?? 'Independent'); ?></div>
                </div>
                <div class="detail-row">
                    <div class="detail-key">Location</div>
                    <div class="detail-value"><?php echo esc($user['location'] ?? 'Global'); ?></div>
                </div>
            </div>

            <section class="section">
                <h2>About Me</h2>
                <div class="activity-card">
                    <p><?php echo esc($user['about'] ?: 'No personal statement added yet. Update your alumni profile to share your experience, skills, and current work focus.'); ?></p>
                </div>
            </section>

            <section class="section">
                <h2>Why This Profile</h2>
                <div class="activity-card">
                    <p>Alumni profiles are now separate and polished for a cleaner member experience. This page is built for your personal career identity, while public network pages can remain shared from the main site.</p>
                </div>
            </section>
        </div>
    </div>
</div>

<script>
    gsap.from('.topbar h1', { y: -40, opacity: 0, duration: 0.9, ease: 'power3.out' });
    gsap.from('.card', { y: 40, opacity: 0, duration: 1, stagger: 0.12, ease: 'power3.out' });
</script>
</body>
</html>
