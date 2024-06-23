<?php
// ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$userapproved = $_SESSION['admin_approved'] ?? '';

 // Log Document
 function _log($str)
 {
     // log to the output
     $log_str = date('d.m.Y') . ": {$str}\r\n";
     echo $log_str;

     // log to file
     if (($fp = fopen('upload_log.txt', 'a+')) !== false) {
         fputs($fp, $log_str);
         fclose($fp);
     }
 }

require_once('inc/config.php');
require_once('layouts/header.php');
require_once('layouts/left_sidebar.php');
?>
<style>
</style>

<div class="content-wrapper">
    <div id="Container" class="" style="position: relative;">
        <br> <br>
        <iframe name="iframe_a" width="100%" height="100%" frameborder="0" allowfullscreen="" style="border:none; height: 100vh; position: fixed; padding-top:25px; padding-bottom:25px;" title="UAS Page Menu"></iframe>
        <br>
        <br>
        <br>
        <div class="main">
        </div>
    </div>

 <?php require_once('layouts/footer.php'); ?>
