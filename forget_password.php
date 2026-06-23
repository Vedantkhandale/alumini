<?php
session_start();
include("includes/db.php");
require_once __DIR__ . "/includes/account_mail.php";

$message = "";
$success = false;

// Ensure reset token and expiry columns exist in database
function ensureResetTokenColumns($conn) {
    static $checked = false;
    if ($checked) return;
    $checked = true;
    
    try {
        $result = $conn->query("SHOW COLUMNS FROM alumni_users LIKE 'reset_token'");
        if (!($result instanceof mysqli_result) || $result->num_rows === 0) {
            $conn->query("ALTER TABLE alumni_users ADD COLUMN reset_token VARCHAR(255) NULL");
            $conn->query("ALTER TABLE alumni_users ADD COLUMN reset_token_expiry DATETIME NULL");
        }
    } catch (Throwable $e) {
        // Column might already exist
    }
}

ensureResetTokenColumns($conn);

// Process form submission
if(isset($_POST['get_code'])){
    $email = trim(strtolower(mysqli_real_escape_string($conn, $_POST['email'])));
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "❌ Please enter a valid email address!";
    } else {
        // Check if email exists
        $check = $conn->prepare("SELECT id, full_name FROM alumni_users WHERE email = ? LIMIT 1");
        $userExists = false;
        $userName = "";
        $userId = null;
        
        if ($check) {
            $check->bind_param("s", $email);
            $check->execute();
            $result = $check->get_result();
            if ($result && $result->num_rows > 0) {
                $userExists = true;
                $user = $result->fetch_assoc();
                $userName = $user['full_name'];
                $userId = $user['id'];
            }
            $check->close();
        }
        
        if($userExists){
            // Generate secure reset token
            $reset_token = bin2hex(random_bytes(32));
            $token_expiry = date("Y-m-d H:i:s", strtotime("+24 hours"));
            
            // Save token to database
            $update = $conn->prepare("UPDATE alumni_users SET reset_token = ?, reset_token_expiry = ? WHERE id = ?");
            if ($update) {
                $update->bind_param("ssi", $reset_token, $token_expiry, $userId);
                $update->execute();
                $update->close();
                
                // Send password reset email
                $emailSent = alumnixSendPasswordResetEmail($userName, $email, $reset_token);
                
                if ($emailSent) {
                    $success = true;
                    $message = "✅ Password reset link sent to your email! Check your inbox.";
                } else {
                    $message = "⚠️ Email could not be sent. Please try again later.";
                }
            }
        } else {
            // Don't reveal if email exists or not (security best practice)
            $message = "✅ If this email is registered, you'll receive a password reset link shortly.";
            $success = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | AlumniX</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #ff4d4d; --dark: #1e293b; --bg: #f8fafc; }
        
        body { 
            background: var(--bg); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            display: flex; justify-content: center; align-items: center; 
            height: 100vh; margin: 0; 
            overflow: hidden;
        }

        /* Sexy Glassmorphism Card */
        .card { 
            width: 100%; max-width: 360px; 
            background: #ffffff; padding: 40px 30px; 
            border-radius: 30px; 
            box-shadow: 0 20px 40px rgba(0,0,0,0.06); 
            border: 1px solid rgba(255,255,255,0.8);
            text-align: center;
            position: relative;
        }

        h2 { font-size: 26px; font-weight: 800; color: var(--dark); margin-bottom: 8px; letter-spacing: -1px; }
        h2 span { color: var(--primary); }
        p.desc { color: #94a3b8; font-size: 13px; margin-bottom: 30px; }

        .input-group { text-align: left; margin-bottom: 20px; }
        .label { font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 8px; display: block; }

        .input-field { 
            width: 100%; padding: 14px 18px; border: 1.5px solid #f1f5f9; 
            border-radius: 15px; font-size: 14px; outline: none; 
            transition: 0.3s; box-sizing: border-box; background: #fdfdfe;
        }
        .input-field:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(255, 77, 77, 0.05); }

        /* Stylish Captcha Box */
        .captcha-wrapper {
            background: #fff5f5; border: 2px dashed #ffcaca; padding: 15px;
            border-radius: 18px; margin: 10px 0 20px 0;
            display: flex; flex-direction: column; align-items: center;
        }
        .captcha-text { font-size: 24px; font-weight: 800; color: var(--primary); letter-spacing: 2px; }
        
        .btn { 
            width: 100%; padding: 16px; background: var(--dark); color: #fff; 
            border: none; border-radius: 15px; font-weight: 700; 
            font-size: 14px; cursor: pointer; transition: 0.3s; 
            text-transform: uppercase; letter-spacing: 1px;
        }
        .btn:hover { background: var(--primary); transform: translateY(-3px); box-shadow: 0 10px 20px rgba(255, 77, 77, 0.2); }

        .error-msg { 
            background: #fff1f2; color: #e11d48; padding: 12px; 
            border-radius: 12px; font-size: 12px; font-weight: 600; 
            margin-bottom: 20px; border: 1px solid #ffe4e6;
        }

        .back-link { margin-top: 25px; display: block; color: #94a3b8; font-size: 12px; text-decoration: none; font-weight: 600; }
        .back-link:hover { color: var(--primary); }
    </style>
</head>
<body>

<div class="card">
    <h2>Forgot <span>Password?</span></h2>
    <p class="desc">Enter your email and we'll send you a password reset link.</p>

    <?php if($message): ?>
        <div class="error-msg" style="<?php echo $success ? 'background: #ecfdf5; color: #047857; border-color: #a7f3d0;' : ''; ?>"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <div class="input-group">
            <label class="label">Email Address</label>
            <input type="email" name="email" class="input-field" placeholder="name@example.com" required>
        </div>

        <button type="submit" name="get_code" class="btn">Send Reset Link</button>
    </form>

    <a href="login.php" class="back-link">← Back to Login</a>
</div>

</body>
</html>
