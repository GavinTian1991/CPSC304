<?php
    require('config/db.php');
    if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }
    $msg = '';
    $msgClass = '';
    $errors = array('name' => '', 'description' => '', 'price' => '', 'stype'=>'','type'=>'');
    //Initialize all variables
    $name = $description = $price = $stype = '';
    $price = '';
    $hotCold = 3; //By default it is both hot or cold
    $typeCode = 1301; //By default it is milk tea

    if(isset($_SESSION['logged_owner_name'],$_SESSION['current_shop_id'])){
        $shop_id = $_SESSION['current_shop_id'];
        //echo "$shop_id"."<br>";
        $shop_name = $_SESSION['current_shop_name'];
        $owner_name = $_SESSION['logged_owner_name'];
    }
    else{
        session_unset();
        header('Location: ../index.php');
        exit;
    }

    if(isset($_POST['save_drink']))
    {
        //Check Drink Name
        if(isset($_POST['drink_name']))
        {
            $name = mysqli_real_escape_string($conn, $_POST['drink_name']);
            if(strlen($name) > 30)
            {
                $errors['name'] = "Drink name is too long, 30 characters at most!";
            }
            else
            {
                if(!preg_match('/^[a-zA-Z0-9\s]+$/', $name)){
                    $errors['name'] = 'Drink name must be letters, digit and space only';
                }
            }
        }
        else
        {
            $errors['name'] = "Drink name cannot be empty!";
        }
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
            echo $typeCode;
        }

        if(!array_filter($errors)) { //check errors array if empty then no error
            $msg =  'User input valid!';
            $msgClass = 'alert-success';
            $maxIDquery = "SELECT max(Drink_ID) AS Max_ID FROM drinks";
            $maxresult = mysqli_query($conn, $maxIDquery);
            $max = mysqli_fetch_assoc($maxresult);
            mysqli_free_result($maxresult);

            $newID = (int)$max['Max_ID'] + 1;
            $istypeofSql = "INSERT INTO drink_is_typeof VALUES('$name','$typeCode')";
            $istypeofResult = mysqli_query($conn, $istypeofSql);
            echo ($conn->error);

            $drinkSql = "INSERT INTO drinks VALUES('$newID', '$name','$description','$price','$hotCold','$stype');";
            $drinkResult = mysqli_query($conn, $drinkSql);
            echo ($conn->error);

            $offerbySql = "INSERT INTO drink_offered_by VALUES('$newID','$shop_id');";
            $offerbyResult = mysqli_query($conn, $offerbySql);
            echo ($conn->error);



            if($drinkResult && $offerbyResult && $istypeofResult){
                mysqli_free_result($drinkResult);
                mysqli_free_result($offerbyResult);
                mysqli_free_result($istypeofResult);
                header('location: shopmanage.php');
            }
            else{
                $msg = 'Save drink information failed!';
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
            <h2 class="navbar-brand">Hello <?=$owner_name?>, Add A Drink To <?=$shop_name?></h2>
        </div>
    </div>
</nav>
<?php include('drinkTemplate.php')?>
<?php include('footer.php'); ?>
</body>
</html>
