<?php
// ERRORS
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$userapproved = $_SESSION['admin_approved'];
if(!isset($_SESSION['user_id'],$_SESSION['user_role_id'])|| $userapproved != 1)
{
    header('location:index.php?lmsg=true');
    exit;
}

require_once('inc/config.php');
require_once('layouts/header.php');
require_once('layouts/left_sidebar.php');
?>

<div class="content-wrapper">
    <div class="container-fluid">
        <!-- ORDERED LIST OF LINKS -->
        <!-- Breadcrumbs-->
<!--        <ol class="breadcrumb">-->
<!--            <li class="breadcrumb-item">-->
<!--                <a href="#">Dashboard</a>-->
<!--            </li>-->
<!--        </ol>-->
        <h1>Welcome to Dashboard</h1>
        <hr>
        Welcome <strong><?php echo $_SESSION['first_name'];
                echo ' ';
                echo $_SESSION['last_name'];?></strong>

        <p>You are login as <strong><?php echo getUserAccessRoleByID($_SESSION['user_role_id']); ?></strong></p>

<!--        <iframe src="demo_iframe.htm" style="border:none;" name="iframe_a" height="950px" width="90%" title="Iframe Example"></iframe>-->
            <iframe style="border:none;" name="iframe_a" height="900px" width="90%" title="User Administration"></iframe>
<!--        <p><a href="http://basfhub.gdslab.org/multi_users/add_user.php" target="iframe_a">Add new user</a></p>-->


        <!--<ul>
            <li><strong>John Doe</strong> has <strong>Administrator</strong> rights so all the left bar items are visible to him</li>
            <li><strong>Ahsan</strong> has <strong>Uploader</strong> rights and he doesn't have access to Settings</li>
            <li><strong>Sarah</strong> has <strong>Author</strong> rights and she can't have access to Appearance, Components and Settings</li>
            <li><strong>Sarah</strong> has <strong>viewer</strong> rights and she has only access to view Posts</li>
        </ul>-->

        <div style="height: 1000px;"></div>
    </div>
    <!-- /.container-fluid-->

    <?php require_once('layouts/footer.php'); ?>
