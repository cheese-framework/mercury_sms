<?php

use App\Core\Helper;
use App\Auth\Auth;
use App\Notifiable\Mail;
use App\Notifiable\Mailable;

include_once './includes/header.php';

if ($sms_role != "Super-Admin") {
    Helper::showNotPermittedPage();
}

$username = "";
$email = "";
$acadqual = "";
$profqual = "";
$contact = "";
$error = [];
$message = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password2 = $_POST['password2'];
    $role = (isset($_POST['role']) ? $_POST['role'] : "");
    $gender = (isset($_POST['gender']) ? $_POST['gender'] : "");
    $acadqual = $_POST['acadqual'];
    $profqual = $_POST['profqual'];
    $contact = $_POST['contact'];
    $yearAppointed = $_POST['yearappoint'];
    $dob = $_POST['dob'];
    if (!Helper::isEmpty($username, $email, $password, $password2, $role, $gender)) {
        if ($password == $password2) {
            $password = password_hash($password, PASSWORD_DEFAULT);
            $bool = Auth::userExists($email);
            if (!$bool) {
                // add country code to number if it doesn't exist

                if ($contact != "") {
                    if (strpos($contact, "+220") !== 0) {
                        $contact = "+220" . $contact;
                    }
                }
                try {
                    $auth = new Auth(['twilio']);

                    $token = Auth::generateToken();

                    if ($auth->createStaff($username, $email, $password, $schoolId, $role, $gender, $dob, $profqual, $acadqual, $yearAppointed, $contact)) {
                        $mail = new Mail();
                        Auth::addToken($token, $email);
                        $welcomeData = [
                            'to' => $email,
                            'name' => $username,
                            'from' => DEFAULT_FROM,
                            'fromName' => DEFAULT_FULLNAME,
                            'subject' => 'Verify Your Account',
                            'message' => "Please confirm your email address by pressing the button below.<br>
                            Email = $email<br>
                            Password = " . $_POST['password'] . "",
                            'link' => SCHOOL_URL . "/verify.php?token=$token"
                        ];
                        $mailable = new Mailable('welcome', $welcomeData);
                        $sent = $mailable->build()->send();

                        // send notice
                        $auth->email = $email;

                        if ($sent) {

                            if (Helper::isUsingSMS($schoolId)) {
                                $auth->notify("You have mail at " . $email . ". From " . Helper::getSchoolName($schoolId));
                            }

                            $message[] = "$username has been added to lists of $role" . "s";
                            $username = "";
                            $email = "";
                            $acadqual = "";
                            $profqual = "";
                            $contact = "";
                        } else {
                            Auth::delete($email);
                            $error[] = "We could not send notification to: '<b>" . $email . "</b>'. So, we had to delete the user";
                        }
                    } else {
                        $error[] = "Something went wrong. Try again later";
                    }
                } catch (Exception $ex) {
                    $error[] = $ex->getMessage();
                }
            } else {
                $error[] = "E-Mail is in use by another user";
            }
        } else {
            $error[] = "Passwords do not match";
        }
    } else {
        $error[] = "Fill in the required fields";
    }
}
?>
<div class="container-scroller">
    <!-- partial:partials/_sidebar.html -->
    <?php include_once './includes/navbar.php'; ?>
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="col-lg-12 mb-2">
                <a href="staffsprofile.php" class="btn btn-dribbble">Staff Profile</a>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <h2>Add Staff</h2>
                    <p>Fields marked with asterisk <em class="text-danger">(*)</em> are required</p>
                    <div class="card">
                        <div class="card-body">
                            <?php
                            if (!empty($error)) {
                                echo "<div class='alert alert-danger col-lg-9' id='msg'>";
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
                            }

                            if (!empty($message)) {
                                echo "<div class='alert alert-success col-lg-9' id='msg'>" . $message[0] . "</div>";
                            ?>
                                <script>
                                    const msg = document.getElementById('msg');
                                    setTimeout(() => {
                                        msg.style.display = "none";
                                    }, 10000);
                                </script>
                            <?php
                            }

                            ?>
                            <form method="POST" action="" autocomplete="off">
                                <div class="form-group">
                                    <label><em class="text-danger">*</em> Full Name: </label>
                                    <input type="text" name="username" placeholder="Fullname" class="form-control" value="<?= $username ?>">
                                </div>
                                <div class="form-group">
                                    <label><em class="text-danger">*</em> Email: </label>
                                    <input type="email" name="email" placeholder="Email Address" class="form-control" value="<?= $email ?>">
                                </div>
                                <div class="form-group">
                                    <label><em class="text-danger">*</em> Password: </label>
                                    <input type="password" name="password" placeholder="Password" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label><em class="text-danger">*</em> Confirm Password: </label>
                                    <input type="password" name="password2" placeholder="Re-type Password" class="form-control">
                                </div>


                                <div class="form-group">
                                    <label><em class="text-danger">*</em> Role: <i class="text-info text-small">(other staffs are not supported yet!)</i></label>
                                    <select class="form-control" name="role">
                                        <option value="Super-Admin">Super Administrator</option>
                                        <option value="Administrator">Administrator</option>
                                        <option value="Headmaster">Head Master || Principal</option>
                                        <option value="Teacher">Teacher</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label><em class="text-danger">*</em> Gender: </label>
                                    <select name="gender" class="form-control">
                                        <option value="F">Female</option>
                                        <option value="M">Male</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Date of Birth: </label>
                                    <input type="date" name="dob" class="form-control">
                                </div>
                                <div class="form-group">
                                    <label>Professional Qualification: </label>
                                    <input type="text" class="form-control" placeholder="Eg: HTC" name="profqual" value="<?= $profqual ?>" />
                                </div>
                                <div class="form-group">
                                    <label>Academic Qualification: </label>
                                    <input type="text" class="form-control" placeholder="Eg: Diploma" name="acadqual" value="<?= $acadqual ?>" />
                                </div>

                                <div class="form-group">
                                    <label>Year of Appointment: </label>
                                    <input type="date" class="form-control" name="yearappoint" />
                                </div>


                                <div class="form-group">
                                    <label>Contact Address: </label>
                                    <input type="text" class="form-control" placeholder="Eg: 000 0000" name="contact" value="<?= $contact ?>" maxlength="11" minlength="7" />
                                </div>

                                <div class="form-group">
                                    <input type="submit" value="Add Staff" class="btn btn-dark" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include_once "includes/footer.php"; ?>