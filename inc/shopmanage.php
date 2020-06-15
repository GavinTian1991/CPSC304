<?php
    require('config/db.php');
    if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }
    $cur_owner_id = '';
    $cur_owner_name = '';


    $msg = '';
    $msgClass = '';
    if(!isset($_SESSION['owner_logged_in']))
    {
        header('Location:../index.php');
        exit;
    }
    else{
        $cur_owner_name = $_SESSION['logged_owner_name'];
        $cur_owner_id = $_SESSION['logged_owner_id'];
    }


    if(!isset($_SESSION['current_shop_id'])){
        if(isset($_GET['id'])){
            $curr_shop_id = mysqli_real_escape_string($conn,$_GET['id']);
            settype($curr_shop_id, 'integer');
            $_SESSION['current_shop_id'] = $curr_shop_id;
        }
        else
        {
            header('Location:ownerprofile.php');
            exit;
        }
    }
    else {
        $curr_shop_id = $_SESSION['current_shop_id'];
    }

    $shop_sql = "SELECT Shop_Name, Address, Zip_Code, Phone_Number, Has_Wifi, Offer_Delivery, Good_For_Group, Price_ID 
    FROM milk_tea_shop
    WHERE Shop_ID = '$curr_shop_id' AND Owner_ID = '$cur_owner_id'";
    $shop_result = mysqli_query($conn, $shop_sql);
    $shop = mysqli_fetch_assoc($shop_result);

    $_SESSION['current_shop_name'] = $shop['Shop_Name'];
    echo $conn->error;
    $zipCode = $shop['Zip_Code'];
    $priceCode = $shop['Price_ID'];

    $regionsql = "SELECT Name
    FROM zipcode_to_region z, region r
    WHERE z.Region = r.Region_ID AND z.Zip_Code = '$zipCode'";
    $regionresult = mysqli_query($conn, $regionsql);
    $regionName = mysqli_fetch_assoc($regionresult);

    $pricesql = "SELECT price_level.Name FROM price_level WHERE Level_ID = '$priceCode'";
    $priceResult = mysqli_query($conn, $pricesql);
    $priceLevel = mysqli_fetch_assoc($priceResult);

    $hourSql = "SELECT Business_Day, Open_Time, Close_Time
                FROM Open_At_Business_Hour
                WHERE Shop_ID = '$curr_shop_id'
                ORDER BY 
                     CASE
                          WHEN Business_Day = 'Monday' THEN 1
                          WHEN Business_Day = 'Tuesday' THEN 2
                          WHEN Business_Day = 'Wednesday' THEN 3
                          WHEN Business_Day = 'Thursday' THEN 4
                          WHEN Business_Day = 'Friday' THEN 5
                          WHEN Business_Day = 'Saturday' THEN 6
                          WHEN Business_Day = 'Sunday' THEN 7
                     END ASC;";
    $hoursResult = mysqli_query($conn, $hourSql);
    $hours = mysqli_fetch_all($hoursResult, MYSQLI_ASSOC);

    $drinkSql = "SELECT d.Drink_ID, d.Drink_Name, d.Description, d.Price, d.Hot_or_Cold, d.Specialized_Type_Name AS SType_Name, sdt.Type_Name
                        FROM Drinks d, Drink_Offered_By dob, drink_is_typeof dit, shared_drink_types sdt 
                        WHERE dob.Shop_ID = '$curr_shop_id' AND dob.Drink_ID = d.Drink_ID
                        AND d.Drink_Name = dit.Drink_Name AND dit.Shared_Type_ID = sdt.Type_ID;";
    $drinkResult = mysqli_query($conn, $drinkSql);
    $drinks = mysqli_fetch_all($drinkResult, MYSQLI_ASSOC);
    echo $conn->error;
    $cur_shop_has_drinks = False;
    if(!empty($drinks))
    {
        $cur_shop_has_drinks = True;
    }

    $eventSql = "SELECT e.Event_name, e.Event_Content
                 FROM holds_sales_event e
                 WHERE e.Shop_ID = '$curr_shop_id';";
    $eventResult = mysqli_query($conn, $eventSql);
    $events = mysqli_fetch_all($eventResult, MYSQLI_ASSOC);
    echo $conn->error;
    $cur_shop_has_events = False;
    if(!empty($events))
    {
        $cur_shop_has_events = True;
    }

    if(isset($_POST['shop_quit']))
    {
        unset($_SESSION['current_shop_id']);
        unset($_SESSION['current_shop_name']);
        header('Location: ownerprofile.php');
    }
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Profile</title>
    <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/cosmo/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="#">Hello <?php echo $cur_owner_name . ' '?></a>
    <div class="collapse navbar-collapse" >
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="addDrink.php">Add Drink</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="event.php">Add Event</a>
            </li>
        </ul>
        <form class="form-inline my-2 my-lg-0" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <button class="btn btn-secondary my-2 my-sm-0" type="submit" name="shop_quit">Back</button>
        </form>
    </div>
</nav>
<ul class="nav nav-tabs">
    <li class = "nav-item active">
        <a class="nav-link" href="#details">Shop Details</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#hours">Business Time</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#drinks">Drinks</a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#events">Events</a>
    </li>
