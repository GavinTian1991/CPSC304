<?php
    if(session_status() !== PHP_SESSION_ACTIVE){
        session_start();
    }

//    $cur_owner_name = '';
//
//    if(!isset($_SESSION['owner_logged_in']))
//    {
//        header('Location:../index.php');
//        exit;
//    }
//
//    if(isset($_SESSION['owner_logged_in'])) {
//        $cur_owner_name = $_SESSION['logged_owner_name'];
//        $cur_owner_id = $_SESSION['logged_owner_id'];
//        echo "owner id is ".$cur_owner_id;
//    }

    if(isset($_POST['ownerLogout'])) {
        unset($_SESSION['owner_logged_in']);
        unset($_SESSION['logged_owner_name']);
        unset($_SESSION['logged_owner_id']);
        header('Location: ../index.php');
    }
?>


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Profile</title>
    <link rel="stylesheet" type="text/css" href="https://bootswatch.com/4/cosmo/bootstrap.min.css">
<!--    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <a class="navbar-brand" href="ownerprofile.php">Hello <?php echo $cur_owner_name . ' '?></a>
        <div class="collapse navbar-collapse" >
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="#">Add Store</a>
                </li>
            </ul>
            <form class="form-inline my-2 my-lg-0" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                <button class="btn btn-secondary my-2 my-sm-0" type="submit" name="ownerLogout">Log Out</button>
            </form>
        </div>
    </nav>