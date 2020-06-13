<?php
    require('config/db.php');
    if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }
    $cur_owner_id = '';
    $cur_owner_name = '';
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
    WHERE A.Account_ID = $cur_owner_id AND A.Account_ID = BA.Account_ID";
    $result = mysqli_query($conn, $account_sql);
	$profile = mysqli_fetch_assoc($result);
	print_r($profile);
    if($profile)
    {
        $id = $profile['ID'];
        $username = $profile['username'];
        $email = $profile['email'];
        $license = $profile['license'];
    }
    else
    {
        echo 'Failed to fetch user profile!';
    }
    $store_sql = "SELECT Shop_ID, Shop_Name, Address FROM milk_tea_shop WHERE Owner_ID = $cur_owner_id";
    $result = mysqli_query($conn, $store_sql);
    $stores = mysqli_fetch_all($result,MYSQLI_ASSOC);
    print_r($stores);
    if(!$stores)
    {
        echo "Failed to fetch store information";
    }

?>

<!DOCTYPE html>
<html>
    <?php include('ownavbar.php'); ?>
<ul class="nav nav-tabs">
    <li class = "nav-item ">
        <a class="nav-link" href="#profile"  >Profile</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="#stores"  >Stores</a>
    </li>
    <li class="nav-item">
        <a class="nav-link active" href="#comments"  >Comments</a>
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
            </div>
        </div>
    </div>
    <div class="tab-pane fade show" id="stores">
        <div class="container">
            <table class = "table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Edit</th>
                        <th scope="col">Shop Name</th>
                        <th scope="col">Shop Address</th>
                        <th scope="col">Details</th>
                        <th scope="col">Delete</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($stores as $store) :?>
                    <tr>
                        <td>
                            <form method="post" action="editstore.php">
                                <button type="submit" name="editstore" value=<?= $store['Shop_ID'];?> class="btn btn-primary">
                                Edit
                                </button>
                            </form>
                        </td>
                        <td><?= $store['Shop_Name'];?></td>
                        <td><?= $store['Address'];?></td>
                        <td>
                            <a class="nav-link" href="shopmanage.php?id=<?= $store['Shop_ID']?>">
                            More</a>
                        </td>
                        <td>
                            <form method="post" action="editstore.php">
                                <button type="submit" name="editstore" value=<?=$_SERVER['PHP_SELF'];?> class="btn btn-primary">
                                X
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>

            </table>
        </div>
    </div>
    <div class="tab-pane fade show" id="comments">
        <p>Comments TODO</p>
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