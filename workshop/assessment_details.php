<?php

use App\Core\Helper;
use App\Extra\Assessment\WorkshopAssessment;
use App\Notifiable\Components\Components;
use App\School\Student;
use App\School\Subject;

include_once "./pages/header.pages.php";

$id = $_GET['id'] ?? "";

if (!$id) {
    Helper::to("dashboard.php");
}

$workshopAssessment = new WorkshopAssessment();
$data = $workshopAssessment->getAssessment($id);

if (!$data) {
    Helper::to("manage-assessments.php?page=$page");
}

?>
<?php include_once "./pages/navbar.pages.php"; ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="mx-auto row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <?= Components::header($data->title, "h3"); ?>
                    </div>
                    <?php
                    if ($data->details) {
                        echo "<div class=\"card-body\" style=\"height:fit-content; max-height:250px;overflow-y:auto\" " . Components::body($data->details) . " 
                 </div>";
                    }
                    ?>
                    <div class="card-footer">
                        <?php
                        if ($data->file) {
                            $ext = explode(".", $data->file);
                            $extension = strtolower(end($ext));
                            if ($extension == "pdf") {
                                $class = "fas fa-file-pdf";
                            } else if ($extension == "html") {
                                $class = "fab fa-html5";
                            } else {
                                $class = "fas fa-file-word";
                            }
                            echo "<a href='files/" . $data->file . "' class='btn btn-success' target='_blank'>View <i class='$class'></i></a>";
                            echo "&nbsp;&nbsp;&nbsp;";
                            echo "<a href='files/" . $data->file . "' class='btn btn-primary' download>Download <i class='$class'></i></a>";
                        }

                        ?>
                    </div>
                </div>
                <?= Components::header("Due: " . date("dS F, Y", strtotime($data->due)), "h4"); ?>

            </div>
            <div class="col-lg-5">
                <?= Components::header("Submitted: " . $data->submitted . " out of " . Student::getNumStudents($data->class, $data->year), "h4"); ?>
                <?= Components::header("Subject: " . Subject::getSubject($data->subject, $schoolId)->subject, "h4"); ?>
                <?= Components::header("Status: " . (Helper::isAssessmentValid($data->created_date, $data->id) ? "Valid" : "Expired"), "h4"); ?>
                <?= Components::header("Grade Mark: " . $data->grade, "h4"); ?>
                <?= Components::header("Class: " . Helper::classEncode($data->class), "h4"); ?>
                <?= Components::link("view-submissions.php?id=" . $data->id, "View Submissions", Components::BTN_SUCCESS) ?>
            </div>
        </div>
    </div>
</div>


<?php include_once "./pages/footer.pages.php"; ?>