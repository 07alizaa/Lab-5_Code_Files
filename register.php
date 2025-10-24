<?php
// register.php
// Allows a new user to register an account.
// Uses PDO from db.php, prepared statements, and password_hash().

session_start();
require_once __DIR__ . '/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect and sanitize input
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if ($username === '' || $password === '' || $email === '') {
        $error = 'Please fill in username, email and password.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        // Hash the password securely
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Prepare an INSERT statement using prepared statements to prevent SQL injection
        $sql = 'INSERT INTO users (username, email, password_hash) VALUES (:username, :email, :password_hash)';
        $stmt = $pdo->prepare($sql);
        try {
            $stmt->execute([
                ':username' => $username,
                ':email' => $email,
                ':password_hash' => $password_hash,
            ]);

            // Registration successful
            // Link back to the original frontend login page (index.html)
            $success = 'Registration successful. You can now <a href="index.html">log in</a>.';
        } catch (PDOException $e) {
            // Handle duplicate username/email constraint (SQLSTATE 23000)
            if ($e->getCode() === '23000') {
                // Could be duplicate username or email depending on DB constraints
                $error = 'Username or email already exists. Please choose another.';
            } else {
                // Generic error message for other database errors
                $error = 'An error occurred while creating your account. Please try again later.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Register</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="login-container">
    <h2>Create an account</h2>

    <!-- Display messages -->
    <?php if ($error): ?>
      <div class="message" style="color:#b00;margin-bottom:10px"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="message" style="color:#080;margin-bottom:10px"><?php echo $success; ?></div>
    <?php endif; ?>

    <!-- Registration form -->
    <form method="POST" action="register.php" novalidate>
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required value="<?php echo isset($username) ? htmlspecialchars($username) : ''; ?>">

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">

      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>

      <button type="submit">Register</button>
    </form>

  <p style="text-align:center;margin-top:10px;"><a href="index.html">Back to Login</a></p>
  </div>
</body>
</html>
