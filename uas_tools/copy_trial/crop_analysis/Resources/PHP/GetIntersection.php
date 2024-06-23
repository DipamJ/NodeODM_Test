<?php

$file = "../Python/intersection.txt";
$content = file_get_contents($file);

$data = explode(";", $content);

$intersection = array("x" => (int)$data[0], "y" => (float)$data[1]);

echo json_encode($intersection);
?>