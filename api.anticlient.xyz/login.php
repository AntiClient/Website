<?php

require_once('/var/www/up.anticlient.xyz/assets/inc/handle.php');
$funcs = new Funcs();

if(isset($_GET['pin'])){
$pin = $_GET['pin'];
if($funcs->APICheckPin($pin) > 0){
	$row = $funcs->APIGetPin($pin);
	if($funcs->APIUpdatePin($pin)) {
		$date = date("Y-m-d h:i:sa");
		if($funcs->APIGenSession($row['username'], $pin, $date)) {
			$_SESSION["username"] = $row["username"];
			$_SESSION["pin"] = $pin;
			exit($row['username'].":".session_id());
		} else {
			echo 'Whoops, something went wrong.';
		}
	} else {
		echo 'Whoops, something went wrong.';
	}
}
}

?>
