<?php
session_id($_GET['SID']);
require_once('/var/www/up.anticlient.xyz/assets/inc/handle.php');
$funcs = new Funcs();

if(isset($_SESSION['username']) && isset($_SESSION['pin'])){

$username = $_SESSION['username'];
$pin = $_SESSION['pin'];

if($funcs->APICheckSession($username, $pin) > 0){
	
  if(!$funcs->APIDestroySession($username, $pin)) {
    echo 'Whoops, something went wrong.';
  }
}
}

?>
