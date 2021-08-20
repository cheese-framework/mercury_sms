<?php

use App\Core\Helper;
use App\Database\Database;

date_default_timezone_set('Africa/Banjul');


// include initializer
include_once __DIR__ . "/../../init.php";

// set counter session
if (!isset($_SESSION['billcheckcount'])) {
    $_SESSION['billcheckcount'] = 0;
}

// get request path
$path = str_replace("/sms/", "", $_SERVER['REQUEST_URI']);
$name = str_replace("/sms/", "", $_SERVER['SCRIPT_NAME']);

if ($name != "myschool/logout.php") {
    $_SESSION['redirect_path_mercury'] = $path;
} else {
    $_SESSION['redirect_path_mercury'] = "myschool";
}


if (!isset($_SESSION['school'])) {
    Helper::to("../login.php");
}

Database::getInstance();

$sms_userId = $_SESSION['sms_userid'];
$sms_username = $_SESSION['sms_username'];
$sms_role = $_SESSION['sms_role'];
$pic = $_SESSION['pic'];
$sms_email = $_SESSION['sms_email'];
$schoolId = $_SESSION['school'];
if ($sms_role == "Headmaster") {
    $ADMIN = "Headmaster";
} else if ($sms_role == "Super-Admin") {
    $ADMIN = "Super-Admin";
} else {
    $ADMIN = "Administrator";
}
$TEACHER = "Teacher";

$page = (int) ($_GET['page'] ?? 0);

new Helper($schoolId);

$name = Helper::getSchoolName($schoolId);

$schoolBadge = Helper::getBagde($schoolId);

if (Helper::isActivated($schoolId)) {
    if ($_SESSION['billcheckcount'] < 1) {
        $payment = Helper::getPaymentId($schoolId);
        if ($payment) {
            $paymentId = $payment->paymentId;
            // check if the payment is valid
            $paidDate = Helper::getPaymentDate($paymentId);
            $isFirstMonth = Helper::isFirstMonth($schoolId);
            if (Helper::isValidDate($paidDate, $isFirstMonth, $payment)) {
                // increment billcheckcount
                $_SESSION['billcheckcount'] += 1;
                // move on
            } else {
                // deactivate account
                Helper::deactivateSchool($schoolId);
            }
        } else {
            // deactivate account
            Helper::deactivateSchool($schoolId);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <title> <?= $name; ?> - My School</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="assets/vendors/mdi/css/materialdesignicons.min.css">
    <link rel="stylesheet" href="assets/vendors/flag-icon-css/css/flag-icon.min.css">
    <!-- <link rel="stylesheet" href="assets/vendors/css/vendor.bundle.base.css"> -->
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <!-- <link rel="stylesheet" href="assets/vendors/jquery-bar-rating/css-stars.css" /> -->
    <link rel="stylesheet" href="assets/vendors/font-awesome/css/font-awesome.min.css" />
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <!-- endinject -->
    <!-- Layout styles -->
    <link rel="stylesheet" href="assets/css/demo_1/style.css" />
    <link rel="stylesheet" href="assets/css/Chart.css" />
    <link rel="stylesheet" href="assets/custom/dataTables.bootstrap4.min.css">
    <!-- <link rel="stylesheet" href="assets/custom/dt/datatables.min.css"> -->
    <!-- End layout styles -->
    <!-- <link rel="shortcut icon" href="assets/images/favicon.png" /> -->
    <style>
        .sticky-top {
            position: sticky;
            top: 0;
        }

        .make-blur {
            filter: blur(3px);
            -webkit-filter: blur(3px);
        }

        .make-scroll {
            max-height: 400px;
            overflow-y: auto;
            overflow-x: auto;
        }
    </style>
</head>

<body class="body">