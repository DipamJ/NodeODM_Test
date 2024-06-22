<?php
//require_once("Resources/PHP/SetDBConnection.php");
//require_once("../../resources/database/SetDBConnection.php");
// CONNECT TO DB
$servername = "localhost";
$username = "hub_admin";
$password = "PurdueGdsl!@2w";
$dbname = "uas_projects";
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['submit'])) { // the POST form has been submitted
    // root_path 1
    if (!empty($_POST['root_path'])) {
        $root_path = ($_POST['root_path']);

        $sql = "UPDATE system_management set Value = '$root_path' where Name = 'root_path'";
        $result = mysqli_query($conn, $sql);
    }

        // header_location 2
    if (!empty($_POST['header_location'])) {
        $header_location = ($_POST['header_location']);

        $sql = "UPDATE system_management set Value = '$header_location' where Name = 'site_name'";
        $result = mysqli_query($conn, $sql);
    }

    // threads_number 3
    if (!empty($_POST['cpu_number'])) {
        $cpu_number = ($_POST['cpu_number']);

        $sql = "UPDATE system_management set Value = '$cpu_number' where Name = 'threads_number'";
        $result = mysqli_query($conn, $sql);
    }

    // admin_email 4
    if (!empty($_POST['admin_email'])) {
        $admin_email = ($_POST['admin_email']);

        $sql = "UPDATE system_management set Value = '$admin_email' where Name = 'admin_email'";
        $result = mysqli_query($conn, $sql);
    }
}

$sql = "SELECT Name, Value FROM system_management WHERE ID = 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $root_path = $row["Value"];
    }
}
///

$sql = "SELECT Name, Value FROM system_management WHERE ID = 2";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $header_location = $row["Value"];
    }
}
///

$sql = "SELECT Name, Value FROM system_management WHERE ID = 3";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $cpu_number = $row["Value"];
    }
}

///
$sql = "SELECT Name, Value FROM system_management WHERE ID = 4";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $admin_email = $row["Value"];
    }
}
///

mysqli_close($conn);
?>
