<?php
require_once __DIR__ . "/helpers.php";
adminOnly();

if (isset($_GET["approve"])) {
    $id = (int) $_GET["approve"];
    // fetch user details for notification
    $u = $conn->query("SELECT full_name, email FROM users WHERE id='$id'")->fetch_assoc();
    $conn->query("UPDATE users SET status='approved' WHERE id='$id'");

    // send approval email
    if ($u && !empty($u['email'])) {
        $site_base = rtrim((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['SCRIPT_NAME'])), '/');
        $login_link = $site_base . '/login.php';
        $to = $u['email'];
        $subject = "AlumniX Account Approved";
        $message = "<html><body>".
                   "<h2>Your AlumniX membership is approved, " . htmlspecialchars($u['full_name'], ENT_QUOTES) . "</h2>".
                   "<p>Congratulations — an admin has approved your account. You can now <a href='$login_link'>login</a> to access the alumni panel.</p>".
                   "<p>— AlumniX Team</p>".
                   "</body></html>";
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= 'From: AlumniX <noreply@' . $_SERVER['HTTP_HOST'] . '>' . "\r\n";
        @mail($to, $subject, $message, $headers);
    }

    header("Location: alumni_list.php?res=approved");
    exit();
}

$alumniUsers = adminRows($conn, "SELECT id, full_name, email, student_id, batch, graduation_year, status FROM users WHERE role='alumni' ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Directory | AlumniX Pro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;500;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary: #ff3e3e;
            --success: #10b981;
            --dark: #0f172a;
            --white: #ffffff;
            --bg: #fafafa;
            --border: #e2e8f0;
            --text-dim: #64748b;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: var(--bg); color: var(--dark); padding-bottom: 50px; }

        /* --- 🛸 PRO HEADER --- */
        header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(15px);
            padding: 25px 5%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border);
            position: sticky; top: 0; z-index: 1000;
        }
        .title h1 { font-size: 1.6rem; font-weight: 800; letter-spacing: -1px; }
        .title span { color: var(--primary); }

        .nav-btns { display: flex; gap: 12px; }
        .btn {
            padding: 10px 20px; border-radius: 12px; font-weight: 700; font-size: 0.85rem;
            text-decoration: none; transition: 0.3s; border: none; cursor: pointer;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-soft { background: #f1f5f9; color: var(--dark); border: 1px solid var(--border); }
        .btn-soft:hover { background: var(--dark); color: white; }
        .btn-primary { background: var(--dark); color: white; }
        .btn-primary:hover { background: var(--primary); }

        /* --- 📂 DIRECTORY LIST --- */
        .container { max-width: 1100px; margin: 40px auto; padding: 0 20px; }

        .member-card {
            background: var(--white);
            border-radius: 24px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid var(--border);
            display: grid;
            grid-template-columns: auto 1fr auto;
            align-items: center;
            gap: 25px;
            transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        }
        .member-card:hover { border-color: var(--primary); transform: translateY(-5px); box-shadow: 0 15px 30px rgba(0,0,0,0.05); }

        /* Profile Icon/Initial */
        .avatar {
            width: 65px; height: 65px; background: #f1f5f9; border-radius: 18px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem; font-weight: 800; color: var(--primary);
            border: 1px solid var(--border);
        }

        .info h2 { font-size: 1.25rem; font-weight: 800; margin-bottom: 8px; display: flex; align-items: center; gap: 10px; }
        
        /* Status Badges */
        .status-dot { width: 8px; height: 8px; border-radius: 50%; display: inline-block; }
        .status-pending { background: #f59e0b; }
        .status-approved { background: var(--success); }

        .details-grid { display: flex; flex-wrap: wrap; gap: 15px; }
        .item { display: flex; align-items: center; gap: 6px; font-size: 0.85rem; color: var(--text-dim); font-weight: 600; }
        .item i { color: var(--dark); font-size: 0.9rem; }

        .approve-btn {
            background: var(--success); color: white; padding: 12px 24px; border-radius: 14px;
            text-decoration: none; font-weight: 800; font-size: 0.85rem; transition: 0.3s;
        }
        .approve-btn:hover { background: var(--dark); transform: scale(1.05); }
        
        .active-badge { color: var(--success); font-weight: 800; font-size: 0.85rem; display: flex; align-items: center; gap: 5px; }

        /* Responsive */
        @media (max-width: 800px) {
            .member-card { grid-template-columns: 1fr; text-align: center; justify-items: center; }
            .details-grid { justify-content: center; }
            header { flex-direction: column; gap: 20px; }
        }
    </style>
</head>
<body>

<header>
    <div class="title">
        <h1>Member <span>Directory</span></h1>
    </div>
    <div class="nav-btns">
        <a href="admin_dashboard.php" class="btn btn-soft"><i class="fas fa-th-large"></i> Dashboard</a>
        <a href="jobs.php" class="btn btn-soft"><i class="fas fa-briefcase"></i> Jobs</a>
        <a href="event.php" class="btn btn-primary"><i class="fas fa-calendar-plus"></i> Events</a>
    </div>
</header>

<div class="container">
    <?php if ($alumniUsers): ?>
        <?php foreach ($alumniUsers as $user): 
            $status = $user["status"] ?: "pending";
            $initial = strtoupper(substr($user["full_name"], 0, 1));
        ?>
            <div class="member-card">
                <!-- Avatar -->
                <div class="avatar"><?php echo $initial; ?></div>

                <!-- Info -->
                <div class="info">
                    <h2>
                        <?php echo adminE($user["full_name"]); ?>
                        <span class="status-dot <?php echo ($status === 'approved') ? 'status-approved' : 'status-pending'; ?>" title="<?php echo ucfirst($status); ?>"></span>
                    </h2>
                    <div class="details-grid">
                        <div class="item"><i class="fas fa-envelope"></i> <?php echo adminE($user["email"]); ?></div>
                        <div class="item"><i class="fas fa-id-card"></i> <?php echo adminE($user["student_id"] ?: "N/A"); ?></div>
                        <div class="item"><i class="fas fa-graduation-cap"></i> <?php echo adminE($user["batch"] ?: "N/A"); ?> (<?php echo adminE($user["graduation_year"] ?: "N/A"); ?>)</div>
                    </div>
                </div>

                <!-- Action -->
                <div class="action">
                    <?php if ($status === "pending"): ?>
                        <a href="?approve=<?php echo (int) $user["id"]; ?>" class="approve-btn">
                            <i class="fas fa-user-check"></i> Approve Member
                        </a>
                    <?php else: ?>
                        <span class="active-badge"><i class="fas fa-check-circle"></i> VERIFIED</span>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="text-align:center; padding:100px 0;">
            <i class="fas fa-users-slash" style="font-size:4rem; color:#e2e8f0; margin-bottom:20px;"></i>
            <h2 style="color:#94a3b8;">No members found in the directory.</h2>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
<script>
    // Cards load animation
    gsap.from(".member-card", {
        opacity: 0,
        y: 30,
        duration: 0.6,
        stagger: 0.1,
        ease: "power2.out"
    });
</script>

</body>
</html>