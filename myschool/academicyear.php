<?php

use App\Core\Helper;
use App\School\AcademicYear;

include_once './includes/header.php';

if ($sms_role != "Super-Admin") {
    Helper::to("index.php");
}

$start = "";
$end = "";
$error = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $start = $_POST['start'];
    $end = $_POST['end'];
    $isCurrent = (isset($_POST['isCurrent']) ? true : false);
    if (!Helper::isEmpty($start, $end)) {
        try {
            $acd = new AcademicYear();
            $acd->setAcademicYear($start, $end);
            $bool = AcademicYear::addAcademicYear($acd, $start, $end, $schoolId, $isCurrent);
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
                <div class="col-lg-5">
                    <h2>Add Academic Year</h2>
                    <p class="text-info text-small"><b>Note:</b> Please note that you cannot delete an academic year once it
                        has been created.</p>
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
                                    <input type="number" name="start" class="form-control" placeholder="Start of Academic Year. Eg: 2020" value="<?= $start ?>" id="start" />
                                </div>

                                <div class="form-group">
                                    <label for="end">* End</label>
                                    <input type="number" name="end" class="form-control" placeholder="End of Academic Year. Eg: 2021" value="<?= $end ?>" id="end" />
                                </div>

                                <div class="form-group">
                                    <label for="isCurrent">Is Current? </label>
                                    <input type="checkbox" name="isCurrent" id="isCurrent">
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="Add Academic Year" class="btn btn-dark" />
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
                <div class="col-lg-7">
                    <h2>View Academic Years</h2>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-striped dt">
                            <thead>
                                <tr>
                                    <!-- <th>ID</th> -->
                                    <th>Academic Year</th>
                                    <th>Is Active</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $data = AcademicYear::loadAcademicYears($schoolId);
                                    if (!empty($data)) {
                                        $currentDate = date("Y");
                                        $currentDate = intval($currentDate);
                                        foreach ($data as $d) {
                                            $current = ($d->isCurrent == 1) ? "True" : "False";
                                            $temp = explode("-", $d->academicYear);
                                            $endOfYear = intval(end($temp));

                                            echo "<tr>";
                                            echo "<td>$d->academicYear</td>";
                                            echo "<td>$current</td>";
                                            if ($currentDate <= $endOfYear) {
                                                echo "<td><a class='btn btn-behance' href='editacads.php?id=$d->academicYearId'>Edit</a></td>";
                                            } else {
                                                echo "<td></td>";
                                            }
                                            echo "</tr>";
                                        }
                                    }
                                } catch (Exception $ex) {
                                    echo "<tr>";
                                    $msg = $ex->getMessage();
                                    echo "<td colspan=3 class='text-center'>$msg</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

        <?php include_once "includes/footer.php"; ?>