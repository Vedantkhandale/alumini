<?php
session_start();
include("includes/db.php");

$reg_success = false;
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $full_name  = mysqli_real_escape_string($conn, $_POST['full_name']);
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $gender     = mysqli_real_escape_string($conn, $_POST['gender']);
    
    $b_start    = isset($_POST['batch_start']) ? mysqli_real_escape_string($conn, $_POST['batch_start']) : '';
    $b_end      = isset($_POST['batch_end']) ? mysqli_real_escape_string($conn, $_POST['batch_end']) : '';
    $batch      = $b_start . " - " . $b_end;

    $grad_year  = mysqli_real_escape_string($conn, $_POST['grad_year']);
    $company    = mysqli_real_escape_string($conn, $_POST['company']);

    $raw_pass    = $_POST['password'];
    $hashed_pass = password_hash($raw_pass, PASSWORD_DEFAULT);

    $img_name = "default.png";
    if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] == 0) {
        $target_dir = "uploads/profiles/";
        if (!is_dir($target_dir)) { mkdir($target_dir, 0777, true); }
        $ext = pathinfo($_FILES['profile_img']['name'], PATHINFO_EXTENSION);
        $img_name = "IMG_" . time() . "." . $ext;
        move_uploaded_file($_FILES['profile_img']['tmp_name'], $target_dir . $img_name);
    }

    $check = $conn->query("SELECT id FROM users WHERE email='$email' OR student_id='$student_id'");
    if ($check->num_rows > 0) {
        $error_msg = "⚠️ Student ID or Email already exists!";
    } else {
        $sql = "INSERT INTO users (full_name, student_id, email, password, gender, batch, graduation_year, company, image, status, role) 
                VALUES ('$full_name', '$student_id', '$email', '$hashed_pass', '$gender', '$batch', '$grad_year', '$company', '$img_name', 'pending', 'alumni')";
        if ($conn->query($sql)) { $reg_success = true; } 
        else { $error_msg = "❌ Error: " . $conn->error; }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join AlumniX | Premium Registration</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { 
            --primary: #ff4d4d; /* Coral Red */
            --bg: #f8f8f8; /* Soft White */
            --card-bg: #ffffff; 
            --text-main: #111111; 
            --text-gray: #6b7280; 
            --border: #e5e7eb; 
        }
        
        * { box-sizing: border-box; margin: 0; padding: 0; }
        
        body { 
            background: var(--bg); 
            color: var(--text-main); 
            font-family: 'Plus Jakarta Sans', sans-serif; 
            height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            overflow: hidden; /* Scroll Hatane ke liye */
        }
        
        .wizard-card { 
            background: var(--card-bg); 
            width: 100%; 
            max-width: 420px; /* Card Chota kiya */
            padding: 30px; 
            border-radius: 24px; 
            border: 1px solid var(--border); 
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04); 
            position: relative; 
        }

        .step-header { text-align: center; margin-bottom: 20px; }
        .step-header h1 { font-size: 24px; font-weight: 800; letter-spacing: -1px; }
        .step-header span { color: var(--primary); }
        #step-title { color: var(--text-gray); font-size: 12px; margin-top: 5px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }

        .progress-bar { display: flex; justify-content: center; gap: 8px; margin-bottom: 25px; }
        .dot { width: 35px; height: 4px; background: var(--border); border-radius: 10px; transition: 0.4s; }
        .dot.active { background: var(--primary); }

        .form-step { display: none; }
        .form-step.active { display: block; animation: fadeIn 0.4s ease; }

        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }

        .field-group { margin-bottom: 15px; }
        .label { font-size: 10px; font-weight: 800; color: var(--text-gray); text-transform: uppercase; margin-bottom: 6px; display: block; letter-spacing: 1px; }
        
        .input-style { 
            width: 100%; padding: 12px 16px; background: #fff; 
            border: 1px solid var(--border); border-radius: 12px; color: var(--text-main); 
            outline: none; transition: 0.2s; font-size: 13px; font-weight: 500;
        }
        .input-style:focus { border-color: var(--primary); background: #fff; }

        .batch-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        
        .btn-group { display: flex; gap: 10px; margin-top: 25px; }
        .btn { flex: 1; padding: 14px; border-radius: 12px; font-weight: 700; border: none; cursor: pointer; transition: 0.2s; text-transform: uppercase; font-size: 12px; letter-spacing: 0.5px; }
        
        .btn-next { background: var(--primary); color: #fff; }
        .btn-next:hover { background: #ef4444; }
        
        .btn-prev { background: #f3f4f6; color: var(--text-gray); }
        .btn-prev:hover { background: #e5e7eb; }

        .img-preview-box { 
            width: 70px; height: 70px; border-radius: 20px; 
            border: 2px dashed var(--border); margin: 0 auto 15px; 
            display: flex; align-items: center; justify-content: center; 
            overflow: hidden; cursor: pointer; 
        }
        .img-preview-box img { width: 100%; height: 100%; object-fit: cover; }
        
        select.input-style { appearance: none; cursor: pointer; }
    </style>
</head>
<body>

    <div class="wizard-card">
        <div class="step-header">
            <h1>Alumni<span>X</span></h1>
            <p id="step-title">Personal Info</p>
        </div>

        <div class="progress-bar">
            <div class="dot active"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>

        <form id="regForm" method="POST" enctype="multipart/form-data">
            <div class="form-step active">
                <div class="field-group">
                    <label class="label">Full Name</label>
                    <input type="text" name="full_name" class="input-style" placeholder="Rahul Singh" required>
                </div>
                <div class="field-group">
                    <label class="label">College ID</label>
                    <input type="text" name="student_id" class="input-style" placeholder="ST-2024-XXX" required>
                </div>
                <div class="field-group">
                    <label class="label">Gender</label>
                    <select name="gender" class="input-style" required>
                        <option value="" disabled selected>Select</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-next" onclick="nextStep(1)">Next Step</button>
                </div>
            </div>

            <div class="form-step">
                <div class="field-group">
                    <label class="label">Batch Period</label>
                    <div class="batch-grid">
                        <input type="number" name="batch_start" class="input-style" placeholder="2020" required>
                        <input type="number" name="batch_end" class="input-style" placeholder="2024" required>
                    </div>
                </div>
                <div class="field-group">
                    <label class="label">Graduation Year</label>
                    <select name="grad_year" class="input-style" required>
                        <option value="" disabled selected>Year</option>
                        <?php for ($y = 2028; $y >= 2010; $y--) echo "<option value='$y'>$y</option>"; ?>
                    </select>
                </div>
                <div class="field-group">
                    <label class="label">Current Org</label>
                    <input type="text" name="company" class="input-style" placeholder="Google / Student">
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-prev" onclick="prevStep(0)">Back</button>
                    <button type="button" class="btn btn-next" onclick="nextStep(2)">Next</button>
                </div>
            </div>

            <div class="form-step">
                <div class="img-preview-box" id="preview" onclick="document.getElementById('fileInput').click();">
                    <i class="fas fa-camera" style="color: #999; font-size: 16px;"></i>
                </div>
                <input type="file" id="fileInput" name="profile_img" style="display:none" accept="image/*" onchange="previewImage(this)">
                
                <div class="field-group">
                    <label class="label">Email</label>
                    <input type="email" name="email" class="input-style" placeholder="rahul@example.com" required>
                </div>
                <div class="field-group">
                    <label class="label">Password</label>
                    <input type="password" name="password" class="input-style" placeholder="••••••••" required>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-prev" onclick="prevStep(1)">Back</button>
                    <button type="submit" name="register" class="btn btn-next">Join Now</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const steps = document.querySelectorAll(".form-step");
        const dots = document.querySelectorAll(".dot");
        const titles = ["Personal Info", "Academic Details", "Secure Account"];

        function nextStep(idx) {
            const inputs = steps[idx-1].querySelectorAll("input[required], select[required]");
            let valid = true;
            inputs.forEach(i => {
                if(!i.value) { i.style.borderColor = "var(--primary)"; valid = false; }
                else { i.style.borderColor = "var(--border)"; }
            });

            if(valid) {
                steps[idx-1].classList.remove("active");
                steps[idx].classList.add("active");
                dots[idx].classList.add("active");
                document.getElementById("step-title").innerText = titles[idx];
            }
        }

        function prevStep(idx) {
            steps[idx+1].classList.remove("active");
            steps[idx].classList.add("active");
            dots[idx+1].classList.remove("active");
            document.getElementById("step-title").innerText = titles[idx];
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = e => document.getElementById('preview').innerHTML = `<img src="${e.target.result}">`;
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>

    <?php if ($reg_success): ?>
        <script>
            Swal.fire({ title: 'Success!', text: 'Pending Approval.', icon: 'success', confirmButtonColor: '#ff4d4d' }).then(() => window.location.href = 'login.php');
        </script>
    <?php endif; ?>

    <?php if ($error_msg): ?>
        <script>
            Swal.fire({ title: 'Oops!', text: '<?= $error_msg ?>', icon: 'error', confirmButtonColor: '#ff4d4d' });
        </script>
    <?php endif; ?>
</body>
</html>