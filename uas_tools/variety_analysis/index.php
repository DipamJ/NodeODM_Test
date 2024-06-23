<?php
$enable_download = false;
$image_available = false;
$error = false;

// print($_POST['check_submit']);

if (isset($_POST['check_submit'])) {
    // echo 'here';
    // die();
    // print($_POST['check_submit']);

    $target_dir = "sample_csvs/";
    $out_suffix = uniqid();
    $file_name = $out_suffix . '_' . basename($_FILES["variety_file"]["name"]);
    $target_file = $target_dir . $file_name;

    move_uploaded_file($_FILES["variety_file"]["tmp_name"], $target_file);

    $command = escapeshellcmd("python3 get_varieties.py $target_file");

    // echo $command . '<br>';

    $result = shell_exec($command);

    // echo $result;

    // die();

    // Returns the value encoded in json in appropriate PHP type
    $varieties = json_decode($result);
}
if (isset($_POST['generate_results'])) {
    $task = $_POST['selectTasks'];
    $v1 = $_POST['variety1'];
    $v2 = $_POST['variety2'];
    $v3 = $_POST['variety3'];
    $v4 = $_POST['variety4'];
    $dap = date("Ymd", strtotime($_POST['dap']));
    if (isset($_POST['chart_type'])) {
        $chart = $_POST['chart_type'];
    } else {
  	$chart = 'bar';
    }
    $target_file = $_POST['target_file'];
    $suffix = $_POST['suffix'];
    $path = explode('/', $target_file);
    $tmp = explode(".", $path[1]);
    $out_file = $tmp[0] . "_results";

    // echo $out_file . '<br>';

    $command = "python3 generate.py \"$target_file\" \"$dap\" \"$task\" \"$chart\" \"$v1\" \"$v2\" \"$v3\" \"$v4\" \"$out_file\"";

    $result = shell_exec($command);

    $zip = new ZipArchive();
    $zip_name = "results/" . $out_file . ".zip";
    if ($zip->open($zip_name, ZipArchive::CREATE) !== TRUE) {
        exit("cannot open <$zip_name>\n");
    }
   if (file_exists("results/" . $out_file . ".csv")) {
        $enable_download = true;
        $zip->addFile("results/" . $out_file . ".csv");
        if (file_exists("results/" . $out_file . ".png")) {
            $zip->addFile("results/" . $out_file . ".png");
        }
    } else {
        $error = true;
    }

    $zip->close();

    if (file_exists("results/" . $out_file . ".csv")) {
        unlink("results/" . $out_file . ".csv");
    }
//    unlink("results/" . $out_file . ".png");

    // echo $command . "<br>";

    // var_dump($result);

    // echo $task . '<br>' . $v1 . '<br>' . $v2 . '<br>' . $v3 . '<br>' . $v4 . '<br>' . $dap . '<br>' . $chart;
    // die('hello');
}


//phpinfo();
// File containing System Variables
// define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
// require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';

// // To check if User has the role required to access the page
// //require_once("Resources/PHP/SetDBConnection.php");
// require_once("../../resources/database/SetDBConnection.php");
// //require_once("../system_management/centralized.php");

// $mysqli = SetDBConnection();

// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// }

// $userName = $_SESSION["email"] ?? '';
// $userapproved = $_SESSION['admin_approved'] ?? '';

// // SELECT the role_name for each users_roles for the logged on user
// // ? is a place holder for our parameter `user_id`
// $sql = "
//     SELECT r.role_name FROM users_roles AS ur
//         JOIN roles AS r ON r.role_id = ur.role_id
//     WHERE ur.user_id = ?
// ";

// $query = $mysqli->prepare($sql);                // Prepare the query
// $query->bind_param("i", $_SESSION["user_id"]);  // Bind the parameter (wherever you store user_id in $_SESSION)
// $query->execute();                              // Run the query
// $query->store_result();                         // Store the result
// $query->bind_result($role_name);                // Bind the result to a variable

