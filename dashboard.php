<?php
// Dashboard: requires authentication. Shows browser/OS info and session management.
session_start();

// Require login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?msg=' . urlencode('Please log in.'));
    exit();
}

// Session timeout: 10 minutes of inactivity
$timeoutSecs = 10 * 60;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeoutSecs)) {
    // Destroy session and redirect to login with message
    session_unset();
    session_destroy();
    header('Location: index.php?msg=' . urlencode('Session expired. Please log in again.'));
    exit();
}
// Update last activity time
$_SESSION['last_activity'] = time();

// Basic browser/OS info from user agent
$ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

// Simple (best-effort) OS detection
$os = 'Unknown OS';
if (preg_match('/Windows/i', $ua)) $os = 'Windows';
elseif (preg_match('/Macintosh|Mac OS X/i', $ua)) $os = 'macOS';
elseif (preg_match('/Linux/i', $ua)) $os = 'Linux';
elseif (preg_match('/Android/i', $ua)) $os = 'Android';
elseif (preg_match('/iPhone|iPad|iPod/i', $ua)) $os = 'iOS';

// Simple browser detection
$browser = 'Unknown Browser';
if (preg_match('/Chrome/i', $ua)) $browser = 'Chrome';
if (preg_match('/Firefox/i', $ua)) $browser = 'Firefox';
if (preg_match('/Safari/i', $ua) && !preg_match('/Chrome/i', $ua)) $browser = 'Safari';
if (preg_match('/MSIE|Trident/i', $ua)) $browser = 'Internet Explorer';
if (preg_match('/Edg/i', $ua)) $browser = 'Edge';

// IP address
$ip = $_SERVER['REMOTE_ADDR'] ?? 'N/A';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style> .meta { background:#fff;padding:10px;border-radius:6px;box-shadow:0 0 6px rgba(0,0,0,0.08);max-width:700px;margin:10px auto;} </style>
</head>
<body>
    <div class="meta">
      <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
      <p><strong>IP address:</strong> <?php echo htmlspecialchars($ip); ?></p>
      <p><strong>Browser:</strong> <?php echo htmlspecialchars($browser); ?></p>
      <p><strong>Operating System:</strong> <?php echo htmlspecialchars($os); ?></p>
      <p><a href="profile.php">Edit Profile</a> | <a href="change_password.php">Change Password</a> | <a href="logout.php">Logout</a></p>
    </div>
</body>
</html>
