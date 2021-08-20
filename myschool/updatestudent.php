<?php

use App\Auth\Auth;
use App\Core\Helper;
use App\Database\Database;
use App\School\Student;

include_once './includes/header.php';
if (isset($_GET['id']) && $_GET['id'] != "") {
    try {
        $id = $_GET['id'];
        $studentData = Student::getStudentDetails($id, $schoolId);
    } catch (Exception $ex) {
        Helper::showErrorPage();
    }
} else {
    Helper::showErrorPage();
}
$name = "";
$dob = "";
$address = "";
$phone = "";
$admission = "";
$parphone = "";
$emergency = "";
$illness = "";
$error = [];

if (trim($studentData->admissionno) == "") {
    $studentData->admissionno = strtoupper(substr(Helper::getSchoolName($schoolId), 0, 2) . "-" . substr(Auth::generateToken(), 0, 10));
}


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

    if (Helper::isUsingOnlineAssessment($schoolId)) {
        if (Helper::isEmpty($email)) {
            $error[] = "The school has opted for online assessment, therefore the student's email is required for continuation of processing.";
        }
    }

    if (!Helper::isEmpty($name, $class, $gender)) {

        if (empty($error)) {
            try {
                $bool = Student::updateStudent($name, $class, $gender, $dob, $phone, $address, $admission, $blood, $parphone, $emergency, $illness, $id, $paremail, $email);
                if ($bool) {
                    Helper::to("viewstudent.php?studentId=$id");
                } else {
                    $error[] = "Something went wrong<br><small>Did you change anything?</small>";
                }
            } catch (Exception $ex) {
                $error[] = $ex->getMessage();
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
                            ?>
                            <form class="form-sample" action="" method="POST" autocomplete="OFF">
                                <p class="card-description">Students Admission</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Full name *</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" placeholder="Full name" name="fullname" value="<?= $studentData->fullname; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Date of Birth</label>
                                            <div class="col-sm-9">
                                                <input type="date" class="form-control" name="dob" value="<?= $studentData->dob; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Gender *</label>
                                            <div class="col-sm-9">
                                                <select class="form-control" name="gender">
                                                    <?php
                                                    if ($studentData->gender == "M") {
                                                        echo "<option value='M'>Male</option>";
                                                        echo "<option value='F'>Female</option>";
                                                    } else {
                                                        echo "<option value='F'>Female</option>";
                                                        echo "<option value='M'>Male</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Address</label>
                                            <div class="col-sm-9">
                                                <input class="form-control" placeholder="Address" name="address" value="<?= $studentData->address; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Phone</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="phone" placeholder="Ex: +2207213232" class="form-control" value="<?= $studentData->phone; ?>" maxlength="11" minlength="7" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Admission Number</label>
                                            <div class="col-sm-9">
                                                <input type="text" name="admissionNumber" placeholder="Admission Number" class="form-control" value="<?= $studentData->admissionno; ?>" />
                                            </div>

                                        </div>
                                    </div>

                                    <?php if (Helper::isUsingOnlineAssessment($schoolId)) : ?>
                                        <div class="col-md-6">
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label" for="email">Student Email *</label>
                                                <div class="col-sm-9">
                                                    <input type="email" class="form-control" placeholder="Student E-mail" name="email" value="<?= $studentData->email; ?>" id="email" />
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <p class="card-description">School Details and Medical History</p>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Class *</label>
                                            <div class="col-sm-9">
                                                <select class="form-control" name="class">
                                                    <?php
                                                    echo "<option value='$studentData->class'> (Current) - " . Helper::classEncode($studentData->class) . "</option>";
                                                    if ($sms_role == $ADMIN) {
                                                        try {
                                                            $data = Helper::loadClass($schoolId);
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
                                            <label class="col-sm-3 col-form-label">Blood Group</label>
                                            <div class="col-sm-9">
                                                <select name="blood" class="form-control">
                                                    <?= "<option value='$studentData->bloodgroup'>$studentData->bloodgroup</option>"; ?>
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
                                            <label class="col-sm-3 col-form-label">Parent's Phone</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" placeholder="Phone" name="parphone" value="<?= $studentData->parphone; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Emergency Contact</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" name="emergcon" placeholder="Emergency Contact" value="<?= $studentData->emergcon; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Medical Disability</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" placeholder="Underlying Illness" name="illness" value="<?= $studentData->medical; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-sm-3 col-form-label">Parent's Email</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control" placeholder="Parent's or Guardian's E-mail Address" name="paremail" value="<?= $studentData->paremail; ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <input type="submit" value="Update Student" class="btn btn-facebook" />
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>


        <?php include_once './includes/footer.php'; ?>