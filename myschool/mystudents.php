<?php

use App\Core\Helper;
use App\Helper\DateLib;
use App\School\AcademicYear;
use App\School\Student;

include_once './includes/header.php';
if ((isset($_GET['classId']) && $_GET['classId'] != "") && (isset($_GET['year']) && $_GET['year'] != "")) {
    $classId = $_GET['classId'];
    $year = $_GET['year'];
} else {
    Helper::showErrorPage();
}
?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">

        <div class="modal" tabindex="-1" role="dialog" id="exampleModal">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Modal title</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Modal body text goes here.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="content-wrapper pb-0">
            <h1>Viewing Students of <?php echo Helper::classEncode($classId); ?></h1>
            <p class="text-info text-small">Please scroll left or right for full content view</p>
            <div class="table-responsive">
                <table class="table table-striped table-bordered table-hover dt">
                    <thead>
                        <tr>
                            <th>Full name</th>
                            <th>Class</th>
                            <th>Gender</th>
                            <th style="display: none;">Details</th>
                            <?php
                            if ($sms_role != $ADMIN) {
                                echo '<th class="text-center">Delete</th>';
                            }
                            ?>
                            <th class="text-center">Term 1</th>
                            <th class="text-center">Term 3</th>
                            <th class="text-center">Term 2</th>
                        </tr>
                    </thead>
                    <tbody id="table">
                        <?php
                        try {
                            $dateLib = new DateLib();
                            $yearAcad = AcademicYear::getAcademicYearId(
                                $year,
                                $schoolId
                            );
                            $studentsArray = Student::getMyStudents($classId, $yearAcad);
                            foreach ($studentsArray as $student) {
                                $sid = $student->getStudentId();
                                echo "<tr>";
                                echo "<td class='view-student'  data-toggle='modal' data-target='#exampleModal'><button class='btn btn-info btn-block'>" .
                                    $student->getStudentFullName() . "</button></td>";
                                echo "<td>" . Helper::classEncode($student->getStudentClass()) . "</td>";
                                echo "<td>" . $student->getStudentGender() . "</td>";
                                // student's details to be hidden
                                try {
                                    $data = Student::getStudentDetails($student->getStudentId(), $schoolId);
                                    $temp = strtotime($data->dob);
                                    if ($data->dob == "") {
                                        $dob = "";
                                    } else {
                                        $dob = date("dS F, Y", $temp);
                                    }
                                    $gender = ($data->gender == "F") ? "Female" : "Male";
                                    echo "<td style='display: none'>";
                                    echo "<div class='col-lg-12 mx-auto'>";
                                    echo "<p><b>Fullname: </b>$data->fullname</p>";
                                    echo "<p><b>Class: </b>" . Helper::classEncode($data->class) . "</p>";
                                    echo "<p><b>Academic Year: </b>" . AcademicYear::getAcademicYearById($data->academicYear) . "</p>";
                                    echo "<p><b>Gender: </b>$gender</p>";
                                    echo "<p><b>Date of Birth: </b>$dob</p>";
                                    echo "<p><b>Age: </b>" . strtoupper($dateLib->fullDateInterval($data->dob, date("Y-m-d"), "old")) . "</p>";
                                    echo "<p><b>Phone: </b>$data->phone</p>";
                                    if (Helper::isUsingOnlineAssessment($schoolId)) {
                                        echo "<p><b>Email: </b>$data->email</p>";
                                    }
                                    echo "<p><b>Address: </b>$data->address</p>";
                                    echo "<p><b>Admission ID: </b>$data->admissionno</p>";
                                    echo "<p><b>Blood Group: </b>$data->bloodgroup</p>";
                                    echo "<p><b>Parent's Phone: </b>$data->parphone</p>";
                                    echo "<p><b>Parent's E-mail: </b>$data->paremail</p>";
                                    echo "<p><b>Emergency Contact: </b>$data->emergcon</p>";
                                    echo "<p><b>Underlying Illness: </b>$data->medical</p>";
                                    echo "<a href='updatestudent.php?id=$data->studentId' class='btn btn-behance' target='_blank'>Update</a>";
                                    echo "</div>";
                                    echo "</td>";
                                } catch (Exception $ex) {
                                    echo "<td style='display:none'>No record found</td>";
                                }
                                if ($sms_role == $TEACHER) {
                                    echo "<td><a href='deletestudent.php?classId=$classId&year=$year&delstu=$sid&page=" . $page . "' class='btn btn-youtube delete'>Delete</a></td>";
                                }
                                echo "<td><a href='viewprogress.php?year=$yearAcad&term=term-1&studentId=$sid&class=$classId' target='_blank' class='btn btn-primary'>Term 1</a></td>";
                                echo "<td><a href='viewprogress.php?year=$yearAcad&term=term-2&studentId=$sid&class=$classId' target='_blank' class='btn btn-success'>Term 2</a></td>";
                                echo "<td><a href='viewprogress.php?year=$yearAcad&term=term-3&studentId=$sid&class=$classId' target='_blank' class='btn btn-github'>Term 3</a></td>";
                                echo "</tr>";
                            }
                        } catch (Exception $ex) {
                            echo "<tr>";
                            echo "<td class='text-center' colspan=6>" . $ex->getMessage() . "</td>";
                            echo "</tr>";
                        }
                        ?>
                    </tbody>
                </table>


            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>
        <script>
            $(".view-student").click(function() {
                let details = this.parentElement.children[3].innerHTML;
                $(".modal-title").text("More details");
                $(".modal-body").html(details);
                $('#myModal').on('shown.bs.modal', function() {
                    $('#myInput').trigger('focus')
                });
            });
        </script>