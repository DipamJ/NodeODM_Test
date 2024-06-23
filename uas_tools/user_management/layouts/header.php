<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require_once LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

//strip string from this characters
// Replace the characters "world" in the string "Hello world!" with "Peter":
//echo str_replace("world","Peter","Hello world!");
$name = str_replace("https://", "", $header_location);
$nameWithNoDot = strtok($name, '.');
$nameWithNoDotCapitalized = strtoupper($nameWithNoDot);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name=”viewport” content=”width=device-width, initial-scale=1″>
  <meta name="description" content="">
  <meta name="author" content="">

  <title><?php echo $nameWithNoDotCapitalized; ?></title>

  <!-- Bootstrap core CSS-->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Custom fonts for this template-->
  <link href="assets/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">
  <!-- Custom styles for this template-->
<!--  <link href="assets/css/sb-admin.css" rel="stylesheet">-->

  <!-- Styles -->
  <link rel="stylesheet" type="text/css" href="Resources/style.css">
</head>
<style>
    .dropdown:hover .dropdown-menu{
        display: block;
    }
    .dropdown-menu{
        margin-top: 0;
    }
</style>
<!--<script>-->
<!--$(document).ready(function(){-->
<!--    $(".dropdown").hover(function(){-->
<!--        var dropdownMenu = $(this).children(".dropdown-menu");-->
<!--        if(dropdownMenu.is(":visible")){-->
<!--            dropdownMenu.parent().toggleClass("open");-->
<!--        }-->
<!--    });-->
<!--});-->
<!--</script>-->

<!--<body class="fixed-nav sticky-footer bg-dark" id="page-top">-->
<body class="" id="page-top">