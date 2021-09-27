<?php

use App\Core\Helper;
use App\School\AcademicYear;
use App\School\SMSParent;

include_once "./pages/header.pages.php"; ?>
<?php include_once "./pages/navbar.pages.php"; ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="row">
            <div class="col-md-12 col-lg-12 col-sm-12 col-xs-12">
                <div class="white-box">
                    <h3 class="text-center">My Wards</h3>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>School</th>
                                    <th>Class</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php

                                $myStudents = $smsParentObj->getMyStudents(AcademicYear::getCurrentYearId($gZoneSchool));
                                if ($myStudents) {
                                    foreach ($myStudents as $student) {
                                        echo "<tr>";
                                        echo "<td>{$student->fullname}</td>";
                                        echo "<td>" . Helper::getSchoolName($student->school) . "</td>";
                                        echo "<td>" . Helper::classEncode($student->class) . "</td>";
                                        echo "<td><a href='module.php?student_id={$student->studentId}' class='btn btn-primary'>View More</a></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' class='text-center'>No record here</td></tr>";
                                }

                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->


<?php include_once "./pages/footer.pages.php"; ?>