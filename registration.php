<?php
session_start();
include("includes/db.php");
$msg = "";

// Check if form was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    
    // Sabhi fields ko safely fetch karo (No more Undefined Key warnings)
    $full_name  = isset($_POST['full_name']) ? mysqli_real_escape_string($conn, $_POST['full_name']) : '';
    $student_id = isset($_POST['student_id']) ? mysqli_real_escape_string($conn, $_POST['student_id']) : '';
    $email      = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';
    $gender     = isset($_POST['gender']) ? mysqli_real_escape_string($conn, $_POST['gender']) : '';
    $batch      = isset($_POST['batch']) ? mysqli_real_escape_string($conn, $_POST['batch']) : '';
    $grad_year  = isset($_POST['grad_year']) ? mysqli_real_escape_string($conn, $_POST['grad_year']) : '';
    $pass       = isset($_POST['password']) ? mysqli_real_escape_string($conn, $_POST['password']) : ''; 

    // Basic validation taaki empty data insert na ho
    if (!empty($email) && !empty($student_id)) {
        
        // Duplicate check logic
        $check = $conn->query("SELECT * FROM users WHERE email='$email' OR student_id='$student_id'");

        if($check->num_rows > 0){
            $msg = "<div class='alert error'>⚠️ Student ID or Email already registered!</div>";
        } else {
            // Data insert (status 'pending' rakha hai admin approval ke liye)
            $sql = "INSERT INTO users (full_name, student_id, email, password, gender, batch, graduation_year, status, role) 
                    VALUES ('$full_name', '$student_id', '$email', '$pass', '$gender', '$batch', '$grad_year', 'pending', 'alumni')";
            
            if($conn->query($sql)){
                $msg = "<div class='alert success'>✅ Registration Submitted! Wait for Admin Approval.</div>";
            } else {
                $msg = "<div class='alert error'>❌ DB Error: " . $conn->error . "</div>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Portal | Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #f8fafc; font-family: 'Plus Jakarta Sans', sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .reg-card { background: #fff; width: 100%; max-width: 600px; padding: 35px; border-radius: 20px; box-shadow: 0 15px 40px rgba(0,0,0,0.06); border: 1px solid #f1f5f9; }
        .header { text-align: center; margin-bottom: 25px; }
        .header h1 { font-size: 30px; font-weight: 800; color: #1e293b; }
        .header span { color: #ff4d4d; }
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        .field-group { margin-bottom: 5px; }
        .label { font-size: 11px; font-weight: 700; color: #64748b; text-transform: uppercase; display: block; margin-bottom: 6px; }
        .input-style, select { width: 100%; padding: 12px; border: 1px solid #e2e8f0; border-radius: 10px; background: #f9fafb; font-size: 14px; outline: none; }
        .input-style:focus, select:focus { border-color: #ff4d4d; background: #fff; box-shadow: 0 0 0 4px rgba(255, 77, 77, 0.05); }
        .btn-red { grid-column: span 2; width: 100%; padding: 16px; background: #ff4d4d; color: #fff; border: none; border-radius: 12px; font-weight: 800; text-transform: uppercase; cursor: pointer; margin-top: 15px; }
        .btn-red:hover { background: #e63939; transform: translateY(-2px); }
        .alert { padding: 12px; border-radius: 10px; margin-bottom: 20px; font-size: 13px; font-weight: 600; text-align: center; border: 1px solid; }
        .error { background: #fef2f2; color: #ef4444; border-color: #fee2e2; }
        .success { background: #f0fdf4; color: #10b981; border-color: #dcfce7; }
        @media (max-width: 600px) { .form-grid { grid-template-columns: 1fr; } .btn-red { grid-column: span 1; } }
    </style>
</head>
<body>

<div class="reg-card">
    <div class="header">
        <h1>Alumni<span>X</span></h1>
        <p style="color: #94a3b8; font-size: 14px;">Membership Registration</p>
    </div>

    <?php if(!empty($msg)) echo $msg; ?>

    <form action="registration.php" method="POST">
        <div class="form-grid">
            <div class="field-group">
                <label class="label">Full Name</label>
                <input type="text" name="full_name" class="input-style" placeholder="Rahul Sharma" required>
            </div>
            
            <div class="field-group">
                <label class="label">Student ID</label>
                <input type="text" name="student_id" class="input-style" placeholder="Ex: ST-2026" required>
            </div>

            <div class="field-group">
                <label class="label">Email Address</label>
                <input type="email" name="email" class="input-style" placeholder="rahul@email.com" required>
            </div>

            <div class="field-group">
                <label class="label">Gender</label>
                <select name="gender" class="input-style" required>
                    <option value="">Choose...</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                    <option value="Other">Other</option>
                </select>
            </div>

            <div class="field-group">
                <label class="label">Batch</label>
                <input type="text" name="batch" class="input-style" placeholder="2020-2024" required>
            </div>

            <div class="field-group">
                <label class="label">Graduation Year</label>
                <select name="grad_year" class="input-style" required>
                    <option value="">Year...</option>
                    <?php 
                    $current = date("Y") + 4;
                    for($i=$current; $i>=2000; $i--) { echo "<option value='$i'>$i</option>"; }
                    ?>
                </select>
            </div>

            <div class="field-group" style="grid-column: span 2;">
                <label class="label">Password</label>
                <input type="password" name="password" class="input-style" placeholder="••••••••" required>
            </div>

            <button type="submit" name="register" class="btn-red">Request Access</button>
        </div>
    </form>
    
    <div style="text-align: center; margin-top: 25px; border-top: 1px solid #f1f5f9; padding-top: 20px;">
        <p style="font-size: 13px; color: #64748b;">Already a member? <a href="login.php" style="color: #ff4d4d; font-weight: 700; text-decoration: none;">Log In</a></p>
    </div>
</div>

</body>
</html>