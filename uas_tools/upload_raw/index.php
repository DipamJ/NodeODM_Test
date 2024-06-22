<?php
//phpinfo();
// File containing System Variables
define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

// To check if User has the role required to access the page
//require_once("Resources/PHP/SetDBConnection.php");
require_once("../../resources/database/SetDBConnection.php");
//require_once("../system_management/centralized.php");

$mysqli = SetDBConnection();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$userName = $_SESSION["email"] ?? '';
$userapproved = $_SESSION['admin_approved'] ?? '';

// SELECT the role_name for each users_roles for the logged on user
// ? is a place holder for our parameter `user_id`
$sql = "
    SELECT r.role_name FROM users_roles AS ur
        JOIN roles AS r ON r.role_id = ur.role_id
    WHERE ur.user_id = ?
";

$query = $mysqli->prepare($sql);                // Prepare the query
$query->bind_param("i", $_SESSION["user_id"]);  // Bind the parameter (wherever you store user_id in $_SESSION)
$query->execute();                              // Run the query
$query->store_result();                         // Store the result
$query->bind_result($role_name);                // Bind the result to a variable

$user_role_array = [];                          // Initialise the user roles array
while ($query->fetch()) {                         // Loop returned records
    $user_role_array[] = $role_name;            // Add user role to array
}

//echo ('role' .$user_role_array);
//echo ('approved' .$userapproved);

