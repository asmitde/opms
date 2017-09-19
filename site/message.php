<?php
// Extract the message and display

include_once("php_includes/check_login_status.php");

$message = "";
if (isset($_GET['msg'])) {
	$msg = preg_replace('#[^a-z 0-9.,:_()]#i', '', $_GET['msg']);
	
	if ($msg == "") {
		header("location: index.php");
	} else if ($msg == "activation_failure") {
		$message = '<h2>Activation Error</h2>';
	} else if ($msg == "activation_success") {
		$message = '<h2>Activation Successful</h2>';
	} else if ($msg == "activation_string_length_issues") {
		$message = '<h2>Activation String Length Problems</h2>';
	} else if ($msg == "missing_GET_variables") {
		$message = '<h2>Missing GET Variables for Activation</h2>';
	} else if ($msg == "signup_during_session") {
		$message = '<h2>Log Out</h2>';
	} else {
		$message = $msg;
	}
} else {
	// Header to homepage if no message is found
	header("location: index.php");
}	
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title> Message | OPMS </title>
    <meta charset="utf-8" />
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="style/style.css" type="text/css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>

<body>
<div id="wrapper">
	<div id="header">
        <?php include_once("php_includes/header.php"); ?>
    </div>
    <div id="content">
    	<div id="mainContent" class="content">
        <div id="contentPlaceholder">
            <?php echo $message; ?>
        </div>
        </div>
    </div>
    <div id="footer">
		<?php include_once("php_includes/footer.php"); ?>
    </div>
</div>
</body>

</html>
