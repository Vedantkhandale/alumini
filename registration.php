<?php
include("includes/db.php");

$msg = "";

if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);

    // Check email exists
    $check = $conn->prepare("SELECT * FROM alumni WHERE email=?");
    $check->bind_param("s", $email);
    $check->execute();
    $result = $check->get_result();

    if($result->num_rows > 0){
        $msg = "⚠️ Email already registered!";
    } else {
        $stmt = $conn->prepare("INSERT INTO alumni (name,email,password) VALUES (?,?,?)");
        $stmt->bind_param("sss", $name, $email, $pass);

        if($stmt->execute()){
            $msg = "✅ Registered Successfully!";
        } else {
            $msg = "❌ Error occurred!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .form-box {
            width: 350px;
            margin: 80px auto;
            background: #1a1a1a;
            padding: 25px;
            border-radius: 10px;
        }
        h2 { text-align: center; }
        .msg { text-align: center; margin: 10px 0; }
    </style>
</head>
<body>

<div class="form-box">
    <h2>Alumni Registration</h2>

    <div class="msg"><?php echo $msg; ?></div>

    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button name="register">Register</button>
    </form>
</div>

</body>
</html>