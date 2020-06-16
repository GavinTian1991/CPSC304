<?php
    require('config/db.php');
    if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }
    $cur_owner_id = $cur_shop_name ='';

    $msg = '';
    $msgClass = '';
    $openTime = $closeTime = '';

    if(!isset($_SESSION['owner_logged_in'],$_SESSION['current_shop_id']))
    {
        header('Location:../index.php');
        exit;
    }
    else{
        $cur_owner_id = $_SESSION['logged_owner_id'];
        $cur_shop_name = $_SESSION['current_shop_name'];
        $cur_shop_id = $_SESSION['current_shop_id'];
    }

    $errors = array('shopName' => '', 'address' => '', 'zipCode' => '', 'phoneNumber'=>'', 'region'=>'','groupSize'=>'');
    //Initialize all variables
    $shopName = $address = $zipCode = $phoneNumber ='';
    $regionCode = $hasWifi = $offerDelivery = $goodForGroup = $rating = 0;
    $priceCode = 599;

    $oldStoreSql = "SELECT Shop_Name, Address, Zip_Code, Phone_Number, Has_Wifi, Offer_Delivery, Good_For_Group, Price_ID, Name
                    FROM milk_tea_shop m, price_level p
                    WHERE Shop_ID = '$cur_shop_id' AND Owner_ID = '$cur_owner_id' AND m.Price_ID = p.Level_ID";
    $oldStoreResult = mysqli_query($conn, $oldStoreSql);
    $oldStorePost = mysqli_fetch_assoc($oldStoreResult);
    echo $conn->error;
    $shopName = $oldStorePost['Shop_Name'];
    $address = $oldStorePost['Address'];
    $zipCode = $oldStorePost['Zip_Code'];
    $phoneNumber = $oldStorePost['Phone_Number'];
    $hasWifi = $oldStorePost['Has_Wifi'];
    $offerDelivery = $oldStorePost['Offer_Delivery'];
    $goodForGroup = $oldStorePost['Good_For_Group'];
    $priceCode = $oldStorePost['Price_ID'];
    $priceName = $oldStorePost['Name'];

    if(isset($_POST['save_edit_store'])) {
        //Check Shop Name
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
        //Check Shop Address
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
        //Check zip code
        $zipCode = mysqli_real_escape_string($conn, $_POST['zip_code']);
        if(!preg_match('/^[0-9A-Z]{3}\s[0-9A-Z]{3}$/', $zipCode))
        {
            $errors['zipCode'] = "Zip Code must be in the form of XXX XXX, digits and capital letters only";
        }
        //Check phone number
        $phoneNumber = mysqli_real_escape_string($conn,$_POST['shop_phone']);
        if(!preg_match('/^\([0-9]{3}\)\s[0-9]{3}\s[0-9]{4}$/', $phoneNumber)){
            $errors['phoneNumber'] = 'Phone number must be in the form of (999) 999 9999!';
        }

        //Check shop location
        if($_POST["shop_location"] != 'none')
        {
            $regionCode = $_POST["shop_location"];
            settype($regionCode,"integer");
        }
        else
        {
            $errors['region'] = "You must pick a region where your store locates in.";
        }

        //Check haswifi checkbox
        if(isset($_POST["hasWifi"]))
        {
            $hasWifi = $_POST["hasWifi"];
            settype($hasWifi,'integer');
        }
        else{
            $hasWifi = 0;
        }
        echo $hasWifi;
        //Check hasdelivery checkbox
        if(isset($_POST["hasDelivery"]))
        {
            $offerDelivery = $_POST["hasDelivery"];
            settype($offerDelivery,'integer');
        }
        else{
            $offerDelivery = 0;
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
        }//else $goodForGroup is old value
        //Check price level
        $priceCode = $_POST["shop_price"];
        settype($priceCode, "integer");
        if(!array_filter($errors)) {
            $msg = 'User input valid!';
            $msgClass = 'alert-danger alert-dismissible';
            $checkzipcodesql = "SELECT count(*) AS count FROM zipcode_to_region WHERE Zip_Code = '$zipCode';";
            $checkZipresult = mysqli_query($conn, $checkzipcodesql);
            $checkZip = mysqli_fetch_assoc($checkZipresult);
            if(!$checkZip["count"]){ //ZIP Code doens't exist if result is false
                $zipcodesql = "INSERT INTO zipcode_to_region VALUES ('$zipCode','$regionCode');";
                $zipcoderesult = mysqli_query($conn, $zipcodesql);
            }
            else
            {
                $zipcoderesult = True;
            }
            $updateStoreSql = "UPDATE milk_tea_shop
            SET Shop_Name = '$shopName', Address = '$address', Zip_Code ='$zipCode', Phone_Number = '$phoneNumber',
            Has_Wifi = $hasWifi, Offer_Delivery = $offerDelivery, Good_For_Group = '$goodForGroup', Price_ID = '$priceCode' 
            WHERE Shop_ID = '$cur_shop_id' AND Owner_ID = '$cur_owner_id'";
            $updateStoreResult = mysqli_query($conn, $updateStoreSql);
            echo $conn->error;
            if($updateStoreResult && $zipcoderesult)
            {
                header("Location: shopmanage.php");
            }
            else{
                $msg = 'Update store information failed!';
                $msgclass = 'alert-danger';
            }
        }
        else{
            $msg =  'User input invalid!';
            $msgClass = 'alert-danger alert-dismissible';
        }
    }
    if(isset($_POST['cancel_edit_store']))
    {
        header("Location: shopmanage.php");
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Store</title>
    <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/cosmo/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <h2 class="navbar-brand">Information of <?=$cur_shop_name?></h2>
        </div>
    </div>
</nav>
<div class="container">
    <?php if($msg != ''): ?>
        <div class="alert <?php echo $msgClass; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
        <legend>Shop Information</legend>
        <div class="form-group">
            <label>Shop Name</label>
            <input type="text" name="shop_name" class="form-control" value="<?=htmlspecialchars($shopName)?>" required>
            <div class="text-danger"><?=$errors['shopName']; ?></div>
        </div>
        <div class="form-group">
            <label>Shop Address</label>
            <input type="text" name="shop_address" class="form-control" value="<?=htmlspecialchars($address)?>" required>
            <div class="text-danger"><?=$errors['address']; ?></div>
        </div>
        <div class="form-group">
            <label>Zip Code</label>
            <input type="text" name="zip_code" class="form-control" value="<?=htmlspecialchars($zipCode)?>" required>
            <div class="text-danger"><?=$errors['zipCode']; ?></div>
        </div>
        <div class="form-group">
            <label>Shop Phone Number</label>
            <input type="text" name="shop_phone" class="form-control" value="<?=htmlspecialchars($phoneNumber)?>" required>
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
                    <input class="form-check-input" type="checkbox" name="hasWifi" value="1"
                    <?php if($hasWifi){
                        echo " checked";
                    }
                    ?>>
                    Does your store provide free Wifi connection?
                </label>
            </div>
        </div>
        <div class="form-group">
            <div class="form-check">
                <label class="form-check-label">
                    <input class="form-check-input" type="checkbox" name="hasDelivery" value="1"
                    <?php if($offerDelivery){
                        echo " checked";
                    }
                    ?>>
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
                <option selected="" value="<?=htmlspecialchars($priceCode)?>"><?=$priceName?></option>
                <option value="501">$</option>
                <option value="502">$$</option>
                <option value="503">$$$</option>
                <option value="504">$$$$</option>
            </select>
        </div>
        <br>
        <button type="submit" name="save_edit_store" class="btn btn-primary">Save</button>
        <button type="submit" name="cancel_edit_store" class="btn btn-primary" formnovalidate>Cancel</button>
    </form>
</div>
<?php include('footer.php'); ?>
</body>
</html>
