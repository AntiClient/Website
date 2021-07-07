<?php
require_once('/var/www/up.anticlient.xyz/assets/inc/bot.php');
require 'vendor/autoload.php';
use Mailgun\Mailgun;
use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\PaymentExecution;
use PayPal\Api\Transaction;


$apiContext = new \PayPal\Rest\ApiContext(
        new \PayPal\Auth\OAuthTokenCredential(
            '<CLIENT ID>',     // ClientID
            '<CLIENT SECRET>'      // ClientSecret
        )
);

$apiContext->setConfig(
	array(
	'mode' => 'live'
	)
);

$logsbot = new Bot("<BOT TOKEN>");
$logsbot->setRestricted(true);
$logsbot->setRestrictedUsers(array('<CHAT ID 1>'));

session_start();

class DB {

  private $db_host = 'localhost'; // Database Hostname
  private $db_name = 'anticlient'; // Database Name
  private $db_user = '<DB USERNAME>'; // Database Username
  private $db_psw = '<DB USER PASSWORD>'; // Database (user) Password


  public function MySQLConnect() {

    try {
      $dbh = new PDO('mysql:host='.$this->db_host.';dbname='.$this->db_name, $this->db_user, $this->db_psw, array(
        PDO::ATTR_PERSISTENT => true
      ));
      return $dbh;
    } catch (PDOException $e) {
      print "Error!: " . $e->getMessage() . "<br/>";
      die();
    }
  }

}

class Funcs {

  private $DB;

  public function __construct() {
    $newcon = new DB();
    $this->DB = $newcon->MySQLConnect();
  }

  public function checkIP() {
    if(isset($_SERVER["HTTP_CF_CONNECTING_IP"])){
        $rIP = $_SERVER["HTTP_CF_CONNECTING_IP"];
    } else{
        $rIP = $_SERVER['REMOTE_ADDR'];
    }
    return $rIP;
  }

