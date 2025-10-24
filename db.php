<?php
// PDO-based database connection helper.
// Update these variables to match your local MySQL setup.
$DB_HOST = '127.0.0.1';
$DB_NAME = 'lab5';
$DB_USER = 'root';
$DB_PASS = '';

try {
    $dsn = "mysql:host={$DB_HOST};dbname={$DB_NAME};charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // In production, log the error instead of echoing it.
    exit('Database connection failed. Check db.php configuration.');
}

/*
SQL to create the `users` table (run once in your database):

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL UNIQUE,
  email VARCHAR(255) DEFAULT NULL,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

To create a demo user from the command line or a one-off script:
<?php
// $hash = password_hash('password123', PASSWORD_DEFAULT);
// INSERT INTO users (username, email, password_hash) VALUES ('student','student@example.com', '$hash');
?>

*/

?>
