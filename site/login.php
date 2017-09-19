<?php
include_once("php_includes/check_login_status.php");

// Redierct to homepage if user already logged in
if ($user_loggedin == true) {
	header("location: index.php");
	exit();
}
?>
<?php
// Verify POSTed user credentials aginst database and log in user
if (isset($_POST['uid']) && isset($_POST['pw'])) {
	// Establish connection to database
	include_once("php_includes/opmsdb_conx.php");
	
	// Sanitize POSTed data
	$uid = preg_replace('#[^A-Z0-9-]#', '', $_POST['uid']);
	$pw = mysqli_real_escape_string($db_conx, $_POST['pw']);
	
	// Hash the password
	$p_hash = md5($pw);
	
	// Handle form errors
	if ($uid == "" || $pw == "") {
		echo 'login_failed';
		exit();
	}
	
	// Check login credentials against database
	$sql = "SELECT id, userid, firstname, password FROM users WHERE userid='$uid' AND password='$p_hash' AND activated='1' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$userid_exists = mysqli_num_rows($query);

	// Send error if no userid match found
	if ($userid_exists < 1) {
		echo 'login_failed';
		exit();
	}
	
	// Verify password if userid match found
	$row = mysqli_fetch_row($query);
	$db_id = $row[0];
	$db_userid = $row[1];
	$db_firstname = $row[2];
	$db_password = $row[3];
	
	// Send error if incorrect password
	if ($p_hash != $db_password) {
		echo 'login_failed';
		exit();
	}
	
	// Create sessions and cookies to log in user
	$_SESSION['id'] = $db_id;
	$_SESSION['userid'] = $db_userid;
	$_SESSION['firstname'] = $db_firstname;
	$_SESSION['password'] = $db_password;	
	setcookie('id', $db_id, strtotime('+5 days'), '/', '', '', true);
	setcookie('userid', $db_userid, strtotime('+5 days'), '/', '', '', true);
	setcookie('firstname', $db_firstname, strtotime('+5 days'), '/', '', '', true);
	setcookie('password', $db_password, strtotime('+5 days'), '/', '', '', true);
	
	// Update database with current ip and login datetime
	$ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	$sql = "UPDATE users SET ip='$ip', lastlogin=now() WHERE id='$db_id' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title> Log In | OPMS </title>
    <meta charset="utf-8" />
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="style/style.css" type="text/css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <style type="text/css">
		h3 {
			margin: 24px;
		}
		
		#loginform {
			margin: 24px;
		}
		
		#loginform > div {
			margin: 10px 0px;
		}
		
		#loginform > input, select {
			width: 200px;
			margin-right: 5px;
			margin-bottom: 10px;
			padding: 2px;
			background: #CCC;
		}
		
		#loginbtn {
			width: 100px;
			height: 25px;
			font-size: 16px;
		}
	</style>
    
    <script src="js/main.js"></script>
	<script src="js/ajax.js"></script>
    <script type="text/javascript">
		// Empty the input field
		function emptyElement(elemid) {
			_(elemid).innerHTML = "";
		}
		
		// Collect data from the form and log in user
		function login() {
			var uid = _("userid").value;
			var pw = _("password").value;
			var status = _("status");
			
			if (uid == "") {
				status.innerHTML = "User ID cannot be empty";
			} else if (pw == "") {
				status.innerHTML = "Password cannot be empty";
			} else {
				_("loginbtn").style.display = "none";
				status.innerHTML = '<img src="images/wait.gif" />';
				var ajax = ajaxObj("POST", "login.php");
				ajax.onreadystatechange =	function() {
					if (ajaxReturn(ajax) == true) {
						if (ajax.responseText == "login_failed") {
							status.innerHTML = "User ID or password is incorrect, please try again.";
							_("loginbtn").style.display = "block";
						} else {
							window.location = "index.php";
						}
					}
				}
				ajax.send("uid="+uid+"&pw="+pw);
			}
		}
	</script>
</head>

<body>
<div id="wrapper">
	<div id="header">
        <?php include_once("php_includes/header.php"); ?>
    </div>
    <div id="content">
    	<div id="mainContent" class="content">
        <div id="contentPlaceholder">
    	<h3>Log In</h3>
        <form name="loginform" id="loginform" onSubmit="return false;" >
            <div> User ID: </div>
            <input id="userid" type="text" onFocus="emptyElement('status')" maxlength="15" />
            <div> Password: </div>
            <input id="password" type="password" onFocus="emptyElement('status')" maxlength="128" />
            <div><button id="loginbtn" onClick="login()"> Log In </button></div>
            <span id="status"></span>
        </form>
        </div>
        </div>
    </div>
    <div id="footer">
		<?php include_once("php_includes/footer.php"); ?>
    </div>
</div>
</body>

</html>