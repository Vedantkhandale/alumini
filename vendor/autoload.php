<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Aapke current folder structure ke hisab se exact sahi path:
require_once __DIR__ . '/../vendor/PHPMailer/PHPMailer/autoload.php';
// Manual Autoloader for PHPMailer without Composer
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

?>