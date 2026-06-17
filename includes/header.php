<?php
$pageTitle = $pageTitle ?? "AlumniX";
$currentPage = basename($_SERVER["SCRIPT_NAME"] ?? "index.php");
$siteRoot = '';
if (strpos($_SERVER['SCRIPT_NAME'] ?? '', '/alumini/') === 0 || basename(dirname($_SERVER['SCRIPT_NAME'] ?? '')) === 'alumini') {
    $siteRoot = '/alumini';
}
$isHomePage = ($currentPage == 'index.php');
$logoPath = $siteRoot . '/images/logo.png';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, "UTF-8"); ?></title>

    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-9ndCyUa6mY5S0vQvVb1rYF0QgY6bZr2a8aE6z9FQZ6Y5mY5QKf5Z2X5p6Jv9K" crossorigin="anonymous">

    <style>
        :root {
            --primary: #ff3b3b;
            --glass-dark: rgba(0, 0, 0, 0.6);
            --glass-light: rgba(255, 255, 255, 0.05);
            --blur: blur(15px);
            --nav-height: 52px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            padding-top: 0;
        }

        .navbar {
            position: fixed;
            top: 0;
            bottom: auto;
            left: 0;
            width: 100%;
            z-index: 1200;
            min-height: 72px;
            background: transparent !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
            border-bottom: 1px solid transparent !important;
            transition: background 0.22s ease, padding 0.22s ease, transform 0.22s ease;
        }

        .nav-container {
            max-width: 1320px;
            margin: auto;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            position: relative;
            min-height: 72px;
        }

        .navbar.transparent {
            background: transparent !important;
            border-bottom-color: transparent !important;
            backdrop-filter: none !important;
            -webkit-backdrop-filter: none !important;
        }

        .navbar.scrolled {
            background: rgba(6, 10, 20, 0.82) !important;
            backdrop-filter: blur(12px) !important;
            -webkit-backdrop-filter: blur(12px) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04) !important;
            transform: translateY(0);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #fff;
            text-decoration: none;
            font-size: 1.05rem;
            font-weight: 800;
        }

        .logo img {
            height: 42px;
            width: auto;
            border-radius: 6px;
        }

        .logo-text {
            letter-spacing: -0.04em;
            color: #fff;
        }

        .logo-text span {
            color: var(--primary);
        }

        .nav-center {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            display: flex;
            align-items: center;
            gap: 1.8rem;
            z-index: 1;
        }

        .nav-center a {
            color: rgba(255, 255, 255, 0.87);
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            position: relative;
            padding: 0.2rem 0;
            transition: color 0.22s ease, transform 0.22s ease;
        }

        .nav-center a:hover,
        .nav-center a.active {
            color: #fff;
            transform: translateY(-1px);
        }

        .nav-center a.active::after {
            content: '';
            position: absolute;
            bottom: -5px;
            left: 0;
            width: 100%;
            height: 2px;
            background: #ff4d4d;
            border-radius: 2px;
        }

        .nav-right {
            display: flex;
            align-items: center;
        }

        .nav-btn {
            padding: 12px 28px;
            border-radius: 999px;
            background: linear-gradient(135deg, #ff5d5d, #ff2d2d);
            color: #fff !important;
            font-size: 15px;
            font-weight: 700;
        }

        .nav-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 42px rgba(255, 77, 77, 0.28);
        }

        .navbar-toggler {
            border: none;
            display: none;
            width: 42px;
            height: 42px;
            border-radius: 14px;
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .navbar-toggler:hover {
            background: rgba(255, 255, 255, 0.14);
        }

        .mobile-menu {
            overflow: hidden;
            max-height: 0;
            opacity: 0;
            transform: translateY(-8px);
            transition: max-height 0.3s ease, opacity 0.3s ease, transform 0.3s ease;
            background: rgba(12, 18, 34, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 0 18px;
            margin: 10px 26px 0;
        }

        .mobile-menu.open {
            max-height: 420px;
            opacity: 1;
            transform: translateY(0);
            padding-top: 16px;
            padding-bottom: 16px;
        }

        .mobile-link {
            display: block;
            color: rgba(255, 255, 255, 0.92);
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            padding: 14px 12px;
            border-radius: 14px;
            transition: background 0.22s ease;
        }

        .mobile-link:hover,
        .mobile-link.active {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
        }

        .mobile-cta {
            display: block;
            margin-top: 12px;
            padding: 12px 0;
            text-align: center;
            background: linear-gradient(135deg, #ff5d5d, #ff2d2d);
            border-radius: 999px;
            color: #fff;
            font-weight: 700;
            letter-spacing: 0.4px;
        }

        @media (max-width: 767.98px) {

            .nav-center,
            .nav-right {
                display: none !important;
            }

            .navbar-toggler {
                display: inline-flex;
            }

            .nav-container {
                padding: 14px 18px;
            }

            .mobile-menu {
                margin: 10px 18px 0;
            }
        }
    </style>
</head>

<body>

    <nav id="mainNav" class="navbar <?= $isHomePage ? 'transparent' : 'scrolled' ?>">
        <div class="container-fluid nav-container">
            <a class="navbar-brand logo" href="<?= $siteRoot ?>/index.php">
                <?php if (!empty($logoPath) && file_exists($_SERVER['DOCUMENT_ROOT'] . $logoPath)): ?>
                    <img src="<?= $logoPath ?>" alt="AlumniX Logo">
                <?php endif; ?>
                <span class="logo-text">Alumni<span>X</span></span>
            </a>

            <div class="nav-center d-none d-md-flex">
                <a class="<?= $currentPage == 'index.php' ? 'active' : '' ?>" href="<?= $siteRoot ?>/index.php">Home</a>
                <a class="<?= (strpos($currentPage, 'events') !== false) ? 'active' : '' ?>" href="<?= $siteRoot ?>/events.php">Events</a>
                <a class="<?= (strpos($currentPage, 'jobs') !== false) ? 'active' : '' ?>" href="<?= $siteRoot ?>/jobs.php">Jobs</a>
                <a class="<?= (strpos($currentPage, 'alumni') !== false) ? 'active' : '' ?>" href="<?= $siteRoot ?>/alumni.php">Network</a>
            </div>

            <div class="nav-right d-none d-md-flex">
                <a href="<?= $siteRoot ?>/registration.php" class="nav-btn">Join Now</a>
            </div>

            <button id="mobileToggle" class="navbar-toggler d-md-none" type="button" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
        </div>

        <div class="mobile-menu" id="mobileMenu">
            <a class="mobile-link <?= $currentPage == 'index.php' ? 'active' : '' ?>" href="<?= $siteRoot ?>/index.php">Home</a>
            <a class="mobile-link <?= (strpos($currentPage, 'events') !== false) ? 'active' : '' ?>" href="<?= $siteRoot ?>/events.php">Events</a>
            <a class="mobile-link <?= (strpos($currentPage, 'jobs') !== false) ? 'active' : '' ?>" href="<?= $siteRoot ?>/jobs.php">Jobs</a>
            <a class="mobile-link <?= (strpos($currentPage, 'alumni') !== false) ? 'active' : '' ?>" href="<?= $siteRoot ?>/alumni.php">Network</a>
            <a class="mobile-cta" href="<?= $siteRoot ?>/registration.php">Join Now</a>
        </div>
    </nav>

    <script>
        const nav = document.getElementById('mainNav');
        const mobileToggle = document.getElementById('mobileToggle');
        const mobileMenu = document.getElementById('mobileMenu');
        const isHomePage = <?= json_encode($isHomePage) ?>;

        const setNavHeight = () => {
            if (!nav) return;
            const height = nav.offsetHeight;
            document.documentElement.style.setProperty('--nav-height', `${height}px`);
        };

        const updateNavState = () => {
            if (!isHomePage) {
                nav.classList.add('scrolled');
                nav.classList.remove('transparent');
                return;
            }

            if (window.scrollY > 24) {
                nav.classList.add('scrolled');
                nav.classList.remove('transparent');
            } else {
                nav.classList.add('transparent');
                nav.classList.remove('scrolled');
            }
        };

        const closeMobileMenu = () => {
            if (!mobileMenu) return;
            mobileMenu.classList.remove('open');
            mobileToggle.setAttribute('aria-expanded', 'false');
        };

        const toggleMobileMenu = () => {
            if (!mobileMenu || !mobileToggle) return;
            const isOpen = mobileMenu.classList.toggle('open');
            mobileToggle.setAttribute('aria-expanded', String(isOpen));
        };

        mobileToggle?.addEventListener('click', toggleMobileMenu);

        window.addEventListener('resize', () => {
            setNavHeight();
            if (window.innerWidth >= 768) {
                closeMobileMenu();
            }
        });

        window.addEventListener('load', () => {
            setNavHeight();
            updateNavState();
        });

        window.addEventListener('scroll', updateNavState);
    </script>
</body>
</html>