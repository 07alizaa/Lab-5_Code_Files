<?php
// Allow authenticated user to change password (verify current password, then store new hash).
session_start();
require_once __DIR__ . '/db.php';

// Require login
if (!isset($_SESSION['user_id'])) {
  header('Location: index.html?msg=' . urlencode('Please log in.'));
  exit();
}

// Session timeout (10 minutes)
$timeoutSecs = 10 * 60;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeoutSecs)) {
  session_unset(); session_destroy();
  header('Location: index.html?msg=' . urlencode('Session expired. Please log in again.'));
    exit();
}
$_SESSION['last_activity'] = time();

$userId = $_SESSION['user_id'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if ($new === '' || strlen($new) < 6) {
        $msg = 'New password must be at least 6 characters.';
    } elseif ($new !== $confirm) {
        $msg = 'New password and confirmation do not match.';
    } else {
        // Fetch current hash
        $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = :id');
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch();
        if (!$user || !password_verify($current, $user['password_hash'])) {
            $msg = 'Current password is incorrect.';
        } else {
            // Update password with new hash
            $newHash = password_hash($new, PASSWORD_DEFAULT);
            $upd = $pdo->prepare('UPDATE users SET password_hash = :ph WHERE id = :id');
            $upd->execute([':ph' => $newHash, ':id' => $userId]);
            $msg = 'Password updated successfully.';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Change Password</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="login-container">
    <h2>Change Password</h2>
    <?php if ($msg): ?>
      <div class="message"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>
    <form method="POST" action="change_password.php">
      <label for="current_password">Current Password:</label>
      <input type="password" id="current_password" name="current_password" required>

      <label for="new_password">New Password:</label>
      <input type="password" id="new_password" name="new_password" required>

      <label for="confirm_password">Confirm New Password:</label>
      <input type="password" id="confirm_password" name="confirm_password" required>

      <button type="submit">Change Password</button>
    </form>
    <p style="text-align:center;margin-top:10px;"><a href="dashboard.php">Dashboard</a> | <a href="profile.php">Profile</a> | <a href="logout.php">Logout</a></p>
  </div>
</body>
</html>
