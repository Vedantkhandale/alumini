<?php
session_start();
if(!isset($_SESSION['admin'])){
    header("Location: login.php");
}
?>

<h2>Admin Panel</h2>

<a href="jobs.php">Manage Jobs</a> |
<a href="events.php">Manage Events</a> |
<a href="../logout.php">Logout</a>