<?php

use App\Core\Helper;


include_once './includes/header.php';

session_unset();
session_destroy();
setcookie('PHPSESSID', 0, time() - 3600);

Helper::to("../login.php");
