<?php
echo "Test PHP";
 ?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title></title>
  </head>

  <body>
    Test HTML
<form action="" method="POST">
    <button <?php
                //echo 'disabled';
            ?> name="generate_results" type="submit" class="btn btn-primary btn-block">Generate Results</button>
</form>
  </body>

<?php
$target_file = 'Home';

  if (isset($_POST['generate_results'])) {
    echo "Button has been pressed.";
    $command = escapeshellcmd("python3 hw3.py $target_file");
    $result = shell_exec($command);
    if ($result){
      echo "Python code has been excuted. Check /var/www/html/temp/test.txt for output.";
    }
  }
?>

</html>
