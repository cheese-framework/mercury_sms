<?php

use App\Auth\Auth;
use App\Core\Helper;
use App\Media\FileUpload;
use App\Notifiable\Components\Components;

include_once './includes/header.php';

try {
    $record = Helper::getStaffRecord($sms_userId);
    $phone = $record->contact_address;
    if ($phone != NULL) {
        if (strpos($phone, "+220") !== 0) {
            $phone = "+220" . $phone;
        }
    }
} catch (\Throwable $th) {
    Helper::showErrorPage();
}

$error = [];

// update profile

if (isset($_POST['update-profile'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['p1'];
    $password2 = $_POST['p2'];
    $phone = $_POST['phone'];

    if (!Helper::isEmpty($username, $email, $password, $password2)) {
        if ($password != $password2) {
            $error[] = "Passwords do not match";
        }
        // check if password exists
        if ($email != $record->staff_email) {
            if (Auth::userExists($email)) {
                $error[] = "E-mail is in use by another user";
            }
        }

        // add country code to phone

        if ($phone != "") {
            if (strpos($phone, "+220") !== 0) {
                $phone = "+220" . $phone;
            }
        }

        // update record
        if (empty($error)) {
            // hash password
            $password = password_hash($password, PASSWORD_DEFAULT);
            Auth::updateProfile($username, $email, $password, $phone, $sms_userId);
            $_SESSION['sms_username'] = $username;
            $_SESSION['sms_email'] = $email;
            Helper::to("setting.php");
        }
    } else {
        $error[] = "All fields are required";
    }
}


// change display profile

if (isset($_POST['change-profile'])) {
    $profile = $_FILES['profile'];
    if (isset($_FILES['profile']) && $profile['name'] != "") {
        try {
            $prev = "assets/profile/" . $pic;
            FileUpload::uploadImage("assets/profile/", $profile);
            $new = FileUpload::$file;
            Helper::changePicture($sms_userId, $new, $prev);
            $_SESSION['pic'] = $new;
            Helper::to("setting.php");
        } catch (Exception $e) {
            $error[] = $e->getMessage();
        }
    } else {
        $error[] = "Please select an image";
    }
}


?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <?= Components::header("Profile page", "h3") ?>

            <?php
            if (!empty($error)) {
                echo "<div class='alert alert-danger'>";
                foreach ($error as $e) {
                    echo $e . "<br>";
                }
                echo "</div>";
            }
            ?>

            <div class="modal" tabindex="-1" role="dialog" id="exampleModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Update your display profile</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="" method="post" enctype="multipart/form-data">
                                <div class="form-group">
                                    <input type="file" name="profile" id="profile">
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="Upload" name="change-profile" class="btn btn-primary">
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                <div class="col-lg-4">
                    <p class="text-small text-info m-1">click the image to change display profile</p>
                    <div class="card text-center bg-primary">
                        <div class="card-body text-light">
                            <img src="assets/profile/<?= $pic; ?>" width="100px" height="100px" style="border-radius: 100%;cursor:pointer;" alt="Profile Picture" id="image" data-toggle="modal" data-target="#exampleModal">
                            <?= Components::header($sms_username, "h4", "center"); ?>
                            <?= Components::body("Role: " . $sms_role . "<br>" . $sms_email, "true", "center"); ?>
                        </div>
                        <div class="card-body bg-behance"></div>
                        <div class="card-body bg-light">
                            <?= Components::header($phone, "h2", "center"); ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card p-3">
                        <form action="" method="post" autocomplete="off">
                            <div class="form-group">
                                <input type="text" name="username" id="username" class="form-control" placeholder="Username" value="<?= $sms_username; ?>" required>
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" id="email" class="form-control" placeholder="E-mail Address" value="<?= $sms_email; ?>" required>
                            </div>
                            <div class="form-group">
                                <input type="password" name="p1" id="password" class="form-control" placeholder="Password" required>
                            </div>
                            <div class="form-group">
                                <input type="password" name="p2" id="confirm" class="form-control" placeholder="Retype password" required>
                            </div>
                            <div class="form-group">
                                <input type="text" name="phone" id="phone" class="form-control" placeholder="Mobile Phone Number" minlength="7" maxlength="11" value="<?= $phone ?>">
                            </div>
                            <div class="form-group">
                                <input type="submit" name="update-profile" id="submit" class="btn btn-facebook" value="Update Profile">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>
        <script>
            const imagePointer = document.getElementById('image');
            imagePointer.onclick = function() {
                $('#myModal').on('shown.bs.modal', function() {
                    $('#myInput').trigger('focus')
                });
            };
        </script>