</ul>
<div id="myTabContent" class="tab-content">
    <div class="tab-pane fade in active show" id="details">
        <div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
            <div class="card-header">Your account details are below:</div>
            <div class="card-body">
                <table class="table table-hover">
                    <tr>
                        <td>Shop Name:</td>
                        <td><?=$shop['Shop_Name']?></td>
                    </tr>
                    <tr>
                        <td>Shop Address:</td>
                        <td><?=$shop['Address']?></td>
                    </tr>
                    <tr>
                        <td>Located in:</td>
                        <td><?=$regionName['Name']?></td>
                    </tr>
                    <tr>
                        <td>Zip Code:</td>
                        <td><?=$zipCode?></td>
                    </tr>
                    <tr>
                        <td>Phone Number:</td>
                        <td><?=$shop['Phone_Number']?></td>
                    </tr>
                    <tr>
                        <td>Has Wifi?:</td>
                        <td><?php echo $shop['Has_Wifi'] ? 'Yes': 'No'; ?></td>
                    </tr>
                    <tr>
                        <td>Offer Delivery:</td>
                        <td><?php echo $shop['Offer_Delivery'] ? 'Yes': 'No'; ?></td>
                    </tr>
                    <tr>
                        <td>Good For Group?:</td>
                        <td><?=$shop['Good_For_Group']?></td>
                    </tr>
                    <tr>
                        <td>Price Level:</td>
                        <td><?=$priceLevel['Name']?></td>
                    </tr>
                </table>
                <br>
                <form action="editstore.php" method="POST">
                    <input type="hidden" name="shop_id_to_edit" value="<?php echo $curr_shop_id; ?>">
                    <button type="submit" name="edit_shop" class="btn btn-secondary my-2 my-sm-0">Edit</button>
                </form>

            </div>
        </div>
    </div>
    <div class="tab-pane fade show" id="hours">
        <div class="container">
            <table class = "table table-hover">
                <thead>
                <tr>
                    <th scope="col">Business Day</th>
                    <th scope="col">Open At</th>
                    <th scope="col">Close At</th>
                    <th scope="col">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($hours as $hour):?>
                    <tr>
                        <td><?= $hour['Business_Day'];?></td>
                        <td><?= $hour['Open_Time'];?></td>
                        <td><?= $hour['Close_Time'];?></td>
                        <td>
                            <a class="nav-link" href="editHour.php?shopid=<?=$curr_shop_id?>&day=<?=$hour['Business_Day']?>">
                                Edit</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="tab-pane fade show" id="drinks">
        <div class="container">
            <?php if($cur_shop_has_drinks):?>
                <table class = "table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">Edit</th>
                        <th scope="col">Drink Name</th>
                        <th scope="col">Description</th>
                        <th scope="col">Price</th>
                        <th scope="col">Hot Or Cold</th>
                        <th scope="col">In store Type Name</th>
                        <th scope="col">Type Name</th>
                        <th scope="col">Delete</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($drinks as $drink):?>
                        <tr>
                            <td>
                                <a class="nav-link" href="editDrink.php?id=<?=$drink['Drink_ID']?>">
                                    GO</a>
                            </td>
                            <td><?= $drink['Drink_Name'];?></td>
                            <td><?php
                                if($drink['Description'] != ""){
                                    echo $drink['Description'];
                                } else {
                                    echo 'None';
                                }
                                ?></td>
                            <td><?= $drink['Price'];?> CAD</td>
                            <td><?php if($drink['Hot_or_Cold'] == 3) {
                                    echo 'Cold & Hot';
                                } else if ($drink['Hot_or_Cold'] == 2) {
                                    echo 'Cold';
                                } else {
                                    echo 'Hot';
                                }
                                ?>
                            </td>
                            <td><?= $drink['SType_Name'];?></td>
                            <td><?= $drink['Type_Name'];?></td>
                            <td>
                                <form method="post" action="<?=$_SERVER['PHP_SELF'];?>">
                                    <button type="submit" name="delete_drink" value="<?=$drink['Drink_ID']?>" class="btn btn-primary">
                                    X
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>This store has no drinks, try add some!</p>
            <?php endif;?>
        </div>
    </div>
    <div class="tab-pane fade show" id="events">
        <div class="container">
            <?php if($cur_shop_has_events):?>
                <table class = "table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">Edit</th>
                        <th scope="col">Event Name</th>
                        <th scope="col">Event Content</th>
                        <th scope="col">Delete</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($events as $event):?>
                        <tr>
                            <td>
                                <a class="nav-link" href="Event.php?name=<?=$event['Event_name']?>">
                                    GO</a>
                            </td>
                            <td><?=$event['Event_name'];?></td>
                            <td><?=$event['Event_Content'];?></td>
                            <td>
                                <form method="post" action="<?=$_SERVER['PHP_SELF'];?>">
                                    <button type="submit" name="delete_event" value="<?=$event['Event_name']?>" class="btn btn-primary">
                                        X
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>This store has no sales event, try add some!</p>
            <?php endif;?>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $(".nav-tabs a").click(function(){
            $(this).tab('show');
        });
    });
</script>
<?php include('footer.php'); ?>
</body>
</html>