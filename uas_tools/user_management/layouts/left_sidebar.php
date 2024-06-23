<?php
// File containing System Variables
//define("LOCAL_PATH_ROOT", $_SERVER["DOCUMENT_ROOT"]);
require LOCAL_PATH_ROOT . '/uas_tools/system_management/centralized_management.php';
?>

<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href="dashboard.php"><img src="Resources/Images/2.png" height="30" alt=""></a>

    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
            data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
            aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        <span class=" navbar-toggler-icon navbar-toggler-icon2"></span>
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav" id="exampleAccordion">
            <li class="nav-item" title="Dashboard">
                <a class="nav-link" href="dashboard.php">
                    <i class="fa fa-fw fa-dashboard"></i>
                    <span class="nav-link-text">Dashboard</span>
                </a>
            </li>

            <!--ESSENTIAL TOOLS-->
            <li class="nav-item dropdown" title="Essential Tools">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-wrench fa-fw"></i>
                    <span class="nav-link-text">Essential Tools</span>
                </a>
                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                     <a class="dropdown-item" href=<?= $header_location . "/uas_tools/uas_data_admin" ?> target="iframe_a">Data Administration</a>
                     <a class="dropdown-item" href=<?= $header_location . "/uas_tools/visualization_generator/" ?> target="iframe_a">Visualization Generator</a>
                     <a class="dropdown-item" href=<?= $header_location . "/uas_tools/data_visualization/" ?> target="iframe_a">Map Viewer</a>
                     <a class="dropdown-item" href=<?= $header_location . "/uas_tools/upload_product/" ?> target="iframe_a">Upload Product</a>
                     <a class="dropdown-item" href=<?= $header_location . "/uas_tools/canopy_attribute_generator/" ?> target="iframe_a">RGB Attributes Generator</a>
                     <a class="dropdown-item" href=<?= $header_location . "/uas_tools/multispectral_attribute_generator/" ?> target="iframe_a">Multispectral Attributes Generator</a>
                    </div>
            </li>

            <!--ADDITIONAL TOOLS-->
            <li class="nav-item dropdown" title="Additional Tools">
              <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="fa fa-gavel fa-fw"></i>
                  <span class="nav-link-text">Additional Tools</span>
              </a>
                   <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href=<?= $header_location . "/uas_tools/upload_geojson2" ?> target="iframe_a">Upload Vector Data</a>
                  </div>
            </li>

            <?php
            // Only visible to admin
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            // DB Connection
            $connect = mysqli_connect("localhost", "hub_admin", "PurdueGdsl!@2w", "uas_projects");
            if (!$connect) {
                echo "Error: Unable to connect to MySQL." . PHP_EOL;
                echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
                exit;
            }

            if (isset($_SESSION['email'])) {
                $_VERIFY = $_SESSION['email'];

                // SELECT USING EMAIL TO GET THE role_id
                $sql = "select role_id from users_roles, users where  users_roles.user_id = users.user_id and email = '" . $_VERIFY . "'";// ORDER BY role_id ASC
                $result = mysqli_query($connect, $sql);
                //_log('select role_id: '.$sql);
                $row = mysqli_fetch_assoc($result);

                // If user is Admin
                if (($row["role_id"] == '1')) {
            ?>

                    <!--USER ADMINISTRATION-->
                    <li class="nav-item dropdown" title="User Administration">

                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-fw fa-user"></i>
                            <span class="nav-link-text">User Administration</span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href=<?= $header_location . "/uas_tools/user_management/AddUser.php" ?> target="iframe_a">Add User</a>
                            <a class="dropdown-item" href=<?= $header_location . "/uas_tools/user_management/user_table.php" ?> target="iframe_a">Modify User</a>
                            <a class="dropdown-item" href=<?= $header_location . "/uas_tools/user_management/ModifyRole.php" ?> target="iframe_a">Modify Role</a>
                        </div>
                    </li>


                    <!--SYSTEM ADMINISTRATION-->
                    <li class="nav-item dropdown" title="System Administration">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-fw fa-server"></i>
                            <span class="nav-link-text">System Administration</span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <a class="dropdown-item" href=<?= $header_location . "/uas_tools/page_access/" ?> target="iframe_a">Access Management</a>
                            <a class="dropdown-item" href=<?= $header_location . "/uas_tools/system_management/" ?> target="iframe_a">System Management</a>
                        </div>
                    </li>
                <?php }
            } ?>
        </ul>


        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <!-- <a class="nav-link log" href="index.php?logout=true"> -->
                <a class="nav-link log" href=<?= $header_location . "/login.php" ?>>
                    <i class="fa fa-fw fa-sign-out"></i>Logout
                </a>
            </li>
        </ul>
    </div>
</nav>
