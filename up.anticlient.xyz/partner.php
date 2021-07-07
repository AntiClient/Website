<?php

require_once('./assets/inc/handle.php');
$funcs = new Funcs();
$uData = $funcs->getUData($_SESSION['uname']);

if(!isset($_SESSION['uname']) || empty($_SESSION['uname'])) {
  $_SESSION['error'] = 'You need to be logged first.';
  header('Location: ./index.php?redirectTo=partner.php');
  exit;
} else if($uData['banned'] == 1) {
  session_destroy();
  session_start();
  $_SESSION['error'] = 'You\'re banned from our website.';
  header('Location: ./index.php');
  exit;
} else if (!($uData['rank'] == 2 || $uData['rank'] == 5)) {
  $_SESSION['error'] = 'You\'re not allowed to visit that page.';
  header('Location: ./dashboard.php');
  exit;
}

if(isset($_GET['add-partner'])) {
  if(!empty($_POST['uname'])) {
    if($funcs->checkUser($_POST['uname']) > 0) {
      if($funcs->checkPartner($_POST['uname']) != 0) {
        $_SESSION['error'] = 'This user is already a partner.';
        header('Location: ./partner.php');
        exit;
      } else {
        if($funcs->addPartner($_POST['uname'], $uData['username'])) {
            $_SESSION['success'] = 'Partner added successfully.';
            header('Location: ./partner.php');
            exit;
        } else {
          $_SESSION['error'] = 'Whoops, something went wrong.';
          header('Location: ./partner.php');
          exit;
        }
      }
    } else {
      $_SESSION['error'] = 'User doesn\'t exist.';
      header('Location: ./partner.php');
      exit;
    }
  } else {
    $_SESSION['error'] = 'Please fill in all fields.';
    header('Location: ./partner.php');
    exit;
  }
} else if(isset($_POST['delpartner']) && $_POST['delpartner'] == true) {
  if($funcs->removePartner($_POST['data'], $uData['username'])) {
    $_SESSION['success'] = 'Partner removed successfully.';
    header('Location: ./partner.php');
    exit;
  } else {
    $_SESSION['error'] = 'Whoops, something went wrong.';
    header('Location: ./partner.php');
    exit;
  }
}

if(isset($_GET['logout'])) {
  session_destroy();
  header('Location: ./index.php');
  exit;
}

 ?>
 <html>
  <head>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="./assets/js/jquery.redirect.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
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
            $.redirect('partner.php', {'delpartner': true,'data': this.id});
          }
        });
      });
    </script>

    <title>AntiClient - Partner</title>

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
            <?php if($uData['rank'] == 5) { echo '<li><a href="./products.php"><i class="fas fa-certificate"></i> Products</a></li>'; } ?>
            <?php if($uData['rank'] == 5) { echo '<li><a href="./logs.php"><i class="fas fa-clipboard-list"></i> Login Logs</a></li>'; } ?>
            <?php if($uData['rank'] == 2 || $uData['rank'] == 5) { echo '<li><a class="active" href="./partner.php"><i class="fas fa-handshake"></i> Partner</a></li>';} ?>
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

              if($uData['licenses'] > 0 && $uData['licenses'] != null) { ?>
                <div class="box add-partner" style="width: 40%;">
                  <div class="box-header">Add a partner</div>
                  <div class="box-content">
                    <form action="?add-partner" method="POST">
                      <div class="form-input-group">
                        <span>Username</span>
                        <input class="form-input" type="text" name="uname" placeholder="Insert here the username" >
                      </div>
                      <div class="form-input-group">
                        <span>You have <b><?php echo $uData['licenses']; ?></b> licenses left</span>
                      </div>
                      <div class="form-input-group">
                        <button class="form-submit" type="submit">Add</button>
                      </div>
                    </form>
                  </div>
                </div>
              <?php } else { ?>
                <div class="box add-partner" style="width: 40%;">
                  <div class="box-header">Add a partner</div>
                  <div class="box-content">
                    <center><b>Whoops, looks like you haven't got any license to use, please talk to an Administrator!</b></center>
                  </div>
                </div>
              <?php } ?>
              <div class="box">
                <div class="box-header">Your partners</div>
                <div class="box-content">
                  <?php if($funcs->checkPartners($uData['username']) > 0) { ?>
                    <table>
                      <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Added on</th>
                        <th>Remove</th>
                      </tr>
                      <?php foreach($funcs->getPartners($uData['username']) as $partner) { ?>
                        <tr>
                          <td><?php echo $partner['id']; ?></td>
                          <td><?php echo $partner['username']; ?></td>
                          <td><?php echo date('d/m/Y', $partner['addedon']); ?></td>
                          <td><div class="tooltip"><i id="<?php echo $partner['id']; ?>" class="fas fa-user-slash delprod"></i><span id="text-<?php echo $partner['id']; ?>" class="tooltiptext">Are you sure? Click again to confirm.</span></div></td>
                        </tr>
                      <?php } ?>
                    </table>
                  <?php } else { ?>
                    <center><b>Looks like you don't have any partner added yet!</b></center>
                  <?php } ?>
                </div>
              </div>

      </div>
  </body>
 </html>
