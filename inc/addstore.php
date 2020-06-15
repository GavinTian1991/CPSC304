<?php
    require('config/db.php');
    if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }
    $msg = '';
    $msgClass = '';
    $errors = array('shopName' => '', 'address' => '', 'zipCode' => '', 'phoneNumber'=>'', 'region'=>'','groupSize'=>'');
    //Initialize all variables
    $shopName = $address = $zipCode = $phoneNumber ='';
    $regionCode = $hasWifi = $offerDelivery = $goodForGroup = $rating = 0;
    $priceCode = 599;
    $monOpen = $monClose = $tueOpen = $tueClose = $wedOpen = $wedClose = $thurOpen = $thurClose
        = $friOpen = $friClose = $satOpen = $satClose = $sunOpen = $sunClose = '';

    if(isset($_SESSION['logged_owner_id'])){
        $owner_id = $_SESSION['logged_owner_id'];
        $owner_name = $_SESSION['logged_owner_name'];
    }
    else{
        unset($_SESSION['owner_logged_in']);
        unset($_SESSION['logged_owner_name']);
        header('Location: ../index.php');
        exit;
    }

    if(isset($_POST['save_shop']))
    {
        //Check Shop Name
        if(isset($_POST['shop_name']))
        {
            $shopName = mysqli_real_escape_string($conn, $_POST['shop_name']);
            if(strlen($shopName) > 30)
            {
                $errors['shopName'] = "Shop name is too long, 30 characters at most!";
            }
            else
            {
                if(!preg_match('/^[a-zA-Z0-9\s]+$/', $shopName)){
                    $errors['shopName'] = 'Shop name must be letters, digit and space only';
                }
            }
        }
        else
        {
            $errors['shopName'] = "Shop name cannot be empty!";
        }
        //Check shop address
        if(isset($_POST['shop_address']))
        {
            $address = mysqli_real_escape_string($conn, $_POST['shop_address']);
            if(strlen($address) > 60)
            {
                $errors['address'] = "Shop address is too long, 60 characters at most!";
            }
            else
            {
                if(!preg_match('/^[a-zA-Z0-9\s]+$/', $address)){
                    $errors['address'] = "Shop address must be letters, digit and space only";
                }
            }
        }
        else
        {
            $errors['address'] = "Shop address cannot be empty!";
        }
        //Check zip code
        if(isset($_POST['zip_code']))
        {
            $zipCode = mysqli_real_escape_string($conn, $_POST['zip_code']);
            if(!preg_match('/^[0-9A-Z]{3}\s[0-9A-Z]{3}$/', $zipCode))
            {
                $errors['zipCode'] = "Zip Code must be in the form of XXX XXX, digits and capital letters only";
            }
        }
        else
        {
            $errors['zipCode'] = "Zip Code cannot be empty.";
        }

        //Check phone number
        if(isset($_POST['shop_phone']))
        {
            $phoneNumber = mysqli_real_escape_string($conn,$_POST['shop_phone']);
            if(!preg_match('/^\([0-9]{3}\)\s[0-9]{3}\s[0-9]{4}$/', $phoneNumber)){
                $errors['phoneNumber'] = 'Phone number must be in the form of (999) 999 9999!';
            }
        }
        else
        {
            $errors['phoneNumber'] = "Shop phone number cannot be empty!";
        }

        //Check shop location
        if($_POST["shop_location"] != 'none')
        {
            $regionCode = $_POST["shop_location"];
            settype($regionCode,"integer");
            //echo $regionCode;
        }
        else
        {
            $errors['region'] = "You must pick a region where your store locates in.";
        }
        //Check haswifi checkbox
        if(isset($_POST["hasWifi"]))
        {
            $hasWifi = $_POST["hasWifi"];
            settype($hasWifi, "integer");
        }
        //Check hasdelivery checkbox
        if(isset($_POST["hasDelivery"]))
        {
            $offerDelivery = $_POST["hasDelivery"];
            settype($offerDelivery, "integer");
        }
        //Check good for group
        if(isset($_POST["shop-groupsize"]))
        {
            $goodForGroup = mysqli_real_escape_string($conn, $_POST["shop-groupsize"]);
            if(!preg_match('/^[0-9]+$/', $goodForGroup)){
                $errors["shop-groupsize"] = 'Please use digits only';
            }
            else{
                settype($goodForGroup, "integer");
                if($goodForGroup>20)
                {
                    $errors["shop-groupsize"] = 'Maximum value is 20.';
                }
            }
        }//else $goodForGroup is default value 0
        //Check price level
        if($_POST["shop_price"] != 'none')
        {
            $priceCode = $_POST["shop_price"];
            settype($priceCode, "integer");
            //echo $priceCode;
        }//else $priceCode is default value 599
        //Check monday hours
        $monOpen = mysqli_real_escape_string($conn, $_POST["mon_open"]);
        $monClose = mysqli_real_escape_string($conn, $_POST["mon_close"]);

        //Check tuesday hours
        $tueOpen = mysqli_real_escape_string($conn, $_POST["tue_open"]);
        $tueClose = mysqli_real_escape_string($conn, $_POST["tue_close"]);

        //Check wednesday hours
        $wedOpen = mysqli_real_escape_string($conn, $_POST["wed_open"]);
        $wedClose = mysqli_real_escape_string($conn, $_POST["wed_close"]);


        //Check thursday hours
        $thurOpen = mysqli_real_escape_string($conn, $_POST["thur_open"]);
        $thurClose = mysqli_real_escape_string($conn, $_POST["thur_close"]);

        //Check friday hours
        $friOpen = mysqli_real_escape_string($conn, $_POST["fri_open"]);
        $friClose = mysqli_real_escape_string($conn, $_POST["fri_close"]);

        //Check saturday hours
        $satOpen = mysqli_real_escape_string($conn, $_POST["sat_open"]);
        $satClose = mysqli_real_escape_string($conn, $_POST["sat_close"]);

        //Check sunday hours
        $sunOpen = mysqli_real_escape_string($conn, $_POST["sun_open"]);
        $sunClose = mysqli_real_escape_string($conn, $_POST["sun_close"]);


        if(!array_filter($errors)) {
            $msg =  'User input valid!';
            $msgClass = 'alert-danger alert-dismissible';
            //Reformat time data
            $monOpen1 = date("H:i:s", strtotime($monOpen));
            $monClose1 = date("H:i:s", strtotime($monClose));
            $tueOpen1 = date("H:i:s", strtotime($tueOpen));
            $tueClose1 = date("H:i:s", strtotime($tueClose));
            $wedOpen1 = date("H:i:s", strtotime($wedOpen));
            $wedClose1 = date("H:i:s", strtotime($wedClose));
            $thurOpen1 = date("H:i:s", strtotime($thurOpen));
            $thurClose1 = date("H:i:s", strtotime($thurClose));
            $friOpen1 = date("H:i:s", strtotime($friOpen));
            $friClose1 = date("H:i:s", strtotime($friClose));
            $satOpen1 = date("H:i:s", strtotime($satOpen));
            $satClose1 = date("H:i:s", strtotime($satClose));
            $sunOpen1 = date("H:i:s", strtotime($sunOpen));
            $sunClose1 = date("H:i:s", strtotime($sunClose));

            $checkzipcodesql = "SELECT count(*) AS count FROM zipcode_to_region WHERE Zip_Code = '$zipCode';";
            $checkresult = mysqli_query($conn, $checkzipcodesql);
            $checkcount = mysqli_fetch_assoc($checkresult);
            if(!$checkcount["count"]){ //ZIP Code doens't exist if result is false
                $zipcodesql = "INSERT INTO zipcode_to_region VALUES ('$zipCode','$regionCode');";
                $zipcoderesult = mysqli_query($conn, $zipcodesql);
            }
            else
            {
                $zipcoderesult = True;
            }
            mysqli_free_result($checkresult);

            $maxIDquery = "SELECT max(Shop_ID) AS Max_ID FROM milk_tea_shop";
            $result = mysqli_query($conn, $maxIDquery);
            $max = mysqli_fetch_assoc($result);
            mysqli_free_result($result);

            $newID = (int)$max['Max_ID'] + 1;
            $storesql = "INSERT INTO milk_tea_shop VALUES('$newID', '$shopName','$address',
                        '$zipCode','$phoneNumber','$hasWifi','$offerDelivery','$goodForGroup',
                        '$rating','$priceCode','$owner_id')";
            $storeresult = mysqli_query($conn, $storesql);
            echo ($conn->error);




            $hoursql = "INSERT INTO open_at_business_hour VALUES
                        ('$newID','Monday','$monOpen1','$monClose1'),
                        ('$newID','Tuesday','$tueOpen1','$tueClose1'),
                        ('$newID','Wednesday','$wedOpen1','$wedClose1'),
                        ('$newID','Thursday','$thurOpen1','$thurClose1'),
                        ('$newID','Friday','$friOpen1','$friClose1'),
                        ('$newID','Saturday','$satOpen1','$satClose1'),
                        ('$newID','Sunday','$sunOpen1','$sunClose1');";
            $hourresult = mysqli_query($conn,$hoursql);
            echo ($conn->error);
            if($storeresult && $zipcoderesult && $hourresult){
                mysqli_free_result($storeresult);
                mysqli_free_result($zipcoderesult);
                mysqli_free_result($hourresult);
                header('location: ownerprofile.php#stores');
            }
            else{
                $msg = 'Save store information failed!';
                $msgclass = 'alert-danger';
            }
        }
        else
        {
            //print_r($errors);
            $msg =  'User input invalid!';
            $msgClass = 'alert-danger alert-dismissible';
        }
    }

    if(isset($_POST['cancel_shop']))
    {
        header('location: ownerprofile.php');
    }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Store</title>
    <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/cosmo/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <p class="navbar-brand">Hello! <?=$owner_name?> </p>
    <div class="collapse navbar-collapse" >
        <h2 class="navbar-nav mr-auto">
            Add Your Store
        </h2>
    </div>
