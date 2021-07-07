<?php

require_once('./assets/inc/handle.php');
$funcs = new Funcs();
$uData = $funcs->getUData($_SESSION['uname']);

if(!isset($_SESSION['uname']) || empty($_SESSION['uname'])) {
  $_SESSION['error'] = 'You need to be logged first.';
  header('Location: ./index.php?redirectTo=purchase.php');
  exit;
} else if($uData['banned'] == 1) {
  session_destroy();
  session_start();
  $_SESSION['error'] = 'You\'re banned from our website.';
  header('Location: ./index.php');
  exit;
}

if(isset($_GET['logout'])) {
  session_destroy();
  header('Location: ./index.php');
  exit;
}

if(isset($_POST['buypkg']) && $_POST['buypkg'] == true) {
  if($uData['rank'] == 0) {
    $pkgData = $funcs->getPackageInfo($_POST['data']);
    $tmp = $funcs->makePayment($pkgData['price'], $pkgData['name']);
    if($tmp != false) {
      header('Location: '.$tmp);
      exit;
    } else {
      $_SESSION['error'] = 'Whoops, something went wrong.';
      header('Location: ./purchase.php');
      exit;
    }
  } else {
    $_SESSION['error'] = 'You already have a plan active.';
    header('Location: ./purchase.php');
    exit;
  }
}

if (isset($_GET['success']) && $_GET['success'] == 'true') {
  if($funcs->executePayment($_GET['paymentId'], $_GET['PayerID'], $_GET['prc'])) {
    if($funcs->updateUserRank($uData['username'], $_GET['plan'])) {
		//if($funcs->logPayment($uData['username'], $_GET['prc'], $_GET['PayerID'], $_GET['plan'], $_GET['paymentId'])){
			$_SESSION['success'] = 'Payment succeeded.';
			header('Location: ./purchase.php');
			exit;
		//}else{
			$_SESSION['error'] = 'Whoops, something went wrong.';
			header('Location: ./purchase.php');
			exit;
		//}
    } else {
      $_SESSION['error'] = 'Whoops, something went wrong.';
      header('Location: ./purchase.php');
      exit;
    }
  } else {
    $_SESSION['error'] = 'Whoops, something went wrong.';
    header('Location: ./purchase.php');
    exit;
  }
} else if(isset($_GET['success']) && $_GET['success'] == 'false'){
  $_SESSION['error'] = 'Operation cancelled.';
  header('Location: ./purchase.php');
  exit;
}

 ?>
 <html>
  <head>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="./assets/js/jquery.redirect.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <script src="./assets/js/loader.defvalues.js"></script>
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <link rel="shortcut icon" type="image/png" href="./assets/img/favicon.ico"/>

    <script>
      $( document ).ready(function() {
        <?php
        foreach($funcs->getProducts() as $product) { ?>
          $("#<?php echo $product['id']; ?>").click(function() {
            $.redirect('purchase.php', {'buypkg': true,'data': <?php echo $product['id']; ?>});
          });
        <?php } ?>
      });
    </script>

    <title>AntiClient - Purchase</title>

  </head>
  <body class="preload">
      <header>
        <div id="opm" class="open-menu">
          <i class="fas fa-bars"></i>
        </div>
        <div class="user-interaction">
          <div class="dropdown">
            <i id="dropd" class="fas fa-user"></i>
            <div class="dropdown-content">
              <a href="./profile.php">Profile <i class="fas fa-cogs"></i></a>
              <a href="?logout">Log out <i class="fas fa-sign-out-alt"></i></a>
            </div>
          </div>
        </div>
      </header>
      <div class="vertical-wrapper">
        <div class="menu-header">
          <span id="clm" class="menu-mobile-form"><i class="fas fa-arrow-left"></i> AntiClient</span>
          <span class="menu-desktop-form"> AntiClient</span>
        </div>
        <div class="menu">
          <ul class="navbar">
            <li><a href="./dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a class="active" href="#"><i class="fas fa-shopping-cart"></i> Purchase</a></li>
            <?php if($uData['rank'] >= 1 || $funcs->checkPartner($uData['username']) > 0) { echo '<li><a href="./customer.php"><i class="fas fa-shield-alt"></i> AntiClient</a></li>'; } ?>
            <?php if($uData['rank'] == 5) { echo '<li><a href="./users.php"><i class="fas fa-users-cog"></i> Users</a></li>'; } ?>
            <?php if($uData['rank'] == 5) { echo '<li><a href="./products.php"><i class="fas fa-certificate"></i> Products</a></li>'; } ?>
            <?php if($uData['rank'] == 5) { echo '<li><a href="./logs.php"><i class="fas fa-clipboard-list"></i> Login Logs</a></li>'; } ?>
            <?php if($uData['rank'] == 2 || $uData['rank'] == 5) { echo '<li><a href="./partner.php"><i class="fas fa-handshake"></i> Partner</a></li>';} ?>
          </ul>
        </div>
        <div class="menu-footer">
          <span class="menu-footer-text">&copy; 2017 - <?php echo date("Y"); ?> Anticlient</span>
        </div>
      </div>
      <div class="content">
        <?php

          if((isset($_SESSION['error']) && !empty($_SESSION['error'])) || (isset($_SESSION['success']) && !empty($_SESSION['success']))) { ?>
            <div class="form-error <?php if(!empty($_SESSION['error'])) { echo 'true'; } else if(!empty($_SESSION['success'])) { echo 'false'; } ?>">
              <?php if(!empty($_SESSION['error'])) { echo $_SESSION['error']; } else if(!empty($_SESSION['success'])) { echo $_SESSION['success']; } ?>
            </div>
        <?php $_SESSION['error'] = null;
              $_SESSION['success'] = null; } ?>

              <div class="packages">
                <?php foreach($funcs->getProducts() as $product) { ?>
                  <div class="package">
                    <div class="package-header">
					<?php echo $product['name']; ?>
					<small>(&euro;<?php echo $product['price']; ?>)</small>
					</div>
                    <div class="package-content">
                      <?php echo $product['features']; ?><button id="<?php echo $product['id'] ?>" class="form-submit">Purchase</button>
                    </div>
                  </div>
                <?php } ?>
              </div>

      </div>
  </body>
 </html>
