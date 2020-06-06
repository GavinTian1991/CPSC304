<?php
    require('config/db.php');

    $query = "SELECT DISTINCT Shop_ID, Shop_Name FROM Milk_Tea_Shop ORDER BY Milk_Tea_Shop.Shop_ID";
    $result = mysqli_query($conn, $query);
    $posts = mysqli_fetch_all($result, MYSQLI_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drinks</title>
    <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/cosmo/bootstrap.min.css">
</head>
<body>
    <?php require('cnavbar.php'); ?>
    <div class="container">
        <?php foreach($posts as $post) : ?>
            <div class="card border-primary mb-3">
                <div class="card-header"><?php echo $post['Shop_Name']?></div>
                <div class="card-body">
                    <?php
                        $shop_ID = $post['Shop_ID'];
                        $dquery = "SELECT d.Drink_Name, d.Description, d.Price, d.Hot_or_Cold
                        FROM Drinks d, Drink_Offered_By dob 
                        WHERE dob.Shop_ID = '$shop_ID' AND dob.Drink_ID = d.Drink_ID;";
                        $dresult = mysqli_query($conn, $dquery);
                        $drinks = mysqli_fetch_all($dresult, MYSQLI_ASSOC);
                    ?>
                    <div class="row">
                        <?php foreach($drinks as $drink) : ?>
                            <div class="col-sm">
                                <p class="text-primary"><?php echo $drink['Drink_Name']?></p>
                            </div>
                            <div class="col-sm">
                                <p class="text-secondary">Des: 
                                    <?php 
                                        if($drink['Description'] != ""){
                                            echo $drink['Description'];
                                        } else {
                                            echo 'None';
                                        }
                                    ?> 
                                </p>
                            </div>
                            <div class="col-sm">
                                <p>$: <?php echo $drink['Price']?></p>
                            </div>
                            <div class="col-sm">
                                <p><?php if($drink['Hot_or_Cold'] == 3) {
                                    echo 'Cold & Hot';
                                } else if ($drink['Hot_or_Cold'] == 2) {
                                    echo 'Cold';
                                } else {
                                    echo 'Hot';
                                }
                                ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php include('footer.php'); ?>
</body>
</html>