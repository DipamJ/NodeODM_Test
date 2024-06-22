<?php
$type = $_GET["type"];

$file = "../Python/" . $type . ".txt";
$content = file_get_contents($file);

$data = explode(";", $content);

$point = array("x" => (int)$data[0], "y" => (float)$data[1]);

echo json_encode($point);
?>