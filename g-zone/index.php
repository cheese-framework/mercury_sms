<?php

use App\Notifiable\Components\Components;

include_once "../init.php";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mercury - SMS |
        ADMIN
    </title>
    <link rel="stylesheet" href="../myschool/assets/css/style.css">
</head>

<body>
    <div class="container">
        <div class="col-lg-8 my-5 mx-auto">
            <div class="card">
                <div class="card-body">
                    <?= Components::header("Login", "h2", "center"); ?>
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="username">Username or E-mail</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="Username | E-mail Address">
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                        </div>

                        <div class="form-group text-center">
                            <input type="submit" value="Login" class="btn btn-primary">
                        </div>
                    </form>
                    <?= Components::body("<small><i>Powered by QHITECH</i></small>", true, "right") ?>
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