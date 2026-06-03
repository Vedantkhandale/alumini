<?php

// 🚀 PHPMailer ki dependency load karo
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ✅ Aapke folder structure ke hisab se exact sahi manual path (No Autoload Needed)
require_once __DIR__ . '/../vendor/PHPMailer/PHPMailer/src/Exception.php';
require_once __DIR__ . '/../vendor/PHPMailer/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../vendor/PHPMailer/PHPMailer/src/SMTP.php';