<?php
session_start();	// Start a session

// Establish connection to database
include_once("php_includes/opmsdb_conx.php");

// Initialize variables
$user_loggedin = false;
$log_id = "";
$log_userid = "";
$log_firstname = "";
$log_password = "";

// Function to verify user credentials against database
function verifyUserCredentials($db_conx, $id, $uid, $fn, $pw) {
	$sql = "SELECT ip FROM users WHERE id='$id' AND userid='$uid' AND firstname='$fn' AND password='$pw' AND activated='1' LIMIT 1";
    $query = mysqli_query($db_conx, $sql);
    $numrows = mysqli_num_rows($query);
	
	if ($numrows > 0) {
		return true;
	}
}

// Check if session is set else if cookie is set
if (isset($_SESSION['id']) && isset($_SESSION['userid']) && isset($_SESSION['firstname']) && isset($_SESSION['password'])) {
	$log_id = preg_replace('#[^0-9]#', '', $_SESSION['id']);
	$log_userid = preg_replace('#[^A-Z0-9-]#', '', $_SESSION['userid']);
	$log_firstname = preg_replace('#[^a-z ]#i', '', $_SESSION['firstname']);
	$log_password = mysqli_real_escape_string($db_conx, $_SESSION['password']);
	
	// Verify user session data
	$user_loggedin = verifyUserCredentials($db_conx, $log_id, $log_userid, $log_firstname, $log_password);
} else if (isset($_COOKIE['id']) && isset($_COOKIE['userid']) && isset($_COOKIE['firstname']) && isset($_COOKIE['password'])) {
	// Create session data from cookie data
	$_SESSION['id'] = preg_replace('#[^0-9]#', '', $_COOKIE['id']);
	$_SESSION['userid'] = preg_replace('#[^A-Z0-9-]#', '', $_COOKIE['userid']);
	$_SESSION['firstname'] = preg_replace('#[^a-z ]#i', '', $_COOKIE['firstname']);
	$_SESSION['password'] = mysqli_real_escape_string($db_conx, $_COOKIE['password']);
	
	$log_id = $_SESSION['id'];
	$log_username = $_SESSION['userid'];
	$log_firstname = $_SESSION['firstname'];
	$log_password = $_SESSION['password'];
	
	// Verify user session data
	$user_loggedin = verifyUserCredentials($db_conx, $log_id, $log_userid, $log_firstname, $log_password);
	if ($user_loggedin == true) {
		// Update user lastlogin datetime and ip
		$ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
		$sql = "UPDATE users SET lastlogin=now() AND ip='$ip' WHERE id='$log_id' LIMIT 1";
		$query = mysqli_query($db_conx, $sql);
	}
}
?>