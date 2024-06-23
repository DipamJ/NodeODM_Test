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

   	<link rel="stylesheet" href="../resources/reset.css"> <!-- CSS reset -->
   	<link rel="stylesheet" href= "../resources/css/style.css"> <!-- Resource style -->
   	<script src="../resources/js/modernizr.js"></script> <!-- Modernizr -->
   </head>

   <body>
<section class="cd-intro">
  <div class="top">
  <h1 class="h-custom-headline center-text  h3"> Welcome to UASHub </h1>
  </div>

  <h6 class="cd-headline letters type">
    <span>Online research collaboration portal for</span>
    <span class="cd-words-wrapper waiting">
      <b class="is-visible">processing </b>
      <b>analyzing </b>
      <b>visualyzing </b>
    </span>
    <span>UAS data.</span>
  </h6>
</section>

<br><br><br><br><br><br><br><br>

<script src="../resources/js/jquery-2.1.1.js"></script>
<script src="../resources/js/main.js"></script> <!-- Resource jQuery -->

      <div class="footer-content">
            <p class="center-text">

              SUPPORTED BY

              <img src="../resources/images/TexasA&MAgriLife.png" alt="60" width="150">
              <img src="../resources/images/PurdueUniversity.png" alt="60" width="150">
              <img src="../resources/images/OracleforResearch.png" alt="60" width="150">
            </p>
      </div>

      <div class="bottom">
        <br><br><br><br><br><br><br><br><br><br><br>
      </div>
   </body>
 </html>
