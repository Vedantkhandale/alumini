<?php
session_start();
include("includes/db.php"); // Path check kar lena agar ye 'includes' folder mein hai

// 1. Pehle variables ko initialize karo (Warnings hatane ke liye)
$error = "";
$success = false;
$redirect_url = "";

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);

    $result = $conn->query("SELECT * FROM users WHERE email='$email' AND password='$pass'");

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();
        $status = strtolower((string) ($user['status'] ?? 'approved'));

        if(in_array($status, ['pending', 'rejected', 'blocked'], true)){
            $error = "Your account is not active yet. Please contact admin.";
        } else {
            $_SESSION['user'] = $user;

            if(($user['role'] ?? '') === 'admin'){
                $_SESSION['admin'] = $user['full_name'] ?? $user['email'];
                $redirect_url = "admin/admin_dashboard.php";
            } else {
                $redirect_url = "alumini/dashboard.php";
            }

            $success = true;
        }
    } else {
        $error = "Incorrect email or password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | AlumniX</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #ff4d4d; --dark: #1e293b; --text-light: #64748b; --bg: #f8fafc; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: var(--bg); font-family: 'Plus Jakarta Sans', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .login-wrapper { width: 100%; max-width: 420px; padding: 20px; }
        .alumnix-card { background: #ffffff; padding: 45px 35px; border-radius: 28px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); border: 1px solid rgba(226, 232, 240, 0.8); }
        .header h1 { font-size: 34px; font-weight: 800; color: var(--dark); letter-spacing: -1.5px; }
        .header span { color: var(--primary); }
        .error-msg { background: #fff1f2; color: #e11d48; padding: 14px; border-radius: 12px; font-size: 13px; font-weight: 600; text-align: center; margin-bottom: 25px; border: 1px solid #ffe4e6; }
        .input-group { margin-bottom: 22px; }
        .label { font-size: 11px; font-weight: 800; color: var(--text-light); text-transform: uppercase; display: block; margin-bottom: 8px; }
        .input-style { width: 100%; padding: 14px 18px; border: 2px solid #f1f5f9; border-radius: 14px; background: #f8fafc; outline: none; transition: 0.3s; }
        .input-style:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px rgba(255, 77, 77, 0.1); }
        .btn-alumnix { width: 100%; padding: 16px; background: var(--primary); color: white; border: none; border-radius: 14px; font-size: 14px; font-weight: 800; cursor: pointer; text-transform: uppercase; transition: 0.3s; box-shadow: 0 10px 20px rgba(255, 77, 77, 0.2); }
        .bottom-links { text-align: center; margin-top: 35px; border-top: 1px solid #f1f5f9; padding-top: 25px; }
        a { text-decoration: none; color: var(--primary); font-weight: 700; }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="alumnix-card">
        <div class="header" style="text-align: center; margin-bottom: 35px;">
            <h1>Alumni<span>X</span></h1>
            <p style="color: var(--text-light); font-size: 14px; margin-top: 5px;">Welcome back to the portal</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="error-msg"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label class="label">Email Address</label>
                <input type="email" name="email" class="input-style" placeholder="Enter your email" required>
            </div>
            <div class="input-group">
                <label class="label">Password</label>
                <input type="password" name="password" class="input-style" placeholder="••••••••" required>
            </div>
            <button type="submit" name="login" class="btn-alumnix">Access Dashboard</button>
        </form>

        <div class="bottom-links">
            <p style="font-size: 13px; color: var(--text-light);">Don't have an account? <a href="registration.php">Join Now</a></p>
            <p style="font-size: 13px; color: var(--text-light); margin-top: 10px;">Forgot your password? <a href="forget_password.php">Reset it</a></p>
        </div>
    </div>
</div>

<?php if($success): ?>
<script>
    Swal.fire({
        title: 'Login Successful!',
        text: 'Redirecting to your dashboard...',
        icon: 'success',
        timer: 2000,
        showConfirmButton: false,
        iconColor: '#10b981'
    }).then(() => {
        window.location.href = '<?php echo $redirect_url; ?>';
    });
</script>
<?php endif; ?>

</body>
</html>
