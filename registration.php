<?php
session_start();
include("includes/db.php");
require_once __DIR__ . "/includes/account_mail.php";

$reg_success = false;
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["register"])) {
    $full_name = mysqli_real_escape_string($conn, $_POST["full_name"]);
    $student_id = mysqli_real_escape_string($conn, $_POST["student_id"]);
    $email = mysqli_real_escape_string($conn, trim($_POST["email"]));
    $gender = mysqli_real_escape_string($conn, $_POST["gender"]);

    $batch_start = isset($_POST["batch_start"]) ? mysqli_real_escape_string($conn, $_POST["batch_start"]) : "";
    $batch_end = isset($_POST["batch_end"]) ? mysqli_real_escape_string($conn, $_POST["batch_end"]) : "";
    $batch = trim($batch_start . " - " . $batch_end, " -");

    $grad_year = mysqli_real_escape_string($conn, $_POST["grad_year"]);
    $company = mysqli_real_escape_string($conn, $_POST["company"]);

    // --- STEP 1: LIVE EMAIL VALIDATION VIA API ---
    $api_key = "YOUR_ABSTRACT_API_KEY_HERE"; // <-- Apni actual API key yahan dalo agar live check chahiye
    $is_valid_email = false;

    // Agar key lagayi hai toh hi API chalega, nahi toh direct standard check par jayega
    if (!empty($api_key) && $api_key !== "YOUR_ABSTRACT_API_KEY_HERE") {
        $api_url = "https://emailvalidation.abstractapi.com/v1/?api_key=" . $api_key . "&email=" . urlencode($email);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        curl_close($ch);

        if ($response) {
            $data = json_decode($response, true);
            if (
                isset($data['is_valid_format']['value']) && $data['is_valid_format']['value'] === true &&
                isset($data['deliverability']) && $data['deliverability'] !== "UNDELIVERABLE" &&
                isset($data['is_disposable_email']['value']) && $data['is_disposable_email']['value'] === false
            ) {
                $is_valid_email = true;
            } else {
                $error_msg = "Ye email real nahi hai, dead hai ya temporary email hai!";
            }
        }
    }

    // Fallback: Agar API key nahi hai ya API down ho gayi toh normal format validation backup dega
    if (!$is_valid_email && empty($error_msg)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $is_valid_email = true;
        } else {
            $error_msg = "Invalid email format! Please enter a valid one.";
        }
    }

    if ($is_valid_email) {
        // --- STEP 2: DUPLICATE CHECK ---
        $check = $conn->query("SELECT id FROM users WHERE email='$email' OR student_id='$student_id'");
        if ($check && $check->num_rows > 0) {
            $error_msg = "Student ID or Email already exists in our system.";
        } else {
            
            // --- STEP 3: SECURE IMAGE UPLOAD ---
            $img_name = "default.png";
            if (isset($_FILES["profile_img"]) && (int) $_FILES["profile_img"]["error"] === 0) {
                $target_dir = "uploads/profiles/";
                if (!is_dir($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }

                $extension = pathinfo($_FILES["profile_img"]["name"], PATHINFO_EXTENSION);
                $allowed_exts = ['jpg', 'jpeg', 'png', 'webp'];
                
                if (in_array(strtolower($extension), $allowed_exts)) {
                    $img_name = "IMG_" . time() . "_" . rand(1000, 9999) . "." . strtolower($extension);
                    move_uploaded_file($_FILES["profile_img"]["tmp_name"], $target_dir . $img_name);
                }
            }

            // --- STEP 4: DB INSERT ---
            $sql = "INSERT INTO users (full_name, student_id, email, password, gender, batch, graduation_year, company, image, status, role)
                    VALUES ('$full_name', '$student_id', '$email', '', '$gender', '$batch', '$grad_year', '$company', '$img_name', 'pending', 'alumni')";

            if ($conn->query($sql)) {
                $reg_success = true;
                // Kuch setups me mail function slow hota h, isliye alerts pehle load ho sakte hain
                @alumnixSendPendingApprovalEmail($full_name, $email);
            } else {
                $error_msg = "Database Error: " . $conn->error;
            }
        }
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
            --primary: #ff4d4d;
            --bg: #f8f8f8;
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
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .wizard-card {
            background: var(--card-bg);
            width: 100%;
            max-width: 430px;
            padding: 32px;
            border-radius: 24px;
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            position: relative;
        }

        .step-header { text-align: center; margin-bottom: 20px; }
        .step-header h1 { font-size: 24px; font-weight: 800; letter-spacing: -1px; }
        .step-header span { color: var(--primary); }
        #step-title {
            color: var(--text-gray);
            font-size: 12px;
            margin-top: 5px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .progress-bar { display: flex; justify-content: center; gap: 8px; margin-bottom: 25px; }
        .dot { width: 35px; height: 4px; background: var(--border); border-radius: 10px; transition: 0.4s; }
        .dot.active { background: var(--primary); }

        .form-step { display: none; }
        .form-step.active { display: block; animation: fadeIn 0.4s ease; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(5px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .field-group { margin-bottom: 15px; }
        .label {
            font-size: 10px;
            font-weight: 800;
            color: var(--text-gray);
            text-transform: uppercase;
            margin-bottom: 6px;
            display: block;
            letter-spacing: 1px;
        }

        .input-style {
            width: 100%;
            padding: 12px 16px;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text-main);
            outline: none;
            transition: 0.2s;
            font-size: 13px;
            font-weight: 500;
        }

        .input-style:focus { border-color: var(--primary); background: #fff; }
        .batch-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .btn-group { display: flex; gap: 10px; margin-top: 25px; }
        
        .btn {
            flex: 1;
            padding: 14px;
            border-radius: 12px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: 0.2s;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .btn-next { background: var(--primary); color: #fff; }
        .btn-next:hover { background: #ef4444; }
        .btn-prev { background: #f3f4f6; color: var(--text-gray); }
        .btn-prev:hover { background: #e5e7eb; }

        .img-preview-box {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            border: 2px dashed var(--border);
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            cursor: pointer;
        }

        .img-preview-box img { width: 100%; height: 100%; object-fit: cover; }
        select.input-style { appearance: none; cursor: pointer; }

        .credential-note {
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: #fff7f7;
        }

        .credential-note p {
            color: var(--text-gray);
            font-size: 12px;
            line-height: 1.7;
        }
        
        .loading-btn {
            background: #ccc !important;
            color: #666 !important;
            cursor: not-allowed !important;
        }
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

        <form id="regForm" method="POST" enctype="multipart/form-data" onsubmit="return handleFormSubmit();">
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
                        <?php for ($year = 2028; $year >= 2010; $year--): ?>
                            <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                        <?php endfor; ?>
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
                    <input type="email" id="userEmail" name="email" class="input-style" placeholder="rahul@example.com" required>
                </div>

                <div class="field-group credential-note">
                    <label class="label">Login Credentials</label>
                    <p>Your email will be your login ID. A generated password will be emailed after admin approval.</p>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-prev" onclick="prevStep(1)">Back</button>
                    <button type="submit" id="finalSubmitBtn" name="register" class="btn btn-next">Join Now</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const steps = document.querySelectorAll(".form-step");
        const dots = document.querySelectorAll(".dot");
        const titles = ["Personal Info", "Academic Details", "Contact Details"];

        function nextStep(idx) {
            const inputs = steps[idx - 1].querySelectorAll("input[required], select[required]");
            let valid = true;

            inputs.forEach((input) => {
                if (!input.value.trim()) {
                    input.style.borderColor = "var(--primary)";
                    valid = false;
                } else {
                    input.style.borderColor = "var(--border)";
                }
            });

            if (valid) {
                steps[idx - 1].classList.remove("active");
                steps[idx].classList.add("active");
                dots[idx].classList.add("active");
                document.getElementById("step-title").innerText = titles[idx];
            }
        }

        function prevStep(idx) {
            steps[idx + 1].classList.remove("active");
            steps[idx].classList.add("active");
            dots[idx + 1].classList.remove("active");
            document.getElementById("step-title").innerText = titles[idx];
        }

        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (event) => {
                    document.getElementById("preview").innerHTML = `<img src="${event.target.result}" alt="Preview">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function handleFormSubmit() {
            const email = document.getElementById("userEmail").value;
            const btn = document.getElementById("finalSubmitBtn");
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if(!emailRegex.test(email)) {
                Swal.fire('Error', 'Sahi email address format dalo!', 'error');
                return false;
            }

            btn.classList.add("loading-btn");
            btn.innerText = "Processing...";
            return true;
        }
    </script>

    <?php if ($reg_success): ?>
        <script>
            Swal.fire({
                title: 'Registration Received!',
                html: 'Thanks — we sent a confirmation to <strong><?= htmlspecialchars($email ?? "", ENT_QUOTES) ?></strong>.',
                icon: 'success',
                confirmButtonColor: '#ff4d4d'
            }).then(() => {
                window.location.href = 'index.php';
            });
        </script>
    <?php endif; ?>

    <?php if (!empty($error_msg)): ?>
        <script>
            Swal.fire({
                title: 'Oops!',
                text: '<?= htmlspecialchars($error_msg, ENT_QUOTES) ?>',
                icon: 'error',
                confirmButtonColor: '#ff4d4d'
            });
            // Button loading state ko reverse karne k liye
            document.getElementById("finalSubmitBtn").classList.remove("loading-btn");
            document.getElementById("finalSubmitBtn").innerText = "Join Now";
        </script>
    <?php endif; ?>
</body>
</html>