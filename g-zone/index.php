<?php

use App\Core\Helper;
use App\Notifiable\Components\Components;
use App\School\SMSParent;

include_once "../init.php";

// redirect to dashboard if session persist
if (isset($_SESSION['g-zone-id'])) {
    Helper::to("./dashboard.php");
}

$error = [];
$email = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['username'] ?? "";
    $password = $_POST['password'] ?? "";

    if (!Helper::isEmpty($email, $password)) {
        $parent = SMSParent::login($email, $password);
        if ($parent) {
            $parent = json_encode($parent);
            $data = json_decode($parent, true);
            $_SESSION['g-zone-id'] = $data['id'];
            $_SESSION['g-zone-name'] = $data['name'];
            $_SESSION['g-zone-email'] = $data['email'];
            $_SESSION['g-zone-school'] = $data['school'];
            $_SESSION['g-zone-phone'] = $data['phone'];
            Helper::to("./dashboard.php");
        } else {
            $error[] = "Invalid credentials";
        }
    } else {
        $error[] = "All fields are required";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mercury - SMS |
        G-ZONE
    </title>
    <link rel="stylesheet" href="../myschool/assets/css/style.css">
</head>

<body>
    <div class="container">
        <div class="col-lg-8 my-5 mx-auto">
            <?php
            if (!empty($error)) {
                echo "<div class='alert alert-danger col-lg-12 text-center' id='msg'>";
                foreach ($error as $e) {
                    echo $e . "<br>";
                }
                echo "</div>";
            }

            ?>
            <div class="card">
                <div class="card-body">
                    <?= Components::header("Login", "h2", "center"); ?>
                    <form action="" method="post" autocomplete="off">
                        <div class="form-group">
                            <label for="username">E-mail</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="E-mail Address">
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                        </div>

                        <div class="form-group text-center">
                            <input type="submit" value="Login" class="btn btn-primary">
                        </div>
                    </form>
                    <?= Components::body("<small><i>Powered by QHITECH</i></small>", true, "center") ?>
                </div>
            </div>
        </div>
    </div>
</body>
<!-- ============================================================== -->
<!-- All Jquery -->
<!-- ============================================================== -->
<script src="../myschool/assets/custom/jquery.min.js"></script>

</html>