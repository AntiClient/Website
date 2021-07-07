<?php

require_once('./assets/inc/handle.php');
$funcs = new Funcs();
$uData = $funcs->getUData($_SESSION['uname']);

if(!isset($_SESSION['uname']) || empty($_SESSION['uname'])) {
  $_SESSION['error'] = 'You need to be logged first.';
  header('Location: ./index.php?redirectTo=products.php');
  exit;
} else if($uData['banned'] == 1) {
  session_destroy();
  session_start();
  $_SESSION['error'] = 'You\'re banned from our website.';
  header('Location: ./index.php');
  exit;
} else if($uData['rank'] != 5) {
  $_SESSION['error'] = 'You\'re not allowed to visit that page.';
  header('Location: ./dashboard.php');
  exit;
}

if(isset($_GET['logout'])) {
  session_destroy();
  header('Location: ./index.php');
  exit;
}

if(isset($_GET['add'])) {
  if(!(empty($_POST['name']) || empty($_POST['price']) || empty($_POST['type']))) {
    if(is_numeric($_POST['price'])) {
      if(empty($_POST['features'])) {
        $_POST['features'] = '300+ detections<div class="package-info-divider"></div><br>Xray pack detection<div class="package-info-divider"></div><br>Alts detections<div class="package-info-divider"></div><br>Recycle bin check<br>';
      }
      if($funcs->addPackage($_POST['name'], $_POST['price'], $_POST['type'], $_POST['features'])) {
        $_SESSION['success'] = 'Product added correctly.';
        header('Location: ./products.php');
        exit;
      } else {
        $_SESSION['error'] = 'Whoops, something went wrong.';
        header('Location: ./products.php');
        exit;
      }
    } else {
      $_SESSION['error'] = 'Price must be a numeric value.';
      header('Location: ./products.php');
      exit;
    }
  } else {
    $_SESSION['error'] = 'Please fill in all fields.';
    header('Location: ./products.php');
    exit;
  }
}

if(isset($_POST['delprod']) && $_POST['delprod'] == true) {
  if($funcs->removePackage($_POST['data'])) {
    $_SESSION['success'] = 'Package removed successfully.';
    header('Location: ./products.php');
    exit;
  } else {
    $_SESSION['error'] = 'Whoops, something went wrong.';
    header('Location: ./products.php');
    exit;
  }
}

