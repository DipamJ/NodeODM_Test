<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

function Email($identifier, $type)
{
    require_once("SetDBConnection.php");
    require_once("CommonFunctions.php");

    $identifier = $_GET["identifier"];

    $con = SetDBConnection();

    if (mysqli_connect_errno()) {
        echo "Failed to connect to database server: " . mysqli_connect_error();
    } else {
        $sql = "SELECT * FROM data_product_notification " .
            "WHERE Identifier = '$identifier'";

        $result = mysqli_query($con, $sql);
        if ($result) {// TO SEND EMAILS
            $row = mysqli_fetch_assoc($result);

            $user = $row["Uploader"];
            $to = $row["Email"];
            $fileName = $row["FileName"];
            $size = FormatBytes($row["FileSize"]);
            $project = $row["Project"];
            $flight = $row["Flight"];
            $folder = $row["Folder"];

            if ($type == "Success") {
                $subject = "Data product '$fileName ($size)': Processing Finished";
                $txt = "The data product ($fileName : $size, Project: $project, Flight: $flight) has been successfully processed.";
            } else {
                $subject = "Data product '$fileName ($size)': Failed";
                $txt = "Failed to process the data product ($fileName : $size, Project: $project, Flight: $flight). Please make sure to select the correct product type and try again.";
            }

            //$headers = "From: " . $_SESSION("admin_email");
            $headers = "From: " . $admin_email;


            mail($to, $subject, $txt, $headers);
        }
        mysqli_close($con);
    }
}
