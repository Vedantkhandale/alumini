<?php
// Error reporting aur strict alerts active rakhein taaki koi query crash chhup na sake
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include("includes/db.php");
require_once __DIR__ . "/includes/account_mail.php";

// Backend API & Registration Logic
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "validate_and_register") {
    
    ob_start(); // Buffer start taaki koi accidental warning output break na kare
    header('Content-Type: application/json');
    
    try {
        // Data Sanitization
        $full_name = mysqli_real_escape_string($conn, htmlspecialchars(trim($_POST["full_name"])));
        $year = mysqli_real_escape_string($conn, htmlspecialchars(trim($_POST["year"])));
        $gender = mysqli_real_escape_string($conn, $_POST["gender"]);
        $batch_start = intval($_POST["batch_start"]);
        $batch_end = intval($_POST["batch_end"]);
        $grad_year = intval($_POST["grad_year"]);
        $company = mysqli_real_escape_string($conn, htmlspecialchars(trim($_POST["company"])));
        $email = mysqli_real_escape_string($conn, trim(strtolower($_POST["email"])));
        $passwordRaw = trim((string) ($_POST["password"] ?? ""));
        $confirmPasswordRaw = trim((string) ($_POST["confirm_pass"] ?? ""));
        
        // Image Handling Logic
        $profile_img_name = "default.png"; 
        if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['profile_img']['tmp_name'];
            $fileName = $_FILES['profile_img']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $profile_img_name = time() . '_' . md5($fileName) . '.' . $fileExtension;
            $uploadFileDir = './uploads/';
            
            if(!is_dir($uploadFileDir)){
                mkdir($uploadFileDir, 0755, true);
            }
            move_uploaded_file($fileTmpPath, $uploadFileDir . $profile_img_name);
        }

        // 1. Basic Format Validations
        if (strlen($full_name) < 3) {
            throw new Exception("Full name must be at least 3 characters long.");
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("The provided email address format is invalid.");
        }

        if (strlen($passwordRaw) < 6) {
            throw new Exception("Password must be at least 6 characters long.");
        }
        if ($passwordRaw !== $confirmPasswordRaw) {
            throw new Exception("Passwords do not match.");
        }

        // 2. Database Duplicate Check mapped to 'alumni_users' (ID check removed)
        $checkStmt = $conn->prepare("SELECT id FROM alumni_users WHERE email = ? LIMIT 1");
        if (!$checkStmt) {
            throw new Exception("Unable to validate email uniqueness right now. Please try again.");
        }
        $checkStmt->bind_param("s", $email);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        if ($checkResult && $checkResult->num_rows > 0) {
            $checkStmt->close();
            throw new Exception("This Email address is already registered.");
        }
        $checkStmt->close();

        // 3. Live API Check via Abstract API (With Dynamic Built-in Fallback)
        $api_key = "38e4450699d38f381bbecb4553803ae9";
        $ch = curl_init("https://emailvalidation.abstractapi.com/v1/?api_key=$api_key&email=" . urlencode($email));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 4); 
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); 
        $api_response = curl_exec($ch);
        $curl_error = curl_error($ch);
        curl_close($ch);

        $validation_passed = true;
        $error_message = "";

        if ($api_response !== false && empty($curl_error)) {
            $data = json_decode($api_response, true);
            if ($data && isset($data['deliverability'])) {
                if ($data['deliverability'] === "UNDELIVERABLE") {
                    $validation_passed = false;
                    $error_message = "This email address does not exist. Please enter a valid live email.";
                } elseif (isset($data['is_disposable_email']['value']) && $data['is_disposable_email']['value'] === true) {
                    $validation_passed = false;
                    $error_message = "Temporary or disposable email addresses are not allowed.";
                } elseif (isset($data['quality_score']) && floatval($data['quality_score']) < 0.2) {
                    $validation_passed = false;
                    $error_message = "This email carries a high risk score. Please use an authentic email address.";
                }
            }
        } else {
            $domain = substr(strrchr($email, "@"), 1);
            if (!checkdnsrr($domain, "MX")) {
                $validation_passed = false;
                $error_message = "The email domain seems invalid or has no mail server attached.";
            }
        }

        if (!$validation_passed) {
            throw new Exception($error_message);
        }

        // 4. Secure Database Insertion into 'alumni_users' (student_id changed to year)
        $yearColumn = null;
        $yearColumnResult = $conn->query("SHOW COLUMNS FROM alumni_users LIKE 'year'");
        if ($yearColumnResult instanceof mysqli_result && $yearColumnResult->num_rows > 0) {
            $yearColumn = "year";
        }
        if ($yearColumnResult instanceof mysqli_result) {
            $yearColumnResult->free();
        }
        if ($yearColumn === null) {
            $studentIdColumnResult = $conn->query("SHOW COLUMNS FROM alumni_users LIKE 'student_id'");
            if ($studentIdColumnResult instanceof mysqli_result && $studentIdColumnResult->num_rows > 0) {
                $yearColumn = "student_id";
            }
            if ($studentIdColumnResult instanceof mysqli_result) {
                $studentIdColumnResult->free();
            }
        }
        if ($yearColumn === null) {
            throw new Exception("Registration table is missing both `year` and `student_id` columns.");
        }

        $passwordHash = password_hash($passwordRaw, PASSWORD_DEFAULT);
        if ($passwordHash === false) {
            throw new Exception("Unable to secure your password. Please try again.");
        }

        $insertStmt = $conn->prepare(
            "INSERT INTO alumni_users (
                full_name,
                {$yearColumn},
                gender,
                batch_start,
                batch_end,
                grad_year,
                company,
                email,
                profile_img,
                status,
                password
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?)"
        );

        if (!$insertStmt) {
            throw new Exception("Unable to prepare registration query. Please try again.");
        }

        $insertStmt->bind_param(
            "sssiiissss",
            $full_name,
            $year,
            $gender,
            $batch_start,
            $batch_end,
            $grad_year,
            $company,
            $email,
            $profile_img_name,
            $passwordHash
        );

        if ($insertStmt->execute()) {
            $insertStmt->close();
            // Send registration confirmation email
            alumnixSendRegistrationConfirmation($full_name, $email);
            
            ob_end_clean();
            echo json_encode(["status" => "success", "email" => $email]);
            exit;
        } else {
            $dbError = $insertStmt->error ?: $conn->error;
            $insertStmt->close();
            throw new Exception("Database Connection Error: " . $dbError);
        }

    } catch (Exception $e) {
        ob_end_clean();
        echo json_encode(["status" => "error", "message" => $e->getMessage()]);
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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            /* Matched with your previous form's light theme */
            --primary: #e11d48;          
            --primary-hover: #be123c;    
            --error-color: #f43f5e;      
            --success-color: #10b981;    
            --bg: #f8fafc;               
            --card-bg: #ffffff;          
            --text-main: #0f172a;        
            --text-gray: #64748b;        
            --border: #e2e8f0;           
            --input-bg: #fbfcfd;         
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
            max-width: 440px;
            padding: 40px 32px;
            border-radius: 30px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.08);
            position: relative;
        }

        .step-header { text-align: center; margin-bottom: 28px; }
        .step-header h1 { font-size: 28px; font-weight: 800; letter-spacing: -1px; color: var(--text-main); }
        .step-header span { color: var(--primary); }
        #step-title {
            color: var(--text-gray);
            font-size: 11px;
            margin-top: 8px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }

        .progress-bar { display: flex; justify-content: center; gap: 8px; margin-bottom: 30px; }
        .dot { width: 40px; height: 5px; background: var(--border); border-radius: 10px; transition: 0.4s; }
        .dot.active { background: var(--primary); box-shadow: 0 0 10px rgba(225, 29, 72, 0.2); }

        .form-step { display: none; }
        .form-step.active { display: block; animation: slideUp 0.4s cubic-bezier(0.16, 1, 0.3, 1); }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .field-group { margin-bottom: 20px; position: relative; }
        .label {
            font-size: 11px;
            font-weight: 800;
            color: var(--text-gray);
            text-transform: uppercase;
            margin-bottom: 8px;
            display: block;
            letter-spacing: 1px;
        }

        .input-style {
            width: 100%;
            padding: 14px 16px;
            background: var(--input-bg);
            border: 1.5px solid var(--border);
            border-radius: 12px;
            color: var(--text-main);
            outline: none;
            transition: all 0.3s ease;
            font-size: 14px;
            font-weight: 500;
        }

        .input-style:focus { border-color: var(--primary); background: #fff; box-shadow: 0 0 0 4px rgba(225, 29, 72, 0.05); }
        .input-style.input-err { border-color: var(--error-color) !important; background-color: rgba(244, 63, 94, 0.05); }
        .input-style.input-success { border-color: var(--success-color) !important; background-color: rgba(16, 185, 129, 0.05); }
        
        .error-msg-text { color: var(--error-color); font-size: 11px; font-weight: 600; margin-top: 6px; display: none;}

        .batch-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .btn-group { display: flex; gap: 12px; margin-top: 30px; }
        
        .btn {
            flex: 1;
            padding: 16px;
            border-radius: 12px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
        }

        .btn-next { background: var(--text-main); color: #fff; }
        .btn-next:hover { background: var(--primary); transform: translateY(-2px); box-shadow: 0 10px 20px rgba(225, 29, 72, 0.2); }
        
        .btn-prev { background: #f1f5f9; color: var(--text-gray); }
        .btn-prev:hover { background: #e2e8f0; color: var(--text-main); }

        .img-preview-box {
            width: 84px;
            height: 84px;
            border-radius: 50%;
            border: 2px dashed #cbd5e1;
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            cursor: pointer;
            background: var(--input-bg);
            transition: 0.2s;
        }
        .img-preview-box:hover { border-color: var(--primary); background: #fff; }
        .img-preview-box img { width: 100%; height: 100%; object-fit: cover; }

        select.input-style { appearance: none; cursor: pointer; }

        .credential-note {
            padding: 14px 16px;
            border: 1px solid rgba(225, 29, 72, 0.2);
            border-radius: 12px;
            background: rgba(225, 29, 72, 0.03);
        }
        .credential-note p { color: var(--text-gray); font-size: 12px; line-height: 1.6; }
        
        .loading-btn {
            background: #e2e8f0 !important;
            color: #94a3b8 !important;
            cursor: not-allowed !important;
            transform: none !important;
            box-shadow: none !important;
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
                   <input type="text" name="full_name" class="input-style" autocomplete="off" placeholder="John Doe" required>
                </div>
                <div class="field-group">
                    <label class="label">Year</label>
                    <input type="text" name="year" class="input-style" placeholder="e.g. Final Year / 4th Year" autocomplete="off" required>
                </div>
                <div class="field-group">
                    <label class="label">Gender</label>
                    <select name="gender" class="input-style" required>
                        <option value="" disabled selected>Select Gender</option>
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
                        <option value="" disabled selected>Select Year</option>
                        <?php for ($year_val = 2028; $year_val >= 2010; $year_val--): ?>
                            <option value="<?php echo $year_val; ?>"><?php echo $year_val; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="field-group">
                    <label class="label">Current Org</label>
                    <input type="text" name="company" class="input-style" placeholder="Google / Student" autocomplete="off">
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-prev" onclick="prevStep(0)">Back</button>
                    <button type="button" class="btn btn-next" onclick="nextStep(2)">Next Step</button>
                </div>
            </div>

            <div class="form-step">
                <div class="img-preview-box" id="preview" onclick="document.getElementById('fileInput').click();">
                    <i class="fas fa-camera" style="color: var(--primary); font-size: 20px;"></i>
                </div>
                <input type="file" id="fileInput" name="profile_img" style="display:none" accept="image/*" onchange="previewImage(this)">

                <div class="field-group">
                    <label class="label">Email Address</label>
                    <input type="email" id="userEmail" name="email" class="input-style" placeholder="name@example.com" autocomplete="off" required>
                    <div class="error-msg-text" id="emailErrorHint">Please enter a valid email format!</div>
                </div>

                <div class="field-group">
                    <label class="label">Password</label>
                    <input type="password" name="password" class="input-style" placeholder="Create a password" autocomplete="new-password" required>
                </div>
                <div class="field-group">
                    <label class="label">Confirm Password</label>
                    <input type="password" name="confirm_pass" class="input-style" placeholder="Confirm password" autocomplete="new-password" required>
                </div>

                <div class="field-group credential-note">
                    <p><i class="fas fa-info-circle" style="color: var(--primary); margin-right: 5px;"></i> Your email will be your login ID. After admin approval, a login link and the latest temporary password will be sent to this email.</p>
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
        const emailInput = document.getElementById("userEmail");
        const emailErrorHint = document.getElementById("emailErrorHint");
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        emailInput.addEventListener("input", validateEmailField);
        emailInput.addEventListener("blur", validateEmailField);

        function validateEmailField() {
            const emailVal = emailInput.value.trim();
            if (emailVal === "") {
                emailInput.classList.remove("input-err", "input-success");
                emailErrorHint.style.display = "none";
                return false;
            }
            if (!emailRegex.test(emailVal)) {
                emailInput.classList.add("input-err");
                emailInput.classList.remove("input-success");
                emailErrorHint.style.display = "block";
                return false;
            } else {
                emailInput.classList.remove("input-err");
                emailInput.classList.add("input-success");
                emailErrorHint.style.display = "none";
                return true;
            }
        }

        function nextStep(idx) {
            const inputs = steps[idx - 1].querySelectorAll("input[required], select[required]");
            let valid = true;

            inputs.forEach((input) => {
                if (!input.value.trim()) {
                    input.classList.add("input-err");
                    valid = false;
                } else {
                    input.classList.remove("input-err");
                }
            });

            if (valid) {
                steps[idx - 1].classList.remove("active");
                steps[idx].classList.add("active");
                dots[idx].classList.add("active");
                document.getElementById("step-title").innerText = titles[idx];
            } else {
                Swal.fire({
                    title: 'Form Incomplete',
                    text: 'Please fill in all required fields.',
                    icon: 'warning',
                    confirmButtonColor: '#e11d48'
                });
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

        document.getElementById("regForm").addEventListener("submit", function(e) {
            e.preventDefault();
            
            if(!validateEmailField()) {
                Swal.fire({
                    title: 'Invalid Email!',
                    text: 'Please input a valid email format to proceed.',
                    icon: 'error',
                    confirmButtonColor: '#e11d48'
                });
                return false;
            }

            const btn = document.getElementById("finalSubmitBtn");
            btn.classList.add("loading-btn");
            btn.innerText = "Processing Profile...";
            btn.disabled = true;

            fetch("", {
                method: "POST",
                body: new FormData(this)
            })
            .then(response => {
                if (!response.ok) throw new Error('Network failure');
                return response.json();
            })
            .then(data => {
                if(data.status === "success") {
                    Swal.fire({
                        title: 'Successfully Submitted!',
                        html: 'Profile saved for verification.<br>Updates will be sent to: <strong>' + data.email + '</strong>.',
                        icon: 'success',
                        confirmButtonColor: '#e11d48'
                    }).then(() => { window.location.reload(); });
                } else {
                    btn.classList.remove("loading-btn");
                    btn.innerText = "Join Now";
                    btn.disabled = false;
                    Swal.fire({
                        title: 'Submission Failed',
                        text: data.message,
                        icon: 'error',
                        confirmButtonColor: '#e11d48'
                    });
                }
            })
            .catch(error => {
                btn.classList.remove("loading-btn");
                btn.innerText = "Join Now";
                btn.disabled = false;
                Swal.fire({
                    title: 'System Error',
                    text: 'Unable to process registration. Try again.',
                    icon: 'error',
                    confirmButtonColor: '#e11d48'
                });
            });
        });
    </script>
</body>
</html>
