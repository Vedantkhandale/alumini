<?php
session_start();
if(!isset($_SESSION['user'])){
    header("Location: ../login.php");
}
?>

<h2>Welcome Alumni</h2>

<a href="post_job.php">Post Job</a> |
<a href="my_jobs.php">My Jobs</a> |
<a href="../logout.php">Logout</a>