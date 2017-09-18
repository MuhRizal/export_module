<?php
	$username = "root";
	$password = "";
	$hostname = "localhost";
	$database = "ws_export";
	$dbhandle = mysql_connect($hostname, $username, $password) 
	or die("Unable to connect to MySQL");
	$selected = mysql_select_db($database,$dbhandle) or die("Could not select examples");
?>