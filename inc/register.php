<?php 

    require('config/db.php');

    $msg = '';
	$msgClass = '';

    if(isset($_POST['owner_register'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $re_password = mysqli_real_escape_string($conn, $_POST['re_password']);
        $lisense = mysqli_real_escape_string($conn, $_POST['lisense']);

        if(!empty($name) && !empty($email) && !empty($password) && !empty($re_password) && !empty($lisense)) {

            $validEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
            $validPassword = $password !== $re_password;

            if($validEmail === false || $validPassword) {
                if($validEmail === false) {
                    $msg = 'Please use a valid email.';
                } else {
                    $msg = 'Password is not the same.';
                }
                $msgClass = 'alert-danger';
            }
            else {
                $maxIDquery = "SELECT max(Account_ID) FROM Account";
                $maxResult = mysqli_query($conn, $maxIDquery);
                $post = mysqli_fetch_assoc($maxResult);
        
                $newID = (int)$post['max(Account_ID)'] + 1;

                $accountAddquery = "INSERT INTO Account VALUES('$newID', '$name', '$email', '$password')";
                $ownerAddquery = "INSERT INTO Business_Owner_Account VALUES('$newID', '$lisense')";

                $accountAddresult = mysqli_query($conn, $accountAddquery);
                $ownerAddresult = mysqli_query($conn, $ownerAddquery);

                if($accountAddresult && $ownerAddresult){
                    header('Location: login.php');
                    echo 'owner added successfully!';
                } else {
                    echo 'Added failed!';
                }
            }
        }
    }
    
    if(isset($_POST['customer_register'])) {
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = mysqli_real_escape_string($conn, $_POST['password']);
        $re_password = mysqli_real_escape_string($conn, $_POST['re_password']);
        $birthday = mysqli_real_escape_string($conn, $_POST['birthday']);

        if(!empty($name) && !empty($password) && !empty($re_password) && !empty($birthday)) {
            $validEmail = filter_var($email, FILTER_VALIDATE_EMAIL);
            $validPassword = $password !== $re_password;

            if($validEmail === false || $validPassword) {
                if($validEmail === false) {
                    $msg = 'Please use a valid email.';
                } else {
                    $msg = 'Password is not the same.';
                }
                $msgClass = 'alert-danger';
            } else {
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
                    echo 'owner added successfully!';
                } else {
                    echo 'Added failed!';
                }
            }
        }
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
          <p class="navbar-brand">User Register</p>
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
		      <input type="text" name="name" class="form-control" value="" required>
	      </div>
		  <div class="form-group">
	      	<label>Email</label>
	      	<input type="text" name="email" class="form-control" value="" required>
          </div>
          <div class="form-group">
	      	<label>Password</label>
	      	<input type="password" name="password" class="form-control" value="" required>
          </div>
          <div class="form-group">
	      	<label>Re-Password</label>
	      	<input type="password" name="re_password" class="form-control" value="" required>
	      </div>
          <div class="form-group">
	      	<label>Business License (Owner Register Only) </label>
	      	<input type="text" name="lisense" class="form-control" value="">
	      </div>
          <p>Customer Birthday (Customer Register Only): 
              <input type="text" name ="birthday" id="datepicker">
          </p>
	      <br>
	      <button type="submit" name="customer_register" class="btn btn-primary">Customer Register</button>
          <button type="submit" name="owner_register" class="btn btn-primary">Owner Register</button>
      </form>
    </div>
    <?php require('footer.php'); ?>
 </body>
 </html>