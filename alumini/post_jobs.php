<?php header("Location: dashboard.php"); exit(); ?>
<?php
session_start();
include("../includes/db.php");

$email = $_SESSION['user'];
$res = $conn->query("SELECT * FROM alumni WHERE email='$email'");
$user = $res->fetch_assoc();

if(isset($_POST['post'])){
    $title = $_POST['title'];
    $company = $_POST['company'];
    $desc = $_POST['description'];
    $loc = $_POST['location'];

    $conn->query("INSERT INTO jobs (alumni_id,title,company,description,location)
    VALUES ('".$user['id']."','$title','$company','$desc','$loc')");

    echo "Job Posted (Pending Approval)";
}
?>

<form method="POST">
    <input type="text" name="title" placeholder="Job Title"><br>
    <input type="text" name="company" placeholder="Company"><br>
    <textarea name="description" placeholder="Description"></textarea><br>
    <input type="text" name="location" placeholder="Location"><br>
    <button name="post">Post Job</button>
</form>
