<?php

class Bot {

	private $token;
	private $restricted;
	private $restrictedUsers;

	public function __construct($token){
		$this->token = $token;
	}

	public function getToken(){
	return $this->token;
	}

	public function setRestricted($b){
	$this->restricted = $b;
	}

	public function setRestrictedUsers($list){
	$this->restrictedUsers = $list;
	}

	public function getRestrictedUsers(){
	return $this->restrictedUsers;
	}

	public function isRestricted(){
		return $this->restricted;
	}

	public function sendMessage($chatid, $message){
	$url = 'https://api.telegram.org/bot'.$this->getToken().'/sendMessage?chat_id='.$chatid.'&parse_mode=HTML&text='.urlencode($message);
	$ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 2000);
    $data = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);
//	file_get_contents($url);
	}

	/*
	* @chatid - User Chat ID
	* @message - Message to send
	* @parse_mode - Markdown or HTML
	*
	*/
	public function sendParsedMessage($chatid, $message, $parse_mode){
	$url = 'https://api.telegram.org/bot'.$this->getToken().'/sendMessage?chat_id='.$chatid.'&parse_mode='.$parse_mode.'&text='.urlencode($message);
	file_get_contents($url);
	}

	public function sendRestrictedMessage($message){
	foreach($this->restrictedUsers as $chatid){
	$url = 'https://api.telegram.org/bot'.$this->getToken().'/sendMessage?chat_id='.$chatid.'&parse_mode=HTML&text='.urlencode($message);
	$ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_NOSIGNAL, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 2000);
    $data = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    $curl_error = curl_error($ch);
    curl_close($ch);
	 }
	}

}

$loginbot = new Bot("<BOT TOKEN>");
$resultbot = new Bot("<BOT TOKEN>");

?>
