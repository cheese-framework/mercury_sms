<?php

use App\Core\Helper;
use App\Auth\Auth;

include_once './includes/header.php';

$error = [];
if (isset($_GET['staff_id']) && $_GET['staff_id'] != "") {
    $staffId = $_GET['staff_id'];
    try {
        $data = Helper::getStaffRecord($staffId);
        $name = $data->staff_name;
        $email = $data->staff_email;
        $role = $data->staff_role;
        $gender = $data->gender;
        $dob = $data->dob;
        $profqual = $data->profqual;
        $acadqual = $data->acadqual;
        $yearAppointed = $data->yearappoint;
        $contact = $data->contact_address;
        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $username = $_POST['username'];
            $enteredEmail = $_POST['email'];
            $role = (isset($_POST['role']) ? $_POST['role'] : "");
            $gender = (isset($_POST['gender']) ? $_POST['gender'] : "");
            $acadqual = $_POST['acadqual'];
            $profqual = $_POST['profqual'];
            $contact = $_POST['contact'];
            $yearAppointed = $_POST['yearappoint'];
            $dob = $_POST['dob'];

            if (!Helper::isEmpty($username, $enteredEmail)) {
                if ($email != $enteredEmail) {
                    if (Auth::userExists($enteredEmail)) {
                        $error[] = "E-mail already exists";
                    }
                } else {
                    // add +220 to phone number
                    if ($contact) {
                        if (strpos($contact, "+220") !== 0) {
                            $contact = "+220" . $contact;
                        }
                    }
                    Helper::updateStaffRecord($username, $enteredEmail, $role, $gender, $dob, $profqual, $acadqual, $yearAppointed, $contact, $staffId);
                    Helper::to("staffsprofile.php");
                }
            } else {
                $error[] = "Username and email are needed";
            }
        }
    } catch (Exception $ex) {
        Helper::to("staffsprofile.php");
    }
} else {
    Helper::to("staffsprofile.php");
}
?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-lg-12">
                    <h2>Update Staff</h2>
                    <p>Fields marked with asterisk <em class="text-danger">(*)</em> are required</p>
                    <div class="card">
                        <div class="card-body">
                            <?php
                            if (!empty($error)) {
                                echo "<div class='alert alert-danger'>";
                                foreach ($error as $e) {
                                    echo $e . "<br>";
                                }
                                echo "</div>";
                            }
                            ?>
                            <form method="POST" action="" autocomplete="off">
                                <div class="form-group">
                                    <label><em class="text-danger">*</em> Full Name: </label>
                                    <input type="text" name="username" placeholder="Fullname" class="form-control" value="<?= $name ?>">
                                </div>
                                <div class="form-group">
                                    <label><em class="text-danger">*</em> Email: </label>
                                    <input type="email" name="email" placeholder="Email Address" class="form-control" value="<?= $email ?>">
                                </div>


                                <div class="form-group">
                                    <label><em class="text-danger">*</em> Role: </label>
                                    <select class="form-control" name="role">
                                        <option value="<?= $role ?>"><?= $role ?></option>
                                        <option value="Administrator">Administrator</option>
                                        <option value="Headmaster">Head Master</option>
                                        <option value="Teacher">Teacher</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label><em class="text-danger">*</em> Gender: </label>
                                    <select name="gender" class="form-control">
                                        <?php
                                        if ($gender == "F") {
                                            echo "<option value='F'>Female</option>";
                                            echo "<option value='M'>Male</option>";
                                        } else {
                                            echo "<option value='M'>Male</option>";
                                            echo "<option value='F'>Female</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Date of Birth: </label>
                                    <input type="date" name="dob" class="form-control" value="<?= $dob ?>">
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
                                    <input type="date" class="form-control" name="yearappoint" value="<?= $yearAppointed ?>" />
                                </div>


                                <div class="form-group">
                                    <label>Contact Address: </label>
                                    <input type="text" class="form-control" placeholder="Eg: 000 0000" name="contact" value="<?= $contact ?>" />
                                </div>

                                <div class="form-group">
                                    <input type="submit" value="Update Record" class="btn btn-dark" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>