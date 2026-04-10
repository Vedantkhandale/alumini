<?php
session_start();
include("../includes/db.php");

if(isset($_POST['login'])){
    $user = $_POST['username'];
    $pass = $_POST['password'];

    $res = $conn->query("SELECT * FROM admin WHERE username='$user' AND password='$pass'");

    if($res->num_rows > 0){
        $_SESSION['admin'] = $user;
        header("Location: dashboard.php");
    } else {
        echo "Invalid Admin Login";
    }
}
?>

<form method="POST">
    <input type="text" name="username" placeholder="Username"><br>
    <input type="password" name="password" placeholder="Password"><br>
    <button name="login">Login</button>
</form>