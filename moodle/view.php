<?php

use App\Core\Helper;
use App\Notifiable\Components\Components;
use App\School\AcademicYear;
use App\School\Subject;

include_once "./pages/header.pages.php";


$term = $_GET['term'];
$subject = $_GET['subject'];

if (!$term && !$subject) {
    Helper::to("dashboard.php");
}


?>
<!-- ============================================================== -->
<?php include_once "./pages/navbar.pages.php"; ?>
<!-- ============================================================== -->
<div class="container-fluid">
    <div class="row justify-content-center">

        <?php

        $myResult = Helper::getResult(AcademicYear::getCurrentYearId($schoolId), $term, $subject, $s_id, $schoolId);

        if ($myResult) {
            echo Components::header("Grade for " . Subject::getName($subject), "h4", "center");
        ?>
            <div class="table-responsive">
                <table class="table text-center table-sm table-hover table-striped">
                    <thead>
                        <tr>
                            <th>Grade Item</th>
                            <th>Grade</th>
                            <th>Feedback</th>
                            <th>Contribution to course total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $assessments = $myResult[0];
                        $exam = $myResult[1];
                        $courseTotal = Helper::getFinalMark(Helper::$resId);
                        $assess = $myResult[2];
                        if (!$assess) {
                            $assess = 0;
                        }
                        $total = $exam + $assess;

                        foreach ($assessments as $assessment) {
                            $gradeItem = $assessment[0];
                            $grade = $assessment[1];
                            $contribution = round(($grade / $courseTotal) * 100, 2);
                            $total += $grade;
                            echo "<tr>";
                            echo "<td>{$gradeItem}</td>";
                            echo "<td>{$grade}</td>";
                            echo "<td>...</td>";
                            echo "<td>$contribution%</td>";
                            echo "</tr>";
                        }


                        if (Helper::isUsingOnlineAssessment($schoolId)) {
                            $contribution = round(($assess / $courseTotal) * 100, 2);

                            echo "<tr>";
                            echo "<td>Assessment</td>";
                            echo "<td>" . number_format($assess, 2) . "</td>";
                            echo "<td>...</td>";
                            echo "<td>$contribution%</td>";
                            echo "</tr>";
                        }

                        $contribution = round(($exam / $courseTotal) * 100, 2);
                        echo "<tr>";
                        echo "<td>Exam</td>";
                        echo "<td>{$exam}</td>";
                        echo "<td>...</td>";
                        echo "<td>$contribution%</td>";
                        echo "</tr>";


                        $contribution = round(($total / $courseTotal) * 100, 2);

                        echo "<tr>";
                        echo "<td>Total</td>";
                        echo "<td>" . number_format($total, 2) . "</td>";
                        echo "<td>...</td>";
                        echo "<td>$contribution%</td>";
                        echo "</tr>";
                        ?>
                    </tbody>
                </table>
            </div>
        <?php
        } else {
            echo Components::header("Nothing to show here :(", "h1", "center");
        }

        ?>



    </div>
</div>
<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->


<?php include_once "./pages/footer.pages.php"; ?>