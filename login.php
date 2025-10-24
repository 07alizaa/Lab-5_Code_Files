<?php
session_start();
ob_start(); // Start output buffering to prevent any output before headers

// Hardcoded credentials (for demo purposes)
$valid_username = "student";
$valid_password = "password123";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate credentials
    if ($username === $valid_username && $password === $valid_password) {
        // Set session and cookie
        $_SESSION['username'] = $username;

        // Set a cookie with username and IP address
        $ip_address = $_SERVER['REMOTE_ADDR'];
        setcookie('user_info', json_encode(['username' => $username, 'ip' => $ip_address]), time() + 3600, '/');

        // Redirect to dashboard
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Invalid credentials. Please try again.";
    }
}

ob_end_flush(); // End output buffering
?>
