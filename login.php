<?php
/*
 Secure login handler
 - Uses PDO prepared statements (db.php)
 - Validates the server-generated CAPTCHA stored in session
 - Limits login attempts to 3, with a 5-minute lockout
 - Uses password_verify() to validate stored password hashes
 - Sets a "remember_username" cookie for 7 days if requested
*/

session_start();
require_once __DIR__ . '/db.php';

// Initialize attempt tracking
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
    $_SESSION['last_attempt_time'] = null;
}

// Helper redirect with message
function redirect($url = 'index.html', $msg = '') {
    if ($msg !== '') {
        $url .= (strpos($url, '?') === false ? '?' : '&') . 'msg=' . urlencode($msg);
    }
    header('Location: ' . $url);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $captcha = trim($_POST['captcha'] ?? '');
    $remember = isset($_POST['remember']);

    // Lockout logic
    $maxAttempts = 3;
    $lockoutSecs = 5 * 60; // 5 minutes
    if ($_SESSION['login_attempts'] >= $maxAttempts) {
        $elapsed = time() - ($_SESSION['last_attempt_time'] ?? 0);
        if ($elapsed < $lockoutSecs) {
        redirect('index.html', 'Too many attempts. Try again later.');
        } else {
            // Reset after lockout period
            $_SESSION['login_attempts'] = 0;
            $_SESSION['last_attempt_time'] = null;
        }
    }

    // Validate captcha
    if (!isset($_SESSION['captcha_answer']) || (string)$_SESSION['captcha_answer'] !== $captcha) {
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt_time'] = time();
    redirect('index.html', 'Invalid CAPTCHA.');
    }

    if ($username === '' || $password === '') {
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt_time'] = time();
    redirect('index.html', 'Please provide username and password.');
    }

    // Fetch user from DB using prepared statement
    $stmt = $pdo->prepare('SELECT id, username, email, password_hash FROM users WHERE username = :username LIMIT 1');
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        // Login success
        $_SESSION['login_attempts'] = 0;
        $_SESSION['last_attempt_time'] = null;

        // Store session info for authenticated user
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['last_activity'] = time(); // for session timeout

        // Remember me cookie (only stores username)
        if ($remember) {
            setcookie('remember_username', $user['username'], time() + 7 * 24 * 3600, '/');
        } else {
            if (isset($_COOKIE['remember_username'])) {
                setcookie('remember_username', '', time() - 3600, '/');
            }
        }

        redirect('dashboard.php');
    } else {
        // Invalid credentials
        $_SESSION['login_attempts']++;
        $_SESSION['last_attempt_time'] = time();
    redirect('index.html', 'Invalid username or password.');
    }
}

// If not POST, send back to login
redirect('index.html');
?>
