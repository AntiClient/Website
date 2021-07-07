<?php

require_once('./assets/inc/handle.php');
$funcs = new Funcs();
$uData = $funcs->getUData($_SESSION['uname']);

if(!isset($_SESSION['uname']) || empty($_SESSION['uname'])) {
  $_SESSION['error'] = 'You need to be logged first.';
  header('Location: ./index.php');
  exit;
} else if($uData['banned'] == 1) {
  session_destroy();
  session_start();
  $_SESSION['error'] = 'You\'re banned from our website.';
  header('Location: ./index.php');
  exit;
}

if(isset($_GET['news-insert']) && !empty($_POST['title']) && !empty($_POST['content'])) {
  $data = array(
    'author' => $_SESSION['uname'],
    'title' => htmlentities($_POST['title'])
  );
  $data = json_encode($data, TRUE);
  if($funcs->insertNews(htmlentities($_POST['content']), $data)) {
    $_SESSION['success'] = 'News inserted correctly.';
    header('Location: ./dashboard.php');
    exit;
  } else {
    $_SESSION['error'] = 'Whoops, something went wrong!';
    header('Location: ./dashboard.php');
    exit;
  }
} else if (isset($_GET['news-insert']) && (empty($_POST['title']) || empty($_POST['content']))) {
  $_SESSION['error'] = 'Please fill in all fields.';
  header('Location: ./dashboard.php');
  exit;
} else if(isset($_GET['news-remove']) && !empty($_GET['news-remove'])) {
  if($funcs->removeNews($_GET['news-remove'])) {
    $_SESSION['success'] = 'News removed successfully.';
    header('Location: ./dashboard.php');
    exit;
  } else {
    $_SESSION['error'] = 'Whoops, something went wrong.';
    header('Location: ./dashboard.php');
    exit;
  }
} else if(isset($_GET['logout'])) {
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
    <script src="./assets/js/loader.defvalues.js"></script>
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <link rel="shortcut icon" type="image/png" href="./assets/img/favicon.ico"/>

    <script>
    $( document ).ready(function() {
      $("#ts3-d").click(function() {
        window.location.href = "ts3server://ts.anticlient.xyz";
      });
      $("#dsr-d").click(function() {
        window.location.href = "https://discord.gg/ue5ZNMU";
      });
      $("#tg-d").click(function() {
        window.location.href = "tg://resolve?domain=AntiClient";
      });
      $("#yt-d").click(function() {
        window.location.href = "https://www.youtube.com/channel/UC6gWqZALJvLDRcwsa9Xqosg";
      });

      $("#ts3-m").click(function() {
        window.location.href = "ts3server://ts.anticlient.xyz";
      });
      $("#dsr-m").click(function() {
        window.location.href = "https://discord.anticlient.xyz";
      });
      $("#tg-m").click(function() {
        window.location.href = "tg://resolve?domain=AntiClient";
      });
      $("#yt-m").click(function() {
        window.location.href = "https://www.youtube.com/channel/UC6gWqZALJvLDRcwsa9Xqosg";
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
            <li><a class="active" href="./dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="./purchase.php"><i class="fas fa-shopping-cart"></i> Purchase</a></li>
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

        <div class="top-panels desktop">
          <div id="ts3-d" class="panel teamspeak">
            <img class="panel-img" src="./assets/img/TS3-logo.png" width="65" height="65" align="middle"> <span class="panel-text">Join our TeamSpeak!</span>
          </div>
          <div id="dsr-d" class="panel discord">
            <img class="panel-img" src="./assets/img/discord-logo.png" width="85" height="85" align="middle"> <span class="panel-text">Join our Discord!</span>
          </div>
          <div id="tg-d" class="panel telegram">
            <img class="panel-img" src="./assets/img/telegram-logo.png" width="85" height="85" align="middle"> <span class="panel-text">Follow us on Telegram!</span>
          </div>
          <div id="yt-d" class="panel youtube">
            <img class="panel-img" src="./assets/img/youtube-logo.png" width="85" height="85" align="middle"> <span class="panel-text">Follow us on YouTube!</span>
          </div>
        </div>

        <div class="top-panels-m mobile">
          <div id="ts3-m" class="panel-m teamspeak">
            <img src="./assets/img/TS3-logo.png" width="65" height="65" align="middle"> Join our TeamSpeak!
          </div>
          <div id="dsr-m" class="panel-m discord">
            <img src="./assets/img/discord-logo.png" width="85" height="85" align="middle"> Join our Discord!
          </div>
          <div id="tg-m" class="panel-m telegram">
            <img src="./assets/img/telegram-logo.png" width="85" height="85" align="middle"> Follow us on Telegram!
          </div>
          <div id="yt-m" class="panel-m youtube">
            <img src="./assets/img/youtube-logo.png" width="85" height="85" align="middle"> <span style="color: black;">Follow us on YouTube!
          </div>
        </div>

        <div class="box">
          <div class="box-header">NEWS</div>
          <?php
            if($uData['rank'] == 5) { ?>

              <div class="news-insert">

                <form action="?news-insert" method="POST">
                  <div class="form-input-group">
                    <span>Title</span>
                    <input class="form-input" type="text" name="title" placeholder="Enter the title" />
                  </div>
                  <div class="form-input-group" style="margin-top: 35px; margin-bottom: 70px;">
                    <span>Content</span>
                    <textarea class="form-input" name="content" placeholder="Insert here the news content. (HTML is supported)"></textarea>
                  </div>
                  <div class="form-input-group">
                    <button class="form-submit" type="submit">Insert</button>
                  </div>
                </form>
              </div>
              <div class="news-divider"></div>

            <?php }

            if($funcs->checkNews() > 0) {
              foreach($funcs->getNews() as $news) {
                $data = $news['data'];
                $data = json_decode($data, TRUE);
                ?>

                <div class="news">
                  <?php if($uData['rank'] == 5) { ?><a class="news-remove-times" href="?news-remove=<?php echo $news['id']; ?>"><i style="float: right;" class="fas fa-times"></i></a><?php } ?>
                  <div class="news-title"><?php echo $data['title'] ?> - <?php echo date('d/m/y', $news['date']) ?></div>
                  <div class="news-content"><?php echo html_entity_decode($news['content'], ENT_QUOTES); ?></div>
                </div>
                <br>
                <div class="news-divider"></div>


              <?php
              }
            }
           ?>
        </div>
      </div>
  </body>
 </html>
