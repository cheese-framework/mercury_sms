<?php

use App\Core\Helper;

date_default_timezone_set('Africa/Banjul');

include "../init.php";

// get request path
$path = str_replace("/sms/", "", $_SERVER['REQUEST_URI']);
$name = str_replace("/sms/", "", $_SERVER['SCRIPT_NAME']);

if ($name != "moodle/logout.php") {
    $_SESSION['moodle_redirect_path_mercury'] = $path;
} else {
    $_SESSION['moodle_redirect_path_mercury'] = "moodle";
}

if (!isset($_SESSION['student_id']) && !isset($_SESSION['school'])) {
    Helper::to("index.php");
}

if (isset($_GET['logout'])) {
    if ($_GET['logout'] == "true") {
        session_unset();
        session_destroy();
        Helper::to("index.php?logged_out=true");
    }
}

$moodle_userId = $_SESSION['student_id'];
$moodle_username = $_SESSION['student_username'];
$moodle_email = $_SESSION['student_email'];
$schoolId = $_SESSION['school'];
$s_id = $_SESSION['s_id'];
$class = $_SESSION['class'];

if (!Helper::isUsingOnlineAssessment($schoolId)) {
    Helper::to("../");
}

$page = (int) ($_GET['page'] ?? 0);

include "./classes/Assessment.php";

?>

<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Mercury - SMS | MOODLE</title>
    <!-- Favicon icon -->
    <!-- Custom CSS -->
    <!-- Custom CSS -->
    <link href="css/style.min.css" rel="stylesheet">
</head>

<body>
    <!-- ============================================================== -->
    <!-- Preloader - style you can find in spinners.css -->
    <!-- ============================================================== -->
    <!-- <div class="preloader">
        <div class="lds-ripple">
            <div class="lds-pos"></div>
            <div class="lds-pos"></div>
        </div>
    </div> -->
    <!-- ============================================================== -->
    <!-- Main wrapper - style you can find in pages.scss -->
    <!-- ============================================================== -->
    <div id="main-wrapper" data-layout="vertical" data-navbarbg="skin5" data-sidebartype="full" data-sidebar-position="absolute" data-header-position="absolute" data-boxed-layout="full">

        <header class="topbar" data-navbarbg="skin5">
            <nav class="navbar top-navbar navbar-expand-md navbar-dark">
                <div class="navbar-header" data-logobg="skin6">

                    <a class="nav-toggler waves-effect waves-light text-dark d-block d-md-none" href="javascript:void(0)"><i class="ti-menu ti-close"></i></a>
                </div>
                <div class="navbar-collapse collapse" id="navbarSupportedContent" data-navbarbg="skin5">
                    <ul class="navbar-nav ms-auto d-flex align-items-center">
                        <li>
                            <a class="profile-pic"><span class="text-white font-medium"><?= $moodle_username; ?></span></a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>