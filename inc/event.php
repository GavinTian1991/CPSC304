<?php
    require('config/db.php');
    if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }
    if(isset($_SESSION['logged_owner_name'],$_SESSION['current_shop_id'])){
        $shop_id = $_SESSION['current_shop_id'];
        $shop_name = $_SESSION['current_shop_name'];
    }
    else{
        session_unset();
        header('Location: ../index.php');
        exit;
    }
    $msg = '';
    $msgClass = '';
    $errors = array('eventname' => '', 'eventcontent' => '');
    if(isset($_GET['name']))
    {
        //Doing editing
        $event_name = mysqli_real_escape_string($conn,$_GET['name']);
        //Query the old event content
        $oldEventSql = "SELECT Event_Content FROM holds_sales_event
                        WHERE Shop_ID = '$shop_id' AND Event_name = '$event_name';";
        $oldEventResult = mysqli_query($conn, $oldEventSql);
        $oldEvent = mysqli_fetch_assoc($oldEventResult);
        echo $conn->error;
        $event_content = $oldEvent['Event_Content'];
        if(isset($_POST['save_event']))
        {
            $event_content = mysqli_real_escape_string($conn, $_POST['event_contents']);
            if(strlen($event_content)>60)
            {
                $errors['eventcontent'] = "Event content is too long, 60 characters at most!";
            }

            if(!array_filter($errors))
            {
                $msg =  'User input valid!';
                $msgClass = 'alert-success';
                $editEventSql = "UPDATE holds_sales_event 
                SET Event_Content = '$event_content'
                WHERE Shop_ID = '$shop_id' AND Event_name = '$event_name';";
                $editEventResult = mysqli_query($conn,$editEventSql);
                if($editEventResult)
                {
                    $msg =  'Update succeeded!';
                    $msgClass = 'alert-success ';
                    //mysqli_free_result($editEventResult);
                    header("Location: shopmanage.php");
                }
                else
                {
                    echo $conn->error;
                    $msg =  'Update failed!';
                    $msgClass = 'alert-danger ';
                }
            }
            else
            {
                $msg =  'User input invalid!';
                $msgClass = 'alert-danger ';
            }
        }

    }
    else
    {
        //Doing adding
        $event_name = $event_content = '';
        if(isset($_POST['save_event']))
        {
            $event_name = mysqli_real_escape_string($conn, $_POST['event_name']);
            $event_content = mysqli_real_escape_string($conn, $_POST['event_contents']);
            if(strlen($event_name)>20)
            {
                $errors['eventname'] = "Event Name is too long，20 characters at most!";
            }
            if(strlen($event_content)>60)
            {
                $errors['eventcontent'] = "Event content is too long, 60 characters at most!";
            }

            if(!array_filter($errors))
            {
                $msg =  'User input valid!';
                $msgClass = 'alert-success';
                $newEventSql = "INSERT INTO holds_sales_event 
                VALUES('$shop_id', '$event_name','$event_content')";
                $newEventResult = mysqli_query($conn,$newEventSql);
                if($newEventResult)
                {    //Only need to send message when the store is favored by any customer
                    $findAccountSql = "SELECT Customer_Account_ID FROM favored_by WHERE Shop_ID = '$shop_id'";
                    $findAccountResult = mysqli_query($conn, $findAccountSql);
                    $accounts = mysqli_fetch_all($findAccountResult,MYSQLI_ASSOC);
                    if(!empty($accounts))
                    {
                        $maxNotiIDSql = "SELECT IFNULL(max(Notification_ID),1700) AS Max_ID FROM notification";
                        $maxNotiIDresult = mysqli_query($conn, $maxNotiIDSql);
                        $maxNotiID = mysqli_fetch_assoc($maxNotiIDresult);
                        mysqli_free_result($maxNotiIDresult);
                        $newID = (int)$maxNotiID['Max_ID'] + 1;
                        $message = $shop_name." is holding a new sales event, come check out!";
                        $type = "sale";
                        $createNotiSql = "INSERT INTO notification 
                                            VALUES($newID, '$type', '$message')";
                        $createNotiResult = mysqli_query($conn,$createNotiSql);
                        echo $conn->error;
                        foreach($accounts AS $account) {
                            $customer_id = $account['Customer_Account_ID'];
                            $datetime = date("Y-m-d H:i:s");
                            $on_read = (int)0;
                            $sendNotiSql = "INSERT INTO sends_to_account
                             VALUES('$newID','$customer_id','$datetime',$on_read);";
                            $sendNotiResult = mysqli_query($conn, $sendNotiSql);
                        }
                        echo $conn->error;
                    }
                    $msg =  'Insertion succeeded!';
                    $msgClass = 'alert-success ';
                    header("Location: shopmanage.php");
                }
                else
                {
                    $msg =  'Insertion failed!';
                    $msgClass = 'alert-danger ';
                }
            }
            else
            {
                $msg =  'User input invalid!';
                $msgClass = 'alert-danger ';
            }
        }
    }

    if(isset($_POST['cancel_event']))
    {
        header('Location: shopmanage.php');
    }
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Drink</title>
    <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/cosmo/bootstrap.min.css">
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <h2 class="navbar-brand">Event for <?=$shop_name?></h2>
        </div>
    </div>
</nav>
<div class="container">
    <?php if($msg != ''): ?>
        <div class="alert <?php echo $msgClass; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF'];?><?php
        if(isset($_GET['name']))
        {
            echo "?name="."$event_name";
        }
    ?>">
        <legend>Event Information</legend>
        <div class="form-group">
            <label>Event Name</label>
            <input type="text" name="event_name" class="form-control" value="<?=htmlspecialchars($event_name);?>" required
            <?php
            if(isset($_GET['name']))
            {
                echo " disabled=\"\"";
            }
            ?>>
            <div class="text-danger"><?=$errors['eventname'];?></div>
        </div>
        <div class="form-group">
            <label>Event Content</label>
            <input type="text" name="event_contents" class="form-control" value="<?=htmlspecialchars($event_content);?>" required>
            <div class="text-danger"><?=$errors['eventcontent']; ?></div>
        </div>
        <br>
        <button type="submit" name="save_event" class="btn btn-primary">Save</button>
        <button type="submit" name="cancel_event" class="btn btn-primary" formnovalidate>Cancel</button>
    </form>
</div>
<?php include('footer.php'); ?>
</body>
</html>
