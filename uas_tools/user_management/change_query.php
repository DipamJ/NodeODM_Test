<?php
require_once("CommonFunctions.php");

$type = $_GET["selected"];
//_log($type);
//echo $type;

///////////////
$records_roles = mysqli_query($connect, "SELECT * FROM roles");
$roles = array();
$count = 0;

// $roles [] = ( [0] => Admin [1] => Subscriber [2] => Viewer [3] => UAS Pilot )
while ($course_roles = mysqli_fetch_assoc($records_roles)) {
    $roles [] =
        $course_roles['role_name'];
    $count++;
}
///////////////
//_log('user_id: ' . $_POST['user_id']);
//$user_id = $_POST['user_id'];
$user_id = 4; // Need to be fed from form

$checkbox1 = explode(",", $type);
//print_r($checkbox1);


// Start by deleting all the roles
for ($i = 0; $i < $count; $i++) {
    $a = $i + 1;
    $query2 = "DELETE FROM users_roles WHERE user_id = '$user_id' AND role_id = '$a'";
    //_log('$query2: ' . $query2);
    $in_ch2 = mysqli_query($connect, $query2);
}

// Insert selected roles
for ($i = 0; $i < sizeof($checkbox1); $i++) {
    $query = "INSERT INTO users_roles(user_id, role_id) VALUES ('$user_id', '" . $checkbox1[$i] . "')";
    //_log('$query: ' . $query);
    $in_ch = mysqli_query($connect, $query);
}
//    if ($in_ch == 1 || $in_ch2 == 1) {
//        echo '<script>alert("Role Updated Successfully")</script>';
//    } else {
//        echo '<script>alert("Failed to Update Role")</script>';
//    }
echo "<meta http-equiv='refresh' content='0'>";
$connect->close();

?>