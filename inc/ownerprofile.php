<?php
    require('config/db.php');
    if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }
    $cur_owner_id = '';
    $cur_owner_name = '';
    $cur_owner_has_store = False;
    $cur_owner_has_comments = False;

    $msg = '';
    $msgClass = '';
    if(!isset($_SESSION['owner_logged_in']))
    {
        header('Location:../index.php');
        exit;
    }

    if(isset($_SESSION['owner_logged_in'])) {
        $cur_owner_name = $_SESSION['logged_owner_name'];
        $cur_owner_id = $_SESSION['logged_owner_id'];
        //echo "owner id is ".$cur_owner_id;
    }
    settype($cur_owner_id, "integer");
    $account_sql = "SELECT A.Account_ID AS ID, A.User_Name AS username, A.Email AS email, BA.Business_License AS license FROM account AS A, business_owner_account AS BA
    WHERE A.Account_ID = '$cur_owner_id' AND A.Account_ID = BA.Account_ID";
    $result = mysqli_query($conn, $account_sql);
	$profile = mysqli_fetch_assoc($result);
    // free the $result from memory (good practise)
    mysqli_free_result($result);
    // close connection
	//print_r($profile);
    if($profile)
    {
        $id = $profile['ID'];
        $username = $profile['username'];
        $email = $profile['email'];
        $license = $profile['license'];
    }
    else
    {
        $msgClass = 'alert-dismissible alert-alert';
        $msg = "Failed find your profile information";
//        unset($_SESSION['owner_logged_in']);
//        unset($_SESSION['logged_owner_name']);
//        unset($_SESSION['logged_owner_id']);
//        header('Location: ../index.php');
//        exit;
    }
    $store_sql = "SELECT Shop_ID, Shop_Name, Address, Average_Rating AS Rating FROM milk_tea_shop WHERE Owner_ID = $cur_owner_id";
    $result = mysqli_query($conn, $store_sql);
    $stores = mysqli_fetch_all($result,MYSQLI_ASSOC);
    // free the $result from memory (good practise)
    mysqli_free_result($result);
    //print_r($stores);
    if(empty($stores))
    {
        $cur_owner_has_store = False;
        $msgClass = 'alert-dismissible alert-primary';
        $msg = "We don't find your store information!";
    }
    else
    {
        $cur_owner_has_store = True;
    }

    $comment_sql = "SELECT m.Shop_Name, c.Comment_ID, c.Contents, c.Rating_Level, c.Date
                    FROM comments_from_customer c, milk_tea_shop m
                    WHERE c.Shop_ID = m.Shop_ID AND m.Owner_ID = '$cur_owner_id'
                    ORDER BY c.date DESC";
    $comment_result = mysqli_query($conn, $comment_sql);
    $comments = mysqli_fetch_all($comment_result, MYSQLI_ASSOC);
    if(!empty($comments))
    {
        $cur_owner_has_comments = True;
    }
    $highestStores = [[]];
    if(isset($_POST['search_comment'])){
        $projectionSql = "SELECT";
        if(isset($_POST['shop_name']))
        {
            $is_shop = $_POST['shop_name'];
            if($is_shop)
            {
                $projectionSql = $projectionSql." m.Shop_Name,";
            }
        }
        if(isset($_POST['shop_address']))
        {
            $is_address = $_POST['shop_address'];
            if($is_address)
            {
                $projectionSql = $projectionSql."  m.Address,";
            }
        }

        if(isset($_POST['shop_zipcode']))
        {
            $is_zipcode = $_POST['shop_zipcode'];
            if($is_zipcode)
            {
                $projectionSql = $projectionSql." m.Zip_Code,";
            }
        }
        if(isset($_POST['shop_phone']))
        {
            $is_phone= $_POST['shop_phone'];
            if($is_phone)
            {
                $projectionSql = $projectionSql." m.Phone_Number,";
            }
        }
        $projectionSql = $projectionSql." m.Average_Rating FROM milk_tea_shop m 
        WHERE m.Owner_ID = '$cur_owner_id' AND m.Average_Rating = 
        (SELECT max(m1.Average_Rating) FROM milk_tea_shop m1 WHERE m1.Owner_ID = m.Owner_ID);";
        //echo $projectionSql;
        $projectionResult = mysqli_query($conn, $projectionSql);
        $highestStores = mysqli_fetch_all($projectionResult, MYSQLI_ASSOC);
        echo $conn->error;
    }
    $badRatings = [];
    if(isset($_POST['search_rating']))
    {
        $badRatingSql = "SELECT m.Shop_Name, min(cc.Rating_Level) as minRate
        FROM comments_from_customer cc, milk_tea_shop m
        WHERE cc.Shop_ID = m.Shop_ID AND cc.Shop_ID IN(
            SELECT m2.Shop_ID
            FROM milk_tea_shop m2
            WHERE m2.Owner_ID = '$cur_owner_id'
        )
        GROUP BY cc.Shop_ID, m.Shop_Name;";
        $badRatingResult = mysqli_query($conn, $badRatingSql);
        $badRatings = mysqli_fetch_all($badRatingResult, MYSQLI_ASSOC);
    }
    if(isset($_POST['edit_owner']))
    {
        header("Location: editowner.php");
    }
?>

<!DOCTYPE html>
<html>
    <?php include('ownavbar.php'); ?>
<ul class="nav nav-tabs">
    <li class = "nav-item active">
        <a class="nav-link" href="#profile"  >Profile</a>
    </li>
    <li class="nav-item">
        <a class="nav-link " href="#stores"  >Stores</a>
    </li>
    <li class="nav-item">
        <a class="nav-link " href="#comments"  >Comments</a>
    </li>