  public function getWebLink() {
    return (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . '/';
  }
  
  public function logPayment($arg1, $arg2, $arg3, $arg4, $arg5){
	$sql = $this->DB->prepare('INSERT INTO purchases (username, amount, payerID, product, paymentId) VALUES (:uname, :amount, :payerid, :product, :paymentid)');
	$sql->bindParam(':uname', $arg1);
	$sql->bindParam(':amount', $arg2);
	$sql->bindParam(':payerid', $arg3);
	$sql->bindParam(':product', $arg4);
	$sql->bindParam(':paymentid', $arg5);
	if($sql->execute()){
		return true;
	}else{
		return false;
	}
  }

  public function checkUser($arg1) {
    $sql = $this->DB->prepare('SELECT COUNT(*) FROM users WHERE username = :uname');
    $sql->bindParam(':uname', $arg1);
    $sql->execute();
    return $sql->fetchColumn();
  }

  public function checkCodeRPW($arg1, $arg2) {
    $sql = $this->DB->prepare('SELECT COUNT(*) FROM users WHERE resetpw = :rpw AND username = :uname');
    $sql->bindParam(':rpw', $arg1);
    $sql->bindParam(':uname', $arg2);
    $sql->execute();
    return $sql->fetchColumn();
  }

  public function getUData($arg1) {
    $sql = $this->DB->prepare('SELECT * FROM users WHERE username = :uname');
    $sql->bindParam(':uname', $arg1);
    $sql->execute();
    $result = $sql->fetchAll();
    return $result[0];
  }

  public function updateUserIP($arg1) {
	$GLOBALS['logsbot']->sendRestrictedMessage("👨🏻‍✈️ ".$arg1." logged in with a new IP Address.\n
	New IP Address: ".$this->checkIP().' <a href="https://check-host.net/ip-info?host='.$this->checkIP().'">Info</a>');
    $sql = $this->DB->prepare('UPDATE users SET lastip = :ip WHERE username = :uname');
    $sql->bindParam(':ip', $this->checkIP());
    $sql->bindParam(':uname', $arg1);
    $sql2 = $this->DB->prepare('INSERT INTO iplogs (username, date, ip) VALUES (:uname, :date, :ip)');
    $sql2->bindParam(':uname', $arg1);
    $sql2->bindParam(':date', time());
    $sql2->bindParam(':ip', $this->checkIP());
    if($sql->execute() && $sql2->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function checkEmail($arg1) {
    $sql = $this->DB->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
    $sql->bindParam(':email', $arg1);
    $sql->execute();
    return $sql->fetchColumn();
  }

  public function regUser($arg1, $arg2, $arg3) {
    $sql = $this->DB->prepare('INSERT INTO users (username, email, password, firstlogin, lastip) VALUES (:uname, :mail, :pw, 1, :lip)');
    $sql->bindParam(':uname', $arg1);
    $sql->bindParam(':mail', $arg2);
    $sql->bindParam(':pw', $arg3);
    $sql->bindParam(':lip', $this->checkIP());
    $sql2 = $this->DB->prepare('INSERT INTO iplogs (username, date, ip) VALUES (:uname, :date, :ip)');
    $sql2->bindParam(':uname', $arg1);
    $sql2->bindParam(':date', time());
    $sql2->bindParam(':ip', $this->checkIP());
    if($sql->execute() && $sql2->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function insertNews($arg1, $arg2) {
    $sql = $this->DB->prepare('INSERT INTO news (content, date, data) VALUES (:content, :date, :data)');
    $sql->bindParam(':content', $arg1);
    $sql->bindParam(':date', time());
    $sql->bindParam(':data', $arg2);
    if($sql->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function checkNews() {
    $sql = $this->DB->prepare('SELECT COUNT(*) FROM news');
    $sql->execute();
    return $sql->fetchColumn();
  }

  public function getNews() {
    $sql = $this->DB->prepare('SELECT * FROM news ORDER BY date desc');
    $sql->execute();
    return $sql->fetchAll();
  }

  public function removeNews($arg1) {
    $sql = $this->DB->prepare('DELETE FROM news WHERE id = :id');
    $sql->bindParam(':id', $arg1);
    if($sql->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function genDown($arg1) {
    $chars = '0123456789aAbBcCdDeEfFgGhHiIlLjJkKmMnNoOpPqQrRsStTuUvVwWxXyYzZ';
    srand((double)microtime()*1000000);
    $i = 0;
    $code = '' ;

    while ($i <= 9) {
      $num = rand() % 33;
      $tmp = substr($chars, $num, 1);
      $code = $code . $tmp;
      $i++;
    }

    $sql = $this->DB->prepare('UPDATE users SET download=:code, download_generated_on=:date WHERE username=:uname');
    $sql->bindParam(':code', $code);
    $sql->bindParam(':date', time());
    $sql->bindParam(':uname', $arg1);
    if($sql->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function genPIN($arg1) {
    while($i < 4) {
      $pin .= rand(0, 9);
      $i++;
    }

    $sql = $this->DB->prepare('UPDATE users SET pin=:pin, pin_generated_on=:date WHERE username=:uname');
    $sql->bindParam(':pin', $pin);
    $sql->bindParam(':date', time());
    $sql->bindParam(':uname', $arg1);
    if($sql->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function checkTimes() {
    $sql = $this->DB->prepare('SELECT * FROM users');
    $sql->execute();
    return $sql->fetchAll();
  }

  public function updateTimes($type, $arg1) {
    if($type == 'pin') {
      $sql = $this->DB->prepare('UPDATE users SET pin=:pin, pin_generated_on=:date WHERE username=:uname');
      $sql->bindValue(':pin', null, PDO::PARAM_INT);
      $sql->bindValue(':date', null, PDO::PARAM_INT);
      $sql->bindParam(':uname', $arg1);
      if($sql->execute()) {
        return true;
      } else {
        return false;
      }
    } else if ($type == 'download') {
      $sql = $this->DB->prepare('UPDATE users SET download=:download, download_generated_on=:date WHERE username=:uname');
      $sql->bindValue(':download', null, PDO::PARAM_INT);
      $sql->bindValue(':date', null, PDO::PARAM_INT);
      $sql->bindParam(':uname', $arg1);
      if($sql->execute()) {
        return true;
      } else {
        return false;
      }
    } else if ($type == 'resetpw') {
      $sql = $this->DB->prepare('UPDATE users SET resetpw=:resetpw, resetpw_generated_on=:date WHERE username=:uname');
      $sql->bindValue(':resetpw', null, PDO::PARAM_INT);
      $sql->bindValue(':date', null, PDO::PARAM_INT);
      $sql->bindParam(':uname', $arg1);
      if($sql->execute()) {
        return true;
      } else {
        return false;
      }
    } else if ($type == 'buy') {
      $sql = $this->DB->prepare('UPDATE users SET rank=0, rank_bought_on=:nrank WHERE username=:uname');
      $sql->bindValue(':nrank', null, PDO::PARAM_INT);
      $sql->bindParam(':uname', $arg1);
      if($sql->execute()) {
        return true;
      } else {
        return false;
      }
    }
  }

  public function checkResults($arg1) {
    $sql = $this->DB->prepare('SELECT COUNT(*) FROM results WHERE username = :uname');
    $sql->bindParam(':uname', $arg1);
    $sql->execute();
    return $sql->fetchColumn();
  }

  public function getResults($arg1) {
    $sql = $this->DB->prepare('SELECT * FROM results WHERE username = :uname ORDER BY id DESC LIMIT 5');
    $sql->bindParam(':uname', $arg1);
    $sql->execute();
    return $sql->fetchAll();
  }

  public function getUsers($arg1) {
    $sql = $this->DB->prepare('SELECT * FROM users ORDER BY id ASC LIMIT :start, 20');
    $sql->bindValue(':start', $arg1, PDO::PARAM_INT);
    $sql->execute();
    return $sql->fetchAll();
  }

  public function getUsersNum() {
    $sql = $this->DB->prepare('SELECT COUNT(*) FROM users');
    $sql->execute();
    return $sql->fetchColumn();
  }

  public function updateRank($arg1, $arg2) {
    $sql = $this->DB->prepare('UPDATE users SET rank=:rank WHERE username=:uname');
    $sql->bindParam(':uname', $arg1);
    $sql->bindParam(':rank', $arg2);
    if($sql->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function updateBan($arg1, $arg2) {
    $sql = $this->DB->prepare('UPDATE users SET banned=:ban WHERE username=:uname');
    $sql->bindParam(':uname', $arg1);
    $sql->bindParam(':ban', $arg2);
    if($sql->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function delUser($arg1) {
    $sql = $this->DB->prepare('DELETE FROM users WHERE id = :id');
    $sql->bindParam(':id', $arg1);
    if($sql->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function updateUser($arg1, $arg2, $arg3) {
    if($arg1 == 'mail') {
      $sql = $this->DB->prepare('UPDATE users SET email=:mail WHERE username = :uname');
      $sql->bindParam(':mail', $arg2);
      $sql->bindParam(':uname', $arg3);
      if($sql->execute()) {
        return true;
      } else {
        return false;
      }
    } else if($arg1 == 'pw') {
      $pwHash = password_hash($arg2, PASSWORD_ARGON2I);
      $sql = $this->DB->prepare('UPDATE users SET password=:pw WHERE username = :uname');
      $sql->bindParam(':pw', $pwHash);
      $sql->bindParam(':uname', $arg3);
      if($sql->execute()) {
        return true;
      } else {
        return false;
      }
    }
  }

  public function getLogs($arg1, $arg2) {
    if($arg1 == 'search') {
      $sql = $this->DB->prepare('SELECT * FROM iplogs WHERE username = :uname ORDER BY date DESC');
      $sql->bindParam(':uname', $arg2);
      $sql->execute();
      return $sql->fetchAll();
    } else if ($arg1 == NULL) {
      $sql = $this->DB->prepare('SELECT * FROM iplogs ORDER BY date DESC LIMIT :start, 20');
      $sql->bindValue(':start', $arg2, PDO::PARAM_INT);
      $sql->execute();
      return $sql->fetchAll();
    } else if ($arg1 == 'userC'){
      $sql = $this->DB->prepare('SELECT COUNT(*) FROM iplogs WHERE username = :uname');
      $sql->bindParam(':uname', $arg2);
      $sql->execute();
      return $sql->fetchColumn();
    }
  }

  public function getLogsNum() {
    $sql = $this->DB->prepare('SELECT COUNT(*) FROM iplogs');
    $sql->execute();
    return $sql->fetchColumn();
  }

  public function getProducts() {
    $sql = $this->DB->prepare('SELECT * FROM products ORDER BY id DESC');
    $sql->execute();
    return $sql->fetchAll();
  }

  public function sendMail($arg1, $arg2, $arg3) {
	  /*
    $mg = Mailgun::create('<MailGun Token>');

    $mg->messages()->send('anticlient.xyz', [
      'from'    => 'noreply@anticlient.xyz',
      'to'      => $arg1,
      'subject' => $arg2,
      'text'    => $arg3,
      'o:dkim'  => 'true',
      'html'    => '

      <style>

      .footer > a:hover {
        color: #0056b3;
        transition: 400ms;
      }
      </style>


      <div id="header" style="width: 100%; height: 150px; background: #0056b3;">
      <center><img style="margin-top: 1%;" src="https://up.anticlient.xyz/assets/img/logo.png" width="100" height="100"></center>
      </div>
      <div id="content" style="width: 100%; min-height: 200px;">
        <p style="margin-left: 1%; margin-right: 1%;">
          '.$arg3.'
        </p>
      </div>
      <div id="footer" style="width: 100%; height: 80px; background: #0e253f; color: white; text-align: center; line-height: 80px;">
      Email sent automatically by <a style="text-decoration: none;" href="http://anticlient.xyz">AntiClient</a> please <b>do not answer</b>
      </div>'
    ]);*/
  }

  public function genResetPw($arg1, $arg2) {
    $chars = '0123456789aAbBcCdDeEfFgGhHiIlLjJkKmMnNoOpPqQrRsStTuUvVwWxXyYzZ';
    srand((double)microtime()*1000000);
    $i = 0;
    $code = '' ;

    while ($i <= 14) {
      $num = rand() % 33;
      $tmp = substr($chars, $num, 1);
      $code = $code . $tmp;
      $i++;
    }

    $sql = $this->DB->prepare('UPDATE users SET resetpw=:code, resetpw_generated_on=:date WHERE username = :uname');
    $sql->bindParam(':code', $code);
    $sql->bindParam(':date', time());
    $sql->bindParam(':uname', $arg1);
    $sql->execute();
    $this->sendMail($arg2, 'Password reset', 'Hi '.$arg1.',<br><br> You have requested to reset your password, to do so please click the link below and follow the procedure. (The link will no longer be active after 5 minutes)<br><br><a href="'.$this->getWebLink().'resetpw.php?uname='.$arg1.'&code='.$code.'">Reset your password</a><br><br>Best regards,<br>Your friends at AntiClient');
  }

  public function updateRPW($arg1, $arg2) {
    $npw = password_hash($arg1, PASSWORD_ARGON2I);

    if($this->getUData($arg2)['firstlogin'] == 0) {
      $sql = $this->DB->prepare('UPDATE users SET firstlogin = 1 WHERE username = :uname');
      $sql->bindParam(':uname', $arg2);
      $sql->execute();
    }

    $sql = $this->DB->prepare('UPDATE users SET password=:pw, resetpw=:code, resetpw_generated_on=:date WHERE username = :uname');
    $sql->bindParam(':pw', $npw);
    $sql->bindValue(':code', null, PDO::PARAM_INT);
    $sql->bindValue(':date', null, PDO::PARAM_INT);
    $sql->bindParam(':uname', $arg2);
    if($sql->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function checkUEmail($arg1) {
    $sql = $this->DB->prepare('SELECT COUNT(*) FROM users WHERE email = :email');
    $sql->bindParam(':email', $arg1);
    $sql->execute();
    return $sql->fetchColumn();
  }

  public function getUDataByEmail($arg1) {
    $sql = $this->DB->prepare('SELECT * FROM users WHERE email = :email');
    $sql->bindParam(':email', $arg1);
    $sql->execute();
    return $sql->fetchAll()[0];
  }

  public function checkPackageE($arg1) {
    $sql = $this->DB->prepare('SELECT COUNT(*) FROM products WHERE id = :id');
    $sql->bindParam(':id', $arg1);
    $sql->execute();
    return $sql->fetchColumn();
  }

  public function getPackageInfo($arg1) {
    $sql = $this->DB->prepare('SELECT * FROM products WHERE id = :id');
    $sql->bindParam(':id', $arg1);
    $sql->execute();
    return $sql->fetchAll()[0];
  }

  public function addPackage($arg1, $arg2, $arg3, $arg4) {
    $sql = $this->DB->prepare('INSERT INTO products (name, price, type, features) VALUES (:name, :price, :type, :features)');
    $sql->bindParam(':name', $arg1);
    $sql->bindParam(':price', $arg2);
    $sql->bindParam(':type', $arg3);
    $sql->bindParam(':features', $arg4);
    if($sql->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function removePackage($arg1) {
    $sql = $this->DB->prepare('DELETE FROM products WHERE id = :id');
    $sql->bindParam(':id', $arg1);
    if($sql->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function updatePackage($arg1, $arg2, $arg3, $arg4, $arg5) {
    $sql = $this->DB->prepare('UPDATE products SET name=:name, price=:price, type=:type, features=:features WHERE id = :id');
    $sql->bindParam(':name', $arg1);
    $sql->bindParam(':price', $arg2);
    $sql->bindParam(':type', $arg3);
    $sql->bindParam(':features', $arg4);
    $sql->bindParam(':id', $arg5);
    if($sql->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function checkDownloadID($arg1) {
    $sql = $this->DB->prepare('SELECT COUNT(*) FROM users WHERE download = :id');
    $sql->bindParam(':id', $arg1);
    $sql->execute();
    return $sql->fetchColumn();

  }

  public function updateDownload($arg1) {
    $sql = $this->DB->prepare('UPDATE users SET download=:download, download_generated_on=:date WHERE username = :uname');
    $sql->bindValue(':download', null, PDO::PARAM_INT);
    $sql->bindValue(':date', null, PDO::PARAM_INT);
    $sql->bindParam(':uname', $arg1);
    if($sql->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function makePayment($arg1, $arg2) {

      $payer = new Payer();
      $payer->setPaymentMethod('paypal');

      $amount = new Amount();
      $amount->setTotal($arg1);
      $amount->setCurrency('EUR');

      $transaction = new Transaction();
      $transaction->setAmount($amount);

      $redirectUrls = new RedirectUrls();
      $redirectUrls->setReturnUrl("https://up.anticlient.xyz/purchase.php?success=true&plan=".$arg2."&prc=".$arg1)
          ->setCancelUrl("https://up.anticlient.xyz/purchase.php?success=false");

      $payment = new Payment();
      $payment->setIntent('sale')
          ->setPayer($payer)
          ->setTransactions(array($transaction))
          ->setRedirectUrls($redirectUrls);

      try {
        $payment->create($GLOBALS['apiContext']);
        return $payment->getApprovalLink();
      } catch (\PayPal\Exception\PayPalConnectionException $ex) {
          return false;
      }
  }

  public function executePayment($arg1, $arg2, $arg3) {
    $paymentId = $arg1;
    $payment = Payment::get($paymentId, $GLOBALS['apiContext']);

    $execution = new PaymentExecution();
    $execution->setPayerId($arg2);

    $transaction = new Transaction();
    $amount = new Amount();
    $amount->setCurrency('EUR');
    $amount->setTotal($arg3);
    $transaction->setAmount($amount);

    $execution->addTransaction($transaction);

    try {
        $result = $payment->execute($execution, $GLOBALS['apiContext']);
        return true;
        try {
            $payment = Payment::get($paymentId, $GLOBALS['apiContext']);
        } catch (Exception $ex) {
            return false;
        }
    } catch (Exception $ex) {
        return false;
    }
    return true;

  }

  public function updateUserRank($arg1, $arg2) {
    if($arg2 != 'Lifetime') {
      $sql = $this->DB->prepare('UPDATE users SET rank=1, rank_bought_on=:date WHERE username=:uname');
      $sql->bindParam(':uname', $arg1);
      $sql->bindParam(':date', time());
      if($sql->execute()) {
        return true;
      } else {
        return false;
      }
    } else {
      $sql = $this->DB->prepare('UPDATE users SET rank=1 WHERE username=:uname');
      $sql->bindParam(':uname', $arg1);
      if($sql->execute()) {
        return true;
      } else {
        return false;
      }
    }
  }

  public function checkPartner($arg1) {
    $sql = $this->DB->prepare('SELECT COUNT(*) FROM partner WHERE username = :uname');
    $sql->bindParam(':uname', $arg1);
    $sql->execute();
    return $sql->fetchColumn();
  }

  public function checkPartners($arg1) {
    $sql = $this->DB->prepare('SELECT COUNT(*) FROM partner WHERE addedby = :uname');
    $sql->bindParam(':uname', $arg1);
    $sql->execute();
    return $sql->fetchColumn();
  }

  public function getPartners($arg1) {
    $sql = $this->DB->prepare('SELECT * FROM partner WHERE addedby = :uname');
    $sql->bindParam(':uname', $arg1);
    $sql->execute();
    return $sql->fetchAll();
  }

  public function addPartner($arg1, $arg2) {
    $nlicense = $this->getUData($arg2)['licenses'] - 1;
    $sql = $this->DB->prepare('INSERT INTO partner (username, addedby, addedon) VALUES (:uname, :by, :date)');
    $sql->bindParam(':uname', $arg1);
    $sql->bindParam(':by', $arg2);
    $sql->bindParam(':date', time());
    $sql2 = $this->DB->prepare('UPDATE users SET licenses = :newlicense WHERE username = :uname');
    $sql2->bindParam(':newlicense', $nlicense);
    $sql2->bindParam(':uname', $arg2);
    if($sql->execute() && $sql2->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function removePartner($arg1, $arg2) {
    $nlicense = $this->getUData($arg2)['licenses'] + 1;
    $sql = $this->DB->prepare('DELETE FROM partner WHERE id = :id');
    $sql->bindParam(':id', $arg1);
    $sql2 = $this->DB->prepare('UPDATE users SET licenses = :newlicense WHERE username = :uname');
    $sql2->bindParam(':newlicense', $nlicense);
    $sql2->bindParam(':uname', $arg2);
    if($sql->execute() && $sql2->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function checkSessions($arg1) {
    $sql = $this->DB->prepare('SELECT COUNT(*) FROM sessions WHERE username = :uname');
    $sql->bindParam(':uname', $arg1);
    $sql->execute();
    return $sql->fetchColumn();
  }

  public function getSessions($arg1) {
    $sql = $this->DB->prepare('SELECT * FROM sessions WHERE username = :uname ORDER BY id DESC LIMIT 5');
    $sql->bindParam(':uname', $arg1);
    $sql->execute();
    return $sql->fetchAll();
  }

  public function APICheckSession($arg1, $arg2) {
    $sql = $this->DB->prepare('SELECT COUNT(*) FROM sessions WHERE username = :uname AND pin = :pin AND status = "Open"');
    $sql->bindParam(':uname', $arg1);
    $sql->bindParam(':pin', $arg2);
    $sql->execute();
    return $sql->fetchColumn();
  }

  public function APIDestroySession($arg1, $arg2) {
    $sql = $this->DB->prepare('UPDATE sessions SET status="Closed" WHERE username = :uname AND status = "Open" AND pin = :pin');
    $sql->bindParam(':uname', $arg1);
    $sql->bindParam(':pin', $arg2);
    if($sql->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function APILog($arg1, $arg2, $arg3, $arg4, $arg5, $arg6, $arg7, $arg8, $arg9) {
    $sql = $this->DB->prepare('INSERT INTO results (username, pin, check1, check2, check3, date, alts, recyclebin, startTime) VALUES (:uname, :pin, :c1, :c2, :c3, :date, :alts, :recbin, :sttime)');
    $sql->bindParam(':uname', $arg1);
    $sql->bindParam(':pin', $arg2);
    $sql->bindParam(':c1', $arg3);
    $sql->bindParam(':c2', $arg4);
    $sql->bindParam(':c3', $arg5);
    $sql->bindParam(':date', $arg6);
    $sql->bindParam(':alts', $arg7);
    $sql->bindParam(':recbin', $arg8);
    $sql->bindParam(':sttime', $arg9);
    if($sql->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function APIGetPin($arg1) {
    $sql = $this->DB->prepare('SELECT * FROM users WHERE pin = :pin');
    $sql->bindParam(':pin', $arg1);
    $sql->execute();
    return $sql->fetchAll()[0];
  }

  public function APICheckPin($arg1) {
    $sql = $this->DB->prepare('SELECT COUNT(*) FROM users WHERE pin = :pin');
    $sql->bindParam(':pin', $arg1);
    $sql->execute();
    return $sql->fetchColumn();
  }

  public function APIUpdatePin($arg1) {
    $sql = $this->DB->prepare('UPDATE users SET pin = :pin WHERE pin = :pinOld');
    $sql->bindValue(':pin', null, PDO::PARAM_INT);
    $sql->bindParam(':pinOld', $arg1);
    if($sql->execute()) {
      return true;
    } else {
      return false;
    }
  }

  public function APIGenSession($arg1, $arg2, $arg3) {
    $sql = $this->DB->prepare('INSERT INTO sessions (username, pin, status, date) VALUES (:uname, :pin, "Open", :date)');
    $sql->bindParam(':uname', $arg1);
    $sql->bindParam(':pin', $arg2);
    $sql->bindParam(':date', $arg3);
    if($sql->execute()) {
      return true;
    } else {
      return false;
    }
  }
}


 ?>
