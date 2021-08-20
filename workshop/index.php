<?php

use App\Core\Helper;
use App\Notifiable\Components\Components;

include_once "../init.php";

if (isset($_SESSION['sms_userid'])) {
    Helper::to("dashboard.php");
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
        MY WORKSHOP
    </title>
    <link rel="stylesheet" href="../myschool/assets/css/style.css">
</head>

<body>
    <div class="container">
        <div class="col-lg-8 my-5 mx-auto">
            <?php

            use App\Auth\Auth;

            $error = [];
            $username = "";

            if ($_SERVER['REQUEST_METHOD'] == "POST") {
                $username = $_POST['username'];
                $password = $_POST['password'];
                $bool = Helper::isEmpty($username, $password);
                if (!$bool) {
                    if (empty($error)) {
                        $auth = new Auth(['twilio']);
                        try {
                            $data = $auth->loginStaff($username, $password);
                            if (!$auth->getStatus($username)) {
            ?>
                                <style>
                                    .card {
                                        width: 100%;
                                        margin: 10px auto;
                                        text-align: center;
                                        display: block;
                                        background: #f3f3f3;
                                        border-radius: 7px;
                                        box-shadow: 1px 1px 2px #ccc;
                                        padding: 10px;
                                        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
                                    }
                                </style>
                                <div class="card">
                                    <h3>You have mail!</h3>
                                    <p>Please verify your account. Check your mail.</p>
                                </div>
            <?php
                                exit;
                            }
                            $_SESSION['sms_username'] = $data["username"];
                            $_SESSION['sms_userid'] = $data["id"];
                            $_SESSION['sms_role'] = $data["role"];
                            $_SESSION['sms_class'] = 0;
                            $_SESSION['pic'] = $data["pic"];
                            $_SESSION['school'] = $data['school'];
                            $_SESSION['sms_email'] = $data['email'];
                            if (isset($_SESSION['workshop_redirect_path_mercury'])) {
                                $to = str_replace("workshop/", "", $_SESSION['workshop_redirect_path_mercury']);
                                Helper::to($to);
                            } else {
                                Helper::to("dashboard.php");
                            }
                        } catch (Exception $ex) {
                            $error[] = $ex->getMessage();
                        }

                        // check login attempt
                        if (Auth::getLoginAttempt($username) >= 3) {
                            Helper::to("../forgotpassword.php?email=" . $username);
                        }
                    }
                } else {
                    $error[] = "All fields are required";
                }
            }


            ?>
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
                            }, 10000);
                        </script>
                    <?php
                    } ?>
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