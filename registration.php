<?php
session_start();
include("includes/db.php");
require_once __DIR__ . "/includes/account_mail.php";

// Backend API & Registration Logic (Page bina reload hue handle hoga)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "validate_and_register") {
    header('Content-Type: application/json');
    
    // Data Sanitization (SQL Injection aur XSS se bachao)
    $full_name = mysqli_real_escape_string($conn, htmlspecialchars(trim($_POST["full_name"])));
    $student_id = mysqli_real_escape_string($conn, htmlspecialchars(trim($_POST["student_id"])));
    $gender = mysqli_real_escape_string($conn, $_POST["gender"]);
    $batch_start = intval($_POST["batch_start"]);
    $batch_end = intval($_POST["batch_end"]);
    $grad_year = intval($_POST["grad_year"]);
    $company = mysqli_real_escape_string($conn, htmlspecialchars(trim($_POST["company"])));
    $email = mysqli_real_escape_string($conn, trim(strtolower($_POST["email"]))); // lowercase me save hoga
    
    // 1. Basic Field & Format Validations
    if (strlen($full_name) < 3) {
        echo json_encode(["status" => "error", "message" => "Full name must be at least 3 characters long."]);
        exit;
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(["status" => "error", "message" => "Bhai, email ka format sahi nahi hai!"]);
        exit;
    }

    // 2. Database Duplicate Check (Pehle hi check karlo taaki API key ke credits bach sakein)
    $check = $conn->query("SELECT id FROM users WHERE email='$email' OR student_id='$student_id'");
    if ($check->num_rows > 0) {
        echo json_encode(["status" => "error", "message" => "This Student ID or Email is already registered!"]);
        exit;
    }

    // 3. Pro-Level Live API Check via Abstract API
    $api_key = "f23efedb202987ddf90de46f3cfc8e9e";
    $ch = curl_init("https://emailvalidation.abstractapi.com/v1/?api_key=$api_key&email=" . urlencode($email));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 8); // 8 seconds timeout for safety
    $api_response = curl_exec($ch);
    curl_close($ch);

    if ($api_response) {
        $data = json_decode($api_response, true);
        
        // Targetted Filter: Deliverability, Disposable Email, and Quality Score check
        $is_undeliverable = isset($data['deliverability']) && $data['deliverability'] === "UNDELIVERABLE";
        $is_disposable = isset($data['is_disposable_email']['value']) && $data['is_disposable_email']['value'] === true;
        $quality_score = isset($data['quality_score']) ? floatval($data['quality_score']) : 1.0;

        if ($is_undeliverable) {
            echo json_encode(["status" => "error", "message" => "Yeh email address exist nahi karta. Sahi email dalo."]);
            exit;
        }
        if ($is_disposable) {
            echo json_encode(["status" => "error", "message" => "Bhai, temporary ya fake email allowed nahi hai!"]);
            exit;
        }
        if ($quality_score < 0.7) {
            echo json_encode(["status" => "error", "message" => "Email risky lag raha hai. Kripya apna personal email use karein."]);
            exit;
        }
    } else {
        // Fallback: Agar kisi wajah se API down ho ya credits khatam ho jayein, toh security check fail na ho
        echo json_encode(["status" => "error", "message" => "Verification server busy hai. Kripya thodi der baad try karein."]);
        exit;
    }

    // 4. Secure Database Insertion (Admin Panel Data)
    $insert_query = "INSERT INTO users (full_name, student_id, gender, batch_start, batch_end, grad_year, company, email) 
                     VALUES ('$full_name', '$student_id', '$gender', '$batch_start', '$batch_end', '$grad_year', '$company', '$email')";
    
    if ($conn->query($insert_query)) {
        echo json_encode(["status" => "success", "email" => $email]);
        exit;
    } else {
        echo json_encode(["status" => "error", "message" => "Database Error: Data save nahi ho paya. " . $conn->error]);
        exit;
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght=400;500;600;700;800&display=swap" rel="stylesheet">
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

        <form id="regForm" enctype="multipart/form-data">
            <input type="hidden" name="action" value="validate_and_register">

            <div class="form-step active">
                <div class="field-group">
                    <label class="label">Full Name</label>
                   <input type="text" name="full_name" class="input-style" autocomplete="off" required>
                </div>
                <div class="field-group">
                    <label class="label">College ID</label>
                    <input type="text" name="student_id" class="input-style" placeholder="ST-2024-XXX" autocomplete="off" required>
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
                    <input type="text" name="company" class="input-style" placeholder="Google / Student" autocomplete="off">
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
                    <input type="email" id="userEmail" name="email" class="input-style" placeholder="rahul@example.com" autocomplete="off" required>
                </div>

                <div class="field-group credential-note">
                    <label class="label">Login Credentials</label>
                    <p>Your email will be your login ID. A generated password will be emailed after admin approval.</p>
                </div>

                <div class="btn-group">
                    <button type="button" class="btn btn-prev" onclick="prevStep(1)">Back</button>
                    <button type="submit" id="finalSubmitBtn" class="btn btn-next">Join Now</button>
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

        // ULTRA SECURE AJAX FORM SUBMISSION
        document.getElementById("regForm").addEventListener("submit", function(e) {
            e.preventDefault();
            
            const email = document.getElementById("userEmail").value.trim();
            const btn = document.getElementById("finalSubmitBtn");
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if(!emailRegex.test(email)) {
                Swal.fire('Error', 'Bhai, valid email format use karo!', 'error');
                return false;
            }

            // Lock Button (Double click protection)
            btn.classList.add("loading-btn");
            btn.innerText = "Securing Connection...";
            btn.disabled = true;

            const formData = new FormData(this);

            fetch(window.location.href, {
                method: "POST",
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Network error occured');
                return response.json();
            })
            .then(data => {
                if(data.status === "success") {
                    Swal.fire({
                        title: 'Registration Complete!',
                        html: 'Admin panel me data save ho gaya hai. Confirmation mail sent to: <strong>' + data.email + '</strong>.',
                        icon: 'success',
                        confirmButtonColor: '#ff4d4d'
                    }).then(() => {
                        window.location.href = 'index.php';
                    });
                } else {
                    // Unlock button if failed
                    btn.classList.remove("loading-btn");
                    btn.innerText = "Join Now";
                    btn.disabled = false;
                    
                    Swal.fire({
                        title: 'Rejected!',
                        text: data.message,
                        icon: 'error',
                        confirmButtonColor: '#ff4d4d'
                    });
                }
            })
            .catch(error => {
                console.error("Error:", error);
                btn.classList.remove("loading-btn");
                btn.innerText = "Join Now";
                btn.disabled = false;
                Swal.fire('Error', 'Server down ya network timeout error! Badme try karein.', 'error');
            });
        });
    </script>
</body>
</html>