<?php

$target_dir = "sample_csvs/";
$file_name = uniqid() . '_' . basename($_FILES["variety_file"]["name"]);
$target_file = $target_dir . $file_name;

move_uploaded_file($_FILES["variety_file"]["tmp_name"], $target_file);

$command = escapeshellcmd("python3 get_varieties.py $target_file");

// echo $command;

$result = shell_exec($command);

$varieties = json_decode($result);

// var_dump($varieties);

foreach ($varieties as $vriety) {
    echo $vriety . '<br>';
}

?>
