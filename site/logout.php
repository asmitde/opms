<?php
session_start();	// Start a session

// Clear session data
$_SESSION = array();

// Expire cookie files if set
if (isset($_COOKIE['id']) && isset($_COOKIE['userid']) && isset($_COOKIE['firstname']) && isset($_COOKIE['password'])) {
	setcookie('id', '', strtotime('-5 days'), '/');
	setcookie('userid', '', strtotime('-5 days'), '/');
	setcookie('firstname', '', strtotime('-5 days'), '/');
	setcookie('password', '', strtotime('-5 days'), '/');
}

// Destroy session
session_destroy();

// Verify that session is destroyed
if (isset($_SESSION['id'])) {
	header("location: message.php?msg=logout_failed");
} else {
	header("location: index.php");
}
?>