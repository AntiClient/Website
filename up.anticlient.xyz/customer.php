<?php

require_once('./assets/inc/handle.php');
$funcs = new Funcs();
$uData = $funcs->getUData($_SESSION['uname']);

if(!isset($_SESSION['uname']) || empty($_SESSION['uname'])) {
  $_SESSION['error'] = 'You need to be logged first.';
  header('Location: ./index.php?redirectTo=customer.php');
  exit;
} else if($uData['banned'] == 1) {
  session_destroy();
  session_start();
  $_SESSION['error'] = 'You\'re banned from our website.';
  header('Location: ./index.php');
  exit;
} else if (!($uData['rank'] > 0 || $funcs->checkPartner($uData['username']) > 0)) {
  $_SESSION['error'] = 'You need to buy a package first!';
  header('Location: ./purchase.php');
  exit;
}

if(isset($_GET['logout'])) {
  session_destroy();
  header('Location: ./index.php');
  exit;
} else if(isset($_GET['gen-download'])) {
  if($funcs->genDown($_SESSION['uname'])) {
    $_SESSION['success'] = 'Link generated successfully.';
    header('Location: ./customer.php');
    exit;
  } else {
    $_SESSION['error'] = 'Whoops, something went wrong.';
    header('Location: ./customer.php');
    exit;
  }
} else if(isset($_GET['gen-pin'])) {
  if($funcs->genPIN($_SESSION['uname'])) {
    $_SESSION['success'] = 'PIN generated successfully.';
    header('Location: ./customer.php');
    exit;
  } else {
    $_SESSION['error'] = 'Whoops, something went wrong.';
    header('Location: ./customer.php');
    exit;
  }
}else if(isset($_GET['removeSession'])){
	
	$username = $_SESSION['uname'];
	$pin = $_POST['pin'];
	
	if(!$funcs->APIDestroySession($username, $pin)){
		$_SESSION['error'] = "Whoops, something went wrong.";
	}else{
		$_SESSION['success'] = "Session destroyed.";
	}
}

 ?>
 <html>
  <head>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    <script src="./assets/js/loader.defvalues.js"></script>
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <link rel="shortcut icon" type="image/png" href="./assets/img/favicon.ico"/>

    <script>

      function copyDownload() {
        $("#dlink").clearQueue();
        $("#copyB").clearQueue();
        $("#dlink").select();
        document.execCommand("copy");
        $("#copyB").html("Copied!");
        setTimeout(function() {
          $("#copyB").html("Copy");
        }, 1200);
        $("#dlink").animate({
          backgroundColor: "green"
        }, 500, function() {
          $("#dlink").animate({
            backgroundColor: "white"
          }, 200)
        });
      }

      function copyPIN() {
        $("#pin").clearQueue();
        $("#copyPINB").clearQueue();
        $("#pin").select();
        document.execCommand("copy");
        $("#copyPINB").html("Copied!");
        setTimeout(function() {
          $("#copyPINB").html("Copy");
        }, 1200);
        $("#pin").animate({
          backgroundColor: "green"
        }, 500, function() {
          $("#pin").animate({
            backgroundColor: "white"
          }, 200)
        });
      }
    </script>

    <title>AntiClient - Control Panel</title>

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
              <a href="profile.php">Profile <i class="fas fa-cogs"></i></a>
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
            <?php if($uData['rank'] >= 1 || $funcs->checkPartner($uData['username']) > 0) { echo '<li><a class="active" href="./customer.php"><i class="fas fa-shield-alt"></i> AntiClient</a></li>'; } ?>
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

              <div class="box">
                <div class="box-header"><i class="fas fa-cloud-download-alt"></i> Generate download link</div>
                <div class="box-content">
                  <div class="gen-form">
                    <form action="?gen-download" method="POST">
                      <div class="form-input-group">
                        <input class="form-input" type="text" id="dlink" <?php if($uData['download'] != null) { echo 'value="'.(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http").'://'.$_SERVER['HTTP_HOST'].'/download.php?user='.$_SESSION['uname'].'&id='.$uData['download'].'"'; } ?> placeholder="Download link" readonly />
                      </div>
                      <div class="form-input-group">
                        <button <?php if($uData['download'] != null) { echo 'style="margin-top: -25px; width: 47%; float: left;"'; } else { echo 'style="margin-top: -25px;"'; } ?> class="form-submit" type="submit">Generate</button>
                        <?php if($uData['download'] != null) {  ?><button style="margin-top: -25px; width: 47%; float: right;" class="form-submit" id="copyB" onclick="copyDownload()" type="button">Copy</button><?php } ?>
                      </div>
                    </form>
                  </div>
                </div>
              </div>
              <div class="box">
                <div class="box-header"><i class="fas fa-key"></i> Generate PIN</div>
                <div class="box-content">
                  <div class="gen-form">
                    <form action="?gen-pin" method="POST">
                      <div class="form-input-group">
                        <input class="form-input" type="text" id="pin" <?php if($uData['pin'] != null) { echo 'value="'.$uData['pin'].'"'; } ?> placeholder="PIN" readonly />
                      </div>
                      <div class="form-input-group">
                        <button <?php if($uData['pin'] != null) { echo 'style="margin-top: -25px; width: 47%; float: left;"'; } else { echo 'style="margin-top: -25px;"'; } ?> class="form-submit" type="submit">Generate</button>
                        <?php if($uData['pin'] != null) {  ?><button style="margin-top: -25px; width: 47%; float: right;" class="form-submit" id="copyPINB" onclick="copyPIN()" type="button">Copy</button><?php } ?>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <?php if($funcs->checkResults($uData['username']) > 0) { ?>
                <div class="box">
                  <div class="box-header">Last Screenshares</div>
                  <div class="box-content">
                    <table>
                      <tr>
                        <th>Check #1</th>
                        <th>Check #2</th>
                        <th>Check #3</th>
                        <th>Date</th>
                      </tr>
                      <?php
                          foreach($funcs->getResults($uData['username']) as $data) { ?>
                            <tr>
                              <td><?php echo $data['check1']; ?></td>
                              <td><?php echo $data['check2']; ?></td>
                              <td><?php echo $data['check3']; ?></td>
                              <td><?php echo $data['date']; ?></td>
                            </tr>
                        <?php  }
                       ?>
                    </table>
                  </div>
                </div>
              <?php }
                if($funcs->checkSessions($uData['username']) > 0) { ?>
                  <div class="box" style="width: 70%;">
                    <div class="box-header">Last Sessions</div>
                    <div class="box-content">
                      <table>
                        <tr>
                          <th>PIN</th>
                          <th>Status</th>
                          <th>Date</th>
                        </tr>
                        <?php foreach($funcs->getSessions($uData['username']) as $session) { ?>
                          <tr>
						  <form action="?removeSession" method="POST">
                            <td><?php echo $session['pin']; ?></td>
                            <td><?php echo $session['status']; ?></td>
                            <td><?php echo $session['date']; ?></td>
							<input name="pin" hidden value="<?php echo $session['pin']; ?>">
							<?php if($session['status'] == 'Open'){?> <td><button style="margin-top: -25px;" class="form-submit" type="submit">Destroy</button></td> <?php } ?>
							</form>
                          </tr>
                        <?php } ?>
                      </table><br>
                    </div>
                  </div>
                <?php } ?>

      </div>
  </body>
 </html>
