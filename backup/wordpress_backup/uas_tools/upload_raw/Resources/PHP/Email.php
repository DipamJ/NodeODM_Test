<?php
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

// If session hasn't been started, start it
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once("SetDBConnection.php");
require_once("CommonFunctions.php");

$userName = $_SESSION["email"];
$identifier = $_GET["identifier"];
//var_dump($identifier);

$con = SetDBConnection();

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    $sql = "SELECT * FROM notification " .
        "WHERE Identifier = '$identifier'";
    //_log('select notification: ');
    //_log($sql);
    $result = mysqli_query($con, $sql);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $to = $row["Receiver"];
        $note = $row["Note"];
        $fileName = $row["FileName"];
        $cc = $row["CC"];
        $project = $row["Project"];
        $flight = $row["Flight"];
        $date = $row["Date"];
        $noteStr = "";
        if ($note != "") {
            $noteStr = "\n Note: $note.";
        }
        /*
$row = mysqli_fetch_assoc($result);

        $to = $row["Receiver"];

        if (filter_var($to, FILTER_VALIDATE_EMAIL)) {
            $note = $row["Note"];
            $fileName = $row["FileName"];
            $cc =  $row["CC"];
            //var_dump($note);
            //var_dump($fileName);
            //var_dump($cc);

            $noteStr = "";
            if ($note != "") {
                $noteStr = "\n Note: $note .";
            }

            $ccStr = "";
            if (filter_var($cc, FILTER_VALIDATE_EMAIL)) {
                $ccStr = "\r\nCC: $cc";
            }

            $size = FormatBytes($row["FileSize"]);
            $project = $row["Project"];
            $flight = $row["Flight"];
            $date = $row["Date"];

            $subject = "$fileName ($size): Upload Finished";
            $txt = "$userName has just uploaded a file ($fileName : $size) (Project: $project, Date: $date, Flight: $flight).".$noteStr;
            $headers = "From: no-reply@uashub.tamucc.edu" .$ccStr;

            //var_dump($to);
            //var_dump($subject);
            //var_dump($txt);
            //var_dump($headers);
            //mail($to, $subject, $txt, $headers);

            $success = mail($to, $subject, $txt, $headers);
            if (!$success) {
                $errorMessage = error_get_last()['message'];
                _log($errorMessage);
            }

        }*/

        // SEND APPROVAL EMAIL
        //require_once '../../../../multi_users/PHPMailer/PHPMailerAutoload.php';
        // If session hasn't been started, start it
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        //$php_mailer_autoload = $_SESSION['php_mailer_autoload'];
        require_once $php_mailer_autoload;

        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = True;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = '465';
        $mail->isHTML();
        $mail->Username = 'uas.hub@gmail.com';// FROM
        $mail->Password = '#4*8H0u1!Vdb';
        $mail->SetFrom('uas.hub@gmail.com');// FROM
        //$mail->Subject = 'User needs approval';
        $size = FormatBytes($row["FileSize"]);
        $mail->Subject = "$fileName($size): Upload Finished";
        //$mail->Body = 'A test email!';// BODY
        $mail->Body = nl2br("$userName has just uploaded a file ($fileName : $size) (Project: $project, Date: $date, Flight: $flight).\r\n" . $noteStr);
        $mail->AddAddress($to);// TO
        $mail->AddCC($cc);// CC

        $mail->Send();
    }
    mysqli_close($con);
}
?>