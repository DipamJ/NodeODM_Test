<?php
// ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$userapproved = $_SESSION['admin_approved'] ?? '';

 // $userapproved;

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

//_log("Status: ".$userapproved);

require_once('inc/config.php');
require_once('layouts/header.php');
require_once('layouts/left_sidebar.php');
?>
<style>
    /*#Container::-webkit-scrollbar {
        display: none;
    }

    #Container {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }*/
</style>


<div class="content-wrapper">
    <div id="Container" class="" style="position: relative;">
        <!--container-fluid-->
        <!--<div id="Container" style="padding-bottom:56.25%; position:relative; display:block; width: 100%">-->
        <!--        <iframe name="iframe_a" width="100%" height="100%" frameborder="0" allowfullscreen="" style="border:none;  position:absolute; top:0; left: 0" title="UAS Page Menu"></iframe>-->
        <!--        THIS ARE THE PARAGRAPH SPACES THAT I ADDDED-->
        <!--        WE NEED TO REMOVE THEM AND MAKE THE PAGES LOOK GOOD INSIDE THE RESPONSIVE iFrame-->
        <br> <br>
        <!--<iframe name="iframe_a" width="100%" height="100%" frameborder="0" allowfullscreen="" style="border:none;  position:absolute; left: 0" title="UAS Page Menu"></iframe>-->
        <!-- <iframe name="iframe_a" width="100%" height="100%" frameborder="0" allowfullscreen="" style="border:none; height: 100vh; position: fixed; padding:25px;" title="UAS Page Menu"></iframe> -->
        <iframe name="iframe_a" width="100%" height="100%" frameborder="0" allowfullscreen="" style="border:none; height: 100vh; position: fixed; padding-top:25px; padding-bottom:25px;" title="UAS Page Menu"></iframe>
        <a href="/testing/regards.php" target="iframe_a"></a>
        <br>
        <br>
        <br>
        <div class="main">
        </div>
    </div>

    <?php require_once('layouts/footer.php'); ?>
