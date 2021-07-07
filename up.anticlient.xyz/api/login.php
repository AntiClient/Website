<?php

require_once('../assets/inc/handle.php');
$funcs = new Funcs();

if($_SERVER['HTTP_HOST'] != "api.anticlient.xyz"){
header("Location: ../index.php");
exit();
}

if(isset($_GET['pin'])){
$pin = $_GET['pin'];
if($funcs->APICheckPin($pin) > 0){
	$row = $funcs->APIGetPin($pin);
	if($funcs->APIUpdatePin($pin)) {
		$date = date("Y-m-d h:i:sa");
		if($funcs->APIGenSession($row['username'], $pin, $date)) {
			$_SESSION["username"] = $row["username"];
			$_SESSION["pin"] = $pin;
			exit($_COOKIE["PHPSESSID"]);
		} else {
			echo 'Whoops, something went wrong.';
		}
	} else {
		echo 'Whoops, something went wrong.';
	}
}
}

?>
