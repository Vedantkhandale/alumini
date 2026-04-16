<?php 
include('db.php'); 
$id = $_GET['id'] ?? 1;
$stmt = $conn->prepare("SELECT * FROM alumni WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if(!$user) die("User not found.");
$avatar = "https://ui-avatars.com/api/?name=".urlencode($user['name'])."&background=ff4d4f&color=fff&bold=true&size=200";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $user['name'] ?> | Profile</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .profile-wrap { display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px; }
        .p-card { max-width: 500px; width: 100%; text-align: center; }
        .p-card img { width: 140px; border-radius: 50%; border: 4px solid #fff; box-shadow: var(--shadow); margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="profile-wrap">
        <div class="card-panel p-card">
            <img src="<?= $avatar ?>" alt="">
            <h1 class="section-title"><?= $user['name'] ?></h1>
            <p class="card-subtitle" style="color:#6b7280"><?= $user['course'] ?> | Batch <?= $user['batch'] ?></p>
            <br>
            <div class="pill" style="justify-content:center">Works at: <?= $user['company'] ?></div>
            <br>
            <a href="index.php" class="section-link">← Back to Directory</a>
        </div>
    </div>
</body>
</html>