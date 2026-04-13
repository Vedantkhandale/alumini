<?php
session_start();
include("includes/db.php");
$message = "";

// 1. Captcha Generate karna (Agar session mein nahi hai)
if (!isset($_SESSION['captcha_ans']) || isset($_GET['refresh'])) {
    $num1 = rand(1, 10);
    $num2 = rand(1, 10);
    $_SESSION['captcha_ans'] = $num1 + $num2;
    $_SESSION['captcha_text'] = "$num1 + $num2";
}

if(isset($_POST['reset_request'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $user_captcha = $_POST['captcha'];

    // 2. Pehle Captcha check karo
    if($user_captcha != $_SESSION['captcha_ans']){
        $message = "<div style='color: #ef4444; background: #fef2f2; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 13px; border: 1px solid #fee2e2;'>Wrong Captcha! Try again.</div>";
        // Refresh captcha on wrong attempt
        $num1 = rand(1, 10); $num2 = rand(1, 10);
        $_SESSION['captcha_ans'] = $num1 + $num2;
        $_SESSION['captcha_text'] = "$num1 + $num2";
    } else {
        // 3. Email Check karo
        $check = $conn->query("SELECT * FROM alumni WHERE email='$email'");
        
        if($check->num_rows > 0){
            $message = "<div style='color: #10b981; background: #f0fdf4; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 13px; border: 1px solid #dcfce7;'>Success! Reset link sent to $email</div>";
            unset($_SESSION['captcha_ans']); // Success pe clear kar do
        } else {
            $message = "<div style='color: #ef4444; background: #fef2f2; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-size: 13px; border: 1px solid #fee2e2;'>Email not found in Alumni database!</div>";
        }
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
            padding: 10px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            color: #1e293b;
            margin-bottom: 10px;
            border: 1px dashed #cbd5e1;
            user-select: none;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="wrapper">
        <div class="alumnix-card" style="padding: 40px; background: #fff; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); border: 1px solid #eee;">
            <div style="text-align: center; margin-bottom: 30px;">
                <h1 style="font-size: 28px; font-weight: 800; color: #1e293b; margin: 0;">Trouble <span style="color: #ff4d4d;">Logging In?</span></h1>
                <p style="color: #94a3b8; font-size: 14px; margin-top: 8px;">Verify you are human to continue.</p>
            </div>

            <?php echo $message; ?>

            <form method="POST">
                <div style="margin-bottom: 20px;">
                    <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Email Address</label>
                    <input type="email" name="email" class="form-control-alumnix" placeholder="Enter registered email" required 
                           style="width: 100%; padding: 12px; margin-top: 8px; border: 1px solid #ddd; border-radius: 8px;">
                </div>

                <div style="margin-bottom: 25px;">
                    <label style="font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;">Solve this: </label>
                    <div class="captcha-box">
                        <?php echo $_SESSION['captcha_text']; ?> = ?
                    </div>
                    <input type="number" name="captcha" class="form-control-alumnix" placeholder="Enter answer" required 
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px;">
                </div>

                <button type="submit" name="reset_request" class="btn-alumnix" style="width: 100%; padding: 14px; background: #ff4d4d; color: #fff; border: none; border-radius: 8px; font-weight: 700; cursor: pointer; text-transform: uppercase;">
                    Verify & Send Link
                </button>
            </form>

            <div style="text-align: center; margin-top: 25px;">
                <a href="login.php" style="color: #64748b; font-size: 12px; text-decoration: none; font-weight: 600; opacity: 0.7; hover:opacity: 1;">← Back to Login</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>