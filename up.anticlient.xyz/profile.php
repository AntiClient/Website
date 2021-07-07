<?php

require_once('./assets/inc/handle.php');
$funcs = new Funcs();
$uData = $funcs->getUData($_SESSION['uname']);

if(!isset($_SESSION['uname']) || empty($_SESSION['uname'])) {
  $_SESSION['error'] = 'You need to be logged first.';
  header('Location: ./index.php?redirectTo=profile.php');
  exit;
} else if($uData['banned'] == 1) {
  session_destroy();
  session_start();
  $_SESSION['error'] = 'You\'re banned from our website.';
  header('Location: ./index.php');
  exit;
}

if(isset($_GET['update'])) {
  $post_data = http_build_query(
      array(
          'secret' => '<RECAPTCHA SECRET>',
          'response' => $_POST['g-recaptcha-response'],
          'remoteip' => $funcs->checkIP()
      )
  );
  $opts = array('http' =>
      array(
          'method'  => 'POST',
          'header'  => 'Content-type: application/x-www-form-urlencoded',
          'content' => $post_data
      )
  );
  $context  = stream_context_create($opts);
  $response = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
  $result = json_decode($response);
  if (!$result->success) {
      $_SESSION['error'] = 'Couldn\'t verify reCAPTCHA. Please re-try.';
      header('Location: ./profile.php');
      exit;
  } else {
    if(isset($_POST['email']) && ($uData['email'] != $_POST['email'])) {
      if($funcs->updateUser('mail', $_POST['email'], $_SESSION['uname'])) {
        $_SESSION['success'] = 'Email updated successfully!';
        header('Location: ./profile.php');
        exit;
      }
    } else if (isset($_POST['email']) && ($uData['email'] == $_POST['email'])) {
      $_SESSION['error'] = 'New email can\'t be the same as the old one.';
      header('Location: ./profile.php');
      exit;
    } else {
      if((isset($_POST['pw']) && isset($_POST['re-pw'])) && !(empty($_POST['pw']) && empty($_POST['re-pw'])) && ($_POST['pw'] == $_POST['re-pw'])) {
        if(strlen($_POST['pw']) >= 6) {
          if($funcs->updateUser('pw', $_POST['pw'], $_SESSION['uname'])) {
            $_SESSION['success'] = 'Password updated successfully!';
            header('Location: ./profile.php');
            exit;
          }
        } else {
          $_SESSION['error'] = 'Password must be 6 characters long or more.';
          header('Location: ./profile.php');
          exit;
        }
      } else if((isset($_POST['pw']) && isset($_POST['re-pw'])) && (empty($_POST['pw']) || empty($_POST['re-pw']))) {
        $_SESSION['error'] = 'Please fill in both password fields.';
        header('Location: ./profile.php');
        exit;
      } else if((isset($_POST['pw']) && isset($_POST['re-pw'])) && !(empty($_POST['pw']) && empty($_POST['re-pw'])) && ($_POST['pw'] != $_POST['re-pw'])) {
        $_SESSION['error'] = 'Both passwords need to be equal.';
        header('Location: ./profile.php');
        exit;
      }
    }
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
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
    <script src="./assets/js/loader.defvalues.js"></script>
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <link rel="shortcut icon" type="image/png" href="./assets/img/favicon.ico"/>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
    function onSubmit(token) {
      document.getElementById("update").submit();
    }

      $( document ).ready(function() {
        var oldMail = $("#email").val();
        var oldPw = $("#pw").val();
        var oldRepw = $("#re-pw").val();
        $("#email").on('input', function () {
          if(oldMail != $("#email").val()) {
            $("#pw").prop('disabled', true);
            $("#re-pw").prop('disabled', true);
            $("#pw").animate({
              backgroundColor: "#ffcccc"
            }, 500);
            $("#re-pw").animate({
              backgroundColor: "#ffcccc"
            }, 500);
          } else {
            $("#pw").prop('disabled', false);
            $("#re-pw").prop('disabled', false);
            $("#pw").animate({
              backgroundColor: "green"
            }, 500, function () {
              $("#pw").animate({
                backgroundColor: "none"
              }, 500)
            });
            $("#re-pw").animate({
              backgroundColor: "green"
            }, 500, function () {
              $("#re-pw").animate({
                backgroundColor: "none"
              }, 500)
            });
          }
        });
        $("#pw").on('input', function () {
          if(oldPw != $("#pw").val()) {
            $("#email").prop('disabled', true);
            $("#email").animate({
              backgroundColor: "#ffcccc"
            }, 500);
          } else {
            $("#email").prop('disabled', false);
            $("#email").animate({
              backgroundColor: "green"
            }, 500, function () {
              $("#email").animate({
                backgroundColor: "none"
              }, 500)
            });
          }
        });
        $("#re-pw").on('input', function () {
          if(oldRepw != $("#re-pw").val()) {
            $("#email").prop('disabled', true);
            $("#email").animate({
              backgroundColor: "#ffcccc"
            }, 500);
          } else {
            $("#email").prop('disabled', false);
            $("#email").animate({
              backgroundColor: "green"
            }, 500, function () {
              $("#email").animate({
                backgroundColor: "none"
              }, 500)
            });
          }
        });
      });
    </script>

    <title>AntiClient - Dashboard</title>

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
            <?php if($uData['rank'] >= 1) { echo '<li><a href="./customer.php"><i class="fas fa-shield-alt"></i> AntiClient</a></li>'; } ?>
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

        <div class="profile-header">
          <i class="far fa-user"></i> <?php echo $uData['username']; ?>
        </div>
        <div class="box">
          <div class="box-header">Profile Settings</div>
          <div class="box-content">
            <form id="update" action="?update" method="POST">
              <div class="form-input-group">
                <span>Username</span>
                <input class="form-input" type="text" value="<?php echo $uData['username']; ?>" placeholder="Your username" disabled/>
              </div>
              <div class="form-input-group">
                <span>Email</span>
                <input class="form-input" id="email" name="email" type="text" value="<?php echo $uData['email']; ?>" placeholder="Your email" />
              </div>
              <div class="form-input-group">
                <span>Change password</span>
                <input class="form-input" id="pw" name="pw" type="password" placeholder="Insert new password" />
              </div>
              <div class="form-input-group">
                <span>Repeat password</span>
                <input class="form-input" id="re-pw" name="re-pw" type="password" placeholder="Repeat new password" />
              </div>
              <div class="form-input-group">
                <button class="form-submit g-recaptcha" data-sitekey="<RECAPTCHA PUBLIC KEY>" data-callback='onSubmit'>Update</button>
              </div>
            </form>
          </div>
        </div>

      </div>
  </body>
 </html>
