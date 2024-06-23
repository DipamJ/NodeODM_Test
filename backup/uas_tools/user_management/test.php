<?php
echo "this is a test";

//header("Refresh:0");


for ($x = 0; $x < 2; $x++) {


    $URL="http://bhub.gdslab.org/web/uas_tools/las_upload/index.php";
    echo "<script>document.location.href='{$URL}';</script>";
    echo '<META HTTP-EQUIV="refresh" content="0;URL=' . $URL . '">';

}

?>