</nav>

<div class="container">
    <?php if($msg != ''): ?>
        <div class="alert <?php echo $msgClass; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <legend>Shop Information</legend>
        <div class="form-group">
            <label>Shop Name</label>
            <input type="text" name="shop_name" class="form-control" value="<?=htmlspecialchars($shopName)?>" >
            <div class="text-danger"><?=$errors['shopName']; ?></div>
        </div>
        <div class="form-group">
            <label>Shop Address</label>
            <input type="text" name="shop_address" class="form-control" value="<?=htmlspecialchars($address)?>" >
            <div class="text-danger"><?=$errors['address']; ?></div>
        </div>
        <div class="form-group">
            <label>Zip Code</label>
            <input type="text" name="zip_code" class="form-control" value="<?=htmlspecialchars($zipCode)?>" >
            <div class="text-danger"><?=$errors['zipCode']; ?></div>
        </div>
        <div class="form-group">
            <label>Shop Phone Number</label>
            <input type="text" name="shop_phone" class="form-control" value="<?=htmlspecialchars($phoneNumber)?>" >
            <div class="text-danger"><?=$errors['phoneNumber'];?></div>
        </div>
        <div class="form-group">
            <label for="shop_location_select">Shop Region</label>
            <select name="shop_location" class="custom-select" id="shop_location_select">
                <option selected="" value="none">Location</option>
                <option value="401">Richmond</option>
                <option value="402">Vancouver</option>
                <option value="403">Burnaby</option>
                <option value="404">Surrey</option>
                <option value="405">Coquitlam</option>
                <option value="406">Downtown</option>
            </select>
            <div class="text-danger"><?=$errors['region'];?></div>
        </div>
        <div class="form-group">
            <div class="form-check">
                <label class="form-check-label">
                    <input class="form-check-input" type="checkbox" name="hasWifi" value="1" >
                    Does your store provide free Wifi connection?
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="form-check">
                <label class="form-check-label">
                    <input class="form-check-input" type="checkbox" name="hasDelivery" value="1" >
                    Does your store provide delivery service?
                </label>
            </div>
        </div>
        <div class="form-group">
            <label>Good for group? (Indicate the maximum size of a group your store can entertain) </label>
            <input type="text" name="shop-groupsize" class="form-control" value="<?=htmlspecialchars($goodForGroup)?>">
            <div class="text-danger"><?=$errors['groupSize'];?></div>
        </div>
        <div class="form-group">
            <label for="shop_price_select">Price Level</label>
            <select name="shop_price" class="custom-select" id="shop_price_select">
                <option selected="" value="none">Price Level</option>
                <option value="501">$</option>
                <option value="502">$$</option>
                <option value="503">$$$</option>
                <option value="504">$$$$</option>
            </select>
        </div>

        <legend>Business Hour</legend>
        <div class="form-group">
            <label>Monday:</label>
            <input type="time" name="mon_open" class="form-control" value="<?=htmlspecialchars($monOpen)?>" required>
            <input type="time" name="mon_close" class="form-control" value="<?=htmlspecialchars($monClose)?>" required>
        </div>
        <div class="form-group">
            <label>Tuesday:</label>
            <input type="time" name="tue_open" class="form-control" value="<?=htmlspecialchars($tueOpen)?>" required>
            <input type="time" name="tue_close" class="form-control" value="<?=htmlspecialchars($tueClose)?>" required>
        </div>
        <div class="form-group">
            <label>Wednesday:</label>
            <input type="time" name="wed_open" class="form-control" value="<?=htmlspecialchars($wedOpen)?>" required>
            <input type="time" name="wed_close" class="form-control" value="<?=htmlspecialchars($wedClose)?>" required>
        </div>
        <div class="form-group">
            <label>Thursday:</label>
            <input type="time" name="thur_open" class="form-control" value="<?=htmlspecialchars($thurOpen)?>" required>
            <input type="time" name="thur_close" class="form-control" value="<?=htmlspecialchars($thurClose)?>" required>
        </div>
        <div class="form-group">
            <label>Friday:</label>
            <input type="time" name="fri_open" class="form-control" value="<?=htmlspecialchars($friOpen)?>" required>
            <input type="time" name="fri_close" class="form-control" value="<?=htmlspecialchars($friClose)?>" required>
        </div>
        <div class="form-group">
            <label>Saturday:</label>
            <input type="time" name="sat_open" class="form-control" value="<?=htmlspecialchars($satOpen)?>" required>
            <input type="time" name="sat_close" class="form-control" value="<?=htmlspecialchars($satClose)?>" required>
        </div>
        <div class="form-group">
            <label>Sunday:</label>
            <input type="time" name="sun_open" class="form-control" value="<?=htmlspecialchars($sunOpen)?>" required>
            <input type="time" name="sun_close" class="form-control" value="<?=htmlspecialchars($sunClose)?>" required>
        </div>
        <br>
        <button type="submit" name="save_shop" class="btn btn-primary">Save</button>
        <button type="submit" name="cancel_shop" class="btn btn-primary" formnovalidate>Cancel</button>
    </form>
</div>
<?php include('footer.php'); ?>
</body>
</html>
