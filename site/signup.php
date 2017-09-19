<?php
include_once("php_includes/check_login_status.php");

// Display error when an user is currently logged in
if ($user_loggedin == true) {
	header("location: message.php?msg=signup_during_session");
	exit();
}
?>
<?php
// Check POSTed userid for duplicates
if (isset($_POST['useridcheck'])) {
	include_once("php_includes/opmsdb_conx.php");
	
	$userid = preg_replace('#[^A-Z0-9-]#', '', $_POST['useridcheck']);
	$sql = "SELECT id FROM users WHERE userid='$userid' LIMIT 1";
	$query = mysqli_query($db_conx, $sql);
	$userid_exists = mysqli_num_rows($query);
	
	if (strlen($userid) != 8 && strlen($userid) != 6) {
		echo '<strong style="color:#F00;"> Enter 8 digit Registration No. (student) or 6 character Employee ID (faculty) </strong>';
		exit();
	}
	if ($userid_exists > 0) {
		echo '<strong style="color:#F00;"> User ID already exists </strong>';
		exit();
	} else {
		echo '<strong style="color:#009900;"> User ID is OK </strong>';
		exit();
	}
}
?>
<?php
// Collect POSTed data to sign up the user and mail the administrator for verification

if (isset($_POST["uid"])) {
	include_once("php_includes/opmsdb_conx.php");
	
	$uid = preg_replace('#[^A-Z0-9]#', '', $_POST['uid']);
	$fn = preg_replace('#[^a-z ]#i', '', $_POST['fn']);
	$ln = preg_replace('#[^a-z ]#i', '', $_POST['ln']);
	$em = mysqli_real_escape_string($db_conx, $_POST['em']);
	$pw = mysqli_real_escape_string($db_conx, $_POST['pw']);
	$ip = preg_replace('#[^0-9.]#', '', getenv('REMOTE_ADDR'));
	$ul = preg_replace('#[^SF]#', '', $_POST['ul']);
	
	$sql = "SELECT id FROM users WHERE userid='$uid' LIMIT 1";
	$query = mysqli_query($db_conx, $sql); 
	$uid_exists = mysqli_num_rows($query);
	
	$sql = "SELECT id FROM users WHERE email='$em' LIMIT 1";
	$query = mysqli_query($db_conx, $sql); 
	$em_exists = mysqli_num_rows($query);
	
	if ($uid == "" || strlen($ul) != 1 || $fn == "" || $ln == "" || $em == "" || $pw == "") {
		echo "Please fill out all the form data";
		exit();
	} else if ($uid_exists > 0) { 
		echo "The User ID you entered is alreay in use";
		exit();
	} else if ($em_exists > 0) { 
		echo "The Email you entered is already in use";
		exit();
	} else if (strlen($uid) != 8 && strlen($uid) != 6) {
		echo "Enter 8 digit Registration No. (student) or 6 character Employee ID (faculty)";
		exit(); 
	} else {
		// Hash the password
		$p_hash = md5($pw);
		
		// Add user to database
		$sql = "INSERT INTO users (userid, firstname, lastname, password, email, userlevel, ip, signuptime, lastlogin)       
				VALUES('$uid','$fn','$ln','$p_hash','$em','$ul','$ip',now(),now())";
		$query = mysqli_query($db_conx, $sql); 
		$id = mysqli_insert_id($db_conx);
		
		// Email to administrator
		require("php_includes/class.phpmailer.php"); // path to the PHPMailer class
 
		$mail = new PHPMailer();  
		 
		$mail->IsSMTP();  // telling the class to use SMTP
		$mail->SMTPAuth = true; // authentication enabled
		$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for GMail
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 465; // or 587
		$mail->Username = "opms.csenitdgp@gmail.com"; // SMTP username
		$mail->Password = "Opms>1.618"; // SMTP password 
		 
		$mail->SetFrom("opms.csenitdgp@gmail.com", "opms.csenitdgp@gmail.com");
		$mail->AddAddress("eruku.ade@gmail.com");
		
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
									Hello Admin,<br /><br />
									The following user has signed up for an account OPMS -<br /><br />
									User Type: '.$ul.'<br />
									User ID: '.$uid.'<br />
									Name: '.$fn.'&nbsp;'.$ln.'<br />
									Email: '.$em.'<br /><br />
									Please verify the above information from university records <br />and click the link below to activate the user account:<br /><br />
									<a href="http://profile.csenitdgp.ac.in/activation.php?id='.$id.'&uid='.$uid.'&ul='.$ul.'&fn='.$fn.'&ln='.$ln.'&em='.$em.'&p='.$p_hash.'">
										Click here to activate the account
									</a>
									<br /><br />
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
	exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title> Sign Up | OPMS </title>
    <meta charset="utf-8" />
    <link rel="icon" href="/favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="style/style.css" type="text/css" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <style type="text/css">
		h3 {
			margin: 24px;
		}
		
		#signupform {
			margin: 24px;
		}
		
		#signupform > div {
			margin: 10px 0px;
		}
		
		#signupform > input, select {
			width: 200px;
			margin-right: 5px;
			margin-bottom: 10px;
			padding: 2px;
			background: #CCC;
		}
		
		#signupbtn {
			width: 150px;
			height: 50px;
			font-size: 16px;
		}
	</style>
    
    <script src="js/main.js"></script>
	<script src="js/ajax.js"></script>
    <script type="text/javascript">
		// Restrict the input field and replace unwanted characters with <null>
		function restrict(elemid) {
			var tf = _(elemid);
			var rx = new RegExp;
			if (elemid == "userid") {
				// Select everything except A-Z, 0-9 and -
				rx = /[^A-Z0-9-]/g;
			} else if (elemid == "firstname" || elemid == "lastname") {
				// Select everything except A-Z and <space>
				rx = /[^a-z ]/gi;
			} else if (elemid == "email") {
				// Select ', " and <space>
				rx = /[' "]/gi;
			}
			tf.value = tf.value.replace(rx, "");
		}
		
		// Empty the input field
		function emptyElement(elemid) {
			_(elemid).innerHTML = "";
		}
		
		// Check for existing occurrences of userid and validate
		function checkuserid() {
			var uid = _("userid").value;
			if(uid != "") {
				// Display a 'checking' symbol until a response from ajax is received
				_("useridstatus").innerHTML = '<img src="images/checking.gif" />';
				
				// Display appropriate message as received from ajax
				var ajax = ajaxObj("POST", "signup.php");
				ajax.onreadystatechange =	function() {
					if (ajaxReturn(ajax) == true) {
						_("useridstatus").innerHTML = ajax.responseText;
					}
				}
				
				// Send to ajax
				ajax.send("useridcheck="+uid);
			}
		}
		
		// Collect data from the form and sign up the user
		function signup() {
			var uid = _("userid").value;
			var ul = _("userlevel").value;
			var fn = _("firstname").value;
			var ln = _("lastname").value;
			var em = _("email").value;
			var p1 = _("password1").value;
			var p2 = _("password2").value;
			var status = _("status");
			
			if (uid == "" || ul == "" || fn == "" || ln == "" || em == "" || p1 == "" || p2 == "") {
				status.innerHTML = "Fill out all the form data";
			} else if (p1 != p2) {
				status.innerHTML = "Password fields do not match";
			} else {
				_("signupbtn").style.display = "none";
				status.innerHTML = '<img src="images/wait.gif" />';
				var ajax = ajaxObj("POST", "signup.php");
				ajax.onreadystatechange =	function() {
					if (ajaxReturn(ajax) == true) {
						if (ajax.responseText != "signup_success") {
							status.innerHTML = ajax.responseText;
							_("signupbtn").style.display = "block";
						} else {
							window.scrollTo(0,0);
							_("signupform").innerHTML = "<p> OK "+fn+", your account has been created and has been sent for verification. \
														After the administrator has activated your account, you will receive a notification \
														email at <u>"+em+"</u>. You will not be able to log in to the site until your account \
														is activated by the administrator. </p>";
							
						}
					}
				}
				ajax.send("uid="+uid+"&ul="+ul+"&fn="+fn+"&ln="+ln+"&em="+em+"&pw="+p1);
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
    	<h3>Sign Up</h3>
        <form name="signupform" id="signupform" onSubmit="return false;" >
            <div> Sign Up As: </div>
            <select id="userlevel">
            	<option value="S"> Student </option>
                <option value="F"> Faculty </option>
            </select>
            <div> User ID: </div>
            <input id="userid" type="text" onFocus="emptyElement('useridstatus')" onBlur="checkuserid()" onKeyUp="restrict('userid')" maxlength="15" />
            <span id="useridstatus"></span>
            <div> First Name: </div>
            <input id="firstname" type="text" onFocus="emptyElement('status')" onKeyUp="restrict('firstname')" maxlength="255" />
            <div> Last Name: </div>
            <input id="lastname" type="text"  onFocus="emptyElement('status')" onKeyUp="restrict('lastname')" maxlength="255" />
            <div> Email: </div>
            <input id="email" type="email" onFocus="emptyElement('status')" onKeyUp="restrict('email')" maxlength="255" />
            <div> Create Password: </div>
            <input id="password1" type="password" onFocus="emptyElement('status')" maxlength="128" />
            <div> Confirm Password: </div>
            <input id="password2" type="password" onFocus="emptyElement('status')" maxlength="128" />
            <div><button id="signupbtn" onClick="signup()"> Create Account </button></div>
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