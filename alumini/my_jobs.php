<?php
session_start();
include("../includes/db.php");

$email = $_SESSION['user'];
$res = $conn->query("SELECT * FROM alumni WHERE email='$email'");
$user = $res->fetch_assoc();

$jobs = $conn->query("SELECT * FROM jobs WHERE alumni_id='".$user['id']."'");

while($row = $jobs->fetch_assoc()){
    echo "<h3>".$row['title']."</h3>";
    echo "Status: ".$row['status']."<br><hr>";
}
?>