<?php

  if(session_status() !== PHP_SESSION_ACTIVE){
    session_start();
  }

  $cur_name = '';

  if(isset($_SESSION['log_in_customer'])) {
    $cur_name = $_SESSION['log_in_customer'];
  }

  if(isset($_POST['logout'])) {
    header('Location: ../index.php');
  }

?> 

 
 <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <a class="navbar-brand" href="customer.php"><?php echo $cur_name . ' '?>Home</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarColor01" aria-controls="navbarColor01" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarColor01">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item">
          <a class="nav-link" href="drinks.php">Drinks</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="cprofile.php">Profile</a>
        </li>
      </ul>
      <form class="form-inline my-2 my-lg-0" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      <button class="btn btn-secondary my-2 my-sm-0" type="submit" name="logout">Log Out</button>
      </form>
    </div>
  </nav>