<?php
// Logout: destroy session and clear remember-me cookie
session_start();

// Destroy session data
$_SESSION = [];
if (ini_get("session.use_cookies")) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params['path'], $params['domain'], $params['secure'], $params['httponly']
	);
}
session_destroy();

// Clear remember cookie if present
if (isset($_COOKIE['remember_username'])) {
	setcookie('remember_username', '', time() - 3600, '/');
}

// Redirect to login (index.php)
header('Location: index.php?msg=' . urlencode('You have been logged out.'));
exit();
?>
