<?php

use App\Core\Helper;
use App\School\AcademicYear;

include_once './includes/header.php';

$start = "";
$end = "";
$error = [];

if (isset($_GET['id']) && $_GET['id']) {
    $id = $_GET['id'];
    $data = AcademicYear::getAcademicYear($schoolId, $id);
    if ($data != null) {
        $dbStart = $data->startYear;
        $dbEnd = $data->endYear;
        $dbCurrent = $data->isCurrent;
    } else {
        Helper::showErrorPage();
    }
} else {
    Helper::showErrorPage();
}


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $start = $_POST['start'];
    $end = $_POST['end'];
    $isCurrent = (isset($_POST['isCurrent']) ? true : false);
    if (!Helper::isEmpty($start, $end)) {
        try {
            $acd = new AcademicYear();
            $acd->setAcademicYear($start, $end);
            $bool = AcademicYear::updateAcademicYear($id, $acd, $start, $end, $schoolId, $isCurrent);
            if ($bool) {
                Helper::to("academicyear.php?page=" . $page);
            } else {
                $error[] = "Seems like the academic year is available already";
            }
        } catch (Exception $ex) {
            $error[] = $ex->getMessage();
        }
    } else {
        $error[] = "All fields are needed";
    }
}
?>
<div class="container-scroller">
    <!-- partial:partials/_sidebar.html -->
    <?php include_once './includes/navbar.php'; ?>
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <h2 class="text-center">Update Academic Year</h2>
                    <div class="card">
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
                            <form method="POST" action="" autocomplete="off">
                                <div class="form-group">
                                    <label for="start">* Start</label>
                                    <input type="number" name="start" class="form-control" placeholder="Start of Academic Year. Eg: 2020" value="<?= $dbStart ?>" id="start" />
                                </div>

                                <div class="form-group">
                                    <label for="end">* End</label>
                                    <input type="number" name="end" class="form-control" placeholder="End of Academic Year. Eg: 2021" value="<?= $dbEnd ?>" id="end" />
                                </div>

                                <div class="form-group">
                                    <label for="isCurrent">Is Current? </label>
                                    <input type="checkbox" name="isCurrent" id="isCurrent" <?php
                                                                                            if ($dbCurrent == 1) {
                                                                                                echo "checked";
                                                                                            }

                                                                                            ?>>
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="Update Academic Year" class="btn btn-dark" />
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <?php include_once "includes/footer.php"; ?>