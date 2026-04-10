<?php
session_start();
include("../includes/db.php");

if(isset($_GET['approve'])){
    $id = $_GET['approve'];
    $conn->query("UPDATE jobs SET status='approved' WHERE id=$id");
}

if(isset($_GET['reject'])){
    $id = $_GET['reject'];
    $conn->query("UPDATE jobs SET status='rejected' WHERE id=$id");
}

$jobs = $conn->query("SELECT jobs.*, alumni.name 
FROM jobs 
JOIN alumni ON jobs.alumni_id = alumni.id");

while($row = $jobs->fetch_assoc()){
    echo "<h3>".$row['title']."</h3>";
    echo "By: ".$row['name']."<br>";
    echo "Status: ".$row['status']."<br>";

    if($row['status'] == 'pending'){
        echo "<a href='?approve=".$row['id']."'>Approve</a> | ";
        echo "<a href='?reject=".$row['id']."'>Reject</a>";
    }

    echo "<hr>";
}
?>