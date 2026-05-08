<?php
session_start();
include("../includes/db.php");

// Session Check
if(!isset($_SESSION['user'])){ header("Location: login.php"); exit(); }

$email = $_SESSION['user']['email']; // Session se email lo
$res = $conn->query("SELECT * FROM alumni WHERE email='$email'");
$user = $res->fetch_assoc();

$msg = "";
if(isset($_POST['post'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $company = mysqli_real_escape_string($conn, $_POST['company']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    $loc = mysqli_real_escape_string($conn, $_POST['location']);
    $link = mysqli_real_escape_string($conn, $_POST['apply_link']);
    $logo = mysqli_real_escape_string($conn, $_POST['logo_url']);
    $alumni_id = $user['id'];

    // Default status 'pending' rakha hai
    $sql = "INSERT INTO jobs (alumni_id, title, company, description, location, apply_link, logo, status) 
            VALUES ('$alumni_id', '$title', '$company', '$desc', '$loc', '$link', '$logo', 'pending')";

    if($conn->query($sql)){
        $msg = "Job submitted! Admin approval ke baad live hogi.";
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post Job | AlumniX</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f4f7fe; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin:0; }
        .form-card { background: white; padding: 40px; border-radius: 30px; box-shadow: 0 20px 50px rgba(0,0,0,0.05); width: 100%; max-width: 500px; }
        h2 { font-weight: 800; color: #0f172a; margin-bottom: 20px; }
        input, textarea { width: 100%; padding: 15px; margin-bottom: 15px; border: 1px solid #e2e8f0; border-radius: 15px; outline: none; transition: 0.3s; }
        input:focus { border-color: #e11d48; }
        .btn-post { background: #e11d48; color: white; border: none; width: 100%; padding: 15px; border-radius: 15px; font-weight: 700; cursor: pointer; transition: 0.3s; }
        .btn-post:hover { background: #0f172a; transform: translateY(-3px); }
    </style>
</head>
<body>

<div class="form-card">
    <h2>Post a Opportunity 🚀</h2>
    <form method="POST">
        <input type="text" name="title" placeholder="Job Title (e.g. Frontend Dev)" required>
        <input type="text" name="company" placeholder="Company Name" required>
        <input type="text" name="location" placeholder="Location (e.g. Nagpur / Remote)" required>
        <input type="text" name="logo_url" placeholder="Company Logo Image URL">
        <input type="text" name="apply_link" placeholder="Career Page / Application Link" required>
        <textarea name="description" placeholder="Short Job Description..." rows="4" required></textarea>
        <button name="post" class="btn-post">Submit for Approval</button>
    </form>
</div>

<?php if($msg): ?>
<script>
    Swal.fire({
        title: 'Received!',
        text: '<?php echo $msg; ?>',
        icon: 'success',
        confirmButtonColor: '#e11d48'
    });
</script>
<?php endif; ?>

</body>
</html>