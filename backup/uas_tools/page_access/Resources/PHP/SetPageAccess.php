<?php
ini_set('display_errors', 1);
//require_once("SetDBConnection.php");
require_once("../../../../resources/database/SetDBConnection.php");

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $selectedPage = $_GET["SelectedPage"];
    //_log('Selected Page: '.$selectedPage.);
    //echo ('Selected Page: ' .$selectedPage); // data_visualization

    if (isset($_REQUEST["SelectedGroups"])) {
        $selectedGroups = $_REQUEST["SelectedGroups"];
        //$selectedGroupStr = implode(";", $selectedGroups);
    } else {
        $selectedGroups = '';
    }

    //_log('Selected Groups: '.$selectedGroups);
    //echo ('Selected Groups: ' .$selectedGroups);
    //print_r($selectedGroups);// Array ( [0] => Admin [1] => Subscriber )

    $selectedGroupStr = implode(";", $selectedGroups);

    $sql = "select * from page_access where Page = '$selectedPage'";
    if ($result = mysqli_query($con, $sql)) {
        if ($row = mysqli_fetch_assoc($result)) {
            $sql = "update page_access set Page_Groups = '$selectedGroupStr' where ID = '" . $row["ID"] . "'";
            //echo ('row id: ' .$row["ID"]);
            echo mysqli_query($con, $sql);
        } else {
            $sql = "insert into page_access (Page, Page_Groups) values ('$selectedPage', '$selectedGroupStr')";
            //_log('insert to page_access: '.$sql);
            //echo('insert to page_access: '.$sql);
            echo !mysqli_query($con, $sql);
        }

    }

    mysqli_close($con);
}
?>