// $user_role_array = [];                          // Initialise the user roles array
// while ($query->fetch()) {                         // Loop returned records
//     $user_role_array[] = $role_name;            // Add user role to array
// }

// if (mysqli_connect_errno()) {
//     echo "Failed to connect to database server: " . mysqli_connect_error();
// } else {
//     if (!$user_role_array || $userapproved == "Disapproved") {
//         echo '<script>alert("You do not have permission to access this page. You will be logout now.")</script>';
//         echo "<html>";
//         echo "<script>";
//         echo "window.top.open('/index.php?logout=true')"; //$_SERVER['HTTP_HOST'] . '/index.php?logout=true'
//         echo "</script>";
//         echo "</html>";
//     } else {
//         $pageName = basename(__DIR__);
//         if ($pageName == "V2") {
//             $pageName = basename(realpath(__DIR__ . "/.."));
//         }

//         $sql1 = "SELECT * FROM page_access WHERE Page = '$pageName'";
//         $allowedGroups = array();
//         if ($result1 = mysqli_query($mysqli, $sql1)) {
//             if ($row1 = mysqli_fetch_assoc($result1)) {
//                 $allowedGroups = explode(";", $row1["Page_Groups"]);
//                 $accessGroupsStr = $row1["Page_Groups"];
//             }
//         }

//         $intersect = array_intersect($user_role_array, $allowedGroups);

