<?php

use App\Core\Helper;

include_once './includes/header.php';

$error = [];
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $min = $_POST['min'];
    $max = $_POST['max'];
    $remark = $_POST['remark'];
    if (!Helper::isEmpty($min, $max, $remark)) {
        try {
            Helper::createRemark($min, $max, $remark, $schoolId);
            Helper::to("remarks.php");
        } catch (Exception $ex) {
            $error[] = $ex->getMessage();
        }
    } else {
        $error[] = "All fields are needed";
    }
}
?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <h1>Grading Scale</h1>
            <div class="row my-3">
                <div class="col-lg-6">
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
                            <form action="" method="POST" autocomplete="off">
                                <div class="form-group">
                                    <label>Minimum: </label>
                                    <input type="number" min="0" max="100" class="form-control" name="min" placeholder="Minimum Number" />
                                </div>
                                <div class="form-group">
                                    <label>Maximum: </label>
                                    <input type="number" min="0" max="100" class="form-control" name="max" placeholder="Maximum Number" />
                                </div>
                                <div class="form-group">
                                    <label>Remark: </label>
                                    <input type="text" class="form-control" name="remark" placeholder="Eg: Excellent" />
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-outline-facebook" value="Create remark" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 mt-4">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>MARKS</th>
                                    <th>INTERPRETATION</th>
                                    <th>Update</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $data = Helper::getRemarks($schoolId);
                                if ($data != "") {
                                    foreach ($data as $d) {
                                        echo "<tr>";
                                        echo "<td>$d->min% &nbsp;&nbsp;-&nbsp;&nbsp; $d->max%</td>";
                                        echo "<td>$d->remark</td>";
                                        echo "<td><a href='updateremark.php?id=$d->remarkId' class='btn btn-primary'>Update</a></td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan=4 class='text-center'>No record</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>