<?php

use App\Core\Helper;
use App\Auth\Auth;
use App\Notifiable\Components\Components;
use App\Notifiable\Mail;

include_once './init.php';

$email = "";
if (isset($_GET['email']) && $_GET['email'] != "") {
    $email = $_GET['email'];
}

$error = [];
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $email = $_POST['email'];
    if (!Helper::isEmpty($email)) {
        if (Auth::userExists($email)) {
            $mail = new Mail();
            $token = Auth::generateToken();
            if (Auth::addToken($token, $email)) {
                $message = Components::header("Update your password", "h2", "center")
                    .
                    Components::body("<br>You have requested for a password update.
                        If you did not request for this, then just ignore it.<br>")
                    .
                    Components::link(SCHOOL_URL . "/passwordreset.php?token=" . $token, "Reset password", Components::BTN_SUCCESS);
                $subject = "Password Reset";
                try {
                    $mail->sendMail([$email => $email], $subject, $message, DEFAULT_FULLNAME, DEFAULT_FROM);
                    echo "<div class='card col-lg-9 mx-auto'>";
                    echo "<h3 class='text-center p-3'>Password Request Link Sent</h3>";
                    echo "<p class='lead'>We have sent a link to your e-mail address<br>Don't see anything? Check spam or   
                    <button class='btn btn-primary' onclick='window.location.reload()'>Resend</button></p>";
                    echo "</div>";
                    die();
                } catch (Exception $e) {
                    $error[] = $e->getMessage();
                    $mail->sendMail([$email => $email], $subject, $message, DEFAULT_FULLNAME, DEFAULT_FROM);
                    if ($mail->sent) {
                        echo "<div class='card col-lg-9 mx-auto'>";
                        echo "<h3 class='text-center p-3'>Password Request Link Sent</h3>";
                        echo "<p class='lead'>We have sent a link to your e-mail address<br>Don't see anything? Check spam or   
                        <button class='btn btn-primary' onclick='window.location.reload()'>Resend</button></p>";
                        echo "</div>";
                        die();
                    } else {
                        http_response_code(503);
                        $error[] = "Our backup server could not send the mail to your email<br>Something went wrong from our end";
                    }
                }
            } else {
                $error[] = "Could not generate your token";
            }
        } else {
            $error[] = "The specified e-mail is not registered with us";
        }
    } else {
        $error[] = "Please specify an e-mail address";
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
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link rel="stylesheet" href="./vendors/css/materialize.min.css"> -->
    <link rel="stylesheet" href="./static/style.css" />

</head>

<body>
    <div class="container">
        <section class="section-auth">
            <h3>Request Password Reset</h3>
            <div class="auth">
                <div class="login-form">
                    <form action="" class="form" method="post" autocomplete="off">
                        <div class="form__group">
                            <input type="email" name="email" id="email" placeholder="Email" required class="form__input" value="<?= $email ?>">
                            <label for="email" class="form__label">Email</label>
                        </div>

                        <div class="form__group">
                            <input type="submit" name="submit" id="submit" value="Request Link" class="form__input">
                        </div>
                    </form>
                    <div class="info">
                        <p>Powered by Mercury School Management System</p>

                        <p>Visit <a href="index.php">Homepage</a></p>
                    </div>
                </div>
            </div>
        </section>
    </div>
</body>

</html>