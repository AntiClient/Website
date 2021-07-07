<?php

require_once('./assets/inc/handle.php');

if(isset($_SESSION['uname']) && !empty($_SESSION['uname'])) {
  header('Location: ./dashboard.php');
  exit;
}

$funcs = new Funcs();

if(isset($_GET['reqreset'])) {
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
    if(!empty($_POST['email'])) {
      if($funcs->checkUEmail($_POST['email']) > 0) {
        $funcs->genResetPw($funcs->getUDataByEmail($_POST['email'])['username'], $_POST['email']);
        $_SESSION['success'] = 'If the email was correct, check your inbox.';
        header('Location: ./reqreset.php');
        exit;
      } else {
        $_SESSION['success'] = 'If the email was correct, check your inbox.';
        header('Location: ./reqreset.php');
        exit;
      }
    } else {
      $_SESSION['error'] = 'Please fill in the field.';
      header('Location: ./reqreset.php');
      exit;
    }
  }
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
         document.getElementById("reqreset").submit();
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
          <form method="POST" id="reqreset" action="?reqreset">
            <div class="form-input-group">
              <span>We will send you a link, follow the instruction to reset your password!</span>
            </div>
            <div class="form-input-group">
              <span>Email</span>
              <input class="form-input" type="text" name="email" placeholder="Enter your email" />
            </div>
            <div class="form-input-group">
              <button class="form-submit g-recaptcha" data-sitekey="<RECAPTCHA PUBLIC KEY>" data-callback='onSubmit'>Request link</button>
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
