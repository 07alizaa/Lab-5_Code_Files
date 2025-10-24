<?php
// Profile editing page: allows logged-in users to update username and email.
session_start();
require_once __DIR__ . '/db.php';

// Require login
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php?msg=' . urlencode('Please log in.'));
    exit();
}

// Session timeout (10 minutes)
$timeoutSecs = 10 * 60;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeoutSecs)) {
    session_unset(); session_destroy();
    header('Location: index.php?msg=' . urlencode('Session expired. Please log in again.'));
    exit();
}
$_SESSION['last_activity'] = time();

$userId = $_SESSION['user_id'];
$msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername = trim($_POST['username'] ?? '');
    $newEmail = trim($_POST['email'] ?? '');

    if ($newUsername === '') {
        $msg = 'Username cannot be empty.';
    } else {
        // Update using prepared statement
        $stmt = $pdo->prepare('UPDATE users SET username = :username, email = :email WHERE id = :id');
        try {
            $stmt->execute([
                ':username' => $newUsername,
                ':email' => $newEmail === '' ? null : $newEmail,
                ':id' => $userId,
            ]);
            $_SESSION['username'] = $newUsername;
            $msg = 'Profile updated successfully.';
        } catch (PDOException $e) {
            // Handle unique username constraint or other DB errors
            if ($e->getCode() == 23000) {
                $msg = 'Username already taken.';
            } else {
                $msg = 'An error occurred. Try again.';
            }
        }
    }
}

// Fetch current user info to prefill form
$stmt = $pdo->prepare('SELECT username, email FROM users WHERE id = :id');
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch();
$currentUsername = $user['username'] ?? '';
$currentEmail = $user['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Profile</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="login-container">
    <h2>Edit Profile</h2>
    <?php if ($msg): ?>
      <div class="message"><?php echo htmlspecialchars($msg); ?></div>
    <?php endif; ?>
    <form method="POST" action="profile.php">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($currentUsername); ?>">

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($currentEmail); ?>">

      <button type="submit">Save</button>
    </form>
    <p style="text-align:center;margin-top:10px;"><a href="dashboard.php">Dashboard</a> | <a href="change_password.php">Change Password</a> | <a href="logout.php">Logout</a></p>
  </div>
</body>
</html>
