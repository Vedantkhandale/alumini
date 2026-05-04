<?php 
include('./includes/db.php'); 
require_once(__DIR__ . "/includes/public_helpers.php");

$id = $_GET['id'] ?? 1;
$stmt = $conn->prepare("SELECT * FROM alumni WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if(!$user) die("User not found.");

// Profile Image logic
$imgUrl = !empty($user['image']) ? "uploads/profiles/".$user['image'] : "https://ui-avatars.com/api/?name=".urlencode($user['name'])."&background=111&color=fff&bold=true&size=800";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($user['name']) ?> | Alumni Elite</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;800&display=swap');

        :root {
            --primary: #ff3b3b;
            --accent: #ff7b7b;
            --bg-dark: #f7ecec;
        }

        body {
            margin: 0; padding: 0;
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg-dark);
            color: #fff;
            overflow-x: hidden;
        }

        /* 📸 BACKGROUND PROFILE IMAGE (BLURRED) */
        .bg-image-overlay {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: url('<?= $imgUrl ?>') center/cover no-repeat;
            filter: blur(80px) brightness(0.3);
            z-index: -1;
            transform: scale(1.1);
        }

        .profile-hero {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            align-items: center;
            padding: 0 5%;
            gap: 50px;
        }

        /* 🖼️ BIG SEXY PROFILE IMAGE */
        .image-container {
            position: relative;
            height: 85vh;
            width: 100%;
            perspective: 1000px;
        }

        .main-frame {
            width: 100%;
            height: 100%;
            background: url('<?= $imgUrl ?>') center/cover no-repeat;
            border-radius: 40px;
            box-shadow: 0 50px 100px rgba(0,0,0,0.8);
            border: 1px solid rgba(255,255,255,0.1);
            position: relative;
            z-index: 2;
        }

        .floating-label {
            position: absolute;
            top: 40px; right: -30px;
            background: var(--primary);
            padding: 15px 30px;
            border-radius: 20px;
            font-weight: 800;
            letter-spacing: 2px;
            transform: rotate(5deg);
            z-index: 3;
            box-shadow: 0 20px 40px rgba(255,59,59,0.4);
        }

        /* 📝 TYPOGRAPHY & DETAILS */
        .info-content {
            padding: 40px;
            z-index: 5;
        }

        .hero-tag {
            color: var(--primary);
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 6px;
            font-size: 14px;
            margin-bottom: 20px;
            display: block;
        }

        .user-name {
            font-size: clamp(60px, 8vw, 110px);
            font-weight: 800;
            line-height: 0.85;
            letter-spacing: -6px;
            margin: 0 0 30px;
        }

        .user-name span {
            color: transparent;
            -webkit-text-stroke: 1.5px rgba(255,255,255,0.3);
        }

        .company-pill {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: rgba(255,255,255,0.05);
            padding: 18px 30px;
            border-radius: 100px;
            border: 1px solid rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            margin-bottom: 40px;
        }

        .pill-logo {
            width: 40px; height: 40px;
            background: var(--primary);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-weight: 900;
        }

        .stats-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .stat-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            padding: 30px;
            border-radius: 30px;
            transition: 0.4s;
        }

        .stat-card:hover {
            background: rgba(255,255,255,0.07);
            border-color: var(--primary);
        }

        .stat-card label {
            color: #666;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 800;
            margin-bottom: 10px;
            display: block;
        }

        .stat-card p {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }

        /* 🔘 ACTION BUTTONS */
        .btn-group {
            margin-top: 50px;
            display: flex;
            gap: 20px;
        }

        .btn-main {
            background: #fff;
            color: #000;
            padding: 22px 45px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 14px;
            transition: 0.3s;
        }

        .btn-main:hover {
            background: var(--primary);
            color: #fff;
            box-shadow: 0 15px 30px rgba(255,59,59,0.3);
            transform: translateY(-5px);
        }

        .btn-secondary {
            border: 1px solid rgba(255,255,255,0.2);
            padding: 22px 30px;
            border-radius: 20px;
            color: #fff;
            text-decoration: none;
            transition: 0.3s;
        }

        .btn-secondary:hover { background: rgba(255,255,255,0.1); }

        @media(max-width: 1100px) {
            .profile-hero { grid-template-columns: 1fr; padding: 100px 5%; }
            .image-container { height: 60vh; }
            .user-name { font-size: 70px; }
        }
    </style>
    
</head>
<body>

    <div class="bg-image-overlay"></div>

    <div class="profile-hero">
        
        <div class="image-container">
            <div class="floating-label">ELITE MEMBER</div>
            <div class="main-frame"></div>
        </div>

        <div class="info-content">
            <span class="hero-tag">Official Alumni Profile</span>
            <h1 class="user-name">
                <?= e(explode(' ', $user['name'])[0]) ?><br>
                <span><?= e(explode(' ', $user['name'])[1] ?? '') ?></span>
            </h1>

            <div class="company-pill">
                <div class="pill-logo"><?= substr($user['company'], 0, 1) ?></div>
                <div>
                    <div style="font-size: 12px; color: #888; font-weight: 700;">Currently At</div>
                    <div style="font-size: 18px; font-weight: 800;"><?= e($user['company']) ?></div>
                </div>
            </div>

            <div class="stats-row">
                <div class="stat-card">
                    <label>Expertise</label>
                    <p><?= e($user['course']) ?></p>
                </div>
                <div class="stat-card">
                    <label>Graduation</label>
                    <p>Class of <?= e($user['batch']) ?></p>
                </div>
                <div class="stat-card">
                    <label>Network ID</label>
                    <p>#ALX-0<?= e($user['id']) ?></p>
                </div>
                <div class="stat-card">
                    <label>Location</label>
                    <p><?= e($user['location'] ?? 'Global') ?></p>
                </div>
            </div>

            <div class="btn-group">
                <a href="mailto:<?= e($user['email']) ?>" class="btn-main">Connect via Email</a>
                <a href="index.php" class="btn-secondary"><i class="fas fa-th"></i></a>
            </div>
        </div>

    </div>

    <script>
        // GSAP Sexy Reveal
        gsap.from(".main-frame", {
            x: -100,
            opacity: 0,
            duration: 1.5,
            ease: "expo.out"
        });

        gsap.from(".info-content > *", {
            x: 100,
            opacity: 0,
            duration: 1,
            stagger: 0.15,
            ease: "power4.out",
            delay: 0.5
        });

        // Background parallax
        document.addEventListener("mousemove", (e) => {
            let x = (e.clientX / window.innerWidth) - 0.5;
            let y = (e.clientY / window.innerHeight) - 0.5;
            gsap.to(".main-frame", {
                rotationY: x * 10,
                rotationX: -y * 10,
                ease: "power2.out"
            });
        });
    </script>
</body>
</html>