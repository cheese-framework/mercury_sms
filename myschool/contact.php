<?php

use App\Notifiable\Mail;
use App\Core\Helper;
use App\Queue\QueuePublisher;

include_once './includes/header.php';

$mail = new Mail();
$error = [];
$success = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $subject = $_POST['subject'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $body = $_POST['body'];
    if (!Helper::isEmpty($subject, $email, $username, $body)) {
        $to = [DEFAULT_FROM => DEFAULT_FULLNAME];
        try {
            $mail = new Mail();
            $mail->sendMail([DEFAULT_FROM => DEFAULT_FULLNAME], $subject, $body, $username, $email);
            $success = $mail->sent ? "Mail sent!" : "Something went wrong";
        } catch (Exception $e) {
            $error[] = "Could not send mail :(";
        }
    } else {
        $error[] = "All fields are required";
    }
}

?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="card col-lg-8 mx-auto">
                <h2 class="text-center p-3">Contact Us</h2>
                <?php
                if (!empty($error)) {
                    echo "<div class='alert alert-danger'>";
                    foreach ($error as $e) {
                        echo $e . "<br>";
                    }
                    echo "</div>";
                }

                if ($success != "") {
                    echo "<div class='alert alert-success'>$success</div>";
                }
                ?>
                <div id="msg"></div>
                <form action="" method="post" id="contactForm" autocomplete="off">
                    <div class="form-group">
                        <label for="">Subject *</label>
                        <input type="text" name="subject" id="subject" class="form-control" placeholder="Subject">
                    </div>
                    <div class="form-group">
                        <label for="">Your E-mail *</label>
                        <input type="email" name="email" id="email" class="form-control" placeholder="From" email>
                    </div>
                    <div class="form-group">
                        <label for="">Username *</label>
                        <input type="text" name="username" id="username" class="form-control" placeholder="Username">
                    </div>
                    <div class="form-group">
                        <label for="">Message *</label>
                        <textarea name="body" id="body" placeholder="Message Body" class="editor form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <input type="submit" id="contactBtn" value="Send" class="btn btn-success">
                    </div>
                </form>
            </div>
        </div>
        <script src="assets/custom/ckeditor.js"></script>
        <?php include_once './includes/footer.php'; ?>