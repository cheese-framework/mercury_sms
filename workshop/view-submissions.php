<?php

use App\Core\Helper;
use App\School\Student;

include_once "../moodle/classes/Assessment.php";

include_once "./pages/header.pages.php";

$id = $_GET['id'] ?? "";



?>
<?php include_once "./pages/navbar.pages.php"; ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Class</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $submissions = Assessment::getSubmissions($id, 0, DEFAULT_RECORD_PER_PAGE);

                    if ($submissions) {
                        foreach ($submissions as $submission) {
                            if ($submission->has_been_graded == 0) {
                                echo "<tr>";
                                echo "<td>" . Student::getFullName($submission->student) . "</td>";
                                echo "<td>" . Helper::classEncode($submission->class) . "</td>";
                                echo "<td><a href='assess.php?id={$submission->assessment}&student={$submission->student}&term={$submission->term}&subject={$submission->subject}&year={$submission->year}' class='btn btn-primary' >Assess</a></td>";
                                echo "</tr>";
                            }
                        }
                    }

                    ?>
                </tbody>
            </table>
        </div>

    </div>
</div>


<?php include_once "./pages/footer.pages.php"; ?>