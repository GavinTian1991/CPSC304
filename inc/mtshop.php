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

  $cur_Shop_ID = '';
  $cur_Customer_Name = '';
  $cur_CustomerID = '';
  $likeThisShop = false;

  if(isset($_SESSION['cur_mts_ID'])) {
    $cur_Shop_ID = (int)$_SESSION['cur_mts_ID'];
    $cur_Customer_Name = $_SESSION['log_in_customer'];

    if($cur_name != 'anonymous') {
        $customerIDquery = "SELECT Account_ID FROM Account WHERE User_Name = '$cur_Customer_Name'";
        $customerIDResult = mysqli_query($conn, $customerIDquery);
        $customerID = mysqli_fetch_assoc($customerIDResult);
    
        $cur_CustomerID = (int)$customerID['Account_ID'];

        $customerLikeShopQuery = "SELECT * FROM Favored_by WHERE Shop_ID = '$cur_Shop_ID' 
        AND Customer_Account_ID = '$cur_CustomerID'";

        $customerLikeShopResult = mysqli_query($conn, $customerLikeShopQuery);
        $customerLikeShopPost = mysqli_fetch_assoc($customerLikeShopResult);
        if($customerLikeShopPost) {
            $likeThisShop = true;
        } 
    }


    $query = "SELECT Shop_Name FROM Milk_Tea_Shop WHERE Shop_ID = '$cur_Shop_ID'";
    $result = mysqli_query($conn, $query);
    $posts = mysqli_fetch_assoc($result);

  } 

  if(isset($_POST['submit_new_comment'])){

    $new_comment = mysqli_real_escape_string($conn, $_POST['newcomment']); 
    $new_rating = mysqli_real_escape_string($conn, $_POST['newrating']);

    $maxcommentIDquery = "SELECT IFNULL(max(Comment_ID),700) AS MaxID FROM Comments_from_Customer";
    $maxCommentIDResult = mysqli_query($conn, $maxcommentIDquery);
    $maxID = mysqli_fetch_assoc($maxCommentIDResult);

    $new_CommentID = (int)$maxID['MaxID'] + 1;

    $cur_Date = date("Y-m-d H:i:s");

    $commentAddquery = "INSERT INTO Comments_from_Customer 
    VALUES('$new_CommentID', '$new_comment', '$new_rating', '$cur_Date', '$cur_CustomerID', '$cur_Shop_ID')";

    $commentAddresult = mysqli_query($conn, $commentAddquery);

    if($commentAddresult) {
        $msg = 'Comment added!';
        $msgClass = 'alert-success';

        $ratingquery = "SELECT Rating_Level FROM Comments_from_Customer WHERE Shop_ID = '$cur_Shop_ID'";
        $ratingresult = mysqli_query($conn, $ratingquery);
        $ratings = mysqli_fetch_all($ratingresult, MYSQLI_ASSOC);
      
        $sumrating = 0.0;
        $avgrating = 0.0;
        $ratingcount = 0;
      
        foreach($ratings as $rating) {
            $sumrating = $sumrating + $rating['Rating_Level'];
            $ratingcount = $ratingcount + 1;
        }

        if($ratingcount != 0) { 
            $avgrating = (float)$sumrating / $ratingcount;
        } else {
            $avgrating = 0.0;
        }
        $ratingsetquery = "UPDATE Milk_Tea_Shop 
        SET Average_Rating = '$avgrating'
        WHERE Shop_ID = '$cur_Shop_ID'";

        if(mysqli_query($conn, $ratingsetquery)) {
            header('Location: mtshop.php');
        } else {
            $msg = 'Average Rating setting failed!';
            $msgClass = 'alert-danger';
        }
    } else {
        $msg = 'You have already comment this shop!';
        $msgClass = 'alert-danger';
    }
  }


  if(isset($_POST['like_this_shop'])){
    $shopLikeQuery = "INSERT INTO Favored_by 
    VALUES('$cur_Shop_ID', '$cur_CustomerID')";
    $shopLikeResult = mysqli_query($conn, $shopLikeQuery);
    if($shopLikeResult) {
        $likeThisShop = true;
        $msg = 'You like this shop!';
        $msgClass = 'alert-success';
    } else {
        $msg = 'Like this shop failed!';
        $msgClass = 'alert-danger';
    }
  }

  if(isset($_POST['dislike_this_shop'])){

    $disLikeShopQuery = "DELETE FROM Favored_by 
    WHERE Shop_ID = '$cur_Shop_ID' AND Customer_Account_ID = '$cur_CustomerID'";

    $disLikeShopResult = mysqli_query($conn, $disLikeShopQuery);
    if($disLikeShopResult) {
        $likeThisShop = false;
        $msg = 'You dislike this shop!';
        $msgClass = 'alert-success';
    } else {
        $msg = 'Dislike this shop failed!';
        $msgClass = 'alert-danger';
    }
  }
  

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/cosmo/bootstrap.min.css">
    <title>Milk Tea Shop</title>
