<?php

use App\Core\Helper;
use App\Extra\Assessment\WorkshopAssessment;
use App\Helper\DateLib;
use App\Notifiable\Components\Components;
use App\School\Student;

include_once "./pages/header.pages.php";

$dlt = ($_GET['dlt'] ?? "");
if ($dlt) {
    WorkshopAssessment::delete($dlt);
    Helper::to("manage-assessments.php?page=$page");
}

?>
<?php include_once "./pages/navbar.pages.php"; ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="white-box">
            <?= Components::header("Assessments Manager", "h3", "center"); ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">Title</th>
                            <th class="text-center">Details</th>
                            <th class="text-center">Submitted</th>
                            <th class="text-center">Due Date</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Edit</th>
                            <th class="text-center">Delete</th>
                            <th class="text-center">Notify</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $assessments = WorkshopAssessment::getAssessments($schoolId, $sms_userId, $page, DEFAULT_RECORD_PER_PAGE);

                        if ($assessments != NULL) {
                            $dateLib = new DateLib();
                            foreach ($assessments as $assessment) {
                                $isValid = Helper::isAssessmentValid($assessment->created_date, $assessment->id);
                                echo "<tr class='text-center'>";
                                echo "<td>" . wordwrap($assessment->title, 10, "<br>") . "</td>";
                                echo "<td><a class='btn btn-info' href='assessment_details.php?id=" . $assessment->id . "' target='_blank'>View More</a></td>";
                                echo "<td>" . $assessment->submitted . " out of " . Student::getNumStudents($assessment->class, $assessment->year) . "</td>";
                                $due = strtotime($assessment->due);
                                $due = date("dS F, Y", $due);
                                echo "<td>" . $dateLib->getDays($assessment->due) . "</td>";
                                echo "<td>" . ($isValid ? "Valid" : "Expired") . "</td>";
                                if ($isValid) {
                                    echo "<td><a class='btn btn-primary' href='assessment_edit.php?id=" . $assessment->id . "' target='_blank'>Edit</a></td>";
                                } else {
                                    echo "<td class='text-center'>...</td>";
                                }
                                echo "<td><a class='btn btn-danger delete' href='?dlt=" . $assessment->id . "&page=$page'>Trash</a></td>";
                                if ($isValid) {
                                    echo "<td><a class='btn btn-success' href='notify.php?id=" . $assessment->id . "' target='_blank'>Notify</a></td>";
                                } else {
                                    echo "<td class='text-center'>...</td>";
                                }

                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='8' class='text-center'>Nothing here...</td></tr>";
                        }

                        ?>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-12 mx-auto my-3">
                <?php if ($page > 0) : ?>
                    <?php
                    if ($page != 0) :
                    ?>
                        <a href="?page=0">First</a> |
                    <?php
                    endif;
                    ?>
                    <a href="?page=<?= $page - 1; ?>">Prev &laquo;</a>
                <?php else : ?>
                    Prev
                <?php endif; ?>
                |
                <a href="?page=<?= $page + 1; ?>">Next &raquo;</a>
            </div>
        </div>
    </div>
</div>


<?php include_once "./pages/footer.pages.php"; ?>
<script src="../myschool/assets/custom/script.js"></script>