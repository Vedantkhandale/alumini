<?php
session_start();
if(file_exists("../includes/db.php")){ include("../includes/db.php"); } else { include("includes/db.php"); }

$error = false;
$success = false; // Redirect control karne ke liye

if(isset($_POST['login'])){
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);

    $res = $conn->query("SELECT * FROM admin WHERE username='$user' AND password='$pass'");

    if($res->num_rows > 0){
        $_SESSION['admin'] = $user;
        $success = true; // Login success
    } else {
        $error = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Vault | AlumniX</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        :root {
            --primary: #ff4d4d;
            --dark: #0f172a;
            --bg: #020617;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body { 
            background: var(--bg); 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            overflow: hidden;
            position: relative;
        }

        .bg-glow {
            position: absolute;
            width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(255, 77, 77, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            z-index: -1;
            filter: blur(80px);
            animation: float 10s infinite alternate;
        }
        .glow-1 { top: -100px; left: -100px; }
        .glow-2 { bottom: -100px; right: -100px; animation-delay: -5s; }

        @keyframes float {
            from { transform: translate(0, 0); }
            to { transform: translate(100px, 100px); }
        }

        .login-card {
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(20px);
            padding: 50px;
            border-radius: 40px;
            width: 100%;
            max-width: 420px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            box-shadow: 0 50px 100px rgba(0,0,0,0.5);
            text-align: center;
        }

        .logo { font-size: 28px; font-weight: 800; color: #fff; text-decoration: none; margin-bottom: 10px; display: block; }
        .logo span { color: var(--primary); }
        
        h2 { color: #fff; font-size: 20px; margin-bottom: 30px; opacity: 0.8; font-weight: 600; }

        .input-group { position: relative; margin-bottom: 20px; }
        .input-group i {
            position: absolute; left: 20px; top: 50%;
            transform: translateY(-50%); color: rgba(255,255,255,0.3);
            transition: 0.3s;
        }

        .input-group input {
            width: 100%; padding: 18px 20px 18px 55px;
            background: rgba(255,255,255,0.03);
            border: 1.5px solid rgba(255,255,255,0.05);
            border-radius: 20px; color: #fff;
            font-size: 15px; font-weight: 500; transition: 0.3s;
        }

        .input-group input:focus {
            border-color: var(--primary);
            background: rgba(255,255,255,0.07);
            outline: none;
            box-shadow: 0 0 20px rgba(255, 77, 77, 0.2);
        }
        .input-group input:focus + i { color: var(--primary); }

        .login-btn {
            width: 100%; padding: 18px;
            background: var(--primary); color: #fff;
            border: none; border-radius: 20px;
            font-size: 16px; font-weight: 800; cursor: pointer;
            transition: 0.4s; box-shadow: 0 15px 30px rgba(255, 77, 77, 0.3);
            margin-top: 10px;
        }

        .login-btn:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(255, 77, 77, 0.4);
            filter: brightness(1.1);
        }

        .footer-text { margin-top: 30px; font-size: 13px; color: rgba(255,255,255,0.4); font-weight: 600; }
    </style>
</head>
<body>

<div class="bg-glow glow-1"></div>
<div class="bg-glow glow-2"></div>

<div class="login-card">
    <a href="../index.php" class="logo">Alumni<span>X</span> Admin</a>
    <h2>Authorize Access</h2>

    <form method="POST">
        <div class="input-group">
            <i class="fas fa-user-shield"></i>
            <input type="text" name="username" placeholder="Admin Username" required>
        </div>
        <div class="input-group">
            <i class="fas fa-lock"></i>
            <input type="password" name="password" placeholder="Master Password" required>
        </div>
        <button type="submit" name="login" class="login-btn">Secure Login</button>
    </form>

    <p class="footer-text"><i class="fas fa-fingerprint"></i> End-to-End Encrypted Session</p>
</div>

<script>
    // ❌ Error Alert
    <?php if($error): ?>
    Swal.fire({
        title: 'Access Denied',
        text: 'Invalid credentials. Please try again.',
        icon: 'error',
        confirmButtonColor: '#ff4d4d',
        background: '#0f172a',
        color: '#fff',
        borderRadius: '30px'
    });
    <?php endif; ?>

    // ✅ Success Alert & Redirect
    <?php if($success): ?>
    Swal.fire({
        title: 'Access Granted',
        text: 'Welcome back, Administrator.',
        icon: 'success',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        background: '#0f172a',
        color: '#fff',
        borderRadius: '30px',
        willClose: () => {
            window.location.href = 'admin_dashboard.php';
        }
    });
    <?php endif; ?>
</script>

</body>
</html>
