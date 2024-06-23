<?php

$fileLocalPath = "../Python/popt.txt";

if (file_exists($fileLocalPath) == true) {
    $content = file_get_contents($fileLocalPath);
    $content = str_replace("[", "", $content);
    $content = str_replace("]", "", $content);
    $content = trim($content);
    $values = preg_split('/\s+/', $content);

    echo json_encode($values);

} else {
    echo 'Could not read input file';
}
?>