<?php
session_start();
include("includes/db.php");

if(isset($_POST['login'])){
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $result = $conn->query("SELECT * FROM alumni WHERE email='$email' AND password='$pass'");

    if($result->num_rows > 0){
        $_SESSION['user'] = $email;
        header("Location: alumni/dashboard.php");
    } else {
        echo "Invalid Login!";
    }
}
?>

<form method="POST">
    <input type="email" name="email" placeholder="Email"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <button name="login">Login</button>
</form>