if(isset($_GET['edit']) && empty($_GET['edit'])) {
  if(!(empty($_POST['name']) || empty($_POST['price']) || empty($_POST['type']) || empty($_POST['features']))) {
    if(is_numeric($_POST['price'])) {
      if($funcs->updatePackage($_POST['name'], $_POST['price'], $_POST['type'], $_POST['features'], $_POST['id'])) {
        $_SESSION['success'] = 'Package updated successfully.';
        header('Location: ./products.php');
        exit;
      } else {
        $_SESSION['error'] = 'Whoops, something went wrong.';
        header('Location: ./products.php');
        exit;
      }
    } else {
      $_SESSION['error'] = 'Price must be a numeric value.';
      header('Location: ./products.php');
      exit;
    }
  } else {
    $_SESSION['error'] = 'Please fill in all fields.';
    header('Location: ./products.php');
    exit;
  }
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
        var clicked = '';
        $(".delprod").click(function() {
          var id = '#text-'+this.id;
          if(clicked == '') {
            clicked = '1'+this.id;
            $(id).css({"visibility":"visible","opacity":"1","transition":"300ms"});
            setTimeout(function() {
              clicked = '';
              $(id).css({"visibility":"hidden","opacity":"0"});
            }, 3000);
          } else if (clicked == '1'+this.id) {
            $.redirect('products.php', {'delprod': true,'data': this.id});
          }
        });
      });
    </script>

    <title>AntiClient - Products</title>

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
            <li><a href="./purchase.php"><i class="fas fa-shopping-cart"></i> Purchase</a></li>
            <?php if($uData['rank'] >= 1 || $funcs->checkPartner($uData['username']) > 0) { echo '<li><a href="./customer.php"><i class="fas fa-shield-alt"></i> AntiClient</a></li>'; } ?>
            <?php if($uData['rank'] == 5) { echo '<li><a href="./users.php"><i class="fas fa-users-cog"></i> Users</a></li>'; } ?>
            <?php if($uData['rank'] == 5) { echo '<li><a class="active" href="./products.php"><i class="fas fa-certificate"></i> Products</a></li>'; } ?>
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
              $_SESSION['success'] = null; }

              if(isset($_GET['edit']) && !empty($_GET['edit'])) {
                if($funcs->checkPackageE($_GET['edit']) > 0) {
                  $pData = $funcs->getPackageInfo($_GET['edit']);
                  ?>
                  <div style="width: 70%;" class="box edit-prod">
                    <div class="box-header">Edit product</div>
                    <div class="box-content">
                      <form method="POST" action="?edit">
                        <div class="form-input-group">
                          <span>Name</span>
                          <input class="form-input" name="name" value="<?php echo $pData['name']; ?>" placeholder="Insert here the name" >
                        </div>
                        <div class="form-input-group">
                          <span>Price</span>
                          <input class="form-input" name="price" value="<?php echo $pData['price']; ?>" placeholder="Insert here the price in &euro;" >
                        </div>
                        <div class="form-input-group">
                          <span>Type</span>
                          <select name="type">
                            <option <?php if($pData['type'] == 'Monthly') { echo 'selected'; } ?>>Monthly</option>
                            <option <?php if($pData['type'] == 'Lifetime') { echo 'selected'; } ?>>Lifetime</option>
                          </select>
                        </div>
                        <div class="form-input-group" style="margin-top: -5px; margin-bottom: 70px;">
                          <span>Features (HTML format)</span>
                          <textarea class="form-input" name="features" placeholder="Insert here the features following the correct HTML format."><?php echo $pData['features']; ?></textarea>
                        </div>
                        <input type="hidden" value="<?php echo $pData['id']; ?>" name="id">
                        <div class="form-input-group">
                          <button class="form-submit" type="submit">Insert</button>
                        </div>
                      </form>
                    </div>
                  </div>
                <?php } } else { ?>
                  <div style="width: 70%;" class="box add-prod">
                    <div class="box-header">Add new product</div>
                    <div class="box-content">
                      <form method="POST" action="?add">
                        <div class="form-input-group">
                          <span>Name</span>
                          <input class="form-input" name="name" placeholder="Insert here the name" >
                        </div>
                        <div class="form-input-group">
                          <span>Price</span>
                          <input class="form-input" name="price" placeholder="Insert here the price in &euro;" >
                        </div>
                        <div class="form-input-group">
                          <span>Type</span>
                          <select name="type">
                            <option>Monthly</option>
                            <option>Lifetime</option>
                          </select>
                        </div>
                        <div class="form-input-group" style="margin-top: -5px; margin-bottom: 70px;">
                          <span>Content</span>
                          <textarea class="form-input" name="features" placeholder="Insert here the features following the correct HTML format. (If left empty it will automatically insert default features.)"></textarea>
                        </div>
                        <div class="form-input-group">
                          <button class="form-submit" type="submit">Insert</button>
                        </div>
                      </form>
                    </div>
                  </div>
                <?php } ?>

              <div class="box">
                <div class="box-header">Products list</div>
                <div class="box-content products">
                  <table>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Price</th>
                      <th>Type</th>
                      <th>Manage</th>
                    </tr>
                    <?php
                    foreach($funcs->getProducts() as $product) { ?>
                      <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td><?php echo $product['name']; ?></td>
                        <td>&euro; <?php echo $product['price']; ?></td>
                        <td><?php echo $product['type']; ?></td>
                        <td class="products-manage"><a class="editprod" href="?edit=<?php echo $product['id']; ?>"><i class="fas fa-edit"></i></a>  -  <div class="tooltip"><i id="<?php echo $product['id']; ?>" class="fas fa-trash-alt delprod"></i><span id="text-<?php echo $product['id']; ?>" class="tooltiptext">Are you sure? Click again to confirm.</span></div></td>
                      </tr>
                    <?php } ?>
                  </table>
                  <br>
                </div>
              </div>

      </div>
  </body>
 </html>
