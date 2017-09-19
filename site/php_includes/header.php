<?php
// Dynamically place user control panel in navigation bar

$login_signup_link = '<a href="login.php">Log In</a> &nbsp; | &nbsp; <a href="signup.php">Sign Up</a>';
if ($user_loggedin == true) {
    $login_signup_link = 'Welcome, &nbsp; <a href="#">'.$log_firstname.'</a> &nbsp; | &nbsp; <a href="logout.php">Log Out</a>';
}
?>
<div id="headerBanner">
	<img id="logo" src="images/logo.png" />
    <div id="title">
    	<div id="institute"> National Institute of Technology, Durgapur </div>
		<div id="department"> Department of Computer Science and Engineering </div>
	</div>
</div>
<div id="headerNav">
	<div class="content">
        <nav>
            <a href="index.php"> Home </a>
            <a href="#"> Student </a>
            <a href="#"> Faculty </a>
            <a href="#"> About Us </a> 		  
        </nav>
        <div id="headerSession">
            <?php echo $login_signup_link; ?>
        </div>
    </div>
</div>