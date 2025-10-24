<?php
// captcha.php
// Server-side CAPTCHA endpoint for existing index.html frontend.
// Generates a simple math question, stores the numeric answer in session,
// and returns the question as JSON to be displayed by script.js.

header('Content-Type: application/json; charset=utf-8');
session_start();

// Generate two random numbers between 1 and 10
$a = random_int(1, 10);
$b = random_int(1, 10);

// Store the sum in session for server-side validation (login.php reads this)
$_SESSION['captcha_answer'] = $a + $b;

$payload = [
    'question' => sprintf('%d + %d =', $a, $b)
];

echo json_encode($payload);
exit();
