<?php

require_once('./assets/inc/handle.php');
$funcs = new Funcs();
$uData = $funcs->getUData($_SESSION['uname']);

if(!isset($_SESSION['uname']) || empty($_SESSION['uname'])) {
  $_SESSION['error'] = 'You need to be logged first.';
  header('Location: ./index.php?redirectTo=users.php');
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

if(isset($_GET['upd-rank']) && isset($_POST['rank'])) {
  if($funcs->updateRank($_POST['upd-uname'], $_POST['rank'])) {
    $_SESSION['success'] = 'Rank for user <b>'.$_POST['upd-uname'].'</b> updated.';
    header('Location: ./users.php');
    exit;
  } else {
    $_SESSION['error'] = 'Whoops, something went wrong.';
    header('Location: ./users.php');
    exit;
  }
} else if(isset($_GET['upd-ban']) && isset($_POST['ban'])) {
  if($funcs->updateBan($_POST['upd-uname'], $_POST['ban'])) {
    $_SESSION['success'] = '<b>'.$_POST['upd-uname'].'</b>\'s Ban state has been updated.';
    header('Location: ./users.php');
    exit;
  } else {
    $_SESSION['error'] = 'Whoops, something went wrong.';
    header('Location: ./users.php');
    exit;
  }
} else if(isset($_POST['delusr']) && $_POST['delusr'] == true) {
  $tmp = json_decode($_POST['data'], TRUE);
  if($funcs->delUser($tmp['id'])) {
    $_SESSION['success'] = 'User <b>'.$tmp['username'].'</b> deleted successfully.';
    header('Location: ./users.php');
    exit;
  } else {
    $_SESSION['error'] = 'Whoops, something went wrong.';
    header('Location: ./users.php');
    exit;
  }
} else if(isset($_GET['search']) && !empty($_GET['search'])) {
  if($funcs->checkUser($_GET['search']) > 0) {
    $tmpUData = $funcs->getUData($_GET['search']);

  } else {
    $_SESSION['error'] = 'That user doesn\'t exist.';
    header('Location: ./users.php');
    exit;
  }
}


if(isset($_GET['logout'])) {
  session_destroy();
  header('Location: ./index.php');
  exit;
}

$mPage = ceil($funcs->getUsersNum()/20);

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
        $(".deluser").click(function() {
          var id = "#text-"+$(this).attr('name');
          if(clicked == '') {
            clicked = '1'+$(this).attr('name');
            $(id).css({"visibility":"visible","opacity":"1","transition":"300ms"});
            setTimeout(function() {
              clicked = '';
              $(id).css({"visibility":"hidden","opacity":"0"});
            }, 3000);
          } else if (clicked == '1'+$(this).attr('name')) {
            $.redirect('users.php', {'delusr': true,'data': this.id});
          }
        });
      });

      function update(form) {
        document.getElementById(form).submit();
      }
    </script>

    <title>AntiClient - Users</title>

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
            <?php if($uData['rank'] == 5) { echo '<li><a class="active" href="./users.php"><i class="fas fa-users-cog"></i> Users</a></li>'; } ?>
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
                <div class="box-header">Users</div>
                <div class="box-content users">
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
                          header('Location: ./users.php');
                          exit;
                        }
                      }

                      $i = $page;
                      $maxI = $page+5;

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

                      $start = ($page-1) * 20;
                    ?>
                  </div>
                  <table>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
					  <th>Email</th>
                      <th>Rank</th>
                      <th>Banned</th>
                      <th>Delete</th>
                    </tr>
                    <?php

                      if(!isset($tmpUData) || empty($tmpUData)) {

                    foreach($funcs->getUsers($start) as $user) {

                            $dataRedir = json_encode(array(
                              "username" => $user['username'],
                              "id" => $user['id']
                            ), TRUE);

                      ?>
                      <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
						<td><?php echo $user['email']; ?></td>
                        <td>
                          <form action="?upd-rank" id="rank-<?php echo $user['id']; ?>" method="POST">
                            <select onchange="update('rank-<?php echo $user['id']; ?>')" name="rank">
                              <option value="0" <?php if($user['rank'] == 0) { echo 'selected'; } ?>>Newbie</option>
                              <option value="1" <?php if($user['rank'] == 1) { echo 'selected'; } ?>>Subscriber</option>
                              <option value="2" <?php if($user['rank'] == 2) { echo 'selected'; } ?>>Partner</option>
                              <option value="3" <?php if($user['rank'] == 3) { echo 'selected'; } ?>>Rank 3</option>
                              <option value="4" <?php if($user['rank'] == 4) { echo 'selected'; } ?>>Rank 4</option>
                              <option value="5" <?php if($user['rank'] == 5) { echo 'selected'; } ?>>Admin</option>
                            </select>
                            <input type="hidden" name="upd-uname" value="<?php echo $user['username']; ?>">
                          </form>
                        </td>
                        <td>
                          <form action="?upd-ban" id="ban-<?php echo $user['id']; ?>" method="POST">
                            <select onchange="update('ban-<?php echo $user['id']; ?>')" name="ban">
                              <option value="0" <?php if($user['banned'] == 0) { echo 'selected'; } ?>>No</option>
                              <option value="1" <?php if($user['banned'] == 1) { echo 'selected'; } ?>>Yes</option>
                            </select>
                            <input type="hidden" name="upd-uname" value="<?php echo $user['username']; ?>">
                          </form>
                        </td>
                        <td><div class="tooltip"><i name="<?php echo $user['id']; ?>" id='<?php echo $dataRedir; ?>' class="fas fa-user-times deluser"></i><span id="text-<?php echo $user['id']; ?>" class="tooltiptext">Are you sure? Click again to confirm.</span></div></td>
                      </tr>
                    <?php } } else {

                      $dataRedir = json_encode(array(
                        "username" => $tmpUData['username'],
                        "id" => $tmpUData['id']
                      ), TRUE);

                      ?>
                      <tr>
                        <td><?php echo $tmpUData['id']; ?></td>
                        <td><?php echo $tmpUData['username']; ?></td>
						<td><?php echo $tmpUData['email']; ?></td>
                        <td>
                          <form action="?upd-rank" id="rank-<?php echo $tmpUData['id']; ?>" method="POST">
                            <select onchange="update('rank-<?php echo $tmpUData['id']; ?>')" name="rank">
                              <option value="0" <?php if($tmpUData['rank'] == 0) { echo 'selected'; } ?>>Newbie</option>
                              <option value="1" <?php if($tmpUData['rank'] == 1) { echo 'selected'; } ?>>Subscriber</option>
                              <option value="2" <?php if($tmpUData['rank'] == 2) { echo 'selected'; } ?>>Partner</option>
                              <option value="3" <?php if($tmpUData['rank'] == 3) { echo 'selected'; } ?>>Rank 3</option>
                              <option value="4" <?php if($tmpUData['rank'] == 4) { echo 'selected'; } ?>>Rank 4</option>
                              <option value="5" <?php if($tmpUData['rank'] == 5) { echo 'selected'; } ?>>Admin</option>
                            </select>
                            <input type="hidden" name="upd-uname" value="<?php echo $tmpUData['username']; ?>">
                          </form>
                        </td>
                        <td>
                          <form action="?upd-ban" id="ban-<?php echo $tmpUData['id']; ?>" method="POST">
                            <select onchange="update('ban-<?php echo $tmpUData['id']; ?>')" name="ban">
                              <option value="0" <?php if($tmpUData['banned'] == 0) { echo 'selected'; } ?>>No</option>
                              <option value="1" <?php if($tmpUData['banned'] == 1) { echo 'selected'; } ?>>Yes</option>
                            </select>
                            <input type="hidden" name="upd-uname" value="<?php echo $tmpUData['username']; ?>">
                          </form>
                        </td>
                        <td><div class="tooltip"><i name="<?php echo $tmpUData['id']; ?>" id='<?php echo $dataRedir; ?>' class="fas fa-user-times deluser"></i><span id="text-<?php echo $tmpUData['id']; ?>" class="tooltiptext">Are you sure? Click again to confirm.</span></div></td>
                      </tr>
                    <?php } ?>
                  </table>
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
                          header('Location: ./users.php');
                          exit;
                        }
                      }

                      $i = $page;
                      $maxI = $page+5;

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
                    ?>
                  </div><br>
                </div>
              </div>

      </div>
  </body>
 </html>
