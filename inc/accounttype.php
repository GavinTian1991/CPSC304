<?php
    require('./config/db.php');

    if(isset($_POST['Customer'])) {
        header('Location: ./customerRegister.php');
    }

    if(isset($_POST['ShopOwner'])) {
        header('Location: ./ownerRegister.php');
    }

    if(isset($_POST['Cancel'])) {
        header('Location: ../index.php');
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Type Choice Page</title>
    <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/cosmo/bootstrap.min.css">
</head>
<body>
<h2 style="text-align:center">Welcome! What type of user account are you attempting to register?</h2>

<div class="container">
    <div class="vertical-center">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <button type="submit" name="Customer" class="btn btn-primary">Customer</button>
            <button type="submit" name="ShopOwner" class="btn btn-primary">Shop Owner</button>
            <button type="submit" name="Cancel" class="btn btn-primary">Cancel</button>
        </form>
    </div>
</div>

<?php require('./footer.php'); ?>
</body>
</html>
