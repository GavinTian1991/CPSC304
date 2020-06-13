<?php
    require('config/db.php');
	// Message Vars
	$msg = '';
	$msgClass = '';
    if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }

    if(isset($_POST['submit_Customer'])){
		// Check Required Fields
		if(isset($_POST['loginEmail'], $_POST['loginPassword'])){
            // Get Form Data
            $email = mysqli_real_escape_string($conn, $_POST['loginEmail']);
            $password = mysqli_real_escape_string($conn, $_POST['loginPassword']);

			$query = "SELECT * FROM Account, Customer_Account 
			WHERE Email = '$email' 
			AND Password = '$password' 
			AND Account.Account_ID = Customer_Account.Account_ID";

			$result = mysqli_query($conn, $query);
			$user = mysqli_fetch_assoc($result);
			//print_r($user);

			if($user){
				$_SESSION['logged_cust_name'] = $user['User_Name'];
				$_SESSION['logged_cust_id'] = $user['Account_ID'];
				echo $_SESSION['logged_cust_name'].$_SESSION['logged_cust_id'];
				$_SESSION['customer_logged_in'] = TRUE;
				echo 'log in successfully!';
				header('Location: customer.php');
			} else {
				$msg = 'log failed!';
				//echo 'ERROR: '. mysqli_error($conn);
			}

		} else {
			// Failed
			$msg = 'Please fill both the username and password fields!';
			$msgClass = 'alert-danger';
		}
	}


	if(isset($_POST['submit_Owner'])){

		// Check Required Fields
		if(isset($_POST['loginEmail'], $_POST['loginPassword'])){
            // Get Form Data
            $email = mysqli_real_escape_string($conn, $_POST['loginEmail']);
            $password = mysqli_real_escape_string($conn, $_POST['loginPassword']);

			$query = "SELECT * FROM Account, Business_Owner_Account 
			WHERE Email = '$email' 
			AND Password = '$password' 
			AND Account.Account_ID = Business_Owner_Account.Account_ID";

			$result = mysqli_query($conn, $query);
			$user = mysqli_fetch_assoc($result);

			if($user){
                $_SESSION['logged_owner_name'] = $user['User_Name'];
                $_SESSION['logged_owner_id'] = $user['Account_ID'];
                echo $_SESSION['logged_owner_name'].$_SESSION['logged_owner_id'];
                $_SESSION['owner_logged_in'] = TRUE;
				echo 'owner log in successfully!';
				header('Location: ownerprofile.php');
			} else {
				echo 'log failed!';
			}

		} else {
			$msg = 'Please fill in all fields';
			$msgClass = 'alert-danger';
		}
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Login</title>
	<link rel="stylesheet" href="https://bootswatch.com/4/cosmo/bootstrap.min.css">
</head>
<body>
	<nav class="navbar navbar-default">
      <div class="container">
        <div class="navbar-header">    
			<h1 class="navbar-brand">User Sign In</h1>
        </div>
      </div>
    </nav>
    <div class="container">	
    	<?php if($msg != ''): ?>
    		<div class="alert <?php echo $msgClass; ?>"><?php echo $msg; ?></div>
    	<?php endif; ?>
      <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	      <div class="form-group">
		      <label>Email</label>
		      <input type="text" name="loginEmail" class="form-control" value="<?php echo isset($_POST['loginEmail']) ? $email : ''; ?>">
	      </div>
		  <div class="form-group">
	      	<label>Password</label>
	      	<input type="password" name="loginPassword" class="form-control" value="">
	      </div>
	      <br>
	      <button type="submit" name="submit_Customer" class="btn btn-primary">Customer Sign In</button>
		  <button type="submit" name="submit_Owner" class="btn btn-primary">Owner Sign In</button>
      </form>
    </div>
	<?php require('footer.php'); ?>
</body>
</html>