</ul>
<div id="myTabContent" class="tab-content">
    <div class="tab-pane fade in active show" id="profile">
        <div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
            <div class="card-header">Your account details are below:</div>
            <div class="card-body">
                <table class="table table-hover">
                    <tr>
                        <td>User_ID:</td>
                        <td><?=$id?></td>
                    </tr>
                    <tr>
                        <td>User_Name:</td>
                        <td><?=$username?></td>
                    </tr>
                    <tr>
                        <td>Email:</td>
                        <td><?=$email?></td>
                    </tr>
                    <tr>
                        <td>Business License:</td>
                        <td><?=$license?></td>
                    </tr>
                </table>
                <br>
                <form action="editowner.php" method="POST">
                    <button type="submit" name="edit_owner" class="btn btn-secondary my-2 my-sm-0">Edit</button>
                </form>
            </div>
        </div>
    </div>
    <div class="tab-pane fade show" id="stores">
        <div class="container">
            <?php if($cur_owner_has_store):?>
                <table class = "table table-hover">
                    <thead>
                        <tr>
                            <th scope="col">Shop Name</th>
                            <th scope="col">Shop Address</th>
                            <th scope="col">Shop Rating</th>
                            <th scope="col">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($stores as $store) :?>
                        <tr>
                            <td><?= $store['Shop_Name'];?></td>
                            <td><?= $store['Address'];?></td>
                            <td><?= $store['Rating'];?></td>
                            <td>
                                <a class="nav-link" href="shopmanage.php?id=<?=$store['Shop_ID']?>">
                                More</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You don't have any store under your name yet, create your first store!!! </p>
            <?php endif;?>
        </div>
    </div>
    <div class="tab-pane fade show" id="comments">
        <div class="container">
            <?php if($cur_owner_has_comments):?>
                <table class = "table table-hover">
                    <thead>
                    <tr>
                        <th scope="col">Shop Name</th>
                        <th scope="col">Comment</th>
                        <th scope="col">Comment Rating</th>
                        <th scope="col">Date</th>
                        <th scope="col">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach($comments as $comment) :?>
                        <tr>
                            <td><?= $comment['Shop_Name'];?></td>
                            <td><?= $comment['Contents'];?></td>
                            <td><?= $comment['Rating_Level'];?></td>
                            <td><?php
                                $cd = $comment['Date'];
                                $discdate = date('Y-m-d',strtotime($cd));
                                echo $discdate;
                                ?>
                            </td>
                            <?php
                                $target_ID = $comment['Comment_ID'];
                                $checkReplyquery = "SELECT count(*) AS Count FROM Reply_with_Feedback 
                                WHERE Account_ID = '$cur_owner_id'AND Replied_Comment_ID = '$target_ID';";
                                $checkReplyResult = mysqli_query($conn, $checkReplyquery);
                                $reply = mysqli_fetch_assoc($checkReplyResult);
                                echo $conn->error;
                            ?>
                            <td>
                                <?php if(!$reply['Count']):?>
                                <a class="nav-link" href="addfeeback.php?index=0&id=<?=$comment['Comment_ID']?>">
                                    Reply</a>
                                <?php else:?>
                                <a class="nav-link" href="addfeeback.php?index=1&id=<?=$comment['Comment_ID']?>">
                                    Edit</a>
                                <?php endif;?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You don't have any comments associated with your business.</p>
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
<div class="container">
    <div class="row">
        <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <legend>Find your highest rated store(s)</legend>
            <label>Pick the attributes you want to view</label>
            <p>(0 means this store has not be commented by any customer)</p>
            <div class="col-sm">
            <div class="form-group">
                <div class="form-check">
                    <label class="form-check-label">
                        <input class="form-check-input" type="checkbox" name="shop_name" value="1" checked>
                        Shop Name
                    </label>
                </div>
            </div>
            </div>
            <div class="col-sm">
                <div class="form-group">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="shop_address" value="1">
                            Shop Address
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-sm">
                <div class="form-group">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="shop_zipcode" value="1">
                            Zip Code
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-sm">
                <div class="form-group">
                    <div class="form-check">
                        <label class="form-check-label">
                            <input class="form-check-input" type="checkbox" name="shop_phone" value="1">
                            Phone Number
                        </label>
                    </div>
                </div>
            </div>
            <div class="col-sm">
                <button type="submit" name="search_comment" class="btn btn-primary">Search</button>
            </div>
        </form>
    </div>
    <div class="row">
        <div class="container">
            <?php foreach($highestStores AS $highestStore):?>
            <div class="row">
                <?php foreach($highestStore AS $itemkey => $itemvalue):?>
                <div class="col-sm">
                    <P><?=$itemkey?> : <?=$itemvalue?></P>
                </div>
                <?php endforeach;?>
            </div>
            <?php endforeach;?>
        </div>
    </div>
    <br><br>
    <div class="row">
        <div class="col-sm">
            <table class = "table table-hover">
                <thead>
                <tr>
                    <th scope="col">Shop Name</th>
                    <th scope="col">Min Rating</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach($badRatings AS $badRating):?>
                    <tr>
                        <td><?=$badRating['Shop_Name']?></td>
                        <td><?=$badRating['minRate']?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="col-sm">
            <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <legend>Find the lowest rating for each of your stores</legend>
                <p>(0 means this store has not be commented by any customer)</p>
                <button type="submit" name="search_rating" class="btn btn-primary">Search</button>
            </form>
        </div>
    </div>
</div>
<?php include('footer.php'); ?>
</body>
</html>