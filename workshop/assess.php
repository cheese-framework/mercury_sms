<?php

use App\Core\Helper;
use App\Extra\Assessment\WorkshopAssessment;
use App\Queue\TempStudent;
use App\School\Subject;

include_once "./pages/header.pages.php";
include_once "../moodle/classes/Assessment.php";

$id = $_GET['id'] ?? "";
$studentId = $_GET['student'] ?? "";
$term = $_GET['term'] ?? "";
$subject = $_GET['subject'] ?? "";
$year = $_GET['year'] ?? "";

if (Helper::isEmpty($id, $studentId, $term, $subject, $year)) {
    Helper::to('dashboard.php');
}

$submittedData = Assessment::getSubmittedAssessment($id);
$assessmentWorkshop = new WorkshopAssessment();
$assessmentData = $assessmentWorkshop->getAssessment($id);

if (!$submittedData && !$assessmentData) {
}

// check if assessment has been graded
if ($submittedData->has_been_graded == 1) {
    Helper::to('dashboard.php');
}

$error = [];

// make assessment submission

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // grade
    $assignedGrade = $_POST['grade'] ?? "";
    if (is_numeric($assignedGrade)) {
        if ($assignedGrade <= $assessmentData->grade) {
            $assessmentList = Helper::getAssessmentGradeList($studentId, $subject, $term, $year, $schoolId);
            $tempList = $assessmentList;
            // get online assessment grade
            $onlineAssessmentGrade = Helper::getAssessmentMark($subject);
            if (!$onlineAssessmentGrade) {
                exit;
            }
            if ($assessmentList != NULL) {
                $assessmentList = explode(',', $assessmentList);
                $prevGrades = 0;
                $count = 0;
                foreach ($assessmentList as $list) {
                    $prevGrades += floatval($list);
                    $count++;
                }
                if ($prevGrades > 0) {
                    $prevGrades = ($prevGrades + $assignedGrade)  / ($count + 1);
                } else {
                    $prevGrades = $assignedGrade;
                }
                $newGrade = ($prevGrades / $assessmentData->grade) * $onlineAssessmentGrade;
            } else {
                // get the value from the grade
                $newGrade = ($assignedGrade / $assessmentData->grade) * $onlineAssessmentGrade;
            }

            if ($tempList) {

                $newAssessmentList = Helper::makeList($tempList . "," . $assignedGrade);
            } else {

                $newAssessmentList = $assignedGrade;
            }

            try {
                Helper::changeResultForAssessment($studentId, $year, $term, $subject, $newAssessmentList, $newGrade, $schoolId);
                Helper::markAsGraded($id, $studentId);
                // send notification
                $tempStudent = new TempStudent(['mail', 'twilio'], $studentId);
                $subjectName = Subject::getName($subject);
                $msg = "Assessment for " . $subjectName . " has been graded. You scored: " . $assignedGrade . ". New grade is {$newGrade}/" . $onlineAssessmentGrade;
                try {
                    $tempStudent->notify($msg, "Assessment Graded", DEFAULT_FULLNAME, DEFAULT_FROM);
                } catch (Exception $e) {
                }
                Helper::to("dashboard.php");
            } catch (Exception $e) {
                $error[] = $e->getMessage();
            }
        } else {
            $error[] = "Assigned grade cannot be more than " . $assessmentData->grade;
        }
    } else {
        $error[] = "Please specify a numeric value";
    }
}

?>
<?php include_once "./pages/navbar.pages.php";

?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="card col-lg-9 ">
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
                <h4 class="text-center">Assess Grade</h4>
                <p>Assessment grade stacked at: <b><?= $assessmentData->grade ?></b></p>
                <?php if ($submittedData->details) : ?>
                    <div class="card p-2" style="height: fit-content; max-height: 300px; overflow:auto">
                        <b>Submission: </b>
                        <?php
                        echo $submittedData->details;
                        ?>
                    </div>
                <?php endif; ?>
                <?php

                if ($submittedData->file) {
                    echo "<p>Submitted File: ";
                    echo  "<a href='./files/submissions/{$submittedData->file}' class='btn btn-primary'>View File</a></p>";
                }

                ?>


                <form action="" method="post" autocomplete="off">
                    <div class="form-group">
                        <label for="grade">Grade Assigned: </label>
                        <input type="text" name="grade" id="grade" class="form-control" value="<?= $assessmentData->grade ?>">
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Assess" name="assess" id="assess" class="btn btn-success">
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


<?php include_once "./pages/footer.pages.php"; ?>

<script>

</script>