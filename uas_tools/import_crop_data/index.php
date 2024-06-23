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

<!DOCTYPE>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name=”viewport” content=”width=device-width, initial-scale=1″>
    <title>Import Crop Data</title>

    <!-- Google Fonts -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Roboto+Condensed" rel="stylesheet"> -->
    <link href="<?php echo $header_location; ?>/libraries/css/Roboto+Condensed.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="/uas_tools/upload_product/Resources/css/bootstrap.min.css">
    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="Resources/style.css">

    <script type="text/javascript" src="Resources/JS/jquery.min.js"></script>
    <script src="Resources/JS/JqueryUI/jquery-ui.min.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/JS/JqueryUI/jquery-ui.css">
    <script type="text/javascript" src="Resources/JS/main.js"></script>
    <script src="Resources/JS/FixedTable/fixed_table_rc.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/JS/FixedTable/fixed_table_rc.css">
    <style>
        .project {
            margin: 0px 0px 0px 0px;
            padding: 25px 35px;
            border-radius: 15px;
            background: #f6f7f9;
        }

        .form-control-1 {
            background: #ededed;
            box-shadow: inset 0px 0px 5px 0px rgb(0 0 0 / 5%);
            margin: 0px 0px 0px 0px;
            padding: 0px 10px 0px 15px;
            font-weight: normal;
            font-size: 18px;
            color: #000000;
            display: block;
            background-color: #ededed;
            line-height: 55px;
            border-radius: 5px;
            border: none;
        }

        input.btnNew {
            padding: 0;
            font-weight: 500;
            font-size: 17px;
            color: #ffffff;
            background: linear-gradient(#2c539e, #254488);
            line-height: 36px;
            border-radius: 5px;
            width: 108px;
            border: 1px solid #00236f;
            float: right;
        }

        #prevPage,
        #nextPage {
            padding: 2px 5px;
            font-size: 12px;
            color: #ffffff;
            background: linear-gradient(#2c539e, #254488);
            border-radius: 5px;
            border: 1px solid #00236f;
        }

        p {
            font-size: 18px !important;
        }
        .ft_container, .ft_rwrapper{
            width: 100% !important;
        }
        .bg-color-dark {
            /* background-color: #343a40 !important; */
            background-color: #343a40;
        }
    </style>
</head>


<body>
    <div id="processing"></div>
    <div class="container py-3">

        <!--<form>-->
        <!--<h2>Import Crop Data</h2>-->
        <div id='impoorted-file-info'>
            <div class="project">
                <h3>Import File</h3>
                <!--Link to download User Guide-->
                <a href="Resources/Files/User_Guide.md" download="User Guide.md">User Guide</a>
                &nbsp;&nbsp;&nbsp;&nbsp;
                <!--Link to download File Template-->
                <a href="Resources/Files/File_Template.csv" download="2016_cc_cotton_cc_spring_parking.csv">File
                    Template</a>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label>Select a file: </label>
                            <input type="file" accept=".csv" id="imported-file" class="form-control">
                            <small>(File name format: year_location_crop_type_season_sublocation. Ex: 2017_wl_tomato_cc_spring_south)</small>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label>Crop <span class="mandatory">*</span></label>
                        <input type="text" id="crop" disabled class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Type <span class="mandatory">*</span></label>
                        <input type="text" id="type" disabled class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Year <span class="mandatory">*</span></label>
                        <input type="text" id="year" disabled class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Season</label>
                        <input type="text" id="season" disabled class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Location <span class="mandatory">*</span></label>
                        <input type="text" id="location" disabled class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label>Sub-location</label>
                        <input type="text" id="sublocation" disabled class="form-control">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-12">
                        <input id="select" type='button' class='btnNew float-right' value='Select' onclick='Select(); return false;' disabled />
                    </div>
                </div>
            </div>
        </div>

        <div id='imported-field-info' style="display:none">
            <div class="project row" style="margin-top: 25px;">
                <div class="col-12">
                    <h3>Criteria and Values</h3>
                    <p>Please select the first value column</p>
                </div>
                <div class="col-12">
                    <div id="imported-field-wrapper" style="overflow-x:auto"></div>
                </div>
                <div class="col-12 mt-4">
                    <input id="import" type='button' class='btnNew float-right' value='Import' onclick='Import(); return false;' disabled />
                </div>
            </div>
        </div>
        <div id='imported-data-info' style="display:none">
            <div id='imported-data-info' class="project" style="margin-top: 25px;">
                <h3>Review Imported Data</h3>
                <div id="imported-list-wrapper"></div>
                <div id="page-control" style="width: 95%; margin: 20px auto; display: none; text-align: center">
                    <span>Row Per Page</span>
                    <select id="row-per-page" class="small">
                        <option value="20" selected>20</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="200">200</option>
                    </select>
                    <span class="label-small">Page</span>
                    <input type="text" id="page" class="tiny">
                    <span class="label-tiny">/</span>
                    <span class="label-tiny" id="page-num"></span>
                    <input type='button' id="prev" value='Prev' onclick='Prev(); return false;' style="margin: 7px 5px;" />
                    <input type='button' id="next" value='Next' onclick='Next(); return false;' style="margin: 7px 5px" />

                </div>
            </div>
        </div>
        <!--</form>-->
    </div>
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
        &subject=Requesting%20access%20to%20the%20import_crop_data%20tool
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
