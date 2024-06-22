<!DOCTYPE html>
<html>
<body>

<?
// CONNECT TO DB
$servername = "localhost";
$username = "hub_admin";
$password = "UasHtp_Rocks^^7";
$dbname = "uas_projects";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// READ VALUE FROM system_management TABLE
$sql = "SELECT Name, Value FROM system_management";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        echo "Name: " . $row["Name"] . " - Value: " . $row["Value"] . " <br>";
    }
} else {
    echo "0 results";
}
$conn->close();
?>

</body>
</html>