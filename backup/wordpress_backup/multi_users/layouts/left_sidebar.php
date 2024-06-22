<?php
// If session hasn't been started, start it
//if (session_status() == PHP_SESSION_NONE) {
session_start();
//}
//echo $_SESSION['header_location'];
?>


<!-- Navigation-->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top" id="mainNav">
    <a class="navbar-brand" href="../dashboard.php"></a>

    <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
            data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false"
            aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarResponsive">
        <ul class="navbar-nav navbar-sidenav" id="exampleAccordion">

            <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Dashboard">
                <a class="nav-link" href="../dashboard.php">
                    <i class="fa fa-fw fa-dashboard"></i>
                    <span class="nav-link-text">Dashboard</span>
                </a>
            </li>

            <li class="nav-item" data-toggle="tooltip" data-placement="right" title="UAS Tools">
                <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseToolsPages"
                   data-parent="#exampleAccordion">
                    <i class="fa fa-fw fa-file"></i>
                    <span class="nav-link-text">UAS Tools</span>
                </a>
                <ul class="sidenav-second-level collapse" id="collapseToolsPages">
                    <!--<li>
                      <a href="#">Login Page</a>
                    </li>-->
                    <!--                    <li><a href="http://basfhub.gdslab.org/uas_tools/uas_data_adminsss" target="iframe_a">Data Administration</a></li>-->
                    <!--                    <li><a href="http://basfhub.gdslab.org/uas_tools/upload_raw" target="iframe_a">Upload Raw Data</a></li>-->
                    <!--                    <li><a href="http://basfhub.gdslab.org/uas_tools/upload_product" target="iframe_a">Upload Product</a></li>-->
                    <!--                    <li><a href="http://basfhub.gdslab.org/uas_tools/visualization_generator/" target="iframe_a">Visualization Generator</a></li>-->
                    <!--                    <li><a href="http://basfhub.gdslab.org/uas_tools/data_visualization/" target="iframe_a">Data Visualization</a></li>-->

                    <li><a href="http://bhub.gdslab.org/uas_tools/uas_data_admin"
                           target="iframe_a">Data Administration</a></li>
                    <li><a href="http://bhub.gdslab.org/uas_tools/upload_raw" target="iframe_a">Upload Raw Data</a></li>
                    <li><a href="http://bhub.gdslab.org/uas_tools/upload_product" target="iframe_a">Upload Product</a>
                    </li>
                    <li><a href="http://bhub.gdslab.org/uas_tools/visualization_generator/" target="iframe_a">Visualization
                            Generator</a></li>
                    <li><a href="http://bhub.gdslab.org/uas_tools/data_visualization/" target="iframe_a">Data
                            Visualization</a></li>

                    <!--                    <li><a href="-->
                    <? //= $_SESSION['header_location'] ?><!--uas_tools/uas_data_adminsss" target="iframe_a">Data-->
                    <!--                            Administration</a></li>-->
                    <!--                    <li><a href="-->
                    <? //= $_SESSION['header_location'] ?><!--uas_tools/upload_raw" target="iframe_a">Upload Raw-->
                    <!--                            Data</a>-->
                    <!--                    </li>-->
                    <!--                    <li><a href="-->
                    <? //= $_SESSION['header_location'] ?><!--uas_tools/upload_product" target="iframe_a">Upload-->
                    <!--                            Product</a></li>-->
                    <!--                    <li><a href="-->
                    <? //= $_SESSION['header_location'] ?><!--uas_tools/visualization_generator/"-->
                    <!--                           target="iframe_a">Visualization-->
                    <!--                            Generator</a></li>-->
                    <!--                    <li><a href="-->
                    <? //= $_SESSION['header_location'] ?><!--uas_tools/data_visualization/" target="iframe_a">Data-->
                    <!--                            Visualization</a></li>-->

                </ul>
            </li>

            <!-- <li class="nav-item" data-toggle="tooltip" data-placement="right" title="Example Pages">
              <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseExamplePages" data-parent="#exampleAccordion">
                <i class="fa fa-fw fa-file"></i>
                <span class="nav-link-text">UAS Tools</span>
              </a>
              <ul class="sidenav-second-level collapse" id="collapseExamplePages">
                <li><a href="http://basfhub.gdslab.org/uas_tools/upload_raw">Upload Raw Data</a></li>
                <li><a href="http://basfhub.gdslab.org/uas_tools/upload_product">Upload Product</a></li>
                <li><a href="http://basfhub.gdslab.org/uas_tools/uas_data_adminsss">Data Administration</a></li>
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
            if (($_SESSION['user_role_id'] == 1)) { ?>

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
                <li class="nav-item" data-toggle="tooltip" data-placement="right" title="User Administration">
                    <a class="nav-link nav-link-collapse collapsed" data-toggle="collapse" href="#collapseExamplePages"
                       data-parent="#exampleAccordion">
                        <i class="fa fa-fw fa-file"></i>
                        <span class="nav-link-text">User Administration</span>
                    </a>
                    <ul class="sidenav-second-level collapse" id="collapseExamplePages">
                        <!--<li>
                          <a href="#">Login Page</a>
                        </li>-->
                        <!--                        <li><a href="http://basfhub.gdslab.org/multi_users/add_user.php">Add User</a></li>-->
                        <!--                        <li><a href="http://basfhub.gdslab.org/multi_users/modify_user.php">Modify User</a></li>-->
                        <!--                        <li><a href="http://basfhub.gdslab.org/multi_users/modify_role.php">Modify Role</a></li>-->

                        <!--                        <li><a href="http://bhub.gdslab.org/multi_users/add_user.php" target="iframe_a">Add User</a>-->
                        <!--                        <li><a href="-->
                        <? //= $_SESSION['header_location'] ?><!--multi_users/add_user.php" target="iframe_a">Add-->
                        <!--                                User</a>-->
                        <!--                        </li>-->

                        <!--                        <li><a href="http://basfhub.gdslab.org/multi_users/modify_user.php" target="iframe_a">Modify User</a></li>-->
                        <li><a href="http://bhub.gdslab.org/multi_users/user_table.php" target="iframe_a">Modify
                                User</a></li>
                        <!--                        <li><a href="-->
                        <? //= $_SESSION['header_location'] ?><!--multi_users/user_table.php" target="iframe_a">Modify-->
                        <!--                                User</a></li>-->

                        <!--                        <li><a href="http://basfhub.gdslab.org/multi_users/modify_role.php" target="iframe_a">Modify Role</a></li>-->
                        <li><a href="http://bhub.gdslab.org/multi_users/role_table.php" target="iframe_a">Modify
                                Role</a></li>
                        <!--                        <li><a href="-->
                        <? //= $_SESSION['header_location'] ?><!--multi_users/role_table.php" target="iframe_a">Modify-->
                        <!--                                Role</a></li>-->

                    </ul>
                </li>
            <?php } ?>
        </ul>

        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="http://bhub.gdslab.org/index.php?logout=true">
                    <i class="fa fa-fw fa-sign-out"></i>Logout
                </a>
            </li>
        </ul>
    </div>
</nav>
