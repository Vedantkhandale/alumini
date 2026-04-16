<?php 
include('./includes/db.php'); 
require_once(__DIR__ . "/includes/public_helpers.php");// Hamare pro helpers include kar liye

$id = $_GET['id'] ?? 1;
$stmt = $conn->prepare("SELECT * FROM alumni WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if(!$user) die("User not found.");

// Avatar with better styling
$avatar = "https://ui-avatars.com/api/?name=".urlencode($user['name'])."&background=ff3b3b&color=fff&bold=true&size=300";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($user['name']) ?> | AlumniX Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #ff3b3b;
            --bg-dark: #0f172a;
            --glass: rgba(255, 255, 255, 0.03);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: #f8fafc; /* Clean light background */
            color: #1e293b;
        }

        .profile-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top right, rgba(255, 59, 59, 0.05), transparent),
                        radial-gradient(circle at bottom left, rgba(255, 59, 59, 0.05), transparent);
            padding: 20px;
        }

        .profile-card {
            background: #ffffff;
            width: 100%;
            max-width: 450px;
            padding: 40px;
            border-radius: 32px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(0, 0, 0, 0.02);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        /* Decorative Background Element */
        .profile-card::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 120px;
            background: linear-gradient(135deg, #ff3b3b, #ff7b7b);
            z-index: 0;
        }

        .content-wrap {
            position: relative;
            z-index: 1;
        }

        .profile-img {
            width: 140px;
            height: 140px;
            border-radius: 40px;
            object-fit: cover;
            border: 6px solid #fff;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            margin-top: 10px; /* Adjusting for the red header area */
        }

        .user-name {
            font-size: 28px;
            font-weight: 800;
            margin: 20px 0 5px;
            color: #0f172a;
            letter-spacing: -1px;
        }

        .user-tagline {
            font-size: 15px;
            color: #64748b;
            font-weight: 500;
            margin-bottom: 25px;
        }

        .info-grid {
            background: #f1f5f9;
            border-radius: 20px;
            padding: 20px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }

        .info-item {
            text-align: left;
        }

        .info-item label {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 700;
            color: #94a3b8;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .info-item span {
            font-size: 14px;
            font-weight: 600;
            color: #334155;
        }

        .company-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            border: 1px solid #e2e8f0;
            padding: 10px 20px;
            border-radius: 14px;
            font-weight: 700;
            font-size: 14px;
            color: #1e293b;
            margin-bottom: 30px;
        }

        .company-badge i {
            color: #ff3b3b;
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: 0.2s;
        }

        .btn-back:hover {
            color: #ff3b3b;
        }

        /* Animation */
        .reveal {
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="profile-container">
    <div class="profile-card reveal">
        <div class="content-wrap">
            <img src="<?= $avatar ?>" class="profile-img" alt="<?= e($user['name']) ?>">
            
            <h1 class="user-name"><?= e($user['name']) ?></h1>
            <p class="user-tagline">Proud Alumnus of Our Community</p>

            <div class="info-grid">
                <div class="info-item">
                    <label>Course</label>
                    <span><?= e($user['course']) ?></span>
                </div>
                <div class="info-item">
                    <label>Batch</label>
                    <span>Class of <?= e($user['batch']) ?></span>
                </div>
            </div>

            <div class="company-badge">
                <i class="fas fa-briefcase"></i>
                <?= e($user['company']) ?: 'Open to Networking' ?>
            </div>

            <div style="border-top: 1px solid #f1f5f9; padding-top: 25px;">
                <a href="index.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Directory
                </a>
            </div>
        </div>
    </div>
</div>

</body>
</html>