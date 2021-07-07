<?php

require_once('/var/www/up.anticlient.xyz/assets/inc/handle.php');

$funcs = new Funcs();


foreach($funcs->checkTimes() as $data) {
  if($data['download_generated_on'] != null) {
    if(time() - $data['download_generated_on'] >= 5 * 60) {
      $funcs->updateTimes('download', $data['username']);
    }
  }
  if($data['pin_generated_on'] != null) {
    if(time() - $data['pin_generated_on'] >= 5 * 60) {
      $funcs->updateTimes('pin', $data['username']);
    }
  }
  if($data['resetpw_generated_on'] != null) {
    if(time() - $data['resetpw_generated_on'] >= 5 * 60) {
      $funcs->updateTimes('resetpw', $data['username']);
    }
  }
  if($data['rank_bought_on'] != null) {
    if(time() - $data['rank_bought_on'] >= 2592000) {
      $funcs->updateTimes('buy', $data['username']);
    }
  }
}

 ?>
