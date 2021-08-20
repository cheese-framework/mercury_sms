<?php

use App\Core\Helper;
use App\School\AcademicYear;
use App\School\Student;

include_once './includes/header.php';

$error = [];

if (isset($_GET['studentId']) && $_GET['studentId'] != "") {
    $id = $_GET['studentId'];
} else {
    Helper::showErrorPage();
}
?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <?php
            try {
                $data = Student::getStudentDetails($id, $schoolId);
                $temp = strtotime($data->dob);
                if ($data->dob == "") {
                    $dob = "";
                } else {
                    $dob = date("dS F, Y", $temp);
                }
                $gender = ($data->gender == "F") ? "Female" : "Male";
                echo "<p><b>Fullname: </b>$data->fullname</p>";
                echo "<p><b>Class: </b>" . Helper::classEncode($data->class) . "</p>";
                echo "<p><b>Academic Year: </b>" . AcademicYear::getAcademicYearById($data->academicYear) . "</p>";
                echo "<p><b>Gender: </b>$gender</p>";
                echo "<p><b>Date of Birth: </b>$dob</p>";
                echo "<p><b>Phone: </b>$data->phone</p>";
                if (Helper::isUsingOnlineAssessment($schoolId)) {
                    echo "<p><b>Email: </b>$data->email</p>";
                }
                echo "<p><b>Address: </b>$data->address</p>";
                echo "<p><b>Admission No: </b>$data->admissionno</p>";
                echo "<p><b>Blood Group: </b>$data->bloodgroup</p>";
                echo "<p><b>Parent's Phone: </b>$data->parphone</p>";
                echo "<p><b>Emergency Contact: </b>$data->emergcon</p>";
                echo "<p><b>Underlying Illness: </b>$data->medical</p>";
                echo "<a href='updatestudent.php?id=$data->studentId' class='btn btn-behance'>Update</a>";
            } catch (Exception $ex) {
                echo "<h2 class='text-center'>" . $ex->getMessage() . "</h2>";
            }
            ?>
        </div>
        <?php include_once './includes/footer.php'; ?>