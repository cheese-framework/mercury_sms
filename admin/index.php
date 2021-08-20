<?php

use App\Auth\Admin\Auth;
use App\Core\Helper;
use App\Notifiable\Components\Components;

include_once "../init.php";

// protect this route
if (isset($_SESSION['sms_admin_id'])) {
    Helper::to('dashboard.php');
}

$error = [];
$username = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'] ?? "";
    $password = $_POST['password'] ?? "";

    if (!Helper::isEmpty($username, $password)) {
        try {
            $userData = Auth::login($username, $password);
            $_SESSION['sms_admin_id'] = $userData->id;
            $_SESSION['sms_admin_username'] = $userData->username;
            $_SESSION['sms_admin_image'] = $userData->image;
            $_SESSION['sms_admin_role'] = $userData->role;
            Helper::to('dashboard.php');
        } catch (\Throwable $th) {
            $error[] = $th->getMessage();
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
    <title>Mercury - SMS | ADMIN
    </title>
    <link rel="stylesheet" href="../myschool/assets/css/style.css">
</head>

<body>
    <div class="container">
        <div class="col-lg-8 my-5 mx-auto">
            <div class="card">
                <div class="card-body">
                    <?php
                    if (!empty($error)) {
                        echo "<div class='alert alert-danger col-lg-12 text-center' id='msg'>";
                        foreach ($error as $e) {
                            echo $e . "<br>";
                        }
                        echo "</div>";
                    ?>

                        <script>
                            const msg = document.getElementById('msg');
                            setTimeout(() => {
                                msg.style.display = "none";
                            }, 4000);
                        </script>
                    <?php
                    } ?>
                    <?= Components::header("Login", "h2", "center"); ?>
                    <form action="" method="post" autocomplete="off">
                        <div class="form-group">
                            <label for="username">Username or E-mail</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="Username | E-mail Address" value="<?= $username; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password" required>
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