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

    if(!isset($_SESSION['current_day'])){
        if(isset($_GET['day'])){
            $cur_day = mysqli_real_escape_string($conn,$_GET['day']);
            $_SESSION['current_day'] = $cur_day;
        }
        else
        {
            header('Location:shopmanage.php');
            exit;
        }
    }
    else {
        $cur_day = $_SESSION['current_day'];
    }

    $oldTimeSql = "SELECT Open_Time, Close_Time FROM open_at_business_hour
                    WHERE Shop_ID = '$cur_shop_id' AND Business_Day = '$cur_day'";
    $oldTimeResult = mysqli_query($conn, $oldTimeSql);
    $oldTime = mysqli_fetch_assoc($oldTimeResult);
    $openTime = date("H:i", strtotime($oldTime['Open_Time']));
    $closeTime = date("H:i", strtotime($oldTime['Close_Time']));

    if(isset($_POST['save_time'])){
        $openTime = mysqli_real_escape_string($conn, $_POST["open_time"]);
        $closeTime = mysqli_real_escape_string($conn, $_POST["close_time"]);

        $openTime = date("H:i:s", strtotime($openTime));
        $closeTime = date("H:i:s", strtotime($closeTime));

        $updateTimeSql = "UPDATE open_at_business_hour 
                        SET Open_Time = '$openTime', Close_Time = '$closeTime'
                        WHERE Shop_ID = '$cur_shop_id' AND Business_Day = '$cur_day'";
        $updateTimeResult = mysqli_query($conn, $updateTimeSql);
        echo $conn->error;

        if($updateTimeResult)
        {
            $msg =  'Update time successful!';
            $msgClass = 'alert-success';
            unset($_SESSION['current_day']);
            header('location: shopmanage.php');
        }
        else
        {
            $msg =  'Update time failed!';
            $msgClass = 'alert-danger ';
        }
    }

    if(isset($_POST['cancel_time']))
    {
        unset($_SESSION['current_day']);
        header('Location: shopmanage.php');
    }


?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Hour</title>
    <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/cosmo/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <h2 class="navbar-brand">Hour For <?=$cur_shop_name?> on <?=$cur_day?></h2>
        </div>
    </div>
</nav>
<div class="container">
    <?php if($msg != ''): ?>
        <div class="alert <?php echo $msgClass; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
        <legend>Business Hour</legend>
        <div class="form-group">
            <label>Open Time</label>
            <input type="time" name="open_time" class="form-control" value="<?=htmlspecialchars($openTime)?>" required>
        </div>
        <div class="form-group">
            <label>Close Time</label>
            <input type="time" name="close_time" class="form-control" value="<?=htmlspecialchars($closeTime)?>" required>
        </div>
        <br>
        <button type="submit" name="save_time" class="btn btn-primary">Save</button>
        <button type="submit" name="cancel_time" class="btn btn-primary" formnovalidate>Cancel</button>
    </form>
</div>
<?php include('footer.php'); ?>
</body>
</html>
