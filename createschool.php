	<?php

use App\Core\Helper;
use App\Auth\Auth;
use App\Auth\Populator;
use App\Notifiable\Mailable;

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
    $confirm = $_POST['password2'];
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

                            try {
                                $mailable = new Mailable('welcome', $welcomeData);
                                $sent = $mailable->build()->send();
                                if ($sent) {
                                    // activate their onetime (three months free activation)
                                    Helper::activateOneMonthFreeOffer($id, $email);

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
    <link rel="stylesheet" href="./static/register.css" />

</head>

<body>
    <?php

    if (!empty($error)) {
        echo "<script>alert('" . $error[0] . "')</script>";
    }

    ?>
    <div class="container">
        <section class="section-auth">
            <h3>Create your account</h3>
            <div class="auth">
                <div class="login-form">
                    <form action="" class="form" method="post" autocomplete="off">
                        <div class="form__group">
                            <input type="text" name="schoolname" id="schoolname" placeholder="School Name" required class="form__input" value="<?= $schoolName ?>">
                            <label for="schoolname" class="form__label">School Name</label>
                        </div>
                        <div class="form__group">
                            <input type="text" name="username" id="username" placeholder="Username" required class="form__input" value="<?= $username ?>">
                            <label for="username" class="form__label">Username</label>
                        </div>
                        <div class="form__group">
                            <input type="email" name="email" id="email" placeholder="Email Address" required class="form__input" value="<?= $email ?>">
                            <label for="email" class="form__label">Email Address</label>
                        </div>
                        <div class="form__group">
                            <input type="password" name="password" id="password" placeholder="Password" required class="form__input">
                            <label for="password" class="form__label">Password</label>
                        </div>
                        <div class="form__group">
                            <input type="password" name="password2" id="password2" placeholder="Retype Password" required class="form__input">
                            <label for="password2" class="form__label">Retype Password</label>
                        </div>
                        <div class="form__group">
                            <input type="submit" name="submit" id="submit" value="Create School" class="form__input">
                        </div>
                    </form>
                    <div class="info">
                        <p>Powered by Mercury School Management System</p>
                        <p>Have an account? <a href="login.php">Login</a></p>
                        <p>Visit <a href="index.php">Homepage</a></p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>

</html>
