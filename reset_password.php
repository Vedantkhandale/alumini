<?php
session_start();
include("includes/db.php");

// Agar user direct is page pe aaye bina code generate kiye, toh wapas bhej do
if(!isset($_SESSION['reset_email']) || !isset($_SESSION['reset_token'])){
    header("Location: forgot.php");
    exit();
}

$message = "";
if(isset($_POST['reset_now'])){
    $input_token = intval($_POST['token']);
    $new_pass = $_POST['new_pass'];
    $confirm_pass = $_POST['confirm_pass'];

    // Validation
    if($input_token !== intval($_SESSION['reset_token'])){
        $message = "❌ Invalid Reset Code!";
    } elseif($new_pass !== $confirm_pass){
        $message = "❌ Passwords do not match!";
    } elseif(strlen($new_pass) < 6){
        $message = "❌ Password must be at least 6 chars!";
    } else {
        $hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);
        $email = $_SESSION['reset_email'];
        
        // Database Update
        $update = $conn->query("UPDATE users SET password='$hashed_pass' WHERE email='$email'");
        
        if($update){
            // Kaam ho gaya, ab session saaf karo
            unset($_SESSION['reset_email']);
            unset($_SESSION['reset_token']);
            
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        title: 'Success!',
                        text: 'Password has been updated. Please login.',
                        icon: 'success',
                        confirmButtonColor: '#ff4d4d'
                    }).then(() => { window.location.href = 'login.php'; });
                }, 100);
            </script>";
        } else {
            $message = "❌ Database error! Try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | AlumniX</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #ff4d4d; --text: #1e293b; --bg: #f8fafc; }
        body { background: var(--bg); font-family: 'Plus Jakarta Sans', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        
        .card { 
            width: 100%; max-width: 350px; background: #fff; padding: 35px 30px; 
            border-radius: 28px; box-shadow: 0 15px 40px rgba(0,0,0,0.06); 
            border: 1px solid #f1f5f9; text-align: center;
        }

        h2 { font-size: 24px; font-weight: 800; color: var(--text); margin-bottom: 8px; letter-spacing: -0.5px; }
        h2 span { color: var(--primary); }
        p.sub-text { color: #94a3b8; font-size: 13px; margin-bottom: 25px; }

        .input-group { text-align: left; margin-bottom: 15px; }
        .label { font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; margin-bottom: 6px; display: block; }
        
        .input-field { 
            width: 100%; padding: 13px 15px; border: 1px solid #e2e8f0; 
            border-radius: 12px; font-size: 14px; outline: none; 
            transition: 0.3s; box-sizing: border-box;
        }
        .input-field:focus { border-color: var(--primary); box-shadow: 0 0 0 3px rgba(255, 77, 77, 0.1); }

        .btn { 
            width: 100%; padding: 15px; background: var(--text); color: #fff; 
            border: none; border-radius: 12px; font-weight: 700; 
            font-size: 14px; cursor: pointer; transition: 0.3s; margin-top: 10px;
        }
        .btn:hover { background: var(--primary); transform: translateY(-2px); }

        .error-box { 
            background: #fff1f2; color: #e11d48; padding: 10px; 
            border-radius: 10px; font-size: 12px; font-weight: 600; 
            margin-bottom: 20px; border: 1px solid #ffe4e6;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Reset <span>Now</span></h2>
    <p class="sub-text">Enter the code and set your new password.</p>

    <?php if($message): ?>
        <div class="error-box"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <div class="input-group">
            <label class="label">Reset Code</label>
            <input type="number" name="token" class="input-field" placeholder="6-digit code" required>
        </div>

        <div class="input-group">
            <label class="label">New Password</label>
            <input type="password" name="new_pass" class="input-field" placeholder="••••••••" required>
        </div>

        <div class="input-group">
            <label class="label">Confirm Password</label>
            <input type="password" name="confirm_pass" class="input-field" placeholder="••••••••" required>
        </div>

        <button type="submit" name="reset_now" class="btn">Update Password</button>
    </form>
</div>

</body>
</html>