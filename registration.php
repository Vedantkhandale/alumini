<?php
session_start();
include("includes/db.php");

$reg_success = false;
$error_msg = "";

// Is block ko replace karlo apne existing PHP logic se
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {

    // Data Sanitization
    $full_name  = mysqli_real_escape_string($conn, $_POST['full_name']);
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $email      = mysqli_real_escape_string($conn, $_POST['email']);
    $gender     = mysqli_real_escape_string($conn, $_POST['gender']);
    
    // Yahan fix hai: Check if keys exist before using them
    $b_start    = isset($_POST['batch_start']) ? mysqli_real_escape_string($conn, $_POST['batch_start']) : '';
    $b_end      = isset($_POST['batch_end']) ? mysqli_real_escape_string($conn, $_POST['batch_end']) : '';
    $batch      = $b_start . " - " . $b_end;

    $grad_year  = mysqli_real_escape_string($conn, $_POST['grad_year']);
    $company    = mysqli_real_escape_string($conn, $_POST['company']);

    // Password Hashing
    $raw_pass    = $_POST['password'];
    $hashed_pass = password_hash($raw_pass, PASSWORD_DEFAULT);

    // ... baaki ka image upload logic same rahega
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
    <title>Elite Registration | AlumniX</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --primary: #ff3b3b; --bg: #050505; --glass: rgba(255, 255, 255, 0.03); --border: rgba(255, 255, 255, 0.08); }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: var(--bg); color: #fff; font-family: 'Plus Jakarta Sans', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 20px; overflow-x: hidden; }
        
        .wizard-card { background: var(--glass); backdrop-filter: blur(25px); width: 100%; max-width: 500px; padding: 40px; border-radius: 35px; border: 1px solid var(--border); box-shadow: 0 40px 100px rgba(0, 0, 0, 0.5); position: relative; }
        .step-header { text-align: center; margin-bottom: 30px; }
        .step-header h1 { font-size: 32px; font-weight: 800; letter-spacing: -1.5px; }
        .step-header span { color: var(--primary); }

        .progress-bar { display: flex; justify-content: center; gap: 12px; margin-bottom: 35px; }
        .dot { width: 45px; height: 6px; background: rgba(255, 255, 255, 0.1); border-radius: 10px; transition: 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        .dot.active { background: var(--primary); box-shadow: 0 0 20px var(--primary); }

        .form-step { display: none; opacity: 0; transform: translateY(10px); transition: 0.4s ease; }
        .form-step.active { display: block; opacity: 1; transform: translateY(0); }

        .field-group { margin-bottom: 22px; }
        .label { font-size: 11px; font-weight: 800; color: #777; text-transform: uppercase; margin-bottom: 10px; display: block; letter-spacing: 1.5px; }
        
        .input-style { 
            width: 100%; padding: 16px 20px; background: rgba(255, 255, 255, 0.03); 
            border: 1px solid var(--border); border-radius: 18px; color: #fff; 
            outline: none; transition: 0.3s; font-size: 14px;
        }
        .input-style:focus { border-color: var(--primary); background: rgba(255, 59, 59, 0.05); box-shadow: 0 0 20px rgba(255, 59, 59, 0.1); }

        /* Custom Dropdown Maza */
        select.input-style {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23ff3b3b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 20px center; background-size: 18px;
        }
        select option { background: #0a0a0a; color: #fff; }

        .batch-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .btn-group { display: flex; gap: 12px; margin-top: 35px; }
        .btn { flex: 1; padding: 18px; border-radius: 18px; font-weight: 800; border: none; cursor: pointer; transition: 0.3s; text-transform: uppercase; font-size: 13px; letter-spacing: 1px; }
        .btn-next { background: var(--primary); color: #fff; box-shadow: 0 10px 25px rgba(255, 59, 59, 0.3); }
        .btn-prev { background: rgba(255, 255, 255, 0.05); color: #ccc; }
        .btn:hover { transform: translateY(-3px); filter: brightness(1.2); }

        .img-preview-box { width: 90px; height: 90px; border-radius: 28px; border: 2px dashed var(--border); margin: 0 auto 20px; display: flex; align-items: center; justify-content: center; overflow: hidden; transition: 0.3s; cursor: pointer; }
        .img-preview-box:hover { border-color: var(--primary); }
        .img-preview-box img { width: 100%; height: 100%; object-fit: cover; }
    </style>
</head>
<body>

    <div class="wizard-card">
        <div class="step-header">
            <h1>Alumni<span>X</span></h1>
            <p id="step-title" style="color: #666; font-size: 13px; margin-top: 8px; font-weight: 600;">Personal Information</p>
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
                        <option value="" disabled selected>Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-next" onclick="nextStep(1)">Continue</button>
                </div>
            </div>

            <div class="form-step">
                <div class="field-group">
                    <label class="label">Batch Duration</label>
                    <div class="batch-grid">
                        <input type="number" name="batch_start" class="input-style" placeholder="Start (2020)" required>
                        <input type="number" name="batch_end" class="input-style" placeholder="End (2024)" required>
                    </div>
                </div>
                <div class="field-group">
                    <label class="label">Graduation Year</label>
                    <select name="grad_year" class="input-style" required>
                        <option value="" disabled selected>Select Year</option>
                        <?php for ($y = 2028; $y >= 2000; $y--) echo "<option value='$y'>Class of $y</option>"; ?>
                    </select>
                </div>
                <div class="field-group">
                    <label class="label">Current Organization</label>
                    <input type="text" name="company" class="input-style" placeholder="Google / Freelancing">
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-prev" onclick="prevStep(0)">Back</button>
                    <button type="button" class="btn btn-next" onclick="nextStep(2)">Almost Done</button>
                </div>
            </div>

            <div class="form-step">
                <div class="img-preview-box" id="preview" onclick="document.getElementById('fileInput').click();">
                    <i class="fas fa-camera" style="color: #444; font-size: 20px;"></i>
                </div>
                <input type="file" id="fileInput" name="profile_img" style="display:none" accept="image/*" onchange="previewImage(this)">
                
                <div class="field-group">
                    <label class="label">Email Address</label>
                    <input type="email" name="email" class="input-style" placeholder="rahul@example.com" required>
                </div>
                <div class="field-group" style="position:relative;">
                    <label class="label">Security Password</label>
                    <input type="password" id="pass" name="password" class="input-style" placeholder="••••••••" required>
                    <i class="fas fa-eye" style="position:absolute; right:20px; top:42px; cursor:pointer; color:#555" onclick="togglePass()"></i>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-prev" onclick="prevStep(1)">Back</button>
                    <button type="submit" name="register" class="btn btn-next">Finalize & Join</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const steps = document.querySelectorAll(".form-step");
        const dots = document.querySelectorAll(".dot");
        const titles = ["Personal Information", "Academic & Career", "Account Security"];

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

        function togglePass() {
            const p = document.getElementById("pass");
            p.type = p.type === "password" ? "text" : "password";
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
            Swal.fire({ title: 'Application Sent!', text: 'Your registration is pending approval.', icon: 'success', background: '#111', color: '#fff', confirmButtonColor: '#ff3b3b' }).then(() => window.location.href = 'login.php');
        </script>
    <?php endif; ?>

    <?php if ($error_msg): ?>
        <script>
            Swal.fire({ title: 'Error!', text: '<?= $error_msg ?>', icon: 'error', background: '#111', color: '#fff' });
        </script>
    <?php endif; ?>
</body>
</html>