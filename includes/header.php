<?php
$pageTitle = $pageTitle ?? "AlumniX";
$currentPage = basename($_SERVER["PHP_SELF"] ?? "index.php");

// Logic: Sirf Home page par navbar transparent rakhenge starting mein
$isHomePage = ($currentPage == 'index.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, "UTF-8"); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --primary: #ff3b3b;
            --glass: rgba(255, 255, 255, 0.85); /* Slightly more solid for readability */
            --blur: blur(12px);
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
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 15px 0;
        }

        .nav-container {
            max-width: 1200px;
            margin: auto;
            padding: 0 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        /* --- TRANSPARENT STATE (Sirf Home Page starting mein) --- */
        nav.transparent {
            background: transparent;
        }
        nav.transparent .logo-text, 
        nav.transparent .nav-links a, 
        nav.transparent .nav-login, 
        nav.transparent .menu-btn { color: #fff; }

        /* --- SCROLLED / VISIBLE STATE (Saare Pages par scroll ke baad ya direct) --- */
        nav.scrolled {
            background: var(--glass);
            backdrop-filter: var(--blur);
            -webkit-backdrop-filter: var(--blur);
            padding: 10px 0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        nav.scrolled .logo-text { color: #111; }
        nav.scrolled .nav-links a { color: #444; }
        nav.scrolled .nav-login { color: #111; }
        nav.scrolled .menu-btn { color: #111; }

        /* --- LOGO --- */
        .logo a {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .logo img { height: 32px; transition: 0.3s; }
        .logo-text { font-size: 22px; font-weight: 700; letter-spacing: -0.5px; transition: 0.3s; }
        .logo-text span { color: var(--primary); }

        /* --- NAV LINKS --- */
        .nav-links { display: flex; gap: 35px; }
        .nav-links a {
            text-decoration: none; font-size: 14px; font-weight: 500;
            transition: 0.3s; position: relative; opacity: 0.8;
        }
        .nav-links a:hover, .nav-links a.is-active {
            opacity: 1; color: var(--primary) !important;
        }

        /* --- ACTIONS --- */
        .nav-actions { display: flex; align-items: center; gap: 15px; }
        .nav-login { text-decoration: none; font-size: 14px; font-weight: 600; transition: 0.3s; }
        .nav-btn {
            text-decoration: none; padding: 10px 24px; border-radius: 50px;
            background: var(--primary); color: #fff !important;
            font-size: 14px; font-weight: 600;
            box-shadow: 0 4px 15px rgba(255, 59, 59, 0.3); transition: 0.3s;
        }
        .nav-btn:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(255, 59, 59, 0.4); }

        .menu-btn { display: none; font-size: 22px; cursor: pointer; }

        @media(max-width: 992px) {
            .nav-links { display: none; }
            .menu-btn { display: block; }
        }
    </style>
</head>

<body>

    <nav id="mainNav" class="<?= $isHomePage ? 'transparent' : 'scrolled' ?>">
        <div class="nav-container">
            <div class="logo">
                <a href="index.php">
                    <img src="images/logo.png" alt="Logo">
                    <div class="logo-text">Alumni<span>X</span></div>
                </a>
            </div>

            <div class="nav-links">
                <a href="index.php" class="<?= $currentPage == 'index.php' ? 'is-active' : '' ?>">Home</a>
                <a href="events.php" class="<?= ($currentPage == 'events.php' || $currentPage == 'page_events.php') ? 'is-active' : '' ?>">Events</a>
                <a href="jobs.php" class="<?= ($currentPage == 'jobs.php' || $currentPage == 'page_jobs.php') ? 'is-active' : '' ?>">Jobs</a>
                <a href="alumni.php" class="<?= ($currentPage == 'alumni.php' || $currentPage == 'page_alumni.php') ? 'is-active' : '' ?>">Alumni</a>
            </div>

            <div class="nav-actions">
                <a href="login.php" class="nav-login">Login</a>
                <a href="registration.php" class="nav-btn">Get Started</a>
                <div class="menu-btn"><i class="fas fa-bars-staggered"></i></div>
            </div>
        </div>
    </nav>

    <script>
        const nav = document.getElementById("mainNav");
        const isHomePage = <?= json_encode($isHomePage) ?>;

        // Scroll event sirf Home page par chalega
        if (isHomePage) {
            window.addEventListener("scroll", () => {
                if (window.scrollY > 50) {
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