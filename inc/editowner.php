<?php
    require('config/db.php');

    if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }
    $cur_owner_id = '';
    $cur_owner_name = '';


    $msg = '';
    $msgClass = '';
    if(!isset($_SESSION['owner_logged_in'])){
        header('Location:../index.php');
        exit;
    }
    else{
        $cur_owner_name = $_SESSION['logged_owner_name'];
        $cur_owner_id = $_SESSION['logged_owner_id'];
    }

    if(isset($_POST['submit_new_general'])){
        $newName = mysqli_real_escape_string($conn, $_POST['new_name']);
        $newEmail = mysqli_real_escape_string($conn, $_POST['new_email']);
        $newLicense = mysqli_real_escape_string($conn, $_POST['new_license']);

        if(!empty($newName) && !empty($newEmail) && !empty($newLicense)) {
            $validEmail = filter_var($newEmail, FILTER_VALIDATE_EMAIL);

            if(!preg_match('/^[0-9]{2}\-[0-9]{6}$/', $newLicense)){
                $validLicense = False;
            }
            else{
                $validLicense = True;
            }


            if($validEmail === false || $validLicense === false) {
                $msg = 'Please verify your email format or your license format, license format need to be 99-999999.';
                $msgClass = 'alert-danger';
            }
            else {
                $accountGeneralQuery = "UPDATE Account 
                SET User_Name = '$newName', Email = '$newEmail'
                WHERE Account_ID = '$cur_owner_id'";

                $customerBirthdayQuery = "UPDATE business_owner_account 
                SET Business_License = '$newLicense'
                WHERE Account_ID = '$cur_owner_id'";

                $accountGeneralResult = mysqli_query($conn, $accountGeneralQuery);
                $ownerLicenseResult = mysqli_query($conn, $customerBirthdayQuery);

                if($accountGeneralResult && $ownerLicenseResult){
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

            $oldPasswordQuery = "SELECT Password FROM Account WHERE Account_ID = '$cur_owner_id'";
            $oldPasswordResult = mysqli_query($conn, $oldPasswordQuery);
            $oldPasswordPosts = mysqli_fetch_assoc($oldPasswordResult);

            $oldDBPassword = $oldPasswordPosts['Password'];

            if($oldDBPassword != $oldPassword) {
                $msg = 'Old input password is not correct.';
                $msgClass = 'alert-danger';
            } else if($newPassword != $reNewPassowrd) {
                $msg = 'New password and re input password are not the same.';
                $msgClass = 'alert-danger';
            } else if ($oldDBPassword == $newPassword) {
                $msg = 'Old and new password is same.';
                $msgClass = 'alert-danger';
            } else {
                $accountPasswordQuery = "UPDATE Account 
                SET Password = '$newPassword'
                WHERE Account_ID = '$cur_owner_id'";

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

    $generalProfileQuery = "SELECT User_Name, Email FROM Account WHERE Account_ID = '$cur_owner_id'";
    $generalProfileResult = mysqli_query($conn, $generalProfileQuery);
    $generalProfilePosts = mysqli_fetch_assoc($generalProfileResult);

    $licenseQuery = "SELECT Business_License FROM business_owner_account WHERE Account_ID = '$cur_owner_id'";
    $licenseResult = mysqli_query($conn, $licenseQuery);
    $licensePosts = mysqli_fetch_assoc($licenseResult);

    $curOwnerEmail = $generalProfilePosts['Email'];
    $curCustomerName = $generalProfilePosts['User_Name'];
    $curOwnerLicense = $licensePosts['Business_License'];

    if(isset($_POST['owner_edit_quit']))
    {
        header("Location: ownerprofile.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Owner Profile</title>
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
    <?php if($msg != ''): ?>
    		<div class="alert <?php echo $msgClass; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="#">Hello <?php echo $cur_owner_name . ' '?></a>
        <div class="collapse navbar-collapse" >
<!--            <ul class="navbar-nav center">-->
<!--                <li class="nav-item">-->
<!--                    <a class="nav-link" href="addDrink.php">Add Drink</a>-->
<!--                </li>-->
<!--                <li class="nav-item">-->
<!--                    <a class="nav-link" href="event.php">Add Event</a>-->
<!--                </li>-->
<!--            </ul>-->
            <form class="mr-auto form-inline my-2 my-lg-0" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <button class="btn btn-secondary my-2 my-sm-0" type="submit" name="owner_edit_quit">Back</button>
            </form>
        </div>
    </nav>
    <div class="row">
    <div class="col-sm">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
          <div class="card border-primary mb-3" style="max-width: 30rem;">
            <div class="card-header">General Information</div>
            <div class="card-body">
              <div class="form-group">
                <label>User Name</label>
                <input id="new_name_text" type="text" name="new_name" class="form-control" value="<?php echo $cur_owner_name?>" required>
                <script type="text/javascript">
                    $("#new_name_text").bind("change paste keyup", function() {
                      $("#general_button").show();
                    });
                </script>
              </div>
              <div class="form-group">
                <label>Email</label>
                <input id="new_email_text" type="text" name="new_email" class="form-control" value="<?php echo $curOwnerEmail?>" required>
                <script type="text/javascript">
                    $("#new_email_text").bind("change paste keyup", function() {
                      $("#general_button").show();
                    });
                </script>
              </div>
              <div class="form-group">
                <label>Business License</label>
                <br>
                <input id="new_license_text" type="text" name ="new_license" class="form-control" value="<?php echo $curOwnerLicense?>" required>
                <script type="text/javascript">
                    $("#new_license_text").bind("change paste keyup", function() {
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
                    <input id="old_password_input" type="password" name="old_pass" class="form-control" value="" required>
                    <script type="text/javascript">
                    $("#old_password_input").bind("change paste keyup", function() {
                      $("#password_button").show();
                    });
                </script>
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_pass" class="form-control" value="" required>
                </div>
                <div class="form-group">
                    <label>Re New Password</label>
                    <input type="password" name="re_new_pass" class="form-control" value="" required>
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
  </div>
   <?php include('footer.php'); ?>
</body>
</html>