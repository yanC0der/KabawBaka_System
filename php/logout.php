<?php
session_start();

// Clear session data
$_SESSION = [];

// If session uses cookies, delete the session cookie
if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000,
		$params['path'], $params['domain'], $params['secure'], $params['httponly']
	);
}

// Destroy the session
session_destroy();

// Return JSON so client-side fetch can handle navigation
header('Content-Type: application/json; charset=UTF-8');
echo json_encode(['success' => true]);
exit();
?>
