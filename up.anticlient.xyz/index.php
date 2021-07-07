<?php

require_once('./assets/inc/handle.php');

if(isset($_SESSION['uname']) && !empty($_SESSION['uname'])) {
  header('Location: ./dashboard.php');
  exit;
}

$funcs = new Funcs();


if(isset($_GET['login']) && isset($_POST['uname']) && isset($_POST['pw'])) {
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
      header('Location: ./index.php?'.http_build_query($_GET));
      exit;
  } else {
    if(empty($_POST['uname']) || empty($_POST['pw'])) {
      $_SESSION['error'] = 'Please fill in all fields.';
      header('Location: ./index.php?'.http_build_query($_GET));
      exit;
    } else {
      if($funcs->checkUser($_POST['uname']) > 0) {
        $uData = $funcs->getUData($_POST['uname']);
        if($uData['banned'] == 1) {
          $_SESSION['error'] = 'You\'re banned from our website.';
          header('Location: ./index.php?'.http_build_query($_GET));
          exit;
        } else {
          if($uData['firstlogin'] != 0) {
            if(password_verify($_POST['pw'], $uData['password'])) {
              if($funcs->updateUserIP($uData['username'])) {
                $_SESSION['uname'] = $uData['username'];
				if(!isset($_GET['redirectTo'])){
                header('Location: ./dashboard.php');
				}else{
					header('Location: ./'.$_GET['redirectTo']);
				}
                exit;
              } else {
                $_SESSION['error'] = 'Whoops, something went wrong.';
                header('Location: ./index.php?'.http_build_query($_GET));
                exit;
              }
            } else {
              $_SESSION['error'] = 'Incorrect password.';
              header('Location: ./index.php?'.http_build_query($_GET));
              exit;
            }
          } else {
            $funcs->genResetPw($uData['username'], $uData['email']);
            $_SESSION['error'] = 'You need to reset your password! Check email.';
            header('Location: ./index.php?'.http_build_query($_GET));
            exit;
          }
        }
      } else {
        $_SESSION['error'] = 'User not found. Please check input and try again.';
        header('Location: ./index.php?'.http_build_query($_GET));
        exit;
      }
    }
  }

}


 ?>
 <html>
  <head>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" async defer></script>
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <link rel="shortcut icon" type="image/png" href="./assets/img/favicon.ico"/>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
     <script async defer>
       function onSubmit(token) {
         document.getElementById("login").submit();
       }

       $( document ).ready(function() {
         $("body").removeClass("preload");
       });
     </script>

    <title>AntiClient - Login</title>

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
          <img class="logo" src="./assets/img/logo100x100.png" width="180" height="180">
          <br>
          <span>AntiClient</span>
        </div>
        <div class="login-form">
          <form method="POST" id="login" action="?login&<?php echo http_build_query($_GET);?>">
            <div class="form-input-group">
              <span>Username</span>
              <input class="form-input" type="text" name="uname" placeholder="Enter your username" />
            </div>
            <div class="form-input-group">
              <span>Password</span>
              <input class="form-input" type="password" name="pw" placeholder="Enter your password" />
            </div>
            <div class="form-input-group">
              <button class="form-submit g-recaptcha" data-sitekey="<RECAPTCHA PUBLIC KEY>" data-callback='onSubmit'>Login</button>
            </div>
          </form>
        </div>
      </div>
      <div class="links">
        <p class="link-password"><a href="./reqreset.php">Forgot your password?</a></p>
        <p class="link-register">Don't have an account? <a href="./register.php">Register here</a></p>
      </div>
    </div>
    <footer>
      <p class="copymark">&copy; 2017-<?php echo date("Y"); ?> AntiClient, all rights reserved.</p>
    </footer>
  </body>
 </html>
