<?php
require_once('./assets/inc/handle.php');

if(isset($_SESSION['uname']) && !empty($_SESSION['uname'])) {
  header('Location: ./dashboard.php');
  exit;
}

$funcs = new Funcs();

if(isset($_GET['uname']) && isset($_GET['code'])) {
  if(($funcs->checkUser($_GET['uname']) > 0) && ($funcs->checkCodeRPW($_GET['code'], $_GET['uname']) > 0)) {
    if(isset($_GET['reset']) && $_GET['reset'] == true) {
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
          header('Location: ./register.php');
          exit;
      } else {
        if(!empty($_POST['pw']) || !empty($_POST['re-pw'])) {
          if($_POST['pw'] == $_POST['re-pw']) {
            if($funcs->updateRPW($_POST['pw'], $_GET['uname'])) {
              $funcs->sendMail($funcs->getUData($_GET['uname'])['email'], 'Your password has been reset', 'Hello '.$_GET['uname'].',<br><br>Your password has been correctly reset, please login now using the link below.<br><br><a href="'.$funcs->getWebLink().'">Login here</a><br><br>Best regards,<br>Your friends at AntiClient');
              $_SESSION['success'] = 'Password updated, use it to login.';
              header('Location: ./index.php');
              exit;
            } else {
              $_SESSION['error'] = 'Whoops, something went wrong.';
              header('Location: ./resetpw.php?uname='.$_GET['uname'].'&code='.$_GET['code']);
              exit;
            }
          } else {
            $_SESSION['error'] = 'Both fields need to be equal.';
            header('Location: ./resetpw.php?uname='.$_GET['uname'].'&code='.$_GET['code']);
            exit;
          }
        } else {
          $_SESSION['error'] = 'You need to fill in both fields.';
          header('Location: ./resetpw.php?uname='.$_GET['uname'].'&code='.$_GET['code']);
          exit;
        }
      }
    }
  } else {
    $_SESSION['error'] = 'Invalid or expired link.';
    header('Location: ./index.php');
    exit;
  }
} else {
  $_SESSION['error'] = 'Invalid, please click the link in your email.';
  header('Location: ./index.php');
  exit;
}


 ?>
 <html>
  <head>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <link rel="shortcut icon" type="image/png" href="./assets/img/favicon.ico"/>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
     <script>
       function onSubmit(token) {
         document.getElementById("reset").submit();
       }

       $( document ).ready(function() {
         $("body").removeClass("preload");
       });
     </script>

    <title>AntiClient - Reset password</title>

  </head>
  <body class="preload">
    <div class="container">
      <?php

        if((isset($_SESSION['error']) && !empty($_SESSION['error'])) || (isset($_SESSION['success']) && !empty($_SESSION['success']))) { ?>
          <div class="form-error <?php if(!empty($_SESSION['error'])) { echo 'true'; } else if(!empty($_SESSION['success'])) { echo 'false'; } ?>">
            <?php if(!empty($_SESSION['error'])) { echo $_SESSION['error']; } else if(!empty($_SESSION['success'])) { echo $_SESSION['success']; } ?>
          </div>
      <?php $_SESSION['error'] = null;
            $_SESSION['success'] = null; } ?>
      <div class="login-box">
        <div class="logo-box">
          <img class="logo" src="./assets/img/logo.png" width="180" height="180">
          <br>
          <span>AntiClient</span>
        </div>
        <div class="login-form">
          <form method="POST" id="reset" action="?uname=<?php echo $_GET['uname']; ?>&code=<?php echo $_GET['code'] ?>&reset=true">
            <div class="form-input-group">
              <span>New Password</span>
              <input class="form-input" type="password" name="pw" placeholder="Enter the new password" />
            </div>
            <div class="form-input-group">
              <span>Repeat new Password</span>
              <input class="form-input" type="password" name="re-pw" placeholder="Enter the new password again" />
            </div>
            <div class="form-input-group">
              <button class="form-submit g-recaptcha" data-sitekey="<RECAPTCHA PUBLIC KEY>" data-callback='onSubmit'>Reset</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <footer>
      <p class="copymark">&copy; 2017-<?php echo date("Y"); ?> AntiClient, all rights reserved.</p>
    </footer>
  </body>
 </html>
