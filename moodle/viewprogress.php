<?php

use App\Core\Helper;
use App\School\Subject;

include_once "./pages/header.pages.php"; ?>
<!-- ============================================================== -->
<?php include_once "./pages/navbar.pages.php"; ?>
<!-- ============================================================== -->
<div class="container-fluid">
    <div class="row justify-content-center">
        <?php
        $subject_id = $_GET['id'] ?? NULL;
        if (!$subject_id) {
            Helper::to("dashboard.php");
        }

        $subject = Subject::getSubject($subject_id, $schoolId);

        if (!$subject) {
            Helper::to("dashboard.php");
        }

        ?>

        <div class="col-lg-9 mx-auto my-3">
            <div class="card">
                <div class="card-body">
                    <form action="view.php" method="GET">
                        <div class="form-group">
                            <label for="subject">Subject</label>
                            <select name="subject" id="subject" class="form-control">
                                <option value="<?= $subject->subjectId; ?>"><?= $subject->subject; ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="term">Term</label>
                            <select name="term" id="term" class="form-control">
                                <option value="term-1">Term 1</option>
                                <option value="term-2">Term 2</option>
                                <option value="term-3">Term 3</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <input type="submit" value="View" class="btn btn-success">
                        </div>
                    </form>
                </div>
            </div>
        </div>



    </div>
</div>
<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->


<?php include_once "./pages/footer.pages.php"; ?>