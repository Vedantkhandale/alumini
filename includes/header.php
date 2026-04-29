<?php
$pageTitle = $pageTitle ?? "AlumniX";
$currentPage = basename($_SERVER["PHP_SELF"] ?? "index.php");
$isHomePage = ($currentPage === "index.php");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, "UTF-8") ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        :root {
            --nav-accent: #ff4d4d;
            --nav-accent-soft: rgba(255, 77, 77, 0.2);
            --nav-text: #f8fafc;
            --nav-muted: rgba(248, 250, 252, 0.75);
            --nav-panel: rgba(8, 12, 22, 0.78);
            --nav-border: rgba(255, 255, 255, 0.14);
            --nav-shadow: 0 18px 42px rgba(0, 0, 0, 0.34);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: "Sora", sans-serif;
            background: #05060a;
            color: #f8fafc;
        }

        #mainNav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1100;
            padding: 18px 0;
            transition: padding 0.35s ease;
        }

        .nav-shell {
            width: min(1280px, calc(100% - 34px));
            margin: 0 auto;
        }

        .nav-panel {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            padding: 12px 18px;
            border-radius: 22px;
            border: 1px solid var(--nav-border);
            background: var(--nav-panel);
            backdrop-filter: blur(16px) saturate(120%);
            -webkit-backdrop-filter: blur(16px) saturate(120%);
            box-shadow: var(--nav-shadow);
            transition: border-color 0.35s ease, background 0.35s ease, box-shadow 0.35s ease;
        }

        #mainNav.transparent .nav-panel {
            border-color: rgba(255, 255, 255, 0.08);
            background: rgba(5, 8, 14, 0.22);
            box-shadow: none;
        }

        #mainNav.scrolled {
            padding: 10px 0;
        }

        #mainNav.scrolled .nav-panel {
            border-color: rgba(255, 255, 255, 0.16);
            background: rgba(7, 10, 18, 0.88);
            box-shadow: var(--nav-shadow);
        }

        .brand {
            flex-shrink: 0;
        }

        .brand a {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: var(--nav-text);
        }

        .brand img {
            height: 40px;
            width: auto;
            filter: drop-shadow(0 10px 18px rgba(255, 77, 77, 0.22));
        }

        .brand-text {
            font-size: 24px;
            font-weight: 800;
            letter-spacing: -0.03em;
        }

        .brand-text span {
            color: var(--nav-accent);
        }

        .brand-sub {
            display: block;
            font-size: 11px;
            color: var(--nav-muted);
            font-weight: 500;
            letter-spacing: 0.12em;
            text-transform: uppercase;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.04);
        }

        .nav-links a {
            text-decoration: none;
            color: var(--nav-muted);
            font-size: 14px;
            font-weight: 600;
            padding: 10px 16px;
            border-radius: 999px;
            transition: background 0.25s ease, color 0.25s ease, transform 0.25s ease;
        }

        .nav-links a:hover,
        .nav-links a.is-active {
            color: var(--nav-text);
            background: rgba(255, 255, 255, 0.08);
            transform: translateY(-1px);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }

        .nav-login,
        .nav-btn {
            text-decoration: none;
            font-size: 13px;
            font-weight: 700;
            border-radius: 999px;
            padding: 11px 18px;
            transition: transform 0.25s ease, box-shadow 0.25s ease, background 0.25s ease, color 0.25s ease;
        }

        .nav-login {
            color: var(--nav-text);
            border: 1px solid rgba(255, 255, 255, 0.2);
            background: rgba(255, 255, 255, 0.02);
        }

        .nav-login:hover {
            transform: translateY(-1px);
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-btn {
            color: #fff;
            background: linear-gradient(120deg, #ff4d4d, #ff7a45);
            border: 1px solid rgba(255, 255, 255, 0.18);
            box-shadow: 0 10px 24px rgba(255, 77, 77, 0.32);
            text-transform: uppercase;
            letter-spacing: 0.07em;
            font-size: 12px;
        }

        .nav-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 28px rgba(255, 77, 77, 0.42);
        }

        .menu-toggle {
            display: none;
            width: 42px;
            height: 42px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.06);
            color: #fff;
            font-size: 18px;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .mobile-menu {
            display: none;
            position: absolute;
            left: 0;
            right: 0;
            top: calc(100% + 10px);
            border-radius: 18px;
            border: 1px solid rgba(255, 255, 255, 0.18);
            background: rgba(7, 10, 18, 0.96);
            box-shadow: var(--nav-shadow);
            padding: 14px;
            transform: translateY(-8px);
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease, transform 0.25s ease;
        }

        .mobile-menu a {
            display: block;
            text-decoration: none;
            color: var(--nav-muted);
            font-weight: 600;
            font-size: 15px;
            border-radius: 12px;
            padding: 12px 14px;
            transition: background 0.2s ease, color 0.2s ease;
        }

        .mobile-menu a:hover,
        .mobile-menu a.is-active {
            color: var(--nav-text);
            background: rgba(255, 255, 255, 0.08);
        }

        .mobile-menu .mobile-cta {
            margin-top: 10px;
            border-top: 1px solid rgba(255, 255, 255, 0.12);
            padding-top: 12px;
        }

        #mainNav.menu-open .mobile-menu {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        @media (max-width: 992px) {
            .nav-shell {
                width: min(1280px, calc(100% - 22px));
            }

            .brand-sub,
            .nav-links,
            .nav-actions {
                display: none;
            }

            .menu-toggle {
                display: inline-flex;
            }

            .mobile-menu {
                display: block;
            }
        }
    </style>
