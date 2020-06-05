<?php 
    require('config/db.php');


    $query = 'SELECT Shop_Name, Address, Zip_Code, Phone_Number FROM Milk_Tea_Shop';

    $maxResult = mysqli_query($conn, $query);
    $post = mysqli_fetch_assoc($maxResult);



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <?php include('navbar.php'); ?>
</body>
</html>