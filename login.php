<?php

use App\Auth\Auth;
use App\Core\Helper;

include_once './init.php';

// if session is on redirect to dashboard

if (isset($_SESSION['sms_userid'])) {
    if (isset($_SESSION['redirect_path_mercury'])) {
        Helper::to($_SESSION['redirect_path_mercury']);
    } else {
        Helper::to("myschool");
    }
}

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
                            color: white;
                            width: 70%;
                            margin: 10px auto;
                            text-align: center;
                            display: block;
                            background: rgb(0, 179, 80);
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
                if (isset($_SESSION['redirect_path_mercury'])) {
                    Helper::to($_SESSION['redirect_path_mercury']);
                } else {
                    Helper::to("myschool");
                }
            } catch (Exception $ex) {
                $error[] = $ex->getMessage();
            }

            // check login attempt
            if (Auth::getLoginAttempt($username) >= 3) {
                usleep(1000);
                Helper::to("forgotpassword.php?email=" . $username);
            }
        }
    } else {
        $error[] = "All fields are required";
    }
}


?>

<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>

<head>
    <meta charset="UTF-8">
    <title>Login to Your School</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./static/style.css" />

</head>

<body>
    <?php

    if (!empty($error)) {
        echo "<script>alert('" . $error[0] . "')</script>";
    }

    ?>
    <div class="container">
        <section class="section-auth">
            <h3>Login to your school</h3>
            <div class="auth">
                <div class="login-form">
                    <form action="" class="form" method="post" autocomplete="off">
                        <div class="form__group">
                            <input type="text" name="username" id="username" placeholder="Username or Email" required class="form__input" value="<?= $username ?>">
                            <label for="username" class="form__label">Username or Email</label>
                        </div>
                        <div class="form__group">
                            <input type="password" name="password" id="password" placeholder="Password" required class="form__input">
                            <label for="password" class="form__label">Password</label>
                        </div>
                        <div class="form__group">
                            <input type="submit" name="submit" id="submit" value="Login" class="form__input">
                        </div>
                    </form>
                    <div class="info">
                        <p>Powered by Mercury School Management System</p>

                        <p>Forgot Password? <a href="./forgotpassword.php">Reset</a></p>

                        <p>Visit <a href="index.php">Homepage</a></p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>

</html>