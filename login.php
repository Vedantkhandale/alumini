<?php
session_start();
include("includes/db.php"); 

$error = "";
$success = false;
$redirect_url = "";

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']);

    // SQL Injection se bachne ke liye prepared statements better hote hain, par abhi tera logic fix kar diya hai
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
    <title>Login | AlumniX Elite</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        :root { 
            --primary: #ff3b3b; 
            --bg-dark: #050505; 
            --glass: rgba(255, 255, 255, 0.03); 
            --border: rgba(255, 255, 255, 0.08); 
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            background: var(--bg-dark); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            display: flex; justify-content: center; align-items: center; 
            min-height: 100vh; 
            color: #fff;
            overflow: hidden;
        }

        /* 🎭 MESH BACKGROUND */
        .mesh {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 10% 20%, rgba(255, 59, 59, 0.1) 0%, transparent 40%),
                        radial-gradient(circle at 90% 80%, rgba(255, 59, 59, 0.1) 0%, transparent 40%);
            z-index: -1;
        }

        .login-wrapper { width: 100%; max-width: 440px; padding: 25px; position: relative; }

        /* 💎 GLASS CARD */
        .alumnix-card { 
            background: var(--glass); 
            padding: 50px 40px; 
            border-radius: 40px; 
            backdrop-filter: blur(20px);
            border: 1px solid var(--border);
            box-shadow: 0 40px 100px rgba(0,0,0,0.5);
            text-align: center;
        }

        .header h1 { 
            font-size: 42px; font-weight: 800; color: #fff; letter-spacing: -2px; 
            margin-bottom: 5px;
        }
        .header h1 span { color: var(--primary); }
        .header p { color: #64748b; font-size: 14px; font-weight: 500; }

        .error-msg { 
            background: rgba(225, 29, 72, 0.1); 
            color: #ff4d4d; 
            padding: 15px; 
            border-radius: 15px; 
            font-size: 13px; 
            font-weight: 600; 
            margin-bottom: 30px; 
            border: 1px solid rgba(225, 29, 72, 0.2); 
        }

        .input-group { text-align: left; margin-bottom: 25px; }
        .label { 
            font-size: 10px; font-weight: 800; color: #444; 
            text-transform: uppercase; letter-spacing: 1.5px; 
            display: block; margin-bottom: 10px; margin-left: 5px;
        }

        .input-style { 
            width: 100%; padding: 16px 20px; 
            border: 1px solid var(--border); 
            border-radius: 18px; 
            background: rgba(255,255,255,0.02); 
            color: #fff; outline: none; transition: 0.4s; 
            font-size: 15px;
        }

        .input-style:focus { 
            border-color: var(--primary); 
            background: rgba(255,255,255,0.05); 
            box-shadow: 0 0 20px rgba(255, 59, 59, 0.15); 
        }

        .btn-alumnix { 
            width: 100%; padding: 18px; 
            background: var(--primary); color: white; 
            border: none; border-radius: 18px; 
            font-size: 14px; font-weight: 800; cursor: pointer; 
            text-transform: uppercase; letter-spacing: 1px;
            transition: 0.4s; 
            box-shadow: 0 15px 30px rgba(255, 59, 59, 0.25); 
        }

        .btn-alumnix:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 20px 40px rgba(255, 59, 59, 0.4);
            filter: brightness(1.1);
        }

        .bottom-links { margin-top: 40px; padding-top: 30px; border-top: 1px solid var(--border); }
        .bottom-links p { font-size: 13px; color: #444; font-weight: 600; }
        
        a { color: var(--primary); text-decoration: none; font-weight: 700; transition: 0.3s; }
        a:hover { color: #fff; }

        /* Animation */
        .reveal { animation: fadeIn 0.8s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="mesh"></div>

<div class="login-wrapper reveal">
    <div class="alumnix-card">
        <div class="header">
            <h1>Alumni<span>X</span></h1>
            <p>Enter your credentials to continue</p>
        </div>

        <div style="margin-top: 35px;">
            <?php if(!empty($error)): ?>
                <div class="error-msg"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="input-group">
                    <label class="label">Email Address</label>
                    <input type="email" name="email" class="input-style" placeholder="name@example.com" required>
                </div>
                <div class="input-group">
                    <label class="label">Password</label>
                    <input type="password" name="password" class="input-style" placeholder="••••••••" required>
                </div>
                <button type="submit" name="login" class="btn-alumnix">Authenticate & Access</button>
            </form>
        </div>

        <div class="bottom-links">
            <p>New to the network? <a href="registration.php">Create Account</a></p>
            <p style="margin-top: 12px;"><a href="forget_password.php" style="color: #64748b; font-size: 12px;">Forgot Credentials?</a></p>
        </div>
    </div>
</div>

<?php if($success): ?>
<script>
    Swal.fire({
        title: 'Access Granted',
        text: 'Synchronizing your dashboard...',
        icon: 'success',
        background: '#0a0a0a',
        color: '#fff',
        timer: 1800,
        showConfirmButton: false,
        iconColor: '#ff3b3b'
    }).then(() => {
        window.location.href = '<?php echo $redirect_url; ?>';
    });
</script>
<?php endif; ?>

</body>
</html>