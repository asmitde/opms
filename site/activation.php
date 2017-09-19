<?php
// Activate the account of the user

if (isset($_GET['id']) && isset($_GET['uid']) && isset($_GET['ul']) && isset($_GET['fn']) && 
	isset($_GET['ln']) && isset($_GET['em']) && isset($_GET['p'])) {
	
	// Connect to database
    include_once("php_includes/opmsdb_conx.php");
	
    $id = preg_replace('#[^0-9]#i', '', $_GET['id']);
	$uid = preg_replace('#[^A-Z0-9]#', '', $_GET['uid']);
	$ul = preg_replace('#[^FS]#i', '', $_GET['ul']);
	$fn = preg_replace('#[^a-z ]#i', '', $_GET['fn']);
	$ln = preg_replace('#[^a-z ]#i', '', $_GET['ln']);
	$em = mysqli_real_escape_string($db_conx, $_GET['em']);
	$p_hash = mysqli_real_escape_string($db_conx, $_GET['p']);
	
	// Evaluate the lengths of the incoming $_GET variable
	if ($id == "" || (strlen($uid) != 6 && strlen($uid) != 8) || strlen($ul) != 1 || strlen($fn) > 255 || 
		$fn == "" || strlen($ln) > 255 || $ln == "" || strlen($em) < 5 || strlen($p_hash) < 32 ) {
		header("location: message.php?msg=activation_string_length_issues");
		
		// MAIL TO ADMIN CODE <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< PLACE HERE
		
		exit(); 
	}
	
	// Check the credentials against database
	$sql = "SELECT * FROM users WHERE id='$id' AND userid='$uid' AND userlevel='$ul' AND firstname='$fn' 
			AND lastname='$ln' AND email='$em' AND password='$p_hash' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$numrows = mysqli_num_rows($query);
	
	if ($numrows == 0) {
		header("location: message.php?msg=Your credentials are not matching anything in our system");
		
		// MAIL TO ADMIN CODE <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< PLACE HERE
		
		exit();
	}
	
	// Match found, activate account
	$sql = "UPDATE users SET activated='1' WHERE id='$id' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	
	// Check if activated
	$sql = "SELECT * FROM users WHERE id='$id' AND activated='1' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$numrows = mysqli_num_rows($query);
	
	// Display activation status
	if ($numrows == 0) {
		// Activation failure
		header("location: message.php?msg=activation_failure");
		
		// MAIL TO ADMIN CODE <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< PLACE HERE
		
		exit();
	} else if ($numrows == 1) {
		// Activation success
		header("location: message.php?msg=activation_success");
		
		// Email the user notifying the activation
		require("php_includes/class.phpmailer.php"); // path to the PHPMailer class
 
		$mail = new PHPMailer();  
		 
		$mail->IsSMTP();  // telling the class to use SMTP
		$mail->SMTPAuth = true; // authentication enabled
		$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 465; // or 587
		$mail->Username = ""; // SMTP username
		$mail->Password = ""; // SMTP password 
		 
		$mail->SetFrom("", "");
		$mail->AddAddress($em);
		
		$mail->Subject  = "OPMS User Account Activation";
		$mail->Body =	'<!DOCTYPE html>
						<html>
							<head>
								<meta charset="UTF-8">
								<title> OPMS User Account Activation </title>
							</head>
							<body style="margin:0px; font-family:Tahoma, Geneva, sans-serif;">
								<div style="height:50px; width:100%; padding:10px; background:#333; font-size:24px; color:#FFF;">
									<a href="http://profile.csenitdgp.ac.in">
										<img src="http://profile.csenitdgp.ac.in/images/logo.png" width="50px" height="50px" alt="logo" style="float:left;" />
									</a>
									<div style="line-height:50px; padding-left:10px; float:left">OPMS User Account Activation</div>
								</div>
								<div style="padding:24px; font-size:14px;">
									Hello '.$fn.',<br /><br />
									Your OPMS account has been verified and activated by the administrator.<br /><br />
									You can now log in to the site with your User ID: '.$uid.'<br /><br />
								</div>
								<div style="padding:24px; font-size:12px;">
									<i>NOTE: This is a system generated mail. Please do not reply to this address.<br />
									If you need to contact the administrator, send and email to ADMIN EMAIL GOES HERE.</i>
								</div>
							</body>
						</html>';
		$mail->IsHTML(true); 
		if ($mail->Send()) {
			echo 'signup_success';
		} else {
			echo '<p>Sorry, there was a problem in sending the mail.</p><p>Mailer error: '.$mail->ErrorInfo.'</p>';
		}
		exit();
	}
} else {
	header("location: message.php?msg=missing_GET_variables");
	
	// MAIL TO ADMIN CODE <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< PLACE HERE
}
?>