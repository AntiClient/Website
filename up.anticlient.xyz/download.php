<?php

require_once('./assets/inc/handle.php');
$funcs = new Funcs();

if((isset($_GET['user']) && isset($_GET['id'])) && !(empty($_GET['user']) || empty($_GET['id']))) {
  if(($funcs->checkUser($_GET['user']) > 0) && ($funcs->checkDownloadID($_GET['id'])) > 0) {


    $DownloadableFilesDirectory = "../download";
    $FileName = 'AntiClient.exe';
    $SaveName = 'AntiClient.exe';
    
    $DownloadableFilesDirectory = preg_replace('/^\/*/','/',$DownloadableFilesDirectory);
    $DownloadableFilesDirectory = preg_replace('/\/*$/','',$DownloadableFilesDirectory);
    $Directory = $_SERVER['DOCUMENT_ROOT'].$DownloadableFilesDirectory;
    $Dfile = $Directory.'/'.$FileName;
    $size = filesize($Dfile);
    if( ! $size )
    {
       echo '<p><b>The download file is empty or was not located.</b></p>';
       exit;
    }
    $ctype = 'application/octet-stream';
    header('Cache-control: private');
    header("Content-Type: $ctype");
    header('Content-Disposition: attachment; filename="'.$SaveName.'"');
    header('Content-Transfer-Encoding: binary');
    header("Content-Length: $size");
    @readfile($Dfile);
    $funcs->updateDownload($_GET['user']);
    exit;

  } else {
    $_SESSION['error'] = 'Invalid input. Try again.';
    header('Location: ./customer.php');
    exit;
  }
} else {
  $_SESSION['error'] = 'Invalid input. Try again.';
  header('Location: ./customer.php');
  exit;
}

 ?>
