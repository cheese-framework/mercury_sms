<?php

use App\Core\Helper;

date_default_timezone_set('Africa/Banjul');

include "../init.php";

// get request path
$path = str_replace("/sms/", "", $_SERVER['REQUEST_URI']);
$name = str_replace("/sms/", "", $_SERVER['SCRIPT_NAME']);

if ($name != "workshop/logout.php") {
    $_SESSION['workshop_redirect_path_mercury'] = $path;
} else {
    $_SESSION['workshop_redirect_path_mercury'] = "workshop";
}

if (!isset($_SESSION['sms_userid']) && !isset($_SESSION['school'])) {
    Helper::to("index.php");
}

$sms_userId = $_SESSION['sms_userid'];
$sms_username = $_SESSION['sms_username'];
$sms_role = $_SESSION['sms_role'];
$pic = $_SESSION['pic'];
$sms_email = $_SESSION['sms_email'];
$schoolId = $_SESSION['school'];

$gen = Helper::getStaffRecord($sms_userId);
$myTitle = "";
if ($gen->gender == NULL) {
    $myTitle = "Mr/Ms.";
} else if ($gen->gender == "M") {
    $myTitle = "Mr.";
} else {
    $myTitle = "Ms.";
}

if ($sms_role != "Teacher") {
    Helper::to("../myschool");
}

if (!Helper::isUsingOnlineAssessment($schoolId)) {
    Helper::to("../myschool");
}

$page = (int) ($_GET['page'] ?? 0);

?>

<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Mercury - SMS | MY WORKSHOP</title>
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
                            <a class="profile-pic">
                                <img src="../myschool/assets/profile/<?= $pic; ?>" alt="user-img" width="36" class="img-circle"><span class="text-white font-medium"><?= $sms_username; ?></span></a>
                        </li>
                    </ul>
                </div>
            </nav>
        </header>