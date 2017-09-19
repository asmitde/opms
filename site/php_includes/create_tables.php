<?php
/* Create tables for the OPMS_db database */

// Include database connection script
include_once("opmsdb_conx.php");


/// Table: users ///
$tbl_users =	"CREATE TABLE IF NOT EXISTS users (
					id				INT UNSIGNED		NOT NULL AUTO_INCREMENT,
					userid			VARCHAR(15)			NOT NULL,
					firstname		VARCHAR(255)		NOT NULL,
					lastname		VARCHAR(255)		NOT NULL,
					password		VARCHAR(255)		NOT NULL,
					email			VARCHAR(255)		NOT NULL,
					userlevel		ENUM('A','S','F')	NOT NULL DEFAULT 'S',
					ip				VARCHAR(255)		NOT NULL,
					signuptime		DATETIME			NOT NULL,
					lastlogin		DATETIME			NOT NULL,
					activated		ENUM('0','1')		NOT NULL DEFAULT '0',
					temppass		VARCHAR(255),
					PRIMARY KEY (id),
					UNIQUE KEY (userid),
					UNIQUE KEY (email)
				)";
				
$query = mysqli_query($db_conx, $tbl_users);
// Check for creation errors
if ($query === TRUE) {
	echo "Creating table: users: SUCCESS";
} else {
	echo "Creating table: users: FAIL";
}


/// Table: student ///
$tbl_student =	"CREATE TABLE IF NOT EXISTS student (
					id				VARCHAR(15)		NOT NULL,
					rollno			VARCHAR(15),
					dob				DATE,
					sex				ENUM('M','F'),
					address1		VARCHAR(255),
					address2		VARCHAR(255),
					city			VARCHAR(255),
					state			VARCHAR(255),
					country			VARCHAR(255),
					pincode			VARCHAR(255),
					hostel			VARCHAR(255),
					room			VARCHAR(15),
					contactno		VARCHAR(31),
					bloodtype		ENUM('A','B','O','AB'),
					bloodrh			ENUM('P','N'),
					aboutme			TEXT,
					photo			VARCHAR(255),
					signature		VARCHAR(255),
					studingyear		ENUM('1','2','3','4','PO','NOR'),
					programme		ENUM('B','M'),
					internship		TEXT,
					placement		TEXT,
					lastedited		DATETIME,
					PRIMARY KEY (id),
					UNIQUE KEY (rollno)
				)";
				
$query = mysqli_query($db_conx, $tbl_student);
// Check for creation errors
if ($query === TRUE) {
	echo "Creating table: student: SUCCESS";
} else {
	echo "Creating table: student: FAIL";
}


/// Table: course ///
$tbl_course =	"CREATE TABLE IF NOT EXISTS course (
					id				INT UNSIGNED	NOT NULL AUTO_INCREMENT,
					coursecode		VARCHAR(7),
					coursename		VARCHAR(255),
					empid			VARCHAR(15),
					PRIMARY KEY (id),
					UNIQUE KEY (coursecode)
				)";
				
$query = mysqli_query($db_conx, $tbl_course);
// Check for creation errors
if ($query === TRUE) {
	echo "Creating table: course: SUCCESS";
} else {
	echo "Creating table: course: FAIL";
}


// Table: publication ///
$tbl_pub = 		"CREATE TABLE IF NOT EXISTS publication (
					id			INT UNSIGNED	NOT NULL AUTO_INCREMENT,
					details		VARCHAR(255)	NOT NULL,
					link		VARCHAR(255),
					author		INT UNSIGNED	NOT NULL,
					PRIMARY KEY (id)
				)";
				
$query = mysqli_query($db_conx, $tbl_pub);
// Check for creation errors
if ($query === TRUE) {
	echo "Creating table: publication: SUCCESS";
} else {
	echo "Creating table: publication: FAIL";
}

?>