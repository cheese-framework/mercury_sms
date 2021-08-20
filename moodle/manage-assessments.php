<?php

use App\Core\Helper;
use App\Helper\DateLib;
use App\Notifiable\Components\Components;

include_once "./pages/header.pages.php"; ?>
<!-- ============================================================== -->
<!-- End Topbar header -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
<?php include_once "./pages/navbar.pages.php"; ?>
<!-- ============================================================== -->
<!-- End Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- Container fluid  -->
<!-- ============================================================== -->
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Three charts -->
    <!-- ============================================================== -->
    <div class="row justify-content-center">
        <div class="white-box">
            <?= Components::header("My Assessments", "h3", "center"); ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">Title</th>
                            <th class="text-center">Details</th>
                            <th class="text-center">Due Date</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php

                        $assessments = Assessment::getAssessmentForStudent($schoolId, $s_id, $class, $page, DEFAULT_RECORD_PER_PAGE);

                        if ($assessments != NULL) {
                            $dateLib = new DateLib();
                            foreach ($assessments as $assessment) {
                                $isValid = Helper::isAssessmentValid($assessment->created_date, $assessment->id);
                                echo "<tr class='text-center'>";
                                echo "<td>" . wordwrap($assessment->title, 10, "<br>") . "</td>";
                                echo "<td><a class='btn btn-info' href='assessment_details.php?id=" . $assessment->id . "'>View More</a></td>";

                                $due = strtotime($assessment->due);
                                $due = date("dS F, Y", $due);
                                echo "<td>" . $dateLib->getDays($assessment->due) . "</td>";
                                echo "<td>" . ($isValid ? "Valid" : "Expired") . "</td>";


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
    <!-- ============================================================== -->
    <!-- End Container fluid  -->
    <!-- ============================================================== -->


    <?php include_once "./pages/footer.pages.php"; ?>