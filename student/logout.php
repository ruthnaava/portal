<?php
// Student panel logout
// Clear session data, remove session cookie, destroy the session and redirect to the site root.

// Ensure session is started (safe to call even if already started)
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}

// Unset all session variables
$_SESSION = [];

// If session uses cookies, delete the session cookie
if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params['path'], $params['domain'],
		$params['secure'], $params['httponly']
	);
}

// Finally destroy the session
session_destroy();

// Redirect user to the site root (adjust path if you want a different landing page)
header('Location: ../../');
exit;

?>