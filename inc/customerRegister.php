<?php 

    require('config/db.php');

    $msg = '';
	$msgClass = '';
    $name = $email = $password = $re_password = $birthday = '';
    $errors = array('name' => '', 'email' => '', 'password' => '', 'rePassword'=>'', 'birthDay'=>'');
    
    if(isset($_POST['register'])) {

        // check username
        if(empty($_POST['name'])){
            $errors['name'] = 'Your username is required';
        } else{
            $name = mysqli_real_escape_string($conn, $_POST['name']);
            if(!preg_match('/^[a-zA-Z0-9\s]+$/', $name)){
                $errors['name'] = 'Username must be letters, digit and space only';
            }
        }

        // check email
        if(empty($_POST['email'])){
            $errors['email'] = 'An email is required';
        } else{
            $email = mysqli_real_escape_string($conn, $_POST['email']);
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $errors['email'] = 'Email must be a valid email address';
            }
        }

        // check password
        if(empty($_POST['password'])){
            $errors['password'] = 'Password cannot be empty';
        } else{
            $password = mysqli_real_escape_string($conn, $_POST['password']);
            if(!preg_match('/^[A-Za-z]\w{3,10}$/', $password)){
                $errors['password'] = 'Password must be 4 to 10 characters which contain only characters, numeric digits, underscore and first character must be a letter';
            }
        }

        // check re-password
        if(empty($_POST['re_password'])){
            $errors['rePassword'] = 'Re-Password cannot be empty';
        } else{
            $re_password = mysqli_real_escape_string($conn, $_POST['re_password']);
            if(!empty($password)) {
                if ($password !== $re_password) {
                    $errors['rePassword'] = 'Re-Password must match with your Password, please check.';
                }
            }
            else{
                $errors['rePassword'] = 'Password cannot be empty.';
            }
        }

        // check birthday
        if(empty($_POST['birthday']))
        {
            $errors['birthDay'] = 'Birthday cannot be empty';
        } else{
            $birthday = mysqli_real_escape_string($conn, $_POST['birthday']);
        }

        if(!array_filter($errors)){
            $maxIDquery = "SELECT max(Account_ID) FROM Account";

            $maxResult = mysqli_query($conn, $maxIDquery);
            $post = mysqli_fetch_assoc($maxResult);

            $newID = (int)$post['max(Account_ID)'] + 1;

            $array = explode("/",$birthday);
            $newBirthday = $array[2] . '-' . $array[0] . '-' . $array[1];

            $accountAddquery = "INSERT INTO Account VALUES('$newID', '$name', '$email', '$password')";
            $customerAddquery = "INSERT INTO Customer_Account VALUES('$newID', '$newBirthday')";

            $accountAddresult = mysqli_query($conn, $accountAddquery);
            $customerAddresult = mysqli_query($conn, $customerAddquery);

            if($accountAddresult && $customerAddresult){
                header('Location: login.php');
                echo 'Owner added successfully!';
            } else {
                $msg =  'Registering failed!';
                $msgClass = 'alert-danger';
            }
        }

    }

    if(isset($_POST['cancel'])) {
        header('Location: ../index.php');
    }

// Close Connection
// mysqli_close($conn);

?>
 
 
 <!DOCTYPE html>
 <html lang="en">
 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Register</title>
     <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/cosmo/bootstrap.min.css">
     <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
     <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
     <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
     <script>
        $( function() {
            $( "#datepicker" ).datepicker();
            var currentDate = $( ".selector" ).datepicker( "getDate" );
        } );
    </script>
 </head>
 <body>
 <nav class="navbar navbar-default">
      <div class="container">
        <div class="navbar-header">    
          <p class="navbar-brand">Customer User Register</p>
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
		      <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($name) ?>" >
              <div class="text-danger"><?php echo $errors['name']; ?></div>
	      </div>
		  <div class="form-group">
	      	<label>Email</label>
	      	<input type="text" name="email" class="form-control" value="<?php echo htmlspecialchars($email) ?>" >
            <div class="text-danger"><?php echo $errors['email']; ?></div>
          </div>
          <div class="form-group">
	      	<label>Password</label>
	      	<input type="password" name="password" class="form-control" value="" >
            <div class="text-danger"><?php echo $errors['password']; ?></div>
          </div>
          <div class="form-group">
	      	<label>Re-Password</label>
	      	<input type="password" name="re_password" class="form-control" value="" >
              <div class="text-danger"><?php echo $errors['rePassword']; ?></div>
	      </div>
          <p>Customer Birthday :
              <input type="text" name ="birthday" id="datepicker">
                <div class="text-danger"><?php echo $errors['birthDay']; ?></div>
          </p>

	      <br>
          <button type="submit" name="register" class="btn btn-primary">Register</button>
	      <button type="submit" name="cancel" class="btn btn-primary">Cancel</button>
      </form>
    </div>
    <?php require('footer.php'); ?>
 </body>
 </html>