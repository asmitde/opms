<?php
// Establish database connection
// MySQL Server:	localhost
// Database Name:	OPMS_db
// User:			opmsbot
// Password:		TyQ8NUbmz8

$db_conx = mysqli_connect("localhost", "opmsbot", "TyQ8NUbmz8", "OPMS_db");

// Check for connection errors
if (mysqli_connect_errno()) {
	echo mysqli_connect_error();				// <---- Change this line to properly handle the error (header and mail)
}
?>