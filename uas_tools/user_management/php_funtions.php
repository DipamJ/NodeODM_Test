<?php
if (isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
    switch ($action) {
        case 'updateUserApproval' :
            updateUserApproval();
            break;
        //case 'blah' : blah();break;
        // ...etc...
    }
}

function updateUserApproval()
{
    echo 'test';
    //_log('test');

// Code to be executed
    // DB Connection
    $connect = mysqli_connect("localhost", "hub_admin", "UasHtp_Rocks^^7", "uas_projects");
    if (!$connect) {
        echo "Error: Unable to connect to MySQL." . PHP_EOL;
        echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
        exit;
    }

//    $selectOption = $_POST['selectbox'];
    $selectOption = $_POST['name'];
    echo $selectOption;

    $user_row = $_POST['user_id'];
    if ($selectOption == 'Approved') {
        $stmt_approved = "UPDATE users SET admin_approved = 'Approved' WHERE user_id = '" . $user_row . "'";
        $result_approved = mysqli_query($connect, $stmt_approved);
    } elseif ($selectOption == 'Disapproved') {
        $stmt_disaproved = "UPDATE users SET admin_approved = 'Disapproved' WHERE user_id= '" . $user_row . "'";
        $result_disaproved = mysqli_query($connect, $stmt_disaproved);
    }

//    if(isset($result_approved))
//    {
//        echo'<script>alert("User has been approved")</script>';
//    }
//    else if (isset($result_disaproved))
//    {
//        echo'<script>alert("User has been disapproved")</script>';
//    }
    //echo "<meta http-equiv='refresh' content='0'>";
    $connect->close();
}

?>