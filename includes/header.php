<?php
$pageTitle = $pageTitle ?? "AlumniX";
$currentPage = basename($_SERVER["PHP_SELF"] ?? "index.php");
$isHomePage = ($currentPage == 'index.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, "UTF-8"); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary: #ff3b3b;
            --glass-dark: rgba(0, 0, 0, 0.6);
            --glass-light: rgba(255, 255, 255, 0.05);
            --blur: blur(15px);
        }

        * {
            margin: 0; padding: 0; box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        /* --- NAVBAR BASE --- */
        nav {
            position: fixed;
            top: 0; left: 0; width: 100%;
            z-index: 1000;
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 25px 0; /* Extra space initially */
        }

        .nav-container {
            max-width: 1300px; /* Thoda wide kiya alignment ke liye */
            margin: auto;
            padding: 0 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* --- TRANSPARENT STATE (Home Start) --- */
        nav.transparent { background: transparent; }
        nav.transparent .nav-links a, 
        nav.transparent .nav-login, 
        nav.transparent .menu-btn { color: #fff; }
        nav.transparent .logo-text { color: #fff; }

        /* --- SCROLLED / DARK GLASS STATE --- */
        nav.scrolled {
            background: var(--glass-dark);
            backdrop-filter: var(--blur);
            -webkit-backdrop-filter: var(--blur);
            padding: 15px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        nav.scrolled .logo-text, 
        nav.scrolled .nav-links a, 
        nav.scrolled .nav-login, 
        nav.scrolled .menu-btn { color: #fff; }

        /* --- LOGO --- */
        .logo a {
            display: flex; align-items: center;
            gap: 12px; text-decoration: none;
        }
        .logo img { height: 38px; width: auto; transition: 0.3s; }
        .logo-text { font-size: 24px; font-weight: 800; letter-spacing: -1px; transition: 0.3s; }
        .logo-text span { color: var(--primary); }

        /* --- NAV LINKS & HOVER EFFECT --- */
        .nav-links { display: flex; gap: 40px; align-items: center; }
        .nav-links a {
            text-decoration: none; font-size: 14px; font-weight: 600;
            transition: 0.3s; position: relative; opacity: 0.7;
            padding: 5px 0;
        }
        
        /* Modern Underline Hover */
        .nav-links a::after {
            content: '';
            position: absolute; bottom: 0; left: 0;
            width: 0; height: 2px;
            background: var(--primary);
            transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .nav-links a:hover::after, .nav-links a.is-active::after { width: 100%; }
        .nav-links a:hover, .nav-links a.is-active { opacity: 1; color: #fff !important; }

        /* --- ACTIONS --- */
        .nav-actions { display: flex; align-items: center; gap: 25px; }
        .nav-login { 
            text-decoration: none; font-size: 14px; font-weight: 700; 
            transition: 0.3s; opacity: 0.8; 
        }
        .nav-login:hover { opacity: 1; color: var(--primary) !important; }

        .nav-btn {
            text-decoration: none; padding: 12px 28px; border-radius: 15px;
            background: var(--primary); color: #fff !important;
            font-size: 14px; font-weight: 800;
            box-shadow: 0 8px 20px rgba(255, 59, 59, 0.2); 
            transition: all 0.4s;
            text-transform: uppercase; letter-spacing: 0.5px;
        }
        .nav-btn:hover { 
            transform: translateY(-3px); 
            box-shadow: 0 12px 25px rgba(255, 59, 59, 0.4);
            filter: brightness(1.1);
        }

        .menu-btn { display: none; font-size: 24px; cursor: pointer; }

        @media(max-width: 992px) {
            .nav-links, .nav-login { display: none; }
            .menu-btn { display: block; }
            .nav-container { padding: 0 20px; }
        }
    </style>
</head>

<body>

    <nav id="mainNav" class="<?= $isHomePage ? 'transparent' : 'scrolled' ?>">
        <div class="nav-container">
            <div class="logo">
                <a href="index.php">
                    <?php if(file_exists('images/logo.png')): ?>
                        <img src="images/logo.png" alt="Logo">
                    <?php endif; ?>
                    <div class="logo-text">Alumni<span>X</span></div>
                </a>
            </div>

            <div class="nav-links">
                <a href="index.php" class="<?= $currentPage == 'index.php' ? 'is-active' : '' ?>">Home</a>
                <a href="events.php" class="<?= (strpos($currentPage, 'events') !== false) ? 'is-active' : '' ?>">Events</a>
                <a href="jobs.php" class="<?= (strpos($currentPage, 'jobs') !== false) ? 'is-active' : '' ?>">Jobs</a>
                <a href="alumni.php" class="<?= (strpos($currentPage, 'alumni') !== false) ? 'is-active' : '' ?>">Network</a>
            </div>

            <div class="nav-actions">
                <a href="login.php" class="nav-login">Sign In</a>
                <a href="registration.php" class="nav-btn">Join Now</a>
                <div class="menu-btn"><i class="fas fa-bars-staggered"></i></div>
            </div>
        </div>
    </nav>

    <script>
        const nav = document.getElementById("mainNav");
        const isHomePage = <?= json_encode($isHomePage) ?>;

        if (isHomePage) {
            window.addEventListener("scroll", () => {
                if (window.scrollY > 40) {
                    nav.classList.add("scrolled");
                    nav.classList.remove("transparent");
                } else {
                    nav.classList.add("transparent");
                    nav.classList.remove("scrolled");
                }
            });
        }
    </script>
</body>
</html>