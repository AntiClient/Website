<?php

require_once('./assets/inc/handle.php');
$funcs = new Funcs();
$uData = $funcs->getUData($_SESSION['uname']);

if(!isset($_SESSION['uname']) || empty($_SESSION['uname'])) {
  $_SESSION['error'] = 'You need to be logged first.';
  header('Location: ./index.php?redirectTo=logs.php');
  exit;
} else if($uData['banned'] == 1) {
  session_destroy();
  session_start();
  $_SESSION['error'] = 'You\'re banned from our website.';
  header('Location: ./index.php');
  exit;
} else if ($uData['rank'] != 5) {
  $_SESSION['error'] = 'You\'re not allowed to visit that page.';
  header('Location: ./dashboard.php');
  exit;
}

if(isset($_GET['logout'])) {
  session_destroy();
  header('Location: ./index.php');
  exit;
}

if(isset($_GET['search']) && !empty($_GET['search'])) {
  if($funcs->checkUser($_GET['search']) > 0) {
    $tmpUData = $funcs->getLogs('search', $_GET['search']);
  } else {
    $_SESSION['error'] = 'That user doesn\'t exist.';
    header('Location: ./logs.php');
    exit;
  }
}

$mPage = ceil($funcs->getLogsNum()/20);
 ?>
 <html>
  <head>

    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
    <script src="./assets/js/loader.defvalues.js"></script>
    <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
    <link rel="shortcut icon" type="image/png" href="./assets/img/favicon.ico"/>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script>
    function onSubmit(token) {
      document.getElementById("update").submit();
    }
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
            <?php if($uData['rank'] == 5) { echo '<li><a class="active" href="./logs.php"><i class="fas fa-clipboard-list"></i> Login Logs</a></li>'; } ?>
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

              <div class="box search" style="width: 40%;">
                <div class="box-header"><i class="fas fa-search"></i> Search</div>
                <div class="box-content">
                  <form style="margin: 0 auto; width: 60%; margin-top: -25px;" method="GET">
                    <div class="form-input-group">
                      <span>Username</span>
                      <input class="form-input" type="text" name="search" placeholder="Insert the username to search" />
                    </div>
                    <div class="form-input-group">
                      <button class="form-submit" type="submit">Search</button>
                    </div>
                  </form>
                </div>
              </div>

              <div class="box">
                <div class="box-header">Last user logs</div>
                <div class="box-content logs">
                  <div class="page-selector">
                    <?php
                      $exit = false;

                      if(!isset($_GET['page'])) {
                        $page = 1;
                      } else {
                        if(is_numeric($_GET['page']) && ($_GET['page'] <= $mPage && $_GET['page'] > 0)) {
                          $page = $_GET['page'];
                        } else {
                          $_SESSION['error'] = 'The page inserted is invalid.';
                          header('Location: ./logs.php');
                          exit;
                        }
                      }

                      $i = $page;
                      $maxI = $page+5;

                      if(!($mPage < 2)) {
                        if($mPage > 5 && ($maxI-$mPage) <= 0) {
                          if($i != 1) {
                            echo '<a class="psb-link" href="?page=1"><div class="page-select-button">1</div></a>';
                            if($i > 2) {
                              echo '<div class="page-select-button">...</div>';
                            }
                          }
                          do {
                            if($i < $maxI) {
                              if($i == $page) {
                                if($i > 2) {
                                  echo '<a class="psb-link" href="?page='.($i-1).'"><div class="page-select-button">'.($i-1).'</div></a>';
                                  echo '<a class="psb-link" href="?page='.$i.'"><div class="page-select-button" style="background: #112d4e;">'.$i.'</div></a>';
                                } else {
                                  echo '<a class="psb-link" href="?page='.$i.'"><div class="page-select-button" style="background: #112d4e;">'.$i.'</div></a>';
                                }
                              } else {
                                echo '<a class="psb-link" href="?page='.$i.'"><div class="page-select-button">'.$i.'</div></a>';
                              }
                            } else {
                              if($mPage != $maxI) {
                                echo '<div class="page-select-button">...</div>';
                              }
                              echo '<a class="psb-link" href="?page='.$mPage.'"><div class="page-select-button">'.$mPage.'</div></a>';
                              $exit = true;
                            }
                            $i++;
                          } while ($exit == false);
                        } else {
                          echo '<a class="psb-link" href="?page=1"><div class="page-select-button">1</div></a><div class="page-select-button">...</div>';
                          for($i = $mPage-5; $i <= $mPage; $i++) {
                            if($i == $page) {
                              echo '<a class="psb-link" href="?page='.$i.'"><div class="page-select-button" style="background: #112d4e;">'.$i.'</div></a>';
                            } else {
                              echo '<a class="psb-link" href="?page='.$i.'"><div class="page-select-button">'.$i.'</div></a>';
                            }
                          }
                        }
                      }

                      $start = ($page-1) * 20;
                    ?>
                  </div>

                  <table>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Date</th>
                      <th>IP Address</th>
                    </tr>
                    <?php

                    if(!isset($tmpUData) || empty($tmpUData)) {

                      foreach($funcs->getLogs(NULL, $start) as $log) {

                    ?>
                    <tr>
                      <td><?php echo $log['id']; ?></td>
                      <td><?php echo $log['username']; ?></td>
                      <td><?php echo date('d/m/Y - G:i:s', $log['date']); ?></td>
                      <td><?php echo $log['ip']; ?></td>
                    </tr>

                  <?php } } else {

                    foreach($tmpUData as $log) { ?>

                      <tr>
                        <td><?php echo $log['id']; ?></td>
                        <td><?php echo $log['username']; ?></td>
                        <td><?php echo date('d/m/Y - G:i:s', $log['date']); ?></td>
                        <td><?php echo $log['ip']; ?></td>
                      </tr>

                  <?php }

                  } ?>
                  </table>
                  <br>

                  <div class="page-selector">
                    <?php
                      $exit = false;

                      if(!isset($_GET['page'])) {
                        $page = 1;
                      } else {
                        if(is_numeric($_GET['page']) && ($_GET['page'] <= $mPage && $_GET['page'] > 0)) {
                          $page = $_GET['page'];
                        } else {
                          $_SESSION['error'] = 'The page inserted is invalid.';
                          header('Location: ./logs.php');
                          exit;
                        }
                      }

                      $i = $page;
                      $maxI = $page+5;

                      if(!($mPage < 2)) {
                        if($mPage > 5 && ($maxI-$mPage) <= 0) {
                          if($i != 1) {
                            echo '<a class="psb-link" href="?page=1"><div class="page-select-button">1</div></a>';
                            if($i > 2) {
                              echo '<div class="page-select-button">...</div>';
                            }
                          }
                          do {
                            if($i < $maxI) {
                              if($i == $page) {
                                if($i > 2) {
                                  echo '<a class="psb-link" href="?page='.($i-1).'"><div class="page-select-button">'.($i-1).'</div></a>';
                                  echo '<a class="psb-link" href="?page='.$i.'"><div class="page-select-button" style="background: #112d4e;">'.$i.'</div></a>';
                                } else {
                                  echo '<a class="psb-link" href="?page='.$i.'"><div class="page-select-button" style="background: #112d4e;">'.$i.'</div></a>';
                                }
                              } else {
                                echo '<a class="psb-link" href="?page='.$i.'"><div class="page-select-button">'.$i.'</div></a>';
                              }
                            } else {
                              if($mPage != $maxI) {
                                echo '<div class="page-select-button">...</div>';
                              }
                              echo '<a class="psb-link" href="?page='.$mPage.'"><div class="page-select-button">'.$mPage.'</div></a>';
                              $exit = true;
                            }
                            $i++;
                          } while ($exit == false);
                        } else {
                          echo '<a class="psb-link" href="?page=1"><div class="page-select-button">1</div></a><div class="page-select-button">...</div>';
                          for($i = $mPage-5; $i <= $mPage; $i++) {
                            if($i == $page) {
                              echo '<a class="psb-link" href="?page='.$i.'"><div class="page-select-button" style="background: #112d4e;">'.$i.'</div></a>';
                            } else {
                              echo '<a class="psb-link" href="?page='.$i.'"><div class="page-select-button">'.$i.'</div></a>';
                            }
                          }
                        }
                      }

                      $start = ($page-1) * 20;
                    ?>
                  </div>
                </div>
              </div>

      </div>
  </body>
 </html>
