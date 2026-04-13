<?php
session_start();
include("includes/db.php");
$error = "";

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);

    // Logic: Email + Pass + Approval Status
    $result = $conn->query("SELECT * FROM alumni WHERE email='$email' AND password='$pass' AND status='approved'");

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();
        $_SESSION['user'] = $email;
        header("Location: alumni/dashboard.php");
        exit();
    } else {
        $error = "Invalid Credentials or Pending Approval!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | AlumniX</title>
    <link rel="stylesheet" href="assets/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        .login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-wrapper { width: 100%; max-width: 400px; padding: 20px; }
        .error-msg { color: #d9534f; background: #fdf7f7; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-size: 13px; font-weight: bold; border: 1px solid #eed3d7; }
        
        /* Utility for the forget row */
        .form-footer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <div class="login-wrapper">
        <div class="alumnix-card" style="padding: 40px; background: #fff; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #eee;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="font-size: 32px; font-weight: 800; color: #1e293b; margin: 0;">
                    Alumni<span style="color: #ff4d4d;">X</span>
                </h1>
                <p style="color: #94a3b8; font-size: 14px; margin-top: 5px;">Secure Access Portal</p>
            </div>

            <?php if($error): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div style="margin-bottom: 20px;">
                    <label style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Email Address</label>
                    <input type="email" name="email" class="form-control-alumnix" placeholder="Enter your email" required style="width: 100%; padding: 12px; margin-top: 8px; border: 1px solid #ddd; border-radius: 8px; background: #fafafa;">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase;">Password</label>
                    <input type="password" name="password" class="form-control-alumnix" placeholder="••••••••" required style="width: 100%; padding: 12px; margin-top: 8px; border: 1px solid #ddd; border-radius: 8px; background: #fafafa;">
                </div>

                <div class="form-footer-row">
                    <div style="display: flex; align-items: center;">
                        <input type="checkbox" id="remember" style="margin-right: 5px; accent-color: #ff4d4d;">
                        <label for="remember" style="font-size: 12px; color: #64748b; cursor: pointer;">Remember me</label>
                    </div>
                    <a href="forget_password.php" style="color: #ff4d4d; font-size: 12px; font-weight: 700; text-decoration: none;">Forgot Password?</a>
                </div>

                <button type="submit" name="login" class="btn-alumnix" style="width: 100%; padding: 12px; background: #ff4d4d; color: white; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; text-transform: uppercase;">Login to Portal</button>
            </form>

            <div style="text-align: center; margin-top: 30px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
                <p style="font-size: 13px; color: #64748b;">New here? <a href="registration.php" style="color: #ff4d4d; font-weight: 700; text-decoration: none;">Register Now</a></p>
                <a href="index.php" style="display: block; margin-top: 15px; font-size: 11px; color: #cbd5e1; text-transform: uppercase; font-weight: 800; text-decoration: none;">← Home</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>