<?php 

	require('inc/config/db.php');

	if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }

	if(isset($_POST['sign'])) {
		header('Location: inc/login.php');
	}

	if(isset($_POST['register'])) {
		header('Location: inc/accounttype.php');
	} 


	if(isset($_POST['browser'])) {
		$_SESSION['log_in_customer'] = 'anonymous';
		header('Location: inc/customer.php');
	} 
	
	// Close Connection
	// mysqli_close($conn);

?>

<!DOCTYPE html>
	<html>
		<head>
			<title>MTS Recommendation Platform</title>
			<link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/cosmo/bootstrap.min.css">
		</head>
	<body>
	<h1>Welcome to Milk Tea Shop Recommendation Platform</h1>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	      <button type="submit" name="sign" class="btn btn-primary">Sign In</button>
		  <button type="submit" name="register" class="btn btn-primary">Register</button>
		  <button type="submit" name="browser" class="btn btn-primary">Browser</button>
    </form>
	<?php require('inc/footer.php'); ?>
	</body>
</html>