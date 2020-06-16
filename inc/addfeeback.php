<?php
    require('config/db.php');
    if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }
    $msg = '';
    $msgClass = '';

    if(isset($_SESSION['logged_owner_id'])){
        $cur_owner_id = $_SESSION['logged_owner_id'];
    }
    else{
        session_unset();
        header('Location: ../index.php');
        exit;
    }
    if(isset($_GET['id'],$_GET['index'])){
        $target_comment_id = mysqli_real_escape_string($conn, $_GET['id']);
        $index = $_GET['index'];
        settype($index, 'integer');
        $error = '';
        if($index){
            //echo "edit comment";
            $getFeedbackSql = "SELECT Comment_ID, Contents FROM Reply_with_Feedback 
                                WHERE Account_ID = '$cur_owner_id'AND Replied_Comment_ID = '$target_comment_id';";
            $getFeedbackResult = mysqli_query($conn, $getFeedbackSql);
            $getFeedback = mysqli_fetch_assoc($getFeedbackResult);
            $replyContent = $getFeedback['Contents'];
            $feedbackID = $getFeedback['Comment_ID'];
        }
        else
        {
            //echo "add comment";
            $replyContent = '';
        }

        if(isset($_POST['save_feedback'])){
            $replyContent = mysqli_real_escape_string($conn,$_POST['owner_feedback']);
            if(strlen($replyContent)>300)
            {
                $error = "This reply is too long, no more than 300 characters";
            }

            if($error === '')
            {
                $cur_datetime = date("Y-m-d H:i:s");
                if($index)
                {
                    $updateFeedbackSql = "UPDATE reply_with_feedback
                    SET Contents = '$replyContent', Date = '$cur_datetime'
                    WHERE Comment_ID = '$feedbackID';";
                    $updateFeedbackResult = mysqli_query($conn, $updateFeedbackSql);
                    echo $conn->error;
                    if($updateFeedbackResult){
                        $msg = 'Update successfully!';
                        $msgClass = 'alert-success';
                        header("Location: ownerprofile.php");
                    }
                    else{
                        $msg = 'Update failed!';
                        $msgClass = 'alert-danger';
                    }
                }
                else{
                    $maxIDSql = "SELECT IFNULL(max(Comment_ID),800) as maxID FROM reply_with_feedback;";
                    $maxIDResult = mysqli_query($conn, $maxIDSql);
                    $maxID = mysqli_fetch_assoc($maxIDResult);
                    $newID = (int)$maxID['maxID'] + 1;
                    $newFeedbackSql = "INSERT INTO reply_with_feedback
                    VALUES('$newID','$replyContent','$cur_datetime','$cur_owner_id','$target_comment_id')";
                    $newFeedbackResult = mysqli_query($conn, $newFeedbackSql);
                    if($newFeedbackResult)
                    {
                        $msg = 'Insertion successfully!';
                        $msgClass = 'alert-success';
                        header("Location: ownerprofile.php");
                    }else
                    {
                        $msg = 'Insertion failed!';
                        $msgClass = 'alert-danger';
                    }
                }
            }
            else
            {
                $msg =  'User input invalid!';
                $msgClass = 'alert-danger ';
            }
        }

        if(isset($_POST['cancel_feedback']))
        {
            header('Location: ownerprofile.php');
        }

    }
    else{
       header('Location: ownerprofile.php');
    }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Feedback</title>
    <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/cosmo/bootstrap.min.css">
</head>
<body>
<?php
    $getShopNameSql = "SELECT m.Shop_Name
    FROM milk_tea_shop m
    WHERE m.Shop_ID IN(
    SELECT cc.Shop_ID
    FROM comments_from_customer cc
    WHERE cc.Comment_ID = '$target_comment_id'
    );";
    $getShopNameResult = mysqli_query($conn, $getShopNameSql);
    $getShopName = mysqli_fetch_assoc($getShopNameResult);
    $shop_name = $getShopName['Shop_Name'];
?>
<nav class="navbar navbar-default">
    <div class="container">
        <div class="navbar-header">
            <h2 class="navbar-brand">Reply feedback of <?=$shop_name?></h2>
        </div>
    </div>
</nav>
<div class="container">
    <?php if($msg != ''): ?>
        <div class="alert <?php echo $msgClass; ?>"><?php echo $msg; ?></div>
    <?php endif; ?>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>?index=<?=$index?>&id=<?=$target_comment_id?>">
        <div class="form-group">
            <?php
                $commentsql = "SELECT Contents FROM comments_from_customer WHERE Comment_ID = '$target_comment_id';";
                $commentResult = mysqli_query($conn, $commentsql);
                $comment = mysqli_fetch_assoc($commentResult);
            ?>
            <label>Customer Comment:</label>
            <p class="text-info"><?php echo $comment['Contents']?></p>
        </div>
        <div class="form-group">
            <label>Your reply</label>
            <input type="text" name="owner_feedback" class="form-control" value="<?=htmlspecialchars($replyContent);?>" required>
            <div class="text-danger"><?=$error;?></div>
        </div>
        <br>
        <button type="submit" name="save_feedback" class="btn btn-primary">Save</button>
        <button type="submit" name="cancel_feedback" class="btn btn-primary" formnovalidate>Cancel</button>
    </form>
</div>
<?php include('footer.php'); ?>
</body>
</html>
