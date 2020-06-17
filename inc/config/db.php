<?php 

     require('config.php');

	 $conn = new mysqli(DB_HOST, DB_USER, DB_PASS,DB_NAME) or die("Connect failed: %s\n". $conn -> error);

	 date_default_timezone_set('America/Vancouver');
	 
	 if(mysqli_connect_errno()) {
		 echo 'Failed to connect to Mysql'. mysqli_connect_errno();;
	 } 
?>