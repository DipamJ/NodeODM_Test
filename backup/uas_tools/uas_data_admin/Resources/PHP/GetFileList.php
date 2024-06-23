<?php
//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");

$con = SetDBConnection();
if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {

    $flight = $_GET['flight'];
    $type = $_GET['type'];

    $sql = "";

    if ($type == "Raw") {
        $sql = "select * from raw_data where flight = $flight order by Name";
        //_log('select raw_data : ' . $sql);
    } else {
        //$sql = "select * from dataproduct where flight = $flight";
        $sql = "select data_product.Name, product_type.Name as Type, data_product.DownloadPath, data_product.HtmlPath, data_product.ThumbPath " .
            "from data_product, product_type " .
            "where flight = $flight and data_product.Type = product_type.ID order by data_product.Name";
        //_log('select data_product.Name... : ' . $sql);
    }
    $result = mysqli_query($con, $sql);

    $fileList = array();

    while ($row = mysqli_fetch_assoc($result)) {
        $fileList[] = $row;
    }
    mysqli_close($con);
    echo json_encode($fileList);
}

//echo json_encode($imageList);
?>
