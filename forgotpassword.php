<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
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
                    use App\Notifiable\Components\Components;
                    use App\Notifiable\Mail;
                    use App\Queue\QueuePublisher;

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
                    <h3 class="text-center">Request Password Reset Link</h3><br><br>
                    <form action="" method="POST" autocomplete="OFF" class="my-3">
                        <div class="input-field">
                            <input type="text" id="email" class="validate" name="email" value="<?= $email ?>" required>
                            <label for="email">* Enter your e-mail address</label>
                            <span class="helper-text" data-error='This field is required'></span>
                        </div>
                        <div class="input-field center">
                            <input type="submit" value="Request Link" class="btn btn-primary">
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