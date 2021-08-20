<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>

<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./vendors/css/materialize.min.css">
    <link rel="stylesheet" href="static/css/style.css" />

</head>

<body>
    <nav>
        <div class="nav-wrapper purple darken-4">
            <a href="index.php" class="brand-logo" style="text-decoration: none;">&nbsp; &nbsp; Mercury - SMS</a>
        </div>
    </nav>
    <div class="container">
        <div class="row my-2">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mx-auto">

                <div class="col-lg-8 my-4 mx-auto">
                    <?php

                    use App\Core\Helper;
                    use App\Auth\Auth;

                    include_once './init.php';
                    $error = [];
                    $token = (isset($_GET['token']) && $_GET['token'] != "" ? $_GET['token'] : $error[]  = "Invalid token");


                    if ($_SERVER['REQUEST_METHOD'] == "POST") {
                        $password = $_POST['password'];
                        $confirm = $_POST['confirm'];
                        $email = $_POST['email'];

                        if (!Helper::isEmpty($password, $confirm, $email)) {
                            if ($password != $confirm) {
                                $error[] = "Passwords do not match";
                            } else {
                                $password = password_hash($password, PASSWORD_DEFAULT);
                                if (Auth::updatePassword($password, $token, $email)) {
                                    Helper::to("login.php");
                                } else {
                                    $error[] = "Could not update password";
                                }
                            }
                        } else {
                            $error[] = "All fields are required";
                        }
                    }

                    if (!empty($error)) {
                        echo "<div class='alert alert-danger '>";
                        foreach ($error as $err) {
                            echo $err . "<br>";
                        }
                        echo "</div>";
                    }

                    ?>


                    <form action="" method="POST" autocomplete="OFF" class="my-3">
                        <h3 class="text-center">Reset Password</h3>
                        <div class="input-field">
                            <input type="text" id="email" class="validate" name="email" required>
                            <label for="user_name">* Enter your e-mail address</label>
                            <span class="helper-text" data-error='This field is required'></span>
                        </div>
                        <div class="input-field">
                            <input type="password" id="password" class="validate" name="password" required>
                            <label for="password">* Enter your new password</label>
                            <span class="helper-text" data-error='This field is required'></span>
                        </div>
                        <div class="input-field">
                            <input type="password" id="confirm" class="validate" name="confirm" required>
                            <label for="confirm">* Retype your new password</label>
                            <span class="helper-text" data-error='This field is required'></span>
                        </div>
                        <div class="input-field center">
                            <input type="submit" value="Reset Password" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <footer>
            <p class="text-right text-small">Powered by ChiStudios 2020 - <?= date("Y") ?></p>
        </footer>
    </div>
    <script src="./vendors/js/materialize.min.js"></script>
</body>

</html>