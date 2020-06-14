<?php
    require('config/db.php');

    if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }
    $msg = '';
    $msgClass = '';

        
    $curCustomerName = '';
    $curCustomerID = $_SESSION['log_in_customer_id'];


    if(isset($_POST['submit_new_general'])){
      $newName = mysqli_real_escape_string($conn, $_POST['new_name']);
      $newEmail = mysqli_real_escape_string($conn, $_POST['new_email']);
      $newBirthday = mysqli_real_escape_string($conn, $_POST['new_birthday']);

      if(!empty($newName) && !empty($newEmail) && !empty($newBirthday)) {
        $validEmail = filter_var($newEmail, FILTER_VALIDATE_EMAIL);

        if($validEmail === false) {
            $msg = 'Please use a valid email.';
            $msgClass = 'alert-danger';
        }
        else {
            $accountGeneralQuery = "UPDATE Account 
            SET User_Name = '$newName', Email = '$newEmail'
            WHERE Account_ID = '$curCustomerID'";

            $customerBirthdayQuery = "UPDATE Customer_Account 
            SET Birthdate = '$newBirthday'
            WHERE Account_ID = '$curCustomerID'";

            $accountGeneralResult = mysqli_query($conn, $accountGeneralQuery);
            $customerBirthdayResult = mysqli_query($conn, $customerBirthdayQuery);

            if($accountGeneralResult && $customerBirthdayResult){
                $msg = 'Update successfully!';
                $msgClass = 'alert-success';
            } else {
                $msg = 'Update failed!';
                $msgClass = 'alert-danger';
            }
        }

      }
    }

    if(isset($_POST['submit_new_password'])){

      $oldPassword = mysqli_real_escape_string($conn, $_POST['old_pass']);
      $newPassword = mysqli_real_escape_string($conn, $_POST['new_pass']);
      $reNewPassowrd = mysqli_real_escape_string($conn, $_POST['re_new_pass']);

      if(!empty($oldPassword) && !empty($newPassword) && !empty($reNewPassowrd)) {

        $oldPasswordQuery = "SELECT Password FROM Account WHERE Account_ID = '$curCustomerID'";
        $oldPasswordResult = mysqli_query($conn, $oldPasswordQuery);
        $oldPasswordPosts = mysqli_fetch_assoc($oldPasswordResult);

        $oldDBPassword = $oldPasswordPosts['Password'];

        if($oldDBPassword != $oldPassword) {
          $msg = 'Old input password is not correct.';
          $msgClass = 'alert-danger';
        } else if($newPassword != $reNewPassowrd) {
          $msg = 'New password and re input passward is not the same.';
          $msgClass = 'alert-danger';
        } else if ($oldDBPassword == $newPassword) {
          $msg = 'Old and new password is same.';
          $msgClass = 'alert-danger';
        } else {
          $accountPasswordQuery = "UPDATE Account 
          SET Password = '$newPassword'
          WHERE Account_ID = '$curCustomerID'";

          $accountPasswordResult = mysqli_query($conn, $accountPasswordQuery);

          if($accountPasswordResult){
              $msg = 'Password update successfully!';
              $msgClass = 'alert-success';
          } else {
            $msg = 'Update password failed.';
            $msgClass = 'alert-danger';
          }
        }
      }
    }

    $generalProfileQuery = "SELECT User_Name, Email FROM Account WHERE Account_ID = '$curCustomerID'";
    $generalProfileResult = mysqli_query($conn, $generalProfileQuery);
    $generalProfilePosts = mysqli_fetch_assoc($generalProfileResult);

    $birthdayQuery = "SELECT Birthdate FROM Customer_Account WHERE Account_ID = '$curCustomerID'";
    $birthdayResult = mysqli_query($conn, $birthdayQuery);
    $birthdayPosts = mysqli_fetch_assoc($birthdayResult);

    $curCustomerEmail = $generalProfilePosts['Email'];
    $curCustomerName = $generalProfilePosts['User_Name'];
    $curCustomerBirthday = $birthdayPosts['Birthdate'];

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile</title>
  <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/cosmo/bootstrap.min.css">
  <!-- <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous"> -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>


</head>

<script type="text/javascript">
    window.onload=function(){
      $("#general_button").hide();
      $("#password_button").hide();
    }
</script>

<body>
    <?php require('cnavbar.php'); ?>
    <?php if($msg != ''): ?>
    		<div class="alert <?php echo $msgClass; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>

    <div class="row">
    <div class="col-sm">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <div class="card border-primary mb-3" style="max-width: 30rem;">
            <div class="card-header">General</div>
            <div class="card-body">
              <div class="form-group">
                <label>Name</label>
                <input id="new_name_text" type="text" name="new_name" class="form-control" value="<?php echo $curCustomerName?>" required>
                <script type="text/javascript">
                    $("#new_name_text").bind("change paste keyup", function() {
                      $("#general_button").show();
                    });
                </script>
              </div>
              <div class="form-group">
                <label>Email</label>
                <input id="new_email_text" type="text" name="new_email" class="form-control" value="<?php echo $curCustomerEmail?>" required>
                <script type="text/javascript">
                    $("#new_email_text").bind("change paste keyup", function() {
                      $("#general_button").show();
                    });
                </script>
              </div>
              <div class="form-group">
                <label>Birthday</label>
                <br>
                <input id="new_birthday_text" type="text" name ="new_birthday" id="datepicker" value="<?php echo $curCustomerBirthday?>">
                <script type="text/javascript">
                    $("#new_birthday_text").bind("change paste keyup", function() {
                      $("#general_button").show();
                    });
                </script>
              </div>
            </div>
            <button id="general_button" type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal">
                Submit
            </button>

            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Warning</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    Are you sure to save changes?
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="submit_new_general" class="btn btn-primary">Save changes</button>
                  </div>
                </div>
              </div>
            </div>



          </div>
        </form>
      </div>
      <div class="col-sm">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <div class="card border-primary mb-3" style="max-width: 30rem;">
              <div class="card-header">Password</div>
              <div class="card-body">
                <div class="form-group">
                    <label>Old Password</label>
                    <input id="old_password_input" type="text" name="old_pass" class="form-control" value="" required>
                    <script type="text/javascript">
                    $("#old_password_input").bind("change paste keyup", function() {
                      $("#password_button").show();
                    });
                </script>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="text" name="new_pass" class="form-control" value="" required>
                </div>
                <div class="form-group">
                    <label>Re New Password</label>
                    <input type="text" name="re_new_pass" class="form-control" value="" required>
                  </div>
                </div>
                <button id="password_button" type="button" class="btn btn-primary" data-toggle="modal" data-target="#passwordModal">
                  Submit
                </button>


                            <!-- Modal -->
            <div class="modal fade" id="passwordModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Warning</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    Are you sure to update password?
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" name="submit_new_password" class="btn btn-primary">Save changes</button>
                  </div>
                </div>
              </div>
            </div>








          </div>
        </form>
      </div>
    </div>
      <div class="card border-primary mb-3">
          <div class="card-header">Comment</div>
          <div class="card-body">
      </div>
  </div>
   <?php include('footer.php'); ?>
</body>
</html>