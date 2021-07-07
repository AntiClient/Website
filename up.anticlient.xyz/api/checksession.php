<?php

require_once('../assets/inc/handle.php');
$funcs = new Funcs();

if($_SERVER['HTTP_HOST'] != "api.anticlient.xyz"){
  header("Location: ../index.php");
  exit();
}

if(isset($_GET['username']) && isset($_GET['pin'])){
  if($funcs->APICheckSession($_GET['username'], $_GET['pin']) < 1){
    echo "true";
  }
}

?>
