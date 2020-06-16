<?php
    require('config/db.php');

	$msg = '';
	$msgClass = '';
    
	if(isset($_POST['submit_Customer'])){

		if(session_status() !== PHP_SESSION_ACTIVE){
			session_start();
		}

		$name = mysqli_real_escape_string($conn, $_POST['name']);
		$password = mysqli_real_escape_string($conn, $_POST['password']);

		if(!empty($name) && !empty($password)){

			$query = "SELECT a.Account_ID FROM Account a, Customer_Account c
			WHERE User_Name = '$name' 
			AND Password = '$password' 
			AND a.Account_ID = c.Account_ID";

			$result = mysqli_query($conn, $query);
			$user = mysqli_fetch_assoc($result);

			if($user){
				$curCustomerID = $user['Account_ID'];
				$_SESSION['log_in_customer'] = $name;
				$_SESSION['log_in_customer_id'] = $curCustomerID;
				header('Location: customer.php');
			} else {
				$msg = 'log failed!';
			}
		} else {
			$msg = 'Please fill in all fields';
			$msgClass = 'alert-danger';
		}
	}


	if(isset($_POST['submit_Owner'])){
		$name = mysqli_real_escape_string($conn, $_POST['name']);
		$password = mysqli_real_escape_string($conn, $_POST['password']);

		if(!empty($name) && !empty($password)){

			$query = "SELECT * FROM Account, Business_Owner_Account 
			WHERE User_Name = '$name' 
			AND Password = '$password' 
			AND Account.Account_ID = Business_Owner_Account.Account_ID";

			$result = mysqli_query($conn, $query);
			$user = mysqli_fetch_assoc($result);

			if($user){
				echo 'owner log in successfully!';
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
			<p class="navbar-brand">User Sign In</p>
        </div>
      </div>
    </nav>
    <div class="container">	
    	<?php if($msg != ''): ?>
    		<div class="alert <?php echo $msgClass; ?>"><?php echo $msg; ?></div>
    	<?php endif; ?>
      <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
	      <div class="form-group">
		      <label>Name</label>
		      <input type="text" name="name" class="form-control" value="<?php echo isset($_POST['name']) ? $name : ''; ?>">
	      </div>
		  <div class="form-group">
	      	<label>Password</label>
	      	<input type="password" name="password" class="form-control" value="">
	      </div>
	      <br>
	      <button type="submit" name="submit_Customer" class="btn btn-primary">Customer Sign In</button>
		  <button type="submit" name="submit_Owner" class="btn btn-primary">Owner Sign In</button>
      </form>
    </div>
	<?php require('footer.php'); ?>
</body>
</html>