</head>
<body>
    <?php require('cnavbar.php'); ?>
    <?php if($msg != ''): ?>
    		<div class="alert <?php echo $msgClass; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>
    <div class="jumbotron">
        <div class="row">
            <div class="col-sm">
                <h1 class="display-5"><?php echo $posts['Shop_Name']?></h1>
                <p>Current Rating: <?php
                    $curRatingQuery = "SELECT Average_Rating FROM Milk_Tea_Shop WHERE 
                    Shop_ID = '$cur_Shop_ID'";
                    $shopRatingResult = mysqli_query($conn, $curRatingQuery);
                    $shopCurRating = mysqli_fetch_assoc($shopRatingResult);
                
                    echo $shopCurRating['Average_Rating'];
                ?></p>
            </div>
            <div class="col-sm">
                <?php 
                    $opentimequery = "SELECT Business_Day, Open_Time, Close_Time
                    FROM Open_At_Business_Hour
                    WHERE Shop_ID = '$cur_Shop_ID'
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
                    $opentimeresult = mysqli_query($conn, $opentimequery);
                    $opentimes = mysqli_fetch_all($opentimeresult, MYSQLI_ASSOC);
                ?>
                <p>Open Hours:</p>
                <?php foreach($opentimes as $opentime) : ?>
                    <p><?php echo $opentime['Business_Day']?>
                    <?php echo $opentime['Open_Time']?>
                    <?php echo $opentime['Close_Time']?>
                    </p>
                <?php endforeach; ?>
            </div>
            <div class="col-sm">
                <p>Sales Event:
                    <?php
                        $salequery = "SELECT Event_name, Event_Content From Holds_Sales_Event WHERE Shop_ID = '$cur_Shop_ID'";
                        $saleresult = mysqli_query($conn, $salequery);
                        $saleevents = mysqli_fetch_all($saleresult, MYSQLI_ASSOC);
                    ?>
                    <?php foreach($saleevents as $saleevent) : ?>
                        <p><?php echo $saleevent['Event_name']?> : <?php echo $saleevent['Event_Content']?></p>
                    <?php endforeach; ?>
                    <?php if (empty($saleevents)): ?>
                            <p>None</p>
                    <?php endif; ?>
                </p>
            </div>
            <?php if ($cur_name != 'anonymous'): ?>
                <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <div class="col-sm">
                    <?php if ($likeThisShop != true): ?>
                        <button type="submit" name="like_this_shop" class="btn btn-success">Like</button>
                    <?php else: ?>
                        <button type="submit" name="dislike_this_shop" class="btn btn-warning">Dislike</button>
                    <?php endif; ?>
                </div>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="card border-primary mb-3">
                <div class="card-header">Drinks</div>
                <div class="card-body">
                    <?php
                        $dquery = "SELECT d.Drink_Name, d.Description, d.Price, d.Hot_or_Cold
                        FROM Drinks d, Drink_Offered_By dob 
                        WHERE dob.Shop_ID = '$cur_Shop_ID' AND dob.Drink_ID = d.Drink_ID;";
                        $dresult = mysqli_query($conn, $dquery);
                        $drinks = mysqli_fetch_all($dresult, MYSQLI_ASSOC);
                    ?>
                        <?php foreach($drinks as $drink) : ?>
                            <div class="row">
                            <div class="col-sm">
                                <p class="text-primary"><?php echo $drink['Drink_Name']?></p>
                            </div>
                            <div class="col-sm">
                                <p class="text-secondary">
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
                        </div>
                        <?php endforeach; ?>
                    
                </div>
            </div>

            <?php if ($cur_name != 'anonymous'): ?>
            <div class="card border-primary mb-3">
                <div class="card-header">Comments</div>
                <div class="card-body">
                    <?php
                        $cquery = "SELECT cfc.Comment_ID, cfc.Contents, cfc.Rating_Level, cfc.Date, a.User_Name 
                        FROM Comments_from_Customer cfc, Account a 
                        WHERE cfc.Shop_ID = '$cur_Shop_ID' 
                        AND cfc.Account_ID = a.Account_ID";
                        $cresult = mysqli_query($conn, $cquery);
                        $comments = mysqli_fetch_all($cresult, MYSQLI_ASSOC);
                    ?>
                    <?php foreach($comments as $comment) : ?>
                        <div class="row">
                            <div class="col-sm">
                                <p class="text-primary"><?php echo $comment['Contents']?></p>
                            </div>
                            <div class="col-sm">
                                <p><?php echo $comment['Rating_Level']?></p>
                            </div>
                            <div class="col-sm">
                                <p><?php 
                                $cd = $comment['Date'];
                                $discdate = date('Y-m-d',strtotime($cd));
                                echo $discdate?></p>
                            </div>
                            <div class="col-sm">
                                <p><?php echo $comment['User_Name']?></p>
                            </div>
                        </div>
                        <?php 
                            $comment_ID = $comment['Comment_ID'];
                            $replyquery = "SELECT Contents, Date FROM Reply_with_Feedback 
                            WHERE Replied_Comment_ID = '$comment_ID'";
                            $rresult = mysqli_query($conn, $replyquery);
                            $replies = mysqli_fetch_all($rresult, MYSQLI_ASSOC);
                        ?> 
                        <?php foreach($replies as $reply) : ?>
                            <div class="row">
                                <div class="col-sm">
                                    <p class="text-info"><?php echo $reply['Contents']?></p>
                                </div>
                                <div class="col-sm">
                                    <p><?php 
                                        $rd = $reply['Date'];
                                        $disrdate = date('Y-m-d',strtotime($rd));
                                        echo $disrdate?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <div class="row">
                            <div class="col-sm">
                                <label for="exampleTextarea">New Comment</label>
                                <textarea class="form-control" name = "newcomment" rows="4" required></textarea>
                            </div>
                            <div class="col-sm">
                                <label for="exampleSelect1">Rating</label>
                                    <select class="form-control" name="newrating" value="0" required>
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                        <option value="4">4</option>
                                        <option value="5">5</option>
                                    </select>
                                <br>
                                <button type="submit" name="submit_new_comment" class="btn btn-primary">Submit</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php endif; ?>
    <?php include('footer.php'); ?>
</body>
</html>