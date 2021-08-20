<?php

use App\Auth\Auth;
use App\Core\Helper;

include "./init.php";

if (isset($_GET['token']) && $_GET['token'] != "") {
    $token = $_GET['token'];
    if (Auth::getToken($token)) {
        // verify user
        Auth::verifyUser($token);
        Helper::to("login.php");
    } else {
        Helper::to("createschool.php");
    }
} else {
    Helper::to("createschool.php");
}
