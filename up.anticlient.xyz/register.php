<?php

require_once('./assets/inc/handle.php');

if(isset($_SESSION['uname']) && !empty($_SESSION['uname'])) {
  header('Location: ./dashboard.php');
  exit;
}

$funcs = new Funcs();

if(isset($_GET['register'])) {
  if(empty($_POST['uname']) || empty($_POST['email']) || empty($_POST['pw']) || empty($_POST['re-pw'])) {
    $_SESSION['error'] = 'Please fill in all fields.';
    header('Location: ./register.php');
    exit;
  } else {
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
      if($funcs->checkUser($_POST['uname']) > 0) {
        $_SESSION['error'] = 'Username already registered.';
        header('Location: ./register.php');
        exit;
      } else {
        $tmpE = explode('@', $_POST['email']);
        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && checkdnsrr($tmpE[1], 'MX')) {
          if($funcs->checkEmail($_POST['email']) > 0) {
            $_SESSION['error'] = 'Email already registered.';
            header('Location: ./register.php');
            exit;
          } else {
            if($_POST['pw'] != $_POST['re-pw']) {
              $_SESSION['error'] = 'Passwords doesn\'t match.';
              header('Location: ./register.php');
              exit;
            } else {
              if(strlen($_POST['pw']) < 6) {
                $_SESSION['error'] = 'Password must be at least 6 characters long.';
                header('Location: ./register.php');
                exit;
              } else {
                if(empty($_POST['tos'])) {
                  $_SESSION['error'] = 'You must accept our TOS.';
                  header('Location: ./register.php');
                  exit;
                } else {
                  $pwHash = password_hash($_POST['pw'], PASSWORD_ARGON2I);
                  if($funcs->regUser($_POST['uname'], $_POST['email'], $pwHash)) {
                    $_SESSION['uname'] = $_POST['uname'];
                    header('Location: ./dashboard.php');
                    exit;
                    } else {
                      $_SESSION['error'] = 'Whoops, something went wrong.';
                      header('Location: ./register.php');
                      exit;
                    }
                  }
                }
              }
            }
        } else {
          $_SESSION['error'] = 'Email not valid.';
          header('Location: ./register.php');
          exit;
        }
      }
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
         document.getElementById("register").submit();
       }

       $( document ).ready(function() {
         var open = false;
         $("body").removeClass("preload");

         $("#OpenTOSPopUp").click(function() {
           if(open == false) {
             $("#TOSPopUp").fadeIn('fast');
             open = true;
           }
         });

		$("#OpenPrivacyPolicyPopUp").click(function() {
           if(open == false) {
             $("#PrivacyPolicyPopUp").fadeIn('fast');
             open = true;
           }
         });

         $("#closeTOSPopUp").click(function () {
           if(open == true) {
             $("#TOSPopUp").fadeOut('fast');
             open = false;
           }
         });
		 $("#closePrivacyPolicyPopUp").click(function () {
           if(open == true) {
             $("#PrivacyPolicyPopUp").fadeOut('fast');
             open = false;
           }
         });
       });
     </script>

    <title>AntiClient - Register</title>

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
      <div class="register-box">
        <div class="logo-box">
          <img class="logo" src="./assets/img/logo100x100.png" width="180" height="180">
          <br>
          <span>AntiClient</span>
        </div>
        <div class="register-form">
          <form id="register" method="POST" action="?register">
            <div class="form-input-group">
              <span>Username</span>
              <input class="form-input" type="text" name="uname" placeholder="Enter your username" />
            </div>
            <div class="form-input-group">
              <span>Email</span>
              <input class="form-input" type="text" name="email" placeholder="Enter your email" />
            </div>
            <div class="form-input-group">
              <span>Password</span>
              <input class="form-input" type="password" name="pw" placeholder="Enter your password" />
            </div>
            <div class="form-input-group">
              <span>Repeat Password</span>
              <input class="form-input" type="password" name="re-pw" placeholder="Repeat your password" />
            </div>
            <div class="form-input-group tos">
              <input type="checkbox" id="test" name="tos" value="tos-true">
              <label for="test">Accept our <a id="OpenTOSPopUp">Terms of Service</a></label>
			  <label>and our <a id="OpenPrivacyPolicyPopUp">Privacy Policy</a></label>
            </div>
            <div class="form-input-group">
              <button class="form-submit g-recaptcha" data-sitekey="<RECAPTCHA PUBLIC KEY>" data-callback='onSubmit'>Register</button>
            </div>
          </form>
        </div>
      </div>
      <div class="links">
        <p class="link-register">Already have an account? <a href="./index.php">Login here</a></p>
      </div>
    </div>
    <footer>
      <p class="copymark">&copy; 2017-<?php echo date("Y"); ?> AntiClient, all rights reserved.</p>
    </footer>

    <div id="TOSPopUp" class="popUp">
      <div class="popUp-content">
        <span id="closeTOSPopUp" class="close">&times;</span>
        <h1>Website Terms and Conditions of Use</h1>
		
<p>Throughout this Agreement, the words “AntiClient,” “us” “we” and “our” refer to our website, AntiClient.xyz, as is appropriate in the context of the use of the words.</p>
<p>The words “Permanent,” “Lifetime” and “Forever” refer to unlimited usage of the platform as soon as it is online.</p>

<h2>1. Terms</h2>

<p>By accessing this Website, accessible from https://anticlient.xyz, you are agreeing to be bound by these Website Terms and Conditions of Use and agree that you are responsible for the agreement with any applicable local laws. If you disagree with any of these terms, you are prohibited from accessing this site. The materials contained in this Website are protected by copyright and trade mark law.</p>

<h2>2. Use License</h2>

<p>Permission is granted to temporarily download one copy of the materials on AntiClient's Website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:</p>

