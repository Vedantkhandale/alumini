<?php $pageTitle = $pageTitle ?? "AlumniX"; ?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, "UTF-8"); ?></title>

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600;700&display=swap" rel="stylesheet">

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f5f7fb;
            color: #333;
        }

        /* 🔥 NAVBAR */
        nav {
            position: sticky;
            top: 0;
            width: 100%;
            padding: 14px 70px;
            display: flex;
            justify-content: space-between;
            align-items: center;

            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.06);
            z-index: 1000;
        }

        /* 🔥 LOGO */
        .logo a {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }

        .logo img {
            height: 50px;
            transition: 0.3s;
        }

        .logo img:hover {
            transform: scale(1.1);
        }

        .logo-text {
            font-size: 25px;
            font-weight: 600;
            color: #222;
        }

        .logo-text span {
            color: #ff3b3b;
        }

        /* 🔗 LINKS */
        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
        }

        .nav-links a {
            position: relative;
            display: flex;
            align-items: center;
            gap: 6px;
            color: #555;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            padding: 6px 0;
            transition: 0.3s;
        }

        /* 🔥 UNDERLINE */
        .nav-links a::after {
            content: "";
            position: absolute;
            left: 0;
            bottom: -4px;
            width: 0%;
            height: 2px;
            background: linear-gradient(to right, #ff3b3b, #ff6b6b);
            transition: 0.3s;
        }

        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-links a:hover {
            color: #000;
        }

        /* 🔥 BUTTON GROUP */
        .nav-actions {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* LOGIN */
        .nav-login {
            padding: 8px 18px;
            border-radius: 20px;
            border: 1px solid #ddd;
            color: #444;
            font-size: 14px;
            transition: 0.3s;
        }

        .nav-login:hover {
            border-color: #ff3b3b;
            color: #ff3b3b;
            background: rgba(255, 59, 59, 0.05);
        }

        /* REGISTER */
        .nav-btn {
            padding: 9px 20px;
            border-radius: 25px;
            background: linear-gradient(45deg, #ff3b3b, #ff6b6b);
            color: white !important;
            font-size: 14px;
            font-weight: 500;
            border: none;
            transition: 0.3s;
            box-shadow: 0 4px 12px rgba(255, 59, 59, 0.3);
        }

        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(255, 59, 59, 0.4);
        }

        /* 📱 MOBILE */
        @media(max-width: 768px) {
            nav {
                padding: 12px 20px;
            }

            .nav-links {
                display: none;
            }

            .logo-text {
                font-size: 16px;
            }
        }
    </style>
    <link rel="stylesheet" href="assets/css/public.css">
</head>

<body>

    <nav>

        <!-- 🔥 LOGO IMAGE + NAME -->
        <div class="logo">
            <a href="index.php">
                <img src="images/logo.png" alt="Alumni Logo">
                <div class="logo-text">Alumni<span>X</span></div>
            </a>
        </div>

        <!-- LINKS -->
        <div class="nav-links">
            <a href="index.php"><i class="fa fa-home"></i> Home</a>
            <a href="events.php">Events</a>
            <a href="jobs.php">Jobs</a>
            <a href="alumni.php">Alumni</a>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="nav-actions">
            <a href="login.php" class="nav-login">Login</a>
            <a href="registration.php" class="nav-btn">Register</a>
        </div>

    </nav>
