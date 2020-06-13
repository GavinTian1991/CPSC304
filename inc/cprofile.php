<?php
    require('config/db.php');

    if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }
    $msg = '';
	  $msgClass = 'alert-danger';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Profile</title>
  <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/cosmo/bootstrap.min.css">
</head>
<body>
    <?php require('cnavbar.php'); ?>
    <?php if($msg != ''): ?>
    		<div class="alert <?php echo $msgClass; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <div class="row">
    <div class="col-sm">
        <div class="card border-primary mb-3" style="max-width: 40rem;">
          <div class="card-header">General</div>
          <div class="card-body">
            <div class="form-group">
              <label>Name</label>
              <input type="text" name="name" class="form-control" value="<?php echo isset($_POST['name']) ? $name : ''; ?>">
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="text" name="name" class="form-control" value="<?php echo isset($_POST['name']) ? $name : ''; ?>">
            </div>
          </div>
          <button type="submit" name="submit_new_general" class="btn btn-primary">Submit</button>
        </div>
      </div>
      <div class="col-sm">
        <div class="card border-primary mb-3" style="max-width: 40rem;">
            <div class="card-header">Password</div>
            <div class="card-body">
              <div class="form-group">
                  <label>Old Password</label>
                  <input type="text" name="name" class="form-control" value="">
              </div>
              <div class="form-group">
                  <label>New Password</label>
                  <input type="text" name="name" class="form-control" value="">
              </div>
              <div class="form-group">
                  <label>Re New Password</label>
                  <input type="text" name="name" class="form-control" value="">
                </div>
              </div>
              <button type="submit" name="submit_new_password" class="btn btn-primary">Submit</button>
        </div>
      </div>
    </div>
    <div class="card border-primary mb-3">
        <div class="card-header">Comment</div>
        <div class="card-body">
      </div>
    </div>
   </form>
   <?php include('footer.php'); ?>
</body>
</html>