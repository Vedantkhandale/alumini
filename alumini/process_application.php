<?php
session_start();
include(__DIR__ . "/../includes/db.php");

if(isset($_POST['ajax_apply'])){
    $job_id = $_POST['job_id'];
    $alumni_id = $_SESSION['user']['id'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $exp = mysqli_real_escape_string($conn, $_POST['experience']);
    $tech = mysqli_real_escape_string($conn, $_POST['tech_languages']);
    $skills = mysqli_real_escape_string($conn, $_POST['skills']);
    $resume = mysqli_real_escape_string($conn, $_POST['resume_link']);

    $sql = "INSERT INTO job_applications (job_id, alumni_id, email, experience, tech_languages, skills, resume_link) 
            VALUES ('$job_id', '$alumni_id', '$email', '$exp', '$tech', '$skills', '$resume')";

    if($conn->query($sql)){
        echo "success";
    } else {
        echo "error";
    }
}
?>