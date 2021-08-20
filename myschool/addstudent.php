<?php

use App\Auth\Auth;
use App\Core\Helper;
use App\Database\Database;
use App\School\AcademicYear;
use App\School\Student;

include_once './includes/header.php';

if (!Helper::hasBeenAssignedAClass($sms_userId, $schoolId, $sms_role)) {
    Helper::showNotPermittedPage();
}

$name = "";
$dob = "";
$address = "";
$phone = "";
$admission = "";
$parphone = "";
$emergency = "";
$illness = "";
$paremail = "";
$email = "";
$error = [];
$success = "";
$admissionNumber = strtoupper(substr(Helper::getSchoolName($schoolId), 0, 2) . "-" . substr(md5(Auth::generateToken()), 0, 10));

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = $_POST['fullname'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $admission = $_POST['admissionNumber'];
    $class = (isset($_POST['class']) ? $_POST['class'] : "");
    $blood = (isset($_POST['blood']) ? $_POST['blood'] : "");
    $parphone = $_POST['parphone'];
    $emergency = $_POST['emergcon'];
    $illness = $_POST['illness'];
    $paremail = $_POST['paremail'];
    $email = (isset($_POST['email']) ? $_POST['email'] : NULL);
    $password = NULL;

    $year = AcademicYear::getCurrentYearId($schoolId);

    if (Helper::isUsingOnlineAssessment($schoolId)) {
        if (Helper::isEmpty($email)) {
            $error[] = "The school has opted for online assessment, therefore the student's email is required for continuation of processing.";
        }
        $password = password_hash("1234", PASSWORD_DEFAULT);
    }

    if (!Helper::isEmpty($name, $class, $gender)) {
        if ($year == "") {
            $error[] = "Academic year is not set";
        } else {
            // get current class capacity
            $classCapacity = Helper::getClassCount($class, $year, $schoolId);
            if (Helper::isActivated($schoolId)) {
                if ($classCapacity < MAX_STUDENT_PER_CLASS_ACTIVATED) {
                    if (empty($error)) {
                        try {
                            $bool = Student::addStudent($name, $class, $year, $gender, $dob, $phone, $address, $admission, $blood, $parphone, $emergency, $illness, $schoolId, $paremail, $email, $password);
                            if ($bool) {
                                $success = $name . " was added successfully";
                                $name = "";
                                $dob = "";
                                $address = "";
                                $phone = "";
                                $admission = "";
                                $parphone = "";
                                $emergency = "";
                                $illness = "";
                                $paremail = "";
                                // Helper::to("addstudent.php");
                            } else {
                                $error[] = "Something went wrong";
                            }
                        } catch (Exception $ex) {
                            $error[] = $ex->getMessage();
                        }
                    }
                } else {
                    if ($sms_role == $ADMIN) {
                        $error[] = "You have exceeded the maximum capacity of " . MAX_STUDENT_PER_CLASS_ACTIVATED . " students per class.<br>Try creating a subsidiary of the selected class";
                    } else {
                        $error[] = "You have exceeded the maximum capacity of " . MAX_STUDENT_PER_CLASS_ACTIVATED . " students per class.";
                    }
                }
            } else {
                if ($classCapacity < MAX_STUDENT_PER_CLASS_UNACTIVATED) {
                    if (empty($error)) {
                        try {
                            $bool = Student::addStudent($name, $class, $year, $gender, $dob, $phone, $address, $admission, $blood, $parphone, $emergency, $illness, $schoolId, $paremail, $email, $password);
                            if ($bool) {
                                $success = $name . " was added successfully";
                                $name = "";
                                $dob = "";
                                $address = "";
                                $phone = "";
                                $admission = "";
                                $parphone = "";
                                $emergency = "";
                                $illness = "";
                                $paremail = "";
                                $email = "";
                            } else {
                                $error[] = "Something went wrong";
                            }
                        } catch (Exception $ex) {
                            $error[] = $ex->getMessage();
                        }
                    }
                } else {
                    if ($sms_role == $ADMIN) {
                        $error[] = "You have exceeded the maximum capacity of " . MAX_STUDENT_PER_CLASS_UNACTIVATED . " students per class.<br>Consider activating your account to get a max of " . MAX_STUDENT_PER_CLASS_ACTIVATED . " students per class";
                    } else {
                        $error[] = "You have exceeded the maximum capacity of " . MAX_STUDENT_PER_CLASS_UNACTIVATED . " students per class.";
                    }
                }
            }
        }
    } else {
        $error[] = "Please fill the required fields";
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
                <p class="text-info">All the fields marked * are required</p>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
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

                            if ($success != "") {
                                echo "<div class='alert alert-success'>$success</div>";
                            }
                            ?>
                            <form class="form-sample" action="" method="POST" autocomplete="OFF">
                                <p class="card-description">Students Admission</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Full
                                                name *</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" placeholder="Full name" name="fullname" value="<?= $name; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Date
                                                of Birth</label>
                                            <div class="col-sm-9">
                                                <input type="date" class="form-control" name="dob" value="<?= $dob; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Gender
                                                *</label>
                                            <div class="col-sm-9">
                                                <select class="form-control" name="gender">
                                                    <option value="M">Male</option>
                                                    <option value="F">Female
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Address</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" placeholder="Address" name="address" value="<?= $address; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Phone</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="phone" placeholder="Ex: +2207213232" class="form-control" value="<?= $phone; ?>" maxlength="11" minlength="7" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Admission
                                                ID</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="admissionNumber" placeholder="Admission Number" class="form-control" value="<?= ($admission == "") ? $admissionNumber : $admission; ?>" />
                                            </div>

                                        </div>
                                    </div>
                                    <?php if (Helper::isUsingOnlineAssessment($schoolId)) : ?>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="email">Student Email *</label>
                                                <div class="col-sm-9">
                                                    <input type="email" class="form-control" placeholder="Student E-mail" name="email" value="<?= $email; ?>" id="email" />
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <p class="card-description">School Details and
                                    Medical History</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Class
                                                *</label>
                                            <div class="col-sm-9">
                                                <select class="form-control" name="class">
                                                    <?php
                                                    if ($sms_role == $ADMIN) {
                                                        try {
                                                            $data =
                                                                Helper::loadClass($schoolId);
                                                            if ($data != null) {
                                                                foreach ($data as $d) {
                                                                    echo "<option value='$d->classId'>$d->className</option>";
                                                                }
                                                            }
                                                        } catch (Exception $ex) {
                                                        }
                                                    } else {
                                                        $db = Database::getInstance();
                                                        $db->query("SELECT classTeacher,classId FROM classes");
                                                        $result = $db->resultset();
                                                        $classList = [];
                                                        if ($result != null) {
                                                            foreach ($result as $res) {
                                                                $list = explode(",", $res->classTeacher);
                                                                foreach ($list as $l) {
                                                                    if ($l == $sms_userId) {
                                                                        $classList[] = $res->classId;
                                                                    }
                                                                }
                                                            }
                                                        } else {
                                                            $classList = [];
                                                        }

                                                        if ($classList != null) {
                                                            foreach ($classList as $cls) {
                                                                echo "<option value='$cls'>" . Helper::classEncode($cls) . "</option>";
                                                            }
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Blood
                                                Group</label>
                                            <div class="col-sm-9">
                                                <select name="blood" class="form-control">

                                                    <option value=""></option>
                                                    <option value="O+">O+</option>
                                                    <option value="A+">A+</option>
                                                    <option value="B+">B+</option>
                                                    <option value="AB+">AB+</option>
                                                    <option value="O-">O-</option>
                                                    <option value="A-">A-</option>
                                                    <option value="B-">B-</option>
                                                    <option value="AB-">AB-</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Parent's
                                                Phone</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" placeholder="Phone" name="parphone" value="<?= $parphone; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Emergency
                                                Contact</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="emergcon" placeholder="Emergency Contact" value="<?= $emergency; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Medical
                                                Disability</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" placeholder="Underlying Illness" name="illness" value="<?= $illness; ?>" />
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Parent's E-mail<br><i class='text-info'>(Must be valid and correspondent)</i></label>
                                            <div class="col-sm-9">
                                                <input type="email" class="form-control" placeholder="Parent/Guardian E-mail" name="paremail" value="<?= $paremail; ?>" />
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="form-group">
                                    <input type="submit" value="Add Student" class="btn btn-facebook" />
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include_once "includes/footer.php"; ?>