if (mysqli_connect_errno()) {
    echo "Failed to connect to database server: " . mysqli_connect_error();
} else {
    if (!$user_role_array || $userapproved == "Disapproved") {
        echo '<script>alert("You do not have permission to access this page. You will be logout now.")</script>';
        echo "<html>";
        echo "<script>";
        echo "window.top.open('/index.php?logout=true')"; //$_SERVER['HTTP_HOST'] . '/index.php?logout=true'
        echo "</script>";
        echo "</html>";
    } else {
        $pageName = basename(__DIR__);
        if ($pageName == "V2") {
            $pageName = basename(realpath(__DIR__ . "/.."));
        }

        $sql1 = "SELECT * FROM page_access WHERE Page = '$pageName'";
        $allowedGroups = array();
        if ($result1 = mysqli_query($mysqli, $sql1)) {
            if ($row1 = mysqli_fetch_assoc($result1)) {
                $allowedGroups = explode(";", $row1["Page_Groups"]);
                $accessGroupsStr = $row1["Page_Groups"];
            }
        }

        $intersect = array_intersect($user_role_array, $allowedGroups);

        if (sizeof($intersect) > 0) {// if match found
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!--                <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>-->
    <!--                <meta name=”viewport” content=”width=device-width, initial-scale=1″>-->

    <!--                <meta http-equiv="Content-Type" content="text/html; "/>-->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, maximum-scale=5.0, initial-scale=1.0, user-scalable=no" />

    <title>Upload Raw Data</title>

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="Resources/style.css">
    <link href="Resources/css/responsive.css" type="text/css" rel="stylesheet">

    <link rel="stylesheet" href="Resources/css/bootstrap.min.css">

    <!--                <script type="text/javascript" src="Resources/JS/jquery.min.js"></script>-->
<!--    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>-->
    <script src="<?php echo $header_location; ?>/libraries/ajax/libs/jquery/1.12.4/jquery.min.js"></script>

    <script src="Resources/jss/bootstrap.min.js"></script>

<!--    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>-->
    <script src="<?php echo $header_location; ?>/libraries/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>

<!--    <script src="https://kit.fontawesome.com/00d920835c.js" crossorigin="anonymous"></script>-->
    <script src="<?php echo $header_location; ?>/libraries/font-awesome/js/00d920835c.js" crossorigin="anonymous"></script>


    <script src="Resources/JS/Chosen/chosen.jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/JS/Chosen/chosen.css">

    <script src="Resources/JS/JqueryUI/jquery-ui.min.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/JS/JqueryUI/jquery-ui.css">
    <script type="text/javascript" src="Resources/JS/resumable.js"></script>
    <script type="text/javascript" src="Resources/JS/main.js"></script>
    <script type="text/javascript" src="Resources/JS/spark-md5.min.js"></script>

    <script src="Resources/JS/FixedTable/fixed_table_rc.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/JS/FixedTable/fixed_table_rc.css">

    <link rel="stylesheet" href="Resources/JS/Lightbox/css/lightbox.min.css">
    <script src="Resources/JS/Lightbox/js/lightbox-plus-jquery.min.js"></script>

    <style>
        .add-button {
            float: right;
            margin: 10px 0 0 0;
            cursor: pointer;
        }

        #warning {
            width: 100%;
            margin-top: 10px;
            text-align: center;
            color: red;
            display: none;
        }

        tr:nth-child(even) {
            background: #FFF
        }

        input.btnNew {
            padding: 0;
            font-weight: 500;
            font-size: 17px;
            color: #ffffff;
            background: linear-gradient(#2c539e, #254488);
            line-height: 36px;
            border-radius: 5px;
            border: 1px solid #00236f;
        }

        p {
            font-size: 18px !important;
        }

    </style>

</head>

<body>
    <input type="hidden" id="user-name" value="<?php echo $userName; ?>" />
    <div class="let's-make-code">
        <div class="preempdia">
            <div class="container py-3">
                <!--main-->
                <div class="">
                    <!--main-row-->
                    <div class="projectBox">
                        <div id="select-flight">
                            <div class="project">
                                <h3>Select Flight</h3>
                                <!--<div class="frm">
                                    <div class="frm1">
                                        <div class="lbl">Project</div>
                                        <select id="project" class="select-large"></select>
                                    </div>
                                </div>-->
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Project</label>
                                            <select id="project" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Platform</label>
                                            <select id="platform" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Date</label>
                                            <select id="date" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Sensor</label>
                                            <select id="sensor" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <label>Flight</label>
                                            <select id="flight" class="form-control"></select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>&nbsp;</label>
                                            <input style="margin: 0;" type="button" class="add btnNew btn-block" value="Add Flight" onclick="ShowAddFlight(); return false;" />
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="text-center">
                                            <p class="text">Flight Attitide: <span type="text" id="altitude" class="normal"></span>,
                                                Forward Overlap: <span type="text" id="forward" class="normal"></span>,
                                                Side Overlap: <span type="text" id="side" class="normal"></span> </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="project" style="margin-top: 25px;">
                                <h3>Notification</h3>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>To</label>
                                            <input id="email-to" type="email" class="form-control" name="Email Address" placeholder="Email Address">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>CC</label>
                                            <input id="cc-address" type="email" class="form-control" name="Email Address" placeholder="Email Address">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>Note</label>
                                            <textarea id="note" name="" class="form-control"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12 float-right">
                                        <input id="upload-button" type="button" class="btnNew" value="Upload" onclick="CreateResumableInstance(''); return false;" />
                                    </div>
                                </div>
                            </div>
                            <div class="project " style="margin-top: 25px;">
                                <h3>Uploading List</h3>
                                <!--<div class='table-responsive'>
                                    <table class='table table-bordered bg-white'>
                                        <tbody>
                                            <tr>
                                                <td class='text-center'>IMG_0805.heic</td>
                                                <td>Unfinished</td>
                                                <td>
                                                    <div class='progress'>
                                                        <div class=progress-bar progress-bar-info role='progressbar' aria-valuenow='100' style='width:100%; background-color: #00ff01;'>
                                                            100% Complete (success)
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class='td-actions text-right'>
                                                    <img style='cursor:pointer;' src='Resources/Images/upload.png' alt='Pause' title='Resume' height='24' width='24'>
                                                    <img style='cursor:pointer;' src='Resources/Images/remove.png' alt='Cancel' title='Cancel' height='24' width='24'>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>-->
                                <div id="unfinished-files" style="text-align:center;"></div>
                                <div id="upload-files" style="text-align:center;"></div>
                                <div id="resumable-list" style="display:none"></div>
                            </div>
                            <div class="project" style="margin-top: 25px;">
                                <!--                    <div class="project project2" style="margin-top: 25px;">-->
                                <h3>Finished List</h3>
                                <div id="finished-list-wrapper" class="table-responsive"></div>
                            </div>
                        </div>

                        <div id="add-flight" style="display:none">
                            <div class="project">

                                <h3>Add Flight</h3>
                                <!--<div class="frm">
                                        <div class="frm-flex">
                                            <div class="frm1">
                                                <label for="" class="lbl">Name</label>
                                                <input type="text" id="flight-name" class="form-control">
                                            </div>
                                        </div>
                                    </div>-->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Name</label>
                                            <input type="text" id="flight-name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Project</label>
                                            <input type="text" id="flight-project" disabled class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Platform</label>
                                            <input type="text" id="flight-platform" disabled class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Sensor</label>
                                            <input type="text" id="flight-sensor" disabled class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Date</label>
                                            <input type="text" id="flight-date" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Flight Altitude</label>
                                            <input type="text" id="flight-altitude" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Forward Overlap</label>
                                            <input type="text" id="flight-forward" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Side Overlap</label>
                                            <input type="text" id="flight-side" class="form-control">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="button" class="btnNew float-left" value="Add" onclick="AddFlight(); return false;" />
                                        <input type="button" class="btnNew float-left ml-2" value="Cancel" onclick="ShowSelectFlight(); return false;" />

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="dialog-confirm" title="Cancel the upload?" style="display:none">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>The upload
            will be cancelled. Are you sure?</p>
    </div>
    <div id="dialog-different-file" title="The files are diffrent." style="display:none">
        <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Please select
            the correct file to resume the upload.</p>
    </div>

    <script>
        /* When the user clicks on the button,
                toggle between hiding and showing the dropdown content */
        function myFunction() {
            document.getElementById("myDropdown").classList.toggle("show");
        }

        // Close the dropdown if the user clicks outside of it
        window.onclick = function(event) {
            if (!event.target.matches('.dropbtn')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                var i;
                for (i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        }

        $(document).ready(function() {
            $(".select_chosen").chosen({
                inherit_select_classes: true
            });
        });

    </script>

</body>

</html>

<?php
        } else {
            $memberOf = (implode("; ", $user_role_array));
            ?>
<!DOCTYPE>
<html lang="html">

<head>
    <title><?php echo $pageName; ?></title>
</head>

<body>
    </br>
    <p>You do not currently have permission to access this tool.</p>
    <p>Please contact admin at
        <a href="mailto:<?= $admin_email ?>?
        &subject=Requesting%20access%20to%20the%20upload_raw%20tool
        &body=Hi,%0D%0A%0D%0AThis%20is%20<?= $admin_email ?>.%20Please%20provide%20me%20access%20to%20the%20tool.">
            <?= $admin_email ?></a>
        to request access to this tool.</p>
</body>

</html>
<?php
        }
    }
}
?>