//         if (sizeof($intersect) > 0) {// if match found
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name=”viewport” content=”width=device-width, initial-scale=1″>
    <title>Variety Analysis</title>
    <!-- Styles -->

    <link rel="stylesheet" type="text/css" href="/uas_tools/upload_product/Resources/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="Resources/style.css">
    <script type="text/javascript" src="Resources/JS/jquery.min.js"></script>
    <script src="Resources/JS/JqueryUI/jquery-ui.min.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/JS/JqueryUI/jquery-ui.css">
    <script type="text/javascript" src="Resources/JS/main.js"></script>
    <script src="Resources/JS/FixedTable/fixed_table_rc.js"></script>
    <link rel="stylesheet" type="text/css" href="Resources/JS/FixedTable/fixed_table_rc.css">
    <script src="Resources/JS/d3/d3.min.js" charset="utf-8"></script>
    <script src="Resources/JS/d3/d3-tip.js" charset="utf-8"></script>
    <script src="Resources/JS/canvg/rgbcolor.js"></script>
    <script src="Resources/JS/canvg/StackBlur.js"></script>
    <script src="Resources/JS/canvg/canvg.js"></script>
    <style>
        .project {
            margin: 0px 0px 0px 0px;
            padding: 25px 35px;
            border-radius: 15px;
            background: #f6f7f9;
        }

        input.btnNew {
            padding: 0;
            font-weight: 500;
            font-size: 17px;
            color: #ffffff;
            background: linear-gradient(#2c539e, #254488);
            line-height: 36px;
            border-radius: 5px;
            /* width: 108px; */
            width: 112px;
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

        #select-data-values button,
        #imported-set-wrapper button {
            padding: 6px;
            background: #d9534f;
            border: none;
            /*font-weight: 500;
            font-size: 12px;
            color: #ffffff;
            background: linear-gradient(#2c539e, #254488);
            padding: 2px 5px;
            border-radius: 5px;
            border: 1px solid #00236f;*/
        }

        .ft_container,
        .ft_rwrapper,
        .ft_scroller {
            width: 100% !important;
        }

        .ft_container {
            height: auto !important;
        }

        p {
            font-size: 18px !important;
        }

        table thead tr th {
            border: none !important;
        }

    </style>
</head>


<body>
    <div id="processing"></div>
    <div class="container py-3">
        <!--<form>-->
        <div id="select-data-set">
            <div class="project">
                <div class="row">
                    <div class="form-group col-md-1">
                    </div>
                    <form action="./" id="file_form" name="file_form" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="check_submit">
                        <div class="form-group col-md-12">
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
                                        <input <?php if (isset($_POST["check_submit"]) || isset($_POST["generate_results"])) {
                                                    echo 'disabled';
                                                } ?> name="variety_file" type="file" accept=".csv" id="imported-file" class="form-control">
                                        <small>(File name format: year_location_crop_type_season_sublocation. Ex: 2017_wl_tomato_cc_spring_south)</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Select Tasks -->
                <form action="./" method="POST">
                    <div class="row">
                        <div class="form-group col-md-1">
                        </div>
                        <div class="form-group col-md-4 my-auto">
                            <h2 class="form-control "> Select Tasks: </h2>
                        </div>
                        <div class="form-group col-md-6">
                            <div class="form-check">
                                <input <?php if (!isset($_POST["check_submit"])) {
                                            echo 'disabled';
                                        } ?> class="form-check-input" type="radio" value="Compare Varieties" required name="selectTasks" id="compareVarities">
                                <label class="form-check-label" for="compareVarities">
                                    Compare Varities
                                </label>
                            </div>
                            <div class="form-check">
                                <input <?php if (!isset($_POST["check_submit"])) {
                                            echo 'disabled';
                                        } ?> class="form-check-input" type="radio" value="Generate Growth Rate" required name="selectTasks" id="generateGrowthModel">
                                <label class="form-check-label" for="generateGrowthModel">
                                    Generate Growth Rate
                                </label>
                            </div>
                            <div class="form-check">
                                <input <?php if (!isset($_POST["check_submit"])) {
                                            echo 'disabled';
                                        } ?> class="form-check-input" type="radio" value="Generate Tabular Data - Row" required name="selectTasks" id="generateTableRow">
                                <label class="form-check-label" for="generateTableRow">
                                    Generate Tabular Data - Row
                                </label>
                            </div>
                            <div class="form-check">
                                <input <?php if (!isset($_POST["check_submit"])) {
                                            echo 'disabled';
                                        } ?> class="form-check-input" type="radio" value="Generate Tabular Data - Rep" required name="selectTasks" id="generateTableRep">
                                <label class="form-check-label" for="generateTableRep">
                                    Generate Tabular Data - Rep
                                </label>
                            </div>
                        </div>
                    </div>
                    <br>
                    <!-- Select Varities -->
                    <input type="hidden" name="target_file" value="<?php echo $target_file; ?>">
                    <input type="hidden" name="suffix" value="<?php echo $out_suffix; ?>">
                    <div class="row">
                        <div class="form-group col-md-1">
                        </div>
                        <div class="form-group col-md-4 my-auto">
                            <h2 class="form-control "> Select Variety(es): </h2>
                        </div>
                        <div class="form-group col-md-6">
                            <div class="form-check">
                                <select <?php if (!isset($_POST["check_submit"])) {
                                            echo 'disabled';
                                        } ?> class="form-control" required name="variety1" id="">
                                    <?php
                                    foreach ($varieties as $variety) { ?>
                                        <option value="<?php echo $variety; ?>"><?php echo $variety; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-check">
                                <select <?php if (!isset($_POST["check_submit"])) {
                                            echo 'disabled';
                                        } ?> class="form-control" required name="variety2" id="">
                                    <?php
                                    foreach ($varieties as $variety) { ?>
                                        <option value="<?php echo $variety; ?>"><?php echo $variety; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-check">
                                <select <?php if (!isset($_POST["check_submit"])) {
                                            echo 'disabled';
                                        } ?> class="form-control" required name="variety3" id="">
                                    <?php
                                    foreach ($varieties as $variety) { ?>
                                        <option value="<?php echo $variety; ?>"><?php echo $variety; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-check">
                                <select <?php if (!isset($_POST["check_submit"])) {
                                            echo 'disabled';
                                        } ?> class="form-control" required name="variety4" id="">
                                    <?php
                                    foreach ($varieties as $variety) { ?>
                                        <option value="<?php echo $variety; ?>"><?php echo $variety; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <br>
                    <!-- Set Planting Date -->
                    <div class="row">
                        <div class="form-group col-md-1">
                        </div>
                        <div class="form-group col-md-4">
                            <h2 class="form-control "> Select Planting Date: </h2>
                        </div>
                        <div class="form-group col-md-6">
                            <input <?php if (!isset($_POST["check_submit"])) {
                                        echo 'disabled';
                                    } ?> name="dap" type="date" required class="form-control">
                        </div>
                    </div>
                    <br>
                    <!-- Select Chart Type -->
                    <div class="row">
                        <div class="form-group col-md-1">
                        </div>
                        <div class="form-group col-md-4 my-auto">
                            <h2 class="form-control "> Select Chart Type: </h2>
                        </div>
                        <div class="form-group col-md-6">
                            <div class="form-check">
                                <input <?php if (!isset($_POST["check_submit"])) {
                                            echo 'disabled';
                                        } ?> class="form-check-input" type="radio" name="chart_type" value="bar" id="bar">
                                <label class="form-check-label" for="bar">
                                    Bar Plot
                                </label>
                            </div>
                            <div class="form-check">
                                <input <?php if (!isset($_POST["check_submit"])) {
                                            echo 'disabled';
                                        } ?> class="form-check-input" type="radio" name="chart_type" value="scatter" id="scatter">
                                <label class="form-check-label" for="scatter">
                                    Scatter Plot
                                </label>
                            </div>
                        </div>
                    </div>
                    <br>
                    <!-- Buttons -->
                    <div id="move_to_result" class="row">
                        <div class="form-group col-md-1">
                        </div>
                        <div class="form-group col-md-5">
                            <button <?php if (!isset($_POST["check_submit"])) {
                                        echo 'disabled';
                                    } ?> name="generate_results" type="submit" class="btn btn-primary btn-block">Generate Results</button>
                        </div>
                        <div class="form-group col-md-5">
                            <?php if ($enable_download) { ?>
                                <a href="<?php echo $zip_name; ?>" <?php if (!$enable_download) {
                                                                        echo 'disabled';
                                                                    } ?> download class="btn btn-primary btn-block">Download Generated Files</a>
                            <?php } else { ?>
                                <button disabled class="btn btn-primary btn-block">Download Generated Files</button>
                            <?php } ?>
                        </div>

                    </div>

                    <div class="row">
                        <div class="form-group w-25 mx-auto">
                            <a href="./index.php#move_to_result" class="btn btn-primary btn-block ">Generate on new data</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="canvas-list">
            <?php
            if ($enable_download) {
                if (file_exists("results/" . $out_file . ".png")) {
                ?>
                    <img src="results/<?php echo $out_file . ".png"; ?>" alt="">
                <?php } else { ?>
                    <h1 class="text-center">No preview available. Please Download the files.</h1>
                <?php } ?>
            <?php
            }
            if ($error) { ?>
                <h1 class="text-center">Unexpected Error. Please try again by clicking 'Generate on new data'.</h1>
            <?php } ?>
        </div>
    </div>
    <script>
        $(function() {
            $("#imported-file").change(function() {
                // var path = $("#imported-file").val();
                // console.log(path)
                $("#file_form").submit()
            });
        });
    </script>
</body>

</html>

<?php
// } else {
//     $memberOf = (implode("; ", $user_role_array));
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?php echo $pageName; ?></title>
</head>

<body>
    <!-- </br>
    <p>You do not currently have permission to access this tool.</p>
    <p>Please contact admin at
        <a href="mailto:<?= $admin_email ?>?
        &subject=Requesting%20access%20to%20the%20crop_analysis%20tool
        &body=Hi,%0D%0A%0D%0AThis%20is%20<?= $admin_email ?>.%20Please%20provide%20me%20access%20to%20the%20tool.">
            <?= $admin_email ?></a>
        to request access to this tool.</p> -->
</body>

</html>
<?php
//         }
//     }
// }
?>
