<?php
session_id($_GET['SID']);
require_once('/var/www/up.anticlient.xyz/assets/inc/handle.php');
$funcs = new Funcs();

$update = file_get_contents('php://input');
$update = json_decode($update, true);

session_start();

if(isset($_SESSION['pin']) && isset($_SESSION['username'])) {
	
$pin = $_SESSION['pin'];
$username = $_SESSION['username'];
if($funcs->APICheckSession($username, $pin) > 0){
	
$check1 = $update['checks']['check1'];
$check2 = $update['checks']['check2'];
$check3 = $update['checks']['check3'];
$date = date("Y-m-d h:i:sa");
$alts = json_encode($update['alts']);
$recycleBin = json_encode($update['recycleBin']);
$processStartTime = json_encode($update['processStartTime']);
if(!(empty($pin) && empty($username) && empty($check1) && empty($check2) && empty($check3))){
  if(!$funcs->APILog($username, $pin, $check1, $check2, $check3, $date, $alts, $recycleBin, $processStartTime)) {
    echo 'Whoops, something went wrong';
  }
$canSend = false;
if($check1 == "Passed"){
$line1 = "✅";
}else{
$line1 = "❌";
$canSend = false;
}
if($check2 == "Passed"){
$line2 = "✅";
}else{
$line2 = "❌";
$canSend = true;
}
if($check3 == "true"){
$line3 = "✅";
$result3 = "Passed";
}else{
$line3 = "❌";
$canSend = true;
$result3 = "Not Passed";
}
if($canSend){
	
$message = "▬▬▬▬▬▬▬▬✅Scan Results✅▬▬▬▬▬▬▬▬\n👨🏻‍✈️SS by: ".$username."\n".$line1."Check #1: ".$check1."\n".$line2."Check #2: ".$check2."\n".$line3."Check #3: ".$result3."\n⏰Date: ".$date."\n💎 Thank you for using AntiClient!\n▬▬▬▬▬▬▬▬✅Scan Results✅▬▬▬▬▬▬▬▬";

}
$resultbot->sendMessage("@anticlientexposed", $message);
}
	
}

}

?>
