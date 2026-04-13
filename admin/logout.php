<?php
// Session start karna zaroori hai destroy karne se pehle
session_start();

// Saare session variables ko saaf kar do
$_SESSION = array();

// Agar browser mein session cookie hai toh usse bhi expire kar do
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, session destroy karo
session_destroy();

// User ko login page par redirect kar do
header("Location: login.php?msg=logged_out");
exit();
?>