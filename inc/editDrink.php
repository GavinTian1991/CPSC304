<?php
    require('config/db.php');
    if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }
    $cur_owner_id = $cur_shop_name ='';

    $msg = '';
    $msgClass = '';
    $errors = array('name' => '', 'description' => '', 'price' => '', 'stype'=>'','type'=>'');
    if(!isset($_SESSION['owner_logged_in']))
    {
        header('Location:../index.php');
        exit;
    }
    else{
        $cur_owner_id = $_SESSION['logged_owner_id'];
        $cur_shop_name = $_SESSION['current_shop_name'];
    }

    if(!isset($_SESSION['current_drink_id'])){
        if(isset($_GET['id'])){
            $cur_drink_id = mysqli_real_escape_string($conn,$_GET['id']);
            settype($cur_drink_id, 'integer');
            $_SESSION['current_drink_id'] = $cur_drink_id;
        }
        else
        {
            header('Location:shopmanage.php');
            exit;
        }
    }
    else {
        $cur_drink_id = $_SESSION['current_drink_id'];
    }

    $oldDrinkSql = "SELECT d.Drink_Name, d.Description, d.Price, d.Specialized_Type_Name AS SType_Name
                        FROM Drinks d, drink_is_typeof dit 
                        WHERE d.Drink_ID = '$cur_drink_id' AND d.Drink_Name = dit.Drink_Name;";
    $oldDrinkResult = mysqli_query($conn, $oldDrinkSql);
    $oldDrink = mysqli_fetch_assoc($oldDrinkResult);
    $name = $oldDrink['Drink_Name'];
    $description = $oldDrink['Description'];
    $price = $oldDrink['Price'];
    $stype = $oldDrink['SType_Name'];
    $hotCold = 3; //By default it is both hot or cold
    $typeCode = 1301; //By default it is milk tea

    if(isset($_POST['save_drink']))
    {
        //Check Drink Description
        if(isset($_POST['drink_description']))
        {
            $description = mysqli_real_escape_string($conn, $_POST['drink_description']);
            if(strlen($description) > 200)
            {
                $errors['description'] = "Your description is too long, 200 characters at most!";
            }
        }//Description can be empty

        //Check Drink Price
        if(isset($_POST['drink_price']))
        {
            $price = mysqli_real_escape_string($conn, $_POST['drink_price']);
            if(!preg_match('/^[0-9]{1,2}\.[0-9]{1,2}$/', $price))
            {
                $errors['price'] = "Price must be within the range of 0.0 and 99.99, two decimals at most.";
            }
            settype($price, 'double');
        }
        else
        {
            $errors['price'] = "Price cannot be empty.";
        }

        //Check hot or code
        $hotCold = mysqli_real_escape_string($conn,$_POST['drink_hot_or_cold']);
        settype($hotCold, 'integer');
        //Check special drink type
        if(isset($_POST['drink_stype']))
        {
            $stype = mysqli_real_escape_string($conn, $_POST['drink_stype']);
            if(strlen($stype) > 20)
            {
                $errors['stype'] = "Type name is too long, 20 characters at most!";
            }
        }
        else
        {
            $errors['stype'] = "Drink in store type cannot be empty!";
        }
        //Check drink general type
        if($_POST["drink_type"] != 'none')
        {
            $typeCode = $_POST["drink_type"];
            settype($typeCode,"integer");
        }

        if(!array_filter($errors)) { //check errors array if empty then no error
            $msg =  'User input valid!';
            $msgClass = 'alert-success';

            $updatetypeofSql = "UPDATE drink_is_typeof 
            SET Shared_Type_ID = '$typeCode'
            WHERE Drink_Name = '$name'";
            $updatetypeofResult = mysqli_query($conn, $updatetypeofSql);
            echo ($conn->error);

            $updatedrinkSql = "UPDATE drinks 
            SET Description = '$description', Price = '$price', Hot_or_Cold = '$hotCold',Specialized_Type_Name ='$stype'
            WHERE Drink_ID = '$cur_drink_id'";
            $updateDrinkResult = mysqli_query($conn, $updatedrinkSql);
            echo ($conn->error);



            if($updateDrinkResult && $updatetypeofResult){
                $msg =  'Update data succeeded!';
                $msgClass = 'alert-success ';
                unset($_SESSION['current_drink_id']);
                header('location: shopmanage.php');
            }
            else{
                $msg = 'Update drink information failed!';
                $msgclass = 'alert-danger';
            }
        }
        else
        {
            $msg =  'User input invalid!';
            $msgClass = 'alert-danger ';
        }
    }
    if(isset($_POST['cancel_drink']))
    {
        unset($_SESSION['current_drink_id']);
        header('Location: shopmanage.php');
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Drink</title>
    <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/cosmo/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <h2 class="navbar-brand">Edit The Drink Of <?=$cur_shop_name?></h2>
        </div>
    </div>
</nav>
<?php include('drinkTemplate.php')?>
<?php include('footer.php'); ?>
</body>
</html>