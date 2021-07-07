<?php
session_id($_GET['SID']);
require_once('/var/www/up.anticlient.xyz/assets/inc/handle.php');
$funcs = new Funcs();

if(isset($_SESSION['username']) && isset($_SESSION['pin'])){
  if($funcs->APICheckSession($_SESSION['username'], $_SESSION['pin']) < 1){
    echo "true";
  }
} else {
	echo "true";
}

?>
