<?php
session_start();
if (file_exists("../includes/db.php")) {
    include("../includes/db.php");
} else {
    include("includes/db.php");
}

$error = false;
$success = false;
$errorMessage = "";

if (isset($_GET["error"]) && $_GET["error"] === "unauthorized") {
    $error = true;
    $errorMessage = "Please log in to continue to the admin workspace.";
}

if (isset($_POST["login"])) {
    $user = trim((string) ($_POST["username"] ?? ""));
    $pass = trim((string) ($_POST["password"] ?? ""));

    $stmt = $conn->prepare("SELECT username FROM admin WHERE username = ? AND password = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("ss", $user, $pass);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows > 0) {
            $_SESSION["admin"] = $user;
            $success = true;
            $error = false;
            $errorMessage = "";
        } else {
            $error = true;
            $errorMessage = "Invalid credentials. Please try again.";
        }

        $stmt->close();
    } else {
        $error = true;
        $errorMessage = "Login query could not be prepared.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Access | AlumniX</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="admin_theme.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="admin-login-theme">
    <div class="login-shell">
        <div class="login-panel">
            <section class="login-showcase">
                <div>
                    <div class="login-kicker"><i class="fas fa-shield-halved"></i> Admin workspace</div>
                    <h1 class="login-title">A cleaner control room for AlumniX operations.</h1>
                    <p class="login-copy">The admin suite now runs on a darker, sharper visual system with better hierarchy, tighter spacing, and a consistent pro feel across every moderation screen.</p>
                </div>
                <div class="login-points">
                    <span><i class="fas fa-bolt"></i> Faster review flow</span>
                    <span><i class="fas fa-layer-group"></i> Unified admin shell</span>
                    <span><i class="fas fa-user-check"></i> Better member moderation</span>
                </div>
            </section>

            <section class="login-card">
                <a href="../index.php" class="login-brand">
                    <div class="brand-mark"><i class="fas fa-graduation-cap"></i></div>
                    <strong>Alumni<span>X</span></strong>
                </a>

                <h2>Authorize access</h2>
                <p>Sign in to enter the refreshed admin workspace.</p>

                <form method="POST" class="login-form">
                    <div class="login-field">
                        <i class="fas fa-user-shield"></i>
                        <input type="text" name="username" placeholder="Admin username" required>
                    </div>
                    <div class="login-field">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" placeholder="Master password" required>
                    </div>
                    <button type="submit" name="login" class="login-submit">Secure Login</button>
                </form>

                <div class="login-note">
                    Admin access remains separate from alumni member login. This screen only controls the moderation workspace.
                </div>
            </section>
        </div>
    </div>

    <script>
        <?php if ($error && $errorMessage !== ""): ?>
        Swal.fire({
            title: 'Access blocked',
            text: <?php echo json_encode($errorMessage); ?>,
            icon: 'error',
            confirmButtonColor: '#ff6a4f',
            background: '#0b1824',
            color: '#ffffff',
            borderRadius: '28px'
        });
        <?php endif; ?>

        <?php if ($success): ?>
        Swal.fire({
            title: 'Access granted',
            text: 'Welcome back to the admin workspace.',
            icon: 'success',
            showConfirmButton: false,
            timer: 1500,
            timerProgressBar: true,
            background: '#0b1824',
            color: '#ffffff',
            borderRadius: '28px',
            willClose: () => {
                window.location.href = 'admin_dashboard.php';
            }
        });
        <?php endif; ?>
    </script>
</body>
</html>
