<?php
session_start();
include("includes/db.php"); 

$error = "";
$success = false;
$redirect_url = "";

if(isset($_POST['login'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password']; // Plain password from form

    // 1. Pehle user ko email se find karo
    $result = $conn->query("SELECT * FROM users WHERE email='$email'");

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();
        
        // 2. Password Verify karo (Kyuki humne reset page pe password_hash use kiya hai)
        if(password_verify($pass, $user['password'])){
            
            // 3. Status Check Karo
            $status = strtolower(trim((string)($user['status'] ?? 'pending')));

            if($status === 'approved'){
                // LOGIN SUCCESS
                $_SESSION['user'] = $user;

                if(($user['role'] ?? '') === 'admin'){
                    $_SESSION['admin'] = $user['full_name'] ?? $user['email'];
                    $redirect_url = "admin/admin_dashboard.php";
                } else {
                    $redirect_url = "alumini/dashboard.php";
                }
                $success = true;
            } else {
                // STATUS NOT APPROVED
                $error = "🚫 Your account is $status. Please wait for Admin Approval.";
            }
        } else {
            $error = "❌ Incorrect password!";
        }
    } else {
        $error = "⚠️ Account not found with this email.";
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { 
            --primary: #ff4d4d; /* Coral Red */
            --bg-soft: #f8f8f8; /* Soft White */
            --white: #ffffff;
            --text-main: #111111;
            --text-gray: #6b7280;
            --border: #e5e7eb;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            background: var(--bg-soft); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            display: flex; justify-content: center; align-items: center; 
            height: 100vh; 
            color: var(--text-main);
            overflow: hidden;
        }

        /* Soft Mesh for Premium Feel */
        .mesh {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: radial-gradient(circle at 0% 0%, rgba(255, 77, 77, 0.05) 0%, transparent 50%),
                        radial-gradient(circle at 100% 100%, rgba(255, 77, 77, 0.03) 0%, transparent 50%);
            z-index: -1;
        }

        .login-wrapper { width: 100%; max-width: 400px; padding: 20px; }

        /* 💎 LUXURY WHITE CARD */
        .alumnix-card { 
            background: var(--white); 
            padding: 40px; 
            border-radius: 32px; 
            border: 1px solid var(--border);
            box-shadow: 0 20px 40px rgba(0,0,0,0.04);
            text-align: center;
        }

        .header h1 { 
            font-size: 32px; font-weight: 800; letter-spacing: -1.5px; 
            margin-bottom: 8px; color: var(--text-main);
        }
        .header h1 span { color: var(--primary); }
        .header p { color: var(--text-gray); font-size: 14px; font-weight: 500; }

        .error-msg { 
            background: #fff5f5; 
            color: #e53e3e; 
            padding: 12px; 
            border-radius: 12px; 
            font-size: 12px; 
            font-weight: 600; 
            margin-bottom: 25px; 
            border: 1px solid #fed7d7; 
        }

        .input-group { text-align: left; margin-bottom: 20px; }
        .label { 
            font-size: 10px; font-weight: 800; color: var(--text-gray); 
            text-transform: uppercase; letter-spacing: 1px; 
            display: block; margin-bottom: 8px;
        }

        .input-style { 
            width: 100%; padding: 14px 18px; 
            border: 1px solid var(--border); 
            border-radius: 14px; 
            background: #fff; 
            color: var(--text-main); outline: none; transition: 0.3s; 
            font-size: 14px; font-weight: 500;
        }

        .input-style:focus { 
            border-color: var(--primary); 
            box-shadow: 0 0 0 4px rgba(255, 77, 77, 0.05);
        }

        .btn-alumnix { 
            width: 100%; padding: 15px; 
            background: var(--text-main); color: white; 
            border: none; border-radius: 14px; 
            font-size: 13px; font-weight: 700; cursor: pointer; 
            text-transform: uppercase; letter-spacing: 0.5px;
            transition: 0.3s; 
        }

        .btn-alumnix:hover { 
            background: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(255, 77, 77, 0.2);
        }

        .bottom-links { margin-top: 30px; padding-top: 25px; border-top: 1px solid var(--border); }
        .bottom-links p { font-size: 13px; color: var(--text-gray); font-weight: 500; }
        
        a { color: var(--primary); text-decoration: none; font-weight: 700; }
        a:hover { text-decoration: underline; }

        .reveal { animation: fadeIn 0.6s ease-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body>

<div class="mesh"></div>

<div class="login-wrapper reveal">
    <div class="alumnix-card">
        <div class="header">
            <h1>Alumni<span>X</span></h1>
            <p>Welcome back, login to continue</p>
        </div>

        <div style="margin-top: 30px;">
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
        </div>

        <div class="bottom-links">
            <p>Don't have an account? <a href="registration.php">Join Now</a></p>
            <p style="margin-top: 10px;"><a href="forget_password.php" style="color: #94a3b8; font-size: 12px; font-weight: 500;">Forgot Password?</a></p>
        </div>
    </div>
</div>

<?php if($success): ?>
<script>
    Swal.fire({
        title: 'Welcome Back',
        text: 'Redirecting to your workspace...',
        icon: 'success',
        confirmButtonColor: '#ff4d4d'
    }).then(() => {
        window.location.href = '<?php echo $redirect_url; ?>';
    });
</script>
<?php endif; ?>

</body>
</html>