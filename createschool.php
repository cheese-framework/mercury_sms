<?php

use App\Core\Helper;
use App\Auth\Auth;
use App\Auth\Populator;
use App\Notifiable\Components\Components;
use App\Notifiable\Mail;
use App\Notifiable\Mailable;
use App\Queue\QueuePublisher;

include_once './init.php';

if (isset($_SESSION['sms-details'])) {
    unset($_SESSION['sms-details']);
}

$error = [];
$schoolName = "";
$username = "";
$email = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $schoolName = $_POST['schoolname'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];
    if (!Helper::isEmpty($schoolName, $username, $email, $password, $confirm)) {
        $bool = Helper::schoolEmailExists($schoolName);
        if ($bool) {
            $error[] = "School email is already taken";
        } else {
            if ($password == $confirm) {
                $b = Auth::userExists($email);
                if (!$b) {
                    $password = password_hash($password, PASSWORD_DEFAULT);
                    $created = date("Y-m-d");
                    $auth = new Auth(['']);
                    $pop = new Populator();
                    $id = $pop->createSchool($schoolName, $email, $created);
                    if ($id != false) {
                        $token = Auth::generateToken();
                        $hasCreatedUser = $auth->createStaff($username, $email, $password, $id);
                        if ($hasCreatedUser) {
                            // add token
                            Auth::addToken($token, $email);
                            $welcomeData = [
                                'to' => $email,
                                'name' => $username,
                                'from' => DEFAULT_FROM,
                                'fromName' => DEFAULT_FULLNAME,
                                'subject' => 'Verify Your Account',
                                'message' => "Thanks so much for joining us. You're on your way to get <b>Unbeatable experience</b> for your school management system with Mercury - SMS<br>Please confirm your email address by pressing the button below.",
                                'link' => SCHOOL_URL . "/verify.php?token=$token"
                            ];
                            // add to queue

                            try {
                                $mailable = new Mailable('welcome', $welcomeData);
                                $sent = $mailable->build()->send();
                                if ($sent) {
                                    // activate their onetime (one month free activation)
                                    Helper::activateOneMonthFreeOffer($id, $email);

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
                                        <p>Check you email address for verification link</p>
                                    </div>
<?php
                                    exit;
                                } else {
                                    $pop->deleteSchool($id);
                                    Auth::delete($email);
                                    $error[] = "Could not send you an email now. Try again.";
                                }
                                // display verify prompt

                            } catch (Exception $e) {

                                $pop->deleteSchool($id);
                                Auth::delete($email);
                                $error[] = "Verification generator failed";
                            }
                        } else {
                            $error[] = "Could not create user";
                        }
                    } else {
                        $error[] = "Something went wrong while creating school";
                    }
                } else {
                    $error[] = "Email is in use by another user";
                }
            } else {
                $error[] = "Passwords do not match";
            }
        }
    } else {
        $error[] = "All fields are required";
    }
}


?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Get Started by Creating your School</title>
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
    <div class="container my-2">
        <div class="row mb-5">
            <div class="col-lg-12 col-md-12 col-sm-12 mx-auto">
                <h1 class="text-center" style="text-shadow: 1px 1px 1px #000;">Get Started by Creating Your School
                </h1>
                <div class="col-lg-8 my-4 mx-auto">
                    <?php
                    if (!empty($error)) {
                        echo "<div class='alert alert-danger' id='err'>";
                        foreach ($error as $err) {
                            echo $err . "<br>";
                        }
                        echo "</div><br>";

                    ?>

                        <script>
                            const err = document.getElementById('err');
                            setTimeout(() => {
                                err.style.display = 'none';
                            }, 3000);
                        </script>

                    <?php
                    }


                    ?>
                    <form action="" method="POST" autocomplete="OFF">
                        <div class="input-field">
                            <input type="text" name="schoolname" class="validate" value="<?= $schoolName; ?>" id="schoolname" required />
                            <label for="schoolname">School Name: </label>
                            <span class="helper-text" data-error='This field is required'></span>
                        </div>

                        <div class="input-field">
                            <input type="text" name="username" class="validate" value="<?= $username; ?>" id="username" required />
                            <label for="username">Username: </label>
                            <span class="helper-text" data-error='This field is required'></span>
                        </div>

                        <div class="input-field">
                            <input type="email" name="email" class="validate" value="<?= $email; ?>" id="email" required />
                            <label for="email">School Email: </label>
                            <span class="helper-text" data-error='This field is required'></span>
                        </div>

                        <div class="input-field">
                            <input type="password" name="password" class="validate" id="password" required />
                            <label for="password">Password: </label>
                            <span class="helper-text" data-error='This field is required'></span>
                        </div>

                        <div class="input-field">
                            <input type="password" name="confirm" class="validate" id="confirm" required />
                            <label for="confirm">Confirm Password: </label>
                            <span class="helper-text" data-error='This field is required'></span>
                        </div>

                        <div class="input-field">
                            <input type="submit" value="Continue" class="btn btn-github">
                        </div>
                    </form>
                    <p class="mt-2">By creating your school's account, you agree to our <a href="terms.html" target="_blank">terms and conditions</a></p>
                    <p class="mt-2">Have an account? <a href="login.php">Login</a></p>
                </div>
            </div>
        </div>
        <footer>
            <p class="text-right text-small">Powered by QHITECH 2020 - <?= date("Y") ?></p>
        </footer>
    </div>
    <script src="./vendors/js/materialize.min.js"></script>

</body>

</html>