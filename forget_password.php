<?php
session_start();
include("includes/db.php");
$message = "";

// Logic: Sirf tabhi naya captcha banao jab page pehli baar load ho ya submit ho chuka ho
if (!isset($_SESSION['captcha_ans']) || isset($_POST['reset_request'])) {
    // Purana answer delete karo submit ke pehle ya baad
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $new_ans = $num1 + $num2;
    $new_text = "$num1 + $num2";
}

if(isset($_POST['reset_request'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $user_captcha = intval($_POST['captcha']); // Number me convert karo
    $correct_ans = intval($_SESSION['captcha_ans']);

    // Captcha Matching
    if($user_captcha !== $correct_ans){
        $message = "<div style='color: #ef4444; background: #fef2f2; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 13px; border: 1px solid #fee2e2;'>❌ Wrong Captcha! Try again.</div>";
    } else {
        // Email database check
        $check = $conn->query("SELECT * FROM alumni WHERE email='$email'");
        
        if($check->num_rows > 0){
            $message = "<div style='color: #10b981; background: #f0fdf4; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 13px; border: 1px solid #dcfce7;'>✅ Success! Reset link sent to $email</div>";
        } else {
            $message = "<div style='color: #ef4444; background: #fef2f2; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 13px; border: 1px solid #fee2e2;'>⚠️ Email not found in our records!</div>";
        }
    }
    
    // Har submit ke baad session update karo taaki naya captcha dikhe
    $_SESSION['captcha_ans'] = $new_ans;
    $_SESSION['captcha_text'] = $new_text;
} else {
    // First time load pe session set karo agar nahi hai
    if(!isset($_SESSION['captcha_ans'])) {
        $_SESSION['captcha_ans'] = $num1 + $num2;
        $_SESSION['captcha_text'] = "$num1 + $num2";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | AlumniX</title>
    <link rel="stylesheet" href="assets/login.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        .container { display: flex; justify-content: center; align-items: center; height: 100vh; background: #f8fafc; }
        .wrapper { width: 100%; max-width: 400px; padding: 20px; }
        .captcha-box {
            background: #f1f5f9;
            padding: 12px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #1e293b;
            margin: 10px 0;
            border: 1px dashed #cbd5e1;
            font-size: 18px;
            letter-spacing: 2px;
        }
        .form-control-alumnix {
            width: 100%; padding: 12px; margin-top: 8px; border: 1px solid #ddd; border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="wrapper">
        <div class="alumnix-card" style="padding: 40px; background: #fff; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #eee;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="font-size: 28px; font-weight: 800; color: #1e293b; margin: 0;">Trouble <span style="color: #ff4d4d;">Logging In?</span></h1>
                <p style="color: #94a3b8; font-size: 14px; margin-top: 8px;">Verify your identity to proceed.</p>
            </div>

            <?php echo $message; ?>

            <form method="POST">
                <div style="margin-bottom: 20px;">
                    <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Email Address</label>
                    <input type="email" name="email" class="form-control-alumnix" placeholder="Enter registered email" required>
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Security Check</label>
                    <div class="captcha-box">
                        <?php echo $_SESSION['captcha_text']; ?> = ?
                    </div>
                    <input type="number" name="captcha" class="form-control-alumnix" placeholder="Enter result" required>
                </div>

                <button type="submit" name="reset_request" class="btn-alumnix" style="width: 100%; padding: 14px; background: #ff4d4d; color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; text-transform: uppercase;">
                    Send Reset Link
                </button>
            </form>

            <div style="text-align: center; margin-top: 25px;">
                <a href="login.php" style="color: #64748b; font-size: 12px; text-decoration: none; font-weight: 600;">← Back to Login</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>