<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href="dashboard.php"></a>

    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
            data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
            aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">

            <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard">
                <a class="nav-link" href="dashboard.php">
                    <i class="fa fa-fw fa-dashboard"></i>
                    <span class="nav-link-text">Dashboard</span>
                </a>
            </li>

            <!--UAS TOOLS-->
            <li class="nav-item" data-toggle="tooltip" data-placement="right" title="UAS Tools">
                <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseToolsPages"
                   data-parent="#exampleAccordion">
                    <!--                    <i class="fa fa-fw fa-file"></i>-->
                    <i class="fa fa-fw fa-wrench"></i>
                    <span class="nav-link-text">UAS Tools</span>
                </a>
                <ul class="sidenav-second-level collapse" id="collapseToolsPages">
                    <!--<li>
                      <a href="#">Login Page</a>
                    </li>-->
                    <li><a href="http://bhub.gdslab.org/uas_tools/uas_data_admin" target="iframe_a">Data
                            Administration</a></li>
                    <li><a href="http://bhub.gdslab.org/uas_tools/upload_raw" target="iframe_a">Upload Raw Data</a>
                    </li>
                    <li><a href="http://bhub.gdslab.org/uas_tools/upload_product" target="iframe_a">Upload
                            Product</a></li>
                    <li><a href="http://bhub.gdslab.org/uas_tools/visualization_generator/" target="iframe_a">Visualization
                            Generator</a></li>
                    <li><a href="http://bhub.gdslab.org/uas_tools/data_visualization/" target="iframe_a">Map
                            Viewer</a></li>
                    <li><a href="http://bhub.gdslab.org/uas_tools/import_crop_data/" target="iframe_a">Import Crop
                            Data</a></li>
                    <li><a href="http://bhub.gdslab.org/uas_tools/crop_analysis/" target="iframe_a">Crop Analysis</a>
                    <li><a href="http://bhub.gdslab.org/uas_tools/plot_boundary/V3/" target="iframe_a">Plot Boundary</a>
                    <li><a href="http://bhub.gdslab.org/uas_tools/plot_grid/" target="iframe_a">Plot Grid</a>
                        <!--                    <li><a href="http://bhub.gdslab.org/uas_tools/plot_boundary/" target="iframe_a">Plot Boundary</a>-->

                    </li>
                </ul>
            </li>

            <!-- <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Example Pages">
              <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseExamplePages" data-parent="#exampleAccordion">
                <i class="fa fa-fw fa-file"></i>
                <span class="nav-link-text">UAS Tools</span>
              </a>
              <ul class="sidenav-second-level collapse" id="collapseExamplePages">
                <li><a href="http://bhub.gdslab.org/uas_tools/upload_raw">Upload Raw Data</a></li>
                <li><a href="http://bhub.gdslab.org/uas_tools/upload_product">Upload Product</a></li>
                <li><a href="http://bhub.gdslab.org/uas_tools/uas_data_admin">Data Administration</a></li>
              </ul>
            </li> -->

            <!-- <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Charts">
             <a class="nav-link" href="#">
               <i class="fa fa-fw fa fa-copy"></i>
               <span class="nav-link-text">Pages</span>
             </a>
           </li>-->

            <!--<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Tables">
              <a class="nav-link" href="#">
                <i class="fa fa-fw fa-circle-o-notch"></i>
                <span class="nav-link-text">Categories</span>
              </a>
            </li>-->

            <?php
            //only visible to admin
            //            if (($_SESSION['user_role_id'] == 1)) {

            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $_VERIFY = $_SESSION['email'];

            // DB Connection
            $connect = mysqli_connect("localhost", "hub_admin", "UasHtp_Rocks^^7", "uas_projects");
            if (!$connect) {
                echo "Error: Unable to connect to MySQL." . PHP_EOL;
                echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
                exit;
            }

            // SELECT USING EMAIL TO GET THE role_id
            $sql = "select role_id from users_roles, users where  users_roles.user_id = users.user_id and email = '" . $_VERIFY . "'";// ORDER BY role_id ASC
            $result = mysqli_query($connect, $sql);
            //_log('select role_id: '.$sql);
            $row = mysqli_fetch_assoc($result);
            //echo $row["role_id"];
            //            if ($row["role_id"] != '1') {
            //                header('location:index.php?lmsg=true');
            //                exit;
            //            }

            if (($row["role_id"] == '1')) { ?>

                <!--<li class="nav-item" data-toggle="tooltip" data-placement="right" title="Components">
                  <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseComponents" data-parent="#exampleAccordion">
                    <i class="fa fa-fw fa-wrench"></i>
                    <span class="nav-link-text">Appearance</span>
                  </a>
                  <ul class="sidenav-second-level collapse" id="collapseComponents">
                    <li>
                      <a href="#">Themes</a>
                    </li>
                    <li>
                      <a href="#">Menus</a>
                    </li>
                  </ul>
                </li>-->

                <!--USER ADMINISTRATION-->
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="User Administration">
                    <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse"
                       href="#collapseUserAdministrationPages"
                       data-parent="#exampleAccordion">
                        <i class="fa fa-fw fa-user"></i>
                        <span class="nav-link-text">User Administration</span>
                    </a>
                    <ul class="sidenav-second-level collapse" id="collapseUserAdministrationPages">
                        <!--<li>
                          <a href="#">Login Page</a>
                        </li>-->
                        <!--                        <li><a href="http://bhub.gdslab.org/multi_users/add_user.php">Add User</a></li>-->
                        <!--                        <li><a href="http://bhub.gdslab.org/multi_users/modify_user.php">Modify User</a></li>-->
                        <!--                        <li><a href="http://bhub.gdslab.org/multi_users/modify_role.php">Modify Role</a></li>-->

                        <li><a href="http://bhub.gdslab.org/uas_tools/user_management/AddUser.php" target="iframe_a">Add
                                User</a></li>
                        <!--                        <li><a href="http://bhub.gdslab.org/multi_users/modify_user.php" target="iframe_a">Modify User</a></li>-->
                        <!--                        <li><a href="http://bhub.gdslab.org/uas_tools/user_management/ModifyUser.php" target="iframe_a">Modify User Roles</a></li>-->
                        <!--                        <li><a href="http://bhub.gdslab.org/multi_users/modify_role.php" target="iframe_a">Modify Role</a></li>-->
                        <li><a href="http://bhub.gdslab.org/uas_tools/user_management/user_table.php"
                               target="iframe_a">Modify User</a></li>
                        <li><a href="http://bhub.gdslab.org/uas_tools/user_management/ModifyRole.php"
                               target="iframe_a">Modify Role</a></li>
                    </ul>
                </li>


                <!--SYSTEM ADMINISTRATION-->
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="System Administration">
                    <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse"
                       href="#collapseSystemAdministrationPages"
                       data-parent="#exampleAccordion">
                        <i class="fa fa-fw fa-server"></i>
                        <span class="nav-link-text">System Administration</span>
                    </a>
                    <ul class="sidenav-second-level collapse" id="collapseSystemAdministrationPages">
                        <li><a href="http://bhub.gdslab.org/uas_tools/page_access/" target="iframe_a">Access
                                Management</a></li>
                        <li><a href="http://bhub.gdslab.org/uas_tools/system_management/" target="iframe_a">System
                                Management</a></li>
                    </ul>
                </li>
            <?php } ?>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="index.php?logout=true">
                    <i class="fa fa-fw fa-sign-out"></i>Logout
                </a>
            </li>
        </ul>
    </div>
</nav>
