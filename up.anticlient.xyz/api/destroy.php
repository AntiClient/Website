<?php

require_once('../assets/inc/handle.php');
$funcs = new Funcs();

if($_SERVER['HTTP_HOST'] != "api.anticlient.xyz"){
  header("Location: ../index.php");
  exit();
}

if(isset($_GET['username']) && isset($_GET['pin'])){

$username = $_GET['username'];
$pin = $_GET['pin'];

if($funcs->APICheckSession($username, $pin) > 0){
	
  if(!$funcs->APIDestroySession($username, $pin)) {
    echo 'Whoops, something went wrong.';
  }
}
}

?>
