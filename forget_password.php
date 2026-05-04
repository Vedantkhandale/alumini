<?php
session_start();
include("includes/db.php");
$message = "";

// 1. Pehle Check karo agar form submit hua hai (Validation First)
if(isset($_POST['get_code'])){
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $user_captcha = intval($_POST['captcha']);
    
    // Session se wahi answer uthao jo screen par dikh raha tha
    $correct_ans = isset($_SESSION['captcha_ans']) ? intval($_SESSION['captcha_ans']) : 0;

    if($user_captcha !== $correct_ans){
        $message = "❌ Wrong Captcha! Try again.";
    } else {
        $check = $conn->query("SELECT * FROM users WHERE email='$email'");
        if($check->num_rows > 0){
            $reset_code = rand(100000, 999999);
            $_SESSION['reset_email'] = $email;
            $_SESSION['reset_token'] = $reset_code;
            
            echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
            echo "<script>
                setTimeout(function() {
                    Swal.fire({
                        title: 'Verification Code',
                        html: 'Copy this code to reset:<br><b style=\"font-size:40px; color:#ff4d4d; letter-spacing:3px;\">$reset_code</b>',
                        icon: 'success',
                        background: '#ffffff',
                        confirmButtonColor: '#1e293b',
                        confirmButtonText: 'Proceed to Reset'
                    }).then(() => { window.location.href = 'reset_password.php'; });
                }, 100);
            </script>";
        } else {
            $message = "⚠️ Email not registered!";
        }
    }
}

// 2. Naya Captcha sirf tab generate hoga jab submit ho chuka ho ya pehli baar load ho
$num1 = rand(10, 30);
$num2 = rand(1, 9);
$_SESSION['captcha_ans'] = $num1 + $num2;
$_SESSION['captcha_text'] = "$num1 + $num2";
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
    <h2>Forgot <span>Access?</span></h2>
    <p class="desc">Verify your identity to get the reset code.</p>

    <?php if($message): ?>
        <div class="error-msg"><?php echo $message; ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <div class="input-group">
            <label class="label">Email Address</label>
            <input type="email" name="email" class="input-field" placeholder="name@example.com" required>
        </div>

        <div class="input-group">
            <label class="label">Security Challenge</label>
            <div class="captcha-wrapper">
                <span style="font-size: 10px; color: #ff9494; font-weight: 700; margin-bottom: 5px;">SOLVE THIS MATH</span>
                <div class="captcha-text"><?php echo $_SESSION['captcha_text']; ?></div>
            </div>
            <input type="number" name="captcha" class="input-field" placeholder="Enter result" required>
        </div>

        <button type="submit" name="get_code" class="btn">Get Verification Code</button>
    </form>

    <a href="login.php" class="back-link">← Back to Login</a>
</div>

</body>
</html>