</head>
<body>
    <nav id="mainNav" class="<?= $isHomePage ? "transparent" : "scrolled" ?>">
        <div class="nav-shell">
            <div class="nav-panel">
                <div class="brand">
                    <a href="index.php">
                        <?php if (file_exists(__DIR__ . "/../images/logo.png")): ?>
                            <img src="images/logo.png" alt="AlumniX logo">
                        <?php endif; ?>
                        <div>
                            <div class="brand-text">Alumni<span>X</span></div>
                            <span class="brand-sub">Career . Events . Network</span>
                        </div>
                    </a>
                </div>

                <div class="nav-links">
                    <a href="index.php" class="<?= $currentPage === "index.php" ? "is-active" : "" ?>">Home</a>
                    <a href="events.php" class="<?= $currentPage === "events.php" ? "is-active" : "" ?>">Events</a>
                    <a href="jobs.php" class="<?= $currentPage === "jobs.php" ? "is-active" : "" ?>">Jobs</a>
                    <a href="alumni.php" class="<?= $currentPage === "alumni.php" ? "is-active" : "" ?>">Network</a>
                </div>

                <div class="nav-actions">
                    <a class="nav-login" href="login.php">Sign In</a>
                    <a class="nav-btn" href="registration.php">Join Now</a>
                </div>

                <button id="menuToggle" class="menu-toggle" type="button" aria-label="Toggle menu" aria-expanded="false">
                    <i class="fas fa-bars"></i>
                </button>

                <div id="mobileMenu" class="mobile-menu">
                    <a href="index.php" class="<?= $currentPage === "index.php" ? "is-active" : "" ?>">Home</a>
                    <a href="events.php" class="<?= $currentPage === "events.php" ? "is-active" : "" ?>">Events</a>
                    <a href="jobs.php" class="<?= $currentPage === "jobs.php" ? "is-active" : "" ?>">Jobs</a>
                    <a href="alumni.php" class="<?= $currentPage === "alumni.php" ? "is-active" : "" ?>">Network</a>
                    <div class="mobile-cta">
                        <a href="login.php">Sign In</a>
                        <a href="registration.php">Join Now</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <script>
        (function () {
            const nav = document.getElementById("mainNav");
            const toggle = document.getElementById("menuToggle");
            const mobileMenu = document.getElementById("mobileMenu");
            const isHomePage = <?= $isHomePage ? "true" : "false" ?>;

            const applyNavState = () => {
                if (!isHomePage) {
                    nav.classList.add("scrolled");
                    nav.classList.remove("transparent");
                    return;
                }

                if (window.scrollY > 30) {
                    nav.classList.add("scrolled");
                    nav.classList.remove("transparent");
                } else {
                    nav.classList.add("transparent");
                    nav.classList.remove("scrolled");
                }
            };

            const closeMenu = () => {
                nav.classList.remove("menu-open");
                if (toggle) {
                    toggle.setAttribute("aria-expanded", "false");
                }
            };

            applyNavState();
            window.addEventListener("scroll", applyNavState, { passive: true });

            if (toggle) {
                toggle.addEventListener("click", (event) => {
                    event.stopPropagation();
                    const isOpen = nav.classList.toggle("menu-open");
                    toggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
                });
            }

            document.addEventListener("click", (event) => {
                if (nav.classList.contains("menu-open") && !nav.contains(event.target)) {
                    closeMenu();
                }
            });

            if (mobileMenu) {
                mobileMenu.querySelectorAll("a").forEach((link) => {
                    link.addEventListener("click", closeMenu);
                });
            }

            window.addEventListener("resize", () => {
                if (window.innerWidth > 992) {
                    closeMenu();
                }
            });
        })();
    </script>
