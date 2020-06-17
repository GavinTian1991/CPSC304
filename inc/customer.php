<?php 
    require('config/db.php');

    if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }

    if(isset($_SESSION['log_in_customer'])) {
        $cur_name = $_SESSION['log_in_customer'];
      }
    $msg = '';
	$msgClass = '';
    
    $mtsquery = "SELECT Shop_ID, Shop_Name, Address, Zip_Code, Phone_Number, 
            Has_Wifi, Good_For_Group, Price_ID FROM Milk_Tea_Shop";
    $mtsresult = mysqli_query($conn, $mtsquery);
    $mtsposts = mysqli_fetch_all($mtsresult, MYSQLI_ASSOC);

    if(isset($_POST['filter'])){ 
        $region = $_POST['location'];
        $pricelevel = $_POST['pricelevel'];
        $ratinglevel = $_POST['ratinglevel'];
        $drinkType = $_POST['drinkType'];

        $regionquery = "SELECT mts.Shop_ID, mts.Shop_Name, mts.Address, mts.Zip_Code, mts.Phone_Number, 
        mts.Has_Wifi, mts.Good_For_Group, mts.Price_ID 
        FROM Milk_Tea_Shop mts, Zipcode_To_Region ztr 
        WHERE mts.Zip_Code = ztr.Zip_Code AND ztr.Region = '$region'";

        $pricequery = "SELECT mts.Shop_ID, mts.Shop_Name, mts.Address, mts.Zip_Code, mts.Phone_Number, 
        mts.Has_Wifi, mts.Good_For_Group, mts.Price_ID 
        FROM Milk_Tea_Shop mts 
        WHERE mts.Price_ID >= '$pricelevel'";

        $ratinglevelquery = "SELECT mts.Shop_ID, mts.Shop_Name, mts.Address, mts.Zip_Code, mts.Phone_Number, 
        mts.Has_Wifi, mts.Good_For_Group, mts.Price_ID 
        FROM Milk_Tea_Shop mts 
        WHERE mts.Average_Rating >= '$ratinglevel'";

        $drinkTypeQuery = "SELECT mts.Shop_ID, mts.Shop_Name, mts.Address, mts.Zip_Code, mts.Phone_Number, 
        mts.Has_Wifi, mts.Good_For_Group, mts.Price_ID 
        FROM Milk_Tea_Shop mts WHERE mts.Shop_ID IN
        (SELECT DISTINCT mts.Shop_ID
        FROM Milk_Tea_Shop mts, Drink_Offered_By dob, Drinks d, Drink_Is_Typeof dit, Shared_Drink_Types sdt
        WHERE mts.Shop_ID = dob.Shop_ID AND 
        dob.Drink_ID = d.Drink_ID AND d.Drink_Name = dit.Drink_Name AND dit.Shared_Type_ID = '$drinkType')";

        $firstQuery = false;
        $filterquery = "";
        if($region != 'none' && $firstQuery == false) {
            $filterquery = $regionquery;
            $firstQuery = true;
        }

        if($pricelevel != 'none') {
            if($firstQuery == true) {
                $filterquery = $filterquery . " INTERSECT " . $pricequery;
            } else {
                $filterquery = $pricequery;
                $firstQuery = true;
            }
        }

        if($ratinglevel != 'none') {
            if($firstQuery == true) {
                $filterquery = $filterquery . " INTERSECT " . $ratinglevelquery;
            } else {
                $filterquery = $ratinglevelquery;
                $firstQuery = true;
            }
        }

        if($drinkType != 'none') {
            if($firstQuery == true) {
                $filterquery = $filterquery . " INTERSECT " . $drinkTypeQuery;
            } else {
                $filterquery = $drinkTypeQuery;
                $firstQuery = true;
            }
        }

        if($filterquery != "") {
            $result = mysqli_query($conn, $filterquery);
            $mtsposts = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    } 

    if(isset($_POST['search'])){
        $shopSearchKeyWord = mysqli_real_escape_string($conn, $_POST['shop_name_search']);

        $shopNameSearchQuery = "SELECT mts.Shop_ID, mts.Shop_Name, mts.Address, mts.Zip_Code, mts.Phone_Number, 
        mts.Has_Wifi, mts.Good_For_Group, mts.Price_ID 
        FROM Milk_Tea_Shop mts
        WHERE mts.Shop_Name LIKE '%$shopSearchKeyWord%'";

        $result = mysqli_query($conn, $shopNameSearchQuery);
        $mtsposts = mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    $favoredByAllSearchPosts = [];
    if(isset($_POST['favored_by_all_search'])){
        $favoredByAllSearchQuery = "SELECT mts.Shop_Name, mts.Zip_Code From Milk_Tea_Shop mts 
        WHERE NOT EXISTS ((SELECT ca.Account_ID FROM Customer_Account ca) EXCEPT 
                          (SELECT fb.Customer_Account_ID FROM Favored_by fb WHERE fb.Shop_ID = mts.Shop_ID))";

        $favoredByAllSearchResult = mysqli_query($conn, $favoredByAllSearchQuery);
        $favoredByAllSearchPosts = mysqli_fetch_all($favoredByAllSearchResult, MYSQLI_ASSOC);
    }

    if(isset($_POST['change_noti_read'])){
        if(isset($_SESSION['log_in_customer_id'])) {
            $curCustomerID = $_SESSION['log_in_customer_id'];
            $notificationReadQuery = "UPDATE Notification n, Sends_To_Account sta
            SET sta.If_On_Read = 1
            WHERE n.Notification_ID = sta.Notification_ID AND sta.Account_ID = '$curCustomerID'";

            $notificationReadResult = mysqli_query($conn, $notificationReadQuery);
        }
    }

    $ratingMaxMinPosts = [];
    if(isset($_POST['ratingMaxMin'])){

        $ratingMaxMinSelection = $_POST['ratingExtreValue'];
        $ratingMaxMinQuery = '';
        if($ratingMaxMinSelection == 'maxRating') {
            $ratingMaxMinQuery = "SELECT mts.Shop_Name, mts.Zip_Code, mts.Average_Rating FROM milk_tea_shop mts where 
            mts.Average_Rating = (SELECT max(Average_Rating) FROM milk_tea_shop)";
        } else {
            $ratingMaxMinQuery = "SELECT mts.Shop_Name, mts.Zip_Code, mts.Average_Rating FROM milk_tea_shop mts where 
            mts.Average_Rating = (SELECT min(Average_Rating) FROM milk_tea_shop WHERE Average_Rating != 0)";
        }

        $ratingMaxMinResult = mysqli_query($conn, $ratingMaxMinQuery);
        $ratingMaxMinPosts = mysqli_fetch_all($ratingMaxMinResult, MYSQLI_ASSOC);
    }




    if(isset($_POST['gotoMTS'])){
        session_start();
        $shop_ID = $_POST['gotoMTS'];
        $_SESSION['cur_mts_ID'] = $shop_ID;
        header('Location: mtshop.php');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer</title>
    <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/cosmo/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
</head>
<body>
    <?php require('cnavbar.php'); ?>
    <?php if($msg != ''): ?>
    		<div class="alert <?php echo $msgClass; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>
    <div class="container">
        <table class="table table-hover">  <!-- We can change to card layout in order to add comment--> 
            <thead>
                <tr>
                <th scope="col">Shop Name</th>
                <th scope="col">Address</th>
                <th scope="col">Zip Code</th>
                <th scope="col">Phone Number</th>
                <th scope="col">Group</th>
                <th scope="col">WiFi</th>
                <th scope="col">Price Level</th>
                <th scope="col">Rating</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($mtsposts as $post) : ?>
                    <tr>
                        <td>
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <button type="submit" name="gotoMTS" value=<?php echo $post['Shop_ID'];?> class="btn btn-primary">                      <?php
                            echo $post['Shop_Name']; ?>
                            </button>
                        </form>
                        </td>
                        <td><?php echo $post['Address']; ?></td>
                        <td><?php echo $post['Zip_Code']; ?></td>
                        <td><?php echo $post['Phone_Number']; ?></td>
                        <td><?php echo $post['Good_For_Group']; ?></td>
                        <td><?php echo $post['Has_Wifi'] ? 'Yes': 'No'; ?></td>
                        <td><?php 
                            $price_level = $post['Price_ID'];
                            $query = "SELECT Name From Price_Level WHERE Level_ID = '$price_level'";
                            $result = mysqli_query($conn, $query);
                            $price = mysqli_fetch_assoc($result);
                            echo $price['Name']; 
                            ?>
                        </td>
                        <td><?php 
                            $shop_ID = $post['Shop_ID'];
                            $ratingquery = "SELECT Rating_Level FROM Comments_from_Customer WHERE Shop_ID = '$shop_ID'";
                            $ratingresult = mysqli_query($conn, $ratingquery);
                            $ratings = mysqli_fetch_all($ratingresult, MYSQLI_ASSOC);

                            $sumrating = 0.0;
                            $ratingcount = 0;
                            $avgrating = 0.0;

                            foreach($ratings as $rating) {
                                $sumrating = $sumrating + $rating['Rating_Level'];
                                $ratingcount = $ratingcount + 1;
                            }
                            $tmpname = $post['Shop_Name'];
                            if($ratingcount != 0) { 
                                $avgrating = (float)$sumrating / $ratingcount;
                                //$_SESSION[$tmpname] = $avgrating;
                            } else {
                                $avgrating = 0.0;
                                //$_SESSION[$tmpname] = 0;
                            }
                            $ratingsetquery = "UPDATE Milk_Tea_Shop 
                            SET Average_Rating = '$avgrating'
                            WHERE Shop_ID = '$shop_ID'";

                            if(mysqli_query($conn, $ratingsetquery)) {
                                echo $avgrating;
                            } else {
                                echo 'none';
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <div class="container">
            <div class="row">
                <div class="col-sm">
                    <select name="location" class="custom-select">
                        <option selected="" value="none">Location</option>
                        <option value="401">Richmond</option>
                        <option value="402">Vancouver</option>
                        <option value="403">Burnaby</option>
                        <option value="404">Surrey</option>
                        <option value="405">Coquitlam</option>
                        <option value="406">Downtown</option>
                    </select>
                </div>
                <div class="col-sm">
                    <select name="pricelevel" class="custom-select">
                        <option selected="" value="none">Price Level</option>
                        <option value="501">$</option>
                        <option value="502">$$</option>
                        <option value="503">$$$</option>
                        <option value="504">$$$$</option>
                    </select>
                </div>
                <div class="col-sm">
                    <select name="ratinglevel" class="custom-select">
                        <option selected="" value="none">Rating Above</option>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                        <option value="5">5</option>
                    </select>
                </div>
                <div class="col-sm">
                    <select name="drinkType" class="custom-select">
                        <option selected="" value="none">Drink Type</option>
                        <option value="1301">Milk Tea</option>
                        <option value="1302">Fruit Tea</option>
                        <option value="1303">Fresh Milk</option>
                        <option value="1304">Fresh Tea</option>
                        <option value="1305">Slush</option>
                        <option value="1306">Cream Cap Tea</option>
                        <option value="1307">Coffee</option>
                        <option value="1308">Dessert</option>
                    </select>
                </div>
                <div class="col-sm">
                    <button type="submit" name="filter" class="btn btn-primary">Filter Confirm</button>
                </div>
            </div>
        </div>
        </form>
        <br>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="container">
                <div class="row">
                    <div class="col-sm">
                        <input id="shop_name_search_input" type="text" name="shop_name_search" class="form-control" value="Please input shop name" required>
                    </div>
                    <div class="col-sm">
                        <button id="shop_name_search_but" type="submit" name="search" class="btn btn-primary" disabled="true">Search</button>
                        <script type="text/javascript">
                            $("#shop_name_search_input").bind("change paste keyup", function() {
                            $("#shop_name_search_but").attr("disabled", false);
                            });
                        </script>
                    </div>
                </div>
            </div>
        </form>







        <br>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="container">
                <div class="row">
                    <div class="col-sm">
                        <button type="submit" name="favored_by_all_search" class="btn btn-primary">Which shop(s) favored by all customers?</button>
                    </div>
                    
                    <div class="col-sm">
                    <?php if (empty($favoredByAllSearchPosts)): ?>
                            <p>No shop favored by all customers</p>
                        <?php else: ?>
                        <?php foreach($favoredByAllSearchPosts as $favoredByShop) : ?>
                            <div class="row">
                                <div class="col-sm">
                                    <p><?php echo $favoredByShop['Shop_Name']?>   <?php echo $favoredByShop['Zip_Code']?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>    
                        <?php endif; ?>
                        </div>

                    <div class="col-sm">
                        <?php if ($cur_name != 'anonymous'): ?>
                            <button id="general_button" type="button" class="btn btn-primary" data-toggle="modal" data-target="#notification_display">
                                Notification
                            </button>
                        <?php endif; ?>


                    <!-- Modal -->
                    <div class="modal fade" id="notification_display" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Notification</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <?php
                            if(isset($_SESSION['log_in_customer_id'])) {
                                $curCustomerID = $_SESSION['log_in_customer_id'];
                                $notificationQuery = "SELECT n.Type, n.Contents, sta.If_On_Read, sta.Send_Date FROM Notification n, Sends_To_Account sta 
                                WHERE n.Notification_ID = sta.Notification_ID AND sta.Account_ID = '$curCustomerID'";
                                $notificationResult = mysqli_query($conn, $notificationQuery);
                                $notificationPosts = mysqli_fetch_all($notificationResult, MYSQLI_ASSOC);
                            }
                            ?>
                            <?php if (empty($notificationPosts)): ?>
                                <p>No Notifications</p>
                            <?php else: ?>
                                <?php foreach($notificationPosts as $notification) : ?>
                                    <?php if ($notification['If_On_Read'] == 1): ?>
                                        <p class="text-muted">
                                    <?php else: ?>
                                        <p>
                                    <?php endif; ?>
                                    <?php echo $notification['Type']?></p>
                                    <?php if ($notification['If_On_Read'] == 1): ?>
                                        <p class="text-muted">
                                    <?php else: ?>
                                        <p>
                                    <?php endif; ?>
                                    <?php echo $notification['Contents']?>
                                    <?php 
                                    $cd = $notification['Send_Date'];
                                    $discdate = date('Y-m-d',strtotime($cd));
                                    echo $discdate?>
                                    </p> 
                                <?php endforeach; ?>    
                            <?php endif; ?>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" name="change_noti_read" class="btn btn-secondary">Close</button>
                        </div>
                        </div>
                    </div>
                    </div>
                    <!-- Modal -->
                    
                    
                    </div>
                </div>
            </div>
        </form>

        <br>
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <div class="container">
                <div class="row">
                    <div class="col-sm">
                        <select name="ratingExtreValue" class="custom-select" required>
                            <option selected="" value="">Rating</option>
                            <option value="maxRating">Maximum</option>
                            <option value="minRating">Minimum</option>
                        </select>
                    </div>
                    <div class="col-sm">
                        <button type="submit" name="ratingMaxMin" class="btn btn-primary">Search</button>  
                    </div>
                    <div class="col-sm">
                    <div class="card border-primary mb-3" style="max-width: 20rem;">
                        <div class="card-header">Shop with max or min average rating</div>
                        <div class="card-body">
                        
                            <?php foreach($ratingMaxMinPosts as $ratingMaxMin) : ?>
                                <div class="row">
                                    <div class="col-sm">
                                        <?php echo $ratingMaxMin['Shop_Name']?> 
                                    </div>
                                    <div class="col-sm">
                                        <?php echo $ratingMaxMin['Zip_Code']?>
                                    </div>
                                    <div class="col-sm">
                                        <?php echo $ratingMaxMin['Average_Rating']?>
                                    </div>
                                </div>
                            <?php endforeach; ?> 
                             
                        

                        </div>
                    </div>
                    </div>
                </div>
            </div>
        </form>







	</div>
    <?php include('footer.php'); ?>
</body>
</html>