<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>

<head>
    <meta charset="UTF-8">
    <title>Go to Your School</title>
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

                    use App\Auth\Auth;
                    use App\Core\Helper;

                    include_once './init.php';

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
                                    sleep(2);
                                    Helper::to("forgotpassword.php?email=" . $username);
                                }
                            }
                        } else {
                            $error[] = "All fields are required";
                        }
                    }


                    ?>
                    <br>
                    <?php

                    if (!isset($_SESSION['school'])) :
                        echo '<h1 class="text-center my-3 mb-2" style="text-shadow: 1px 1px 1px #000;">Log into your school\'s account</h1>';
                        if (!empty($error)) {
                            echo "<div class='alert alert-danger '>";
                            foreach ($error as $err) {
                                echo $err . "<br>";
                            }
                            echo "</div><br>";
                        }
                    ?>
                        <form action="" method="POST" autocomplete="OFF" class="my-3">
                            <div class="input-field">
                                <input type="text" id="user_name" class="validate" name="username" required value="<?= $username ?>">
                                <label for="user_name">*E-Mail</label>
                                <span class="helper-text" data-error='This field is required'></span>
                            </div>
                            <div class="input-field">
                                <input type="password" id="password" name="password" class="validate" required minlength="4">
                                <label for="password">*Password</label>
                                <span class="helper-text" data-error='Password is required'></span>
                            </div>
                            <div class="input-field center">
                                <input type="submit" value="Sign in" class="btn btn-primary">
                            </div>
                        </form>
                        <p class="text-center">Forgot Password? <a href="forgotpassword.php">Reset</a></p>
                    <?php
                    else :
                        // Display the badge and button to sign out or go back to dashboard
                        echo "<h2 class='text-center'>You are already signed in!</h2>";
                        if ($badge = Helper::getBagde($_SESSION['school'])) {
                            if ($badge !== "") {
                                echo "<div class='text-center'>
                                <br>
                                <img src='myschool/assets/images/$badge' alt='School Logo' class='img-fluid mb-2' width='160px' height='120px' style='border-radius: 10px; box-shadow: 1px 1px 10px #424242'></div><br><br>";
                            }
                        }

                        echo "<div class='text-center my-3'>
                            <a href='myschool' class='btn btn-primary'>My School</a>
                        <a href='logout.php' class='btn btn-danger'>Logout</a>
                        </div>";


                    endif;

                    ?>
                </div>
            </div>
        </div>
        <footer>
            <p class="text-center text-small">Powered by ChiStudios 2020 - <?= date("Y") ?></p>
        </footer>
    </div>
    <script src="./vendors/js/materialize.min.js"></script>
</body>

</html>