<?php 
    require('config/db.php');

    $query = 'SELECT Shop_ID, Shop_Name, Address, Zip_Code, Phone_Number, 
            Has_Wifi, Good_For_Group, Price_ID FROM Milk_Tea_Shop';
    $result = mysqli_query($conn, $query);
    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

    if(isset($_POST['filter'])){  //TODO: price level, drink type (rating?)
        $region = $_POST['location'];
        $query = "SELECT mts.Shop_ID, mts.Shop_Name, mts.Address, mts.Zip_Code, mts.Phone_Number, 
        mts.Has_Wifi, mts.Good_For_Group, mts.Price_ID 
        FROM Milk_Tea_Shop mts, Zipcode_To_Region ztr 
        WHERE mts.Zip_Code = ztr.Zip_Code AND ztr.Region = '$region'";
        $result = mysqli_query($conn, $query);
        $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);
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
</head>
<body>
    <?php require('cnavbar.php'); ?>
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
                <th scope="col">Sales Event</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($posts as $post) : ?>
                    <tr>
                        <td>
                        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <button type="submit" name="gotoMTS" value=<?php echo $post['Shop_ID']?> class="btn btn-primary">                      <?php 
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
                            $query = "SELECT Event_Content From Holds_Sales_Event WHERE Shop_ID = '$shop_ID'";
                            $result = mysqli_query($conn, $query);
                            $event = mysqli_fetch_assoc($result);
                            if(isset($event['Event_Content'])) {
                                echo $event['Event_Content'];
                            } else {
                                echo 'None';
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
                        <option selected="">Location</option>
                        <option value="401">Richmond</option>
                        <option value="402">Vancouver</option>
                        <option value="403">Burnaby</option>
                        <option value="404">Surrey</option>
                        <option value="405">Coquitlam</option>
                        <option value="406">Downtown</option>
                    </select>
                </div>
                <div class="col-sm">
                    <select name="price" class="custom-select">
                        <option selected="">Price Level</option>
                        <option value="1">$</option>
                        <option value="2">$$</option>
                        <option value="3">$$$</option>
                        <option value="4">$$$$</option>
                    </select>
                </div>
                <div class="col-sm">
                    <select name="drinktype" class="custom-select">
                        <option selected="">Like Drink Type</option>
                        <option value="1">Cream Cap Tea</option>
                        <option value="2">Dessert</option>
                        <option value="3">Fresh Tea</option>
                        <option value="4">Fruit Tea</option>
                        <option value="5">Milk Tea</option>
                        <option value="6">Slush</option>
                    </select>
                </div>
                <div class="col-sm">
                    <button type="submit" name="filter" class="btn btn-primary">Filter Confirm</button>
                </div>
            </div>
        </div>
        </form>
	</div>
    <?php include('footer.php'); ?>
</body>
</html>