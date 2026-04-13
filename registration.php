<?php
session_start();
include("includes/db.php");

$msg = "";

if(isset($_POST['register'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, $_POST['password']); 

    $check = $conn->query("SELECT * FROM alumni WHERE email='$email'");

    if($check->num_rows > 0){
        $msg = "<div class='alert error'>⚠️ Email is already registered!</div>";
    } else {
        $sql = "INSERT INTO alumni (name, email, password, status) VALUES ('$name', '$email', '$pass', 'pending')";
        if($conn->query($sql)){
            $msg = "<div class='alert success'>✅ Request Sent! Wait for Admin approval.</div>";
        } else {
            $msg = "<div class='alert error'>❌ Database Error. Try again.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join AlumniX | Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    
    <style>
        * { box-sizing: border-box; }
        body {
            background-color: #f8fafc;
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px; /* Mobile spacing */
        }
        .reg-container {
            width: 100%;
            max-width: 450px;
        }
        .alumnix-card {
            background: #fff;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.05);
            border: 1px solid #f1f5f9;
        }
        .brand-logo { text-align: center; margin-bottom: 30px; }
        .brand-logo h1 {
            font-size: 32px; font-weight: 800; color: #1e293b; margin: 0; letter-spacing: -1px;
        }
        .brand-logo span { color: #ff4d4d; }
        
        /* Inputs & Labels */
        .field-group { margin-bottom: 18px; text-align: left; }
        .field-label {
            font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase;
            letter-spacing: 1px; margin-bottom: 8px; display: block;
        }
        .form-control-alumnix {
            width: 100%; padding: 14px; border: 1px solid #e2e8f0; border-radius: 10px;
            background: #f9fafb; font-size: 15px; transition: 0.3s;
        }
        .form-control-alumnix:focus {
            border-color: #ff4d4d; background: #fff; outline: none;
        }
        
        .btn-red {
            width: 100%; padding: 15px; background: #ff4d4d; color: #fff; border: none;
            border-radius: 10px; font-weight: 800; text-transform: uppercase;
            cursor: pointer; font-size: 13px; margin-top: 10px; transition: 0.3s;
        }
        .btn-red:hover { background: #e63939; }

        /* Alert Boxes */
        .alert {
            padding: 12px; border-radius: 8px; margin-bottom: 20px; font-size: 13px;
            font-weight: 600; text-align: center; border: 1px solid;
        }
        .alert.error { background: #fef2f2; color: #ef4444; border-color: #fee2e2; }
        .alert.success { background: #f0fdf4; color: #10b981; border-color: #dcfce7; }

        /* RESPONSIVE DESIGN (Media Queries) */
        @media (max-width: 480px) {
            .alumnix-card {
                padding: 25px 20px; /* Mobile par padding kam takki screen barbad na ho */
            }
            .brand-logo h1 { font-size: 26px; }
            .form-control-alumnix { padding: 12px; font-size: 14px; }
            .btn-red { padding: 13px; font-size: 12px; }
        }
    </style>
</head>
<body>

<div class="reg-container">
    <div class="alumnix-card">
        <div class="brand-logo">
            <h1>Alumni<span>X</span></h1>
            <p style="color: #94a3b8; font-size: 13px; margin-top: 5px;">Join the Elite Network</p>
        </div>

        <?php echo $msg; ?>

        <form method="POST">
            <div class="field-group">
                <label class="field-label">Full Name</label>
                <input type="text" name="name" class="form-control-alumnix" placeholder="Rahul Sharma" required>
            </div>

            <div class="field-group">
                <label class="field-label">Email Address</label>
                <input type="email" name="email" class="form-control-alumnix" placeholder="rahul@college.edu" required>
            </div>

            <div class="field-group">
                <label class="field-label">Password</label>
                <input type="password" name="password" class="form-control-alumnix" placeholder="••••••••" required>
            </div>

            <button type="submit" name="register" class="btn-red">
                Register Now
            </button>
        </form>

        <div style="text-align: center; margin-top: 30px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
            <p style="font-size: 13px; color: #64748b;">Already have an account? 
                <a href="login.php" style="color: #ff4d4d; font-weight: 700; text-decoration: none;">Login</a>
            </p>
        </div>
    </div>
</div>

</body>
</html>