<?php

include '../../inc/database.php';
include '../../lib/Bot.php';

$update = file_get_contents('php://input');
$update = json_decode($update, TRUE);

$chatid = $update["message"]["chat"]["id"];
$message = $update["message"]["text"];

function startsWith($haystack, $needle) {
    // search backwards starting from haystack length characters from the end
    return $needle === ''
      || strrpos($haystack, $needle, -strlen($haystack)) !== false;
}

if(startsWith($message, "/setcode")){
$code = explode(" ", $message)[1];
if($code != ""){
$cquery = $old->prepare("SELECT COUNT(*) FROM `licenses` WHERE `ChatId` = '$chatid'");
$cquery->execute();
$count = $cquery->fetchColumn();
if($count > 0){
$query = $old->prepare("SELECT * FROM `licenses` WHERE `ChatId` = '$chatid'");
$query->execute();
$row = $query->fetchAll();
if($row[0]['migrated'] == 1){
$loginbot->sendMessage($chatid, "❌Error: You've already migrated your account.");
}else{
	if($row[0]['code'] != NULL){
	$loginbot->sendMessage($chatid, "❌Error: You've already set your backup code.");
	}else{
		$old->prepare("UPDATE `licenses` SET `code`='$code' WHERE `ChatId` = '$chatid'")->execute();
		$loginbot->sendMessage($chatid, "✅Backup code set.");
	}
}
}else{
$loginbot->sendMessage($chatid, "❌Error: Only licensed users can set a backup code.");
}
}else{
$loginbot->sendMessage($chatid, "❌Error: Invalid code, please try again.");
}
}

if($message == "/id"){
$loginbot->sendMessage($chatid, $chatid);
}

?>