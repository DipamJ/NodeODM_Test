<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';
?>

 <!DOCTYPE html>
 <html lang="en" dir="ltr">
   <head>
     <meta charset="utf-8">
     <title>Regards</title>
     <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='<?php echo $header_location; ?>/libraries/css/Source+Sans+Pro:300,400,700.css' rel='stylesheet' type='text/css'>

   	<link rel="stylesheet" href="reset.css"> <!-- CSS reset -->
   	<link rel="stylesheet" href= "css/style.css"> <!-- Resource style -->
   	<script src="js/modernizr.js"></script> <!-- Modernizr -->
   </head>

   <body>
<section class="cd-intro">
  <div class="top">
  <h1 class="h-custom-headline center-text  h3"> Welcome to West-TX Cotton UASHub </h1>
  </div>

  <h6 class="cd-headline letters type">
    <span>Online portal for</span>
    <span class="cd-words-wrapper waiting">
      <b class="is-visible">processing </b>
      <b>analyzing </b>
      <b>visualyzing </b>
    </span>
    <span>UAS data for West Texas Cotton.</span>
  </h6>
</section>

<br><br><br><br><br><br><br><br>

<script src="js/jquery-2.1.1.js"></script>
<script src="js/main.js"></script> <!-- Resource jQuery -->

      <div class="footer-content">
            <p class="center-text">

              SUPPORTED BY

              <img src="images/TexasA&MAgriLife.png" alt="60" width="150">
              <img src="images/PurdueUniversity.png" alt="60" width="150">
              <img src="images/cottoninc-logo.gif" alt="60" width="150">
              <img src="images/plains_cotton_growers.png" alt="60" width="150">
            </p>
      </div>

      <div class="bottom">
        <br><br><br><br><br><br><br><br><br><br><br>
      </div>
   </body>
 </html>
