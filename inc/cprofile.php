<?php
    require('config/db.php');

    if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }
    $msg = '';
    $msgClass = '';
    $commentIndex = 0;

        
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


    if(isset($_POST['submit_new_comment']) || isset($_POST['delete_comment'])){

      if(isset($_POST['submit_new_comment'])) {
        $newCommentindex = $_POST['submit_new_comment'];
      } else {
        $newCommentindex = $_POST['delete_comment'];
      }

      $newCommentContent = $_POST['new_comment_array'][$newCommentindex];
      $newCommentRating = $_POST['new_rating_array'][$newCommentindex];
      $newCommentShopName = $_POST['new_comment_shop'][$newCommentindex];
      $newCommentShopID = $_POST['new_comment_shop_ID'][$newCommentindex];
      $newCommentDate = $_POST['new_comment_date'][$newCommentindex];

      $accountCommentResult = 0;
      $commentDeleteResult = 0;


      if(isset($_POST['submit_new_comment'])) {
        
        $accountCommentQuery = "UPDATE Comments_from_Customer 
        SET Contents = '$newCommentContent', Rating_Level = '$newCommentRating'
        WHERE Account_ID = '$curCustomerID' AND Shop_ID = '$newCommentShopID'";

        $accountCommentResult = mysqli_query($conn, $accountCommentQuery);

      }

      if(isset($_POST['delete_comment'])) {

        $commentDeleteQuery = "DELETE FROM Comments_from_Customer 
        WHERE Account_ID = '$curCustomerID' AND Shop_ID = '$newCommentShopID'";

        $commentDeleteResult = mysqli_query($conn, $commentDeleteQuery);

      }



      //update current shop rating
      $ratingquery = "SELECT Rating_Level FROM Comments_from_Customer WHERE Shop_ID = '$newCommentShopID'";
      $ratingresult = mysqli_query($conn, $ratingquery);
      $ratings = mysqli_fetch_all($ratingresult, MYSQLI_ASSOC);
    
      $sumrating = 0.0;
      $avgrating = 0.0;
      $ratingcount = 0;
    
      foreach($ratings as $rating) {
          $sumrating = $sumrating + $rating['Rating_Level'];
          $ratingcount = $ratingcount + 1;
      }

      if($ratingcount != 0) { 
          $avgrating = (float)$sumrating / $ratingcount;
      } else {
          $avgrating = 0.0;
      }
      $ratingsetquery = "UPDATE Milk_Tea_Shop 
      SET Average_Rating = '$avgrating'
      WHERE Shop_ID = '$newCommentShopID'";

      $shopAverageRatingResult = mysqli_query($conn, $ratingsetquery);

      if($accountCommentResult && $shopAverageRatingResult){
          $msg = 'Comment update successfully!';
          $msgClass = 'alert-success';
      } else if ($commentDeleteResult && $shopAverageRatingResult){
          $msg = 'Comment delete successfully!';
          $msgClass = 'alert-success';
      }
      else {
        $msg = 'Comment update failed.';
        $msgClass = 'alert-danger';
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
      //$(".btn-primary").hide();
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
                <input id="new_birthday_text" type="text" name ="new_birthday" id="datepicker" value="<?php echo $curCustomerBirthday?>" required>
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
                    <button type="submit" name="submit_new_general" class="btn btn-secondary">Save changes</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Modal -->




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
                    <button type="submit" name="submit_new_password" class="btn btn-secondary">Save changes</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>


    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <div class="card border-primary mb-3">
          <div class="card-header">Comment</div>
            <div class="card-body">
                <?php 

                  $cusCommentQuery = "SELECT cfc.Comment_ID, cfc.Contents, cfc.Rating_Level, cfc.Date, mts.Shop_Name, mts.Shop_ID
                  FROM Comments_from_Customer cfc, Milk_Tea_Shop mts 
                  WHERE cfc.Account_ID = '$curCustomerID' 
                  AND cfc.Shop_ID = mts.Shop_ID";
                  $cusCommentResult = mysqli_query($conn, $cusCommentQuery);
                  $cusCommentPosts = mysqli_fetch_all($cusCommentResult, MYSQLI_ASSOC);
                ?>
                
                <?php foreach($cusCommentPosts as $comment) : ?>
                  <div class="row">
                      <div class="col-sm">
                        <div class="form-group">
                          <input type="text" name="new_comment_array[]" class="form-control" value="<?php echo $comment['Contents']?>" required>
                        </div>
                      </div>
                      <div class="col-sm">
                          <select class="form-control" name="new_rating_array[]" required>
                              <option selected="selected">
                                <?php echo $comment['Rating_Level']?>
                              </option>
                              <option value="1">1</option>
                              <option value="2">2</option>
                              <option value="3">3</option>
                              <option value="4">4</option>
                              <option value="5">5</option>
                          </select>
                      </div>
                      <div class="col-sm">
                          <input type="text" name="new_comment_shop[]" class="form-control" value="<?php echo $comment['Shop_Name']?>" readonly="true" required>
                      </div>
                      <div class="col-sm">
                        <input type="text" name="new_comment_date[]" class="form-control" value="<?php 
                              $rd = $comment['Date'];
                              $disrdate = date('Y-m-d',strtotime($rd));
                              echo $disrdate?>" readonly="true" required>
                      </div>
                      <div class="col-sm">
                        <input type="hidden" name="new_comment_shop_ID[]" value="<?php echo $comment['Shop_ID']?>">
                        <button type="submit" name="submit_new_comment" value="<?php echo $commentIndex?>" class="btn btn-primary">Submit</button>
                      </div>
                      <div class="col-sm">
                        <button type="submit" name="delete_comment" value="<?php echo $commentIndex++?>" class="btn btn-warning" formnovalidate>Delete</button>
                      </div>
                  </div>
                <?php endforeach; ?>
            </div>
        </div>
      </form>
   <?php include('footer.php'); ?>
</body>
</html>