<ul>
    <li>modify or copy the materials;</li>
    <li>use the materials for any commercial purpose or for any public display;</li>
    <li>attempt to reverse engineer any software contained on AntiClient's Website;</li>
    <li>remove any copyright or other proprietary notations from the materials; or</li>
    <li>transferring the materials to another person or "mirror" the materials on any other server.</li>
</ul>

<p>This will let AntiClient to terminate upon violations of any of these restrictions. Upon termination, your viewing right will also be terminated and you should destroy any downloaded materials in your possession whether it is printed or electronic format.</p>

<h2>3. Disclaimer</h2>

<p>All the materials on AntiClient’s Website are provided "as is". AntiClient makes no warranties, may it be expressed or implied, therefore negates all other warranties. Furthermore, AntiClient does not make any representations concerning the accuracy or reliability of the use of the materials on its Website or otherwise relating to such materials or any sites linked to this Website.</p>

<h2>4. Limitations</h2>

<p>AntiClient or its suppliers will not be hold accountable for any damages that will arise with the use or inability to use the materials on AntiClient’s Website, even if AntiClient or an authorize representative of this Website has been notified, orally or written, of the possibility of such damage. Some jurisdiction does not allow limitations on implied warranties or limitations of liability for incidental damages, these limitations may not apply to you.</p>

<h2>5. Revisions and Errata</h2>

<p>The materials appearing on AntiClient’s Website may include technical, typographical, or photographic errors. AntiClient will not promise that any of the materials in this Website are accurate, complete, or current. AntiClient may change the materials contained on its Website at any time without notice. AntiClient does not make any commitment to update the materials.</p>

<h2>6. Links</h2>

<p>AntiClient has not reviewed all of the sites linked to its Website and is not responsible for the contents of any such linked site. The presence of any link does not imply endorsement by AntiClient of the site. The use of any linked website is at the user’s own risk.</p>

<h2>7. Site Terms of Use Modifications</h2>

<p>AntiClient may revise these Terms of Use for its Website at any time without prior notice. By using this Website, you are agreeing to be bound by the current version of these Terms and Conditions of Use.</p>

<h2>8. Governing Law</h2>

<p>Any claim related to AntiClient's Website shall be governed by the laws of it without regards to its conflict of law provisions.</p>

<h2>9. Account Sharing</h2>

<p>License, PIN, Download sharing is not allowed and can result in account lockout.</p>
      </div>
    </div>
    <div id="PrivacyPolicyPopUp" class="popUp">
      <div class="popUp-content">
        <span id="closePrivacyPolicyPopUp" class="close">&times;</span>
		<h1>Privacy Policy for AntiClient</h1>

<p>At AntiClient, accessible from https://anticlient.xyz, one of our main priorities is the privacy of our visitors. This Privacy Policy document contains types of information that is collected and recorded by AntiClient and how we use it.</p>

<p>If you have additional questions or require more information about our Privacy Policy, do not hesitate to contact us through email at support@anticlient.net</p>

<h2>Log Files</h2>

<p>AntiClient follows a standard procedure of using log files. These files log visitors when they visit websites. All hosting companies do this and a part of hosting services' analytics. The information collected by log files include internet protocol (IP) addresses, browser type, Internet Service Provider (ISP), date and time stamp, referring/exit pages, and possibly the number of clicks. These are not linked to any information that is personally identifiable. The purpose of the information is for analyzing trends, administering the site, tracking users' movement on the website, and gathering demographic information.</p>

<h2>Cookies and Web Beacons</h2>

<p>Like any other website, AntiClient uses 'cookies'. These cookies are used to store information including visitors' preferences, and the pages on the website that the visitor accessed or visited. The information is used to optimize the users' experience by customizing our web page content based on visitors' browser type and/or other information.</p>



<h2>Privacy Policies</h2>

<P>You may consult this list to find the Privacy Policy for each of the advertising partners of AntiClient.</p>

<p>Third-party ad servers or ad networks uses technologies like cookies, JavaScript, or Web Beacons that are used in their respective advertisements and links that appear on AntiClient, which are sent directly to users' browser. They automatically receive your IP address when this occurs. These technologies are used to measure the effectiveness of their advertising campaigns and/or to personalize the advertising content that you see on websites that you visit.</p>

<p>Note that AntiClient has no access to or control over these cookies that are used by third-party advertisers.</p>

<h2>Third Party Privacy Policies</h2>

<p>AntiClient's Privacy Policy does not apply to other advertisers or websites. Thus, we are advising you to consult the respective Privacy Policies of these third-party ad servers for more detailed information. It may include their practices and instructions about how to opt-out of certain options. You may find a complete list of these Privacy Policies and their links here: Privacy Policy Links.</p>

<p>You can choose to disable cookies through your individual browser options. To know more detailed information about cookie management with specific web browsers, it can be found at the browsers' respective websites. What Are Cookies?</p>

<h2>Children's Information</h2>

<p>Another part of our priority is adding protection for children while using the internet. We encourage parents and guardians to observe, participate in, and/or monitor and guide their online activity.</p>

<p>AntiClient does not knowingly collect any Personal Identifiable Information from children under the age of 13. If you think that your child provided this kind of information on our website, we strongly encourage you to contact us immediately and we will do our best efforts to promptly remove such information from our records.</p>

<h2>Online Privacy Policy Only</h2>

<p>This Privacy Policy applies only to our online activities and is valid for visitors to our website with regards to the information that they shared and/or collect in AntiClient. This policy is not applicable to any information collected offline or via channels other than this website.</p>

<h2>Consent</h2>

<p>By using our website, you hereby consent to our Privacy Policy and agree to its Terms and Conditions.</p>
      </div>
    </div>

  </body>
 </html>
