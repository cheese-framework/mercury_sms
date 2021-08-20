<?php

use App\Core\Helper;
use App\School\AcademicYear;
use App\School\Student;
use App\School\Subject;

include_once "includes/header.php";

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

if ((isset($_GET['year']) && $_GET['year'] != "") &&
    (isset($_GET['term']) && $_GET['term'] != "") &&
    (isset($_GET['studentId']) && $_GET['studentId'] != "") &&
    (isset($_GET['class']) && $_GET['class'] != "")
) {
    $year = $_GET['year'];
    $term = $_GET['term'];
    $id = $_GET['studentId'];
    $class = $_GET['class'];
} else {
    Helper::showErrorPage();
}

$isClassTeacher = Helper::isATeacherInClass($class, $sms_userId, $schoolId);

?>
<div class="container-scroller">

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div id="wait" class="text-center">
                <h2><i class="mdi mdi-loading mdi-spin" style="font-size: 60px;"></i></h2>
            </div>
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 mx-auto" id="panel" style="display: none;">


                    <div class="card my-3 p-3">
                        <h5>School: <?= Helper::getSchoolName($schoolId); ?></h5>
                        <h5>Student: <?= Student::getFullName($id) ?></h5>
                        <h5>Class: <?= Helper::classEncode($class) ?></h5>
                        <h5>Term: <?= ucfirst($term) ?></h5>
                        <h5>Academic year: <?= AcademicYear::getAcademicYearById($year); ?></h5>
                        <?php


                        try {
                            $data = Subject::returnSubject($class, $schoolId);
                            $studentsSub = Student::getMySubjects($id);
                            $subjects = [];
                            if ($studentsSub) {
                                foreach ($studentsSub as $sub) {
                                    $temp = Subject::getSubject($sub, $schoolId);
                                    if ($temp) {
                                        $subjects[] = $temp;
                                    }
                                }

                                if ($subjects) {
                                    $data = array_merge_recursive($data, $subjects);
                                }
                            }
                            if ($data != null) {
                                echo "<div class='col-lg-12 mx-auto mb-3 mt-4 table-responsive'>";
                                echo "<table class='table table-hover table-striped table-bordered'>";
                                echo "<thead>";
                                echo "<th><b>Subject</b></th>";
                                echo "<div class='table-responsive'>";
                                echo "<th class='text-center'><b>Continuous Assessment</b></th>";
                                echo "<th>Exam</th>";
                                echo "<th><b>Total %</b></th>";
                                echo "<th><b>Remark</b></th>";
                                echo "</thead>";
                                echo "<tbody>";
                                $grandTotal = 0;
                                $subCount = 0;
                                $grandTotalAlt = 0;
                                foreach ($data as $d) {

                                    $record = Helper::getResult(
                                        $year,
                                        $term,
                                        $d->subjectId,
                                        $id,
                                        $schoolId
                                    );
                                    $resId = Helper::$resId;
                                    $total = 0;
                                    echo "<tr>";
                                    echo "<td>" . $d->subject . "</td>";
                                    if ($record != null) {
                                        $subCount += 100;
                                        echo "<td>";
                                        echo "<table class='table table-bordered table-striped table-hover'>
                                    <thead>";
                                        $gradingSheet = Helper::getGradeTypes($d->subjectId);
                                        $explodedSheet = explode(",", $gradingSheet);
                                        foreach ($explodedSheet as $rec) {
                                            echo "<th class='text-center'><b>" .
                                                $rec . "</b></th>";
                                        }
                                        echo "</thead>
                                            <tbody><tr class='" . (!$isClassTeacher && !in_array($d->subjectId, $mySubjects) ? 'make-blur' : '') . "'>";
                                        foreach ($record[0] as $r) {
                                            if (in_array($r[0], $explodedSheet)) {
                                                echo "<th class='text-center'><b>" .
                                                    floatval($r[1]) . "</b></th>";
                                                $total += $r[1];
                                            }
                                        }
                                        $total += $record[1];
                                        $total += number_format(floatval($record[2]), 1);
                                        echo "</tr>
                                            </tbody>
                                        </table>";
                                        echo "</td>";
                                        $final = Helper::getFinalMark(
                                            $resId
                                        );
                                        $originalGrade = round(
                                            ($total / $final) * 100,
                                            2
                                        );
                                        $grandTotalAlt += $originalGrade;
                                        echo "<td class='" . (!$isClassTeacher && !in_array($d->subjectId, $mySubjects) ? 'make-blur' : '') . "'>" . floatval($record[1]) . "</td>";
                                        echo "<td class='" . (!$isClassTeacher && !in_array($d->subjectId, $mySubjects) ? 'make-blur' : '') . "'><b>" . $originalGrade .
                                            "</b></td>";
                                        if ($record[1] != "" || $record[1] != 0) {
                                            echo "<td>" .
                                                Helper::getRemark(
                                                    $originalGrade,
                                                    $schoolId
                                                ) .
                                                "</td>";
                                        } else {
                                            echo "<td></td>";
                                        }
                                    } else {
                                        echo "<td class='text-center'><b>...</b></td>";
                                    }

                                    echo "</tr>";
                                }
                                if ($subCount > 0) {
                                    $grandTotalAlt = round($grandTotalAlt);
                                    $avr = number_format(
                                        ($grandTotalAlt / $subCount) * 100,
                                        2
                                    );
                                } else {
                                    $avr = 0;
                                }
                                echo "<tr>";
                                echo "<td><b>Total:</b></td>";
                                echo "<td class='text-center'><b><i>...</i></b></td>";
                                echo "<td class='text-center'><b><i>...</i></b></td>";
                                echo "<td class='text-center'><b> ~ $grandTotalAlt ~ </b></td>";
                                echo "<td><b>Average: $avr%</b></td>";
                                echo "<td></td>";
                                echo "</tr>";
                                echo "</tbody>";

                                echo "</table>";
                                echo "</div>";
                                echo "</div>";
                            }
                        } catch (Exception $ex) {
                            echo "<div class='alert alert-warning text-center'>" .
                                $ex->getMessage() . "</div>";
                        }


                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php

        // include_once './includes/footer.php';
        ?>


        <script>
            const wait = document.getElementById("wait");
            const panel = document.getElementById("panel");

            function printWindow() {
                window.print();
            }
            setTimeout(() => {
                wait.style.display = "none";
                panel.style.display = "block";
                printWindow();
            }, 2000);
        </script>