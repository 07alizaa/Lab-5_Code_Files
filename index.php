<?php
// Server-rendered login page with simple math CAPTCHA and optional username autofill.
session_start();

// If user already logged in, send to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Generate simple math captcha and store the answer in session
$a = rand(2, 9);
$b = rand(1, 9);
$_SESSION['captcha_answer'] = $a + $b;
$captcha_question = "$a + $b =";

// Read remember cookie if present to prefill username
$remembered = $_COOKIE['remember_username'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="login-container">
    <h2>Login</h2>

    <?php if (!empty($_GET['msg'])): ?>
      <div class="message"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <form id="loginForm" method="POST" action="login.php">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required value="<?php echo htmlspecialchars($remembered); ?>">

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>

      <label for="captcha">CAPTCHA: <?php echo $captcha_question; ?></label>
      <input type="text" id="captcha" name="captcha" required placeholder="Answer">

      <label><input type="checkbox" id="remember" name="remember"> Remember Me</label>

      <button type="submit">Login</button>
    </form>
    <p style="text-align:center;margin-top:10px;"><a href="profile.php">Profile</a> | <a href="change_password.php">Change Password</a></p>
  </div>
  <script src="script.js"></script>
</body>
</html>
