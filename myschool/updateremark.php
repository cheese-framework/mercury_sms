<?php

use App\Core\Helper;

include_once './includes/header.php';

if (isset($_GET['id']) && $_GET['id'] != "") {
    $rid = $_GET['id'];
    $data = Helper::getRemarkId($rid);
} else {
    Helper::to("remarks.php");
}

if (isset($_GET['deleteRemark'])) {
    Helper::deleteRemark($_GET['deleteRemark'], $schoolId);
    Helper::to("remarks.php");
}


$error = [];
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $min = $_POST['min'];
    $max = $_POST['max'];
    $remark = $_POST['remark'];
    if (!Helper::isEmpty($min, $max, $remark)) {
        try {
            Helper::updateRemark($rid, $min, $max, $remark);
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
            <h1>Grading Scale (Update)</h1>
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
                                    <input type="number" min="0" max="100" class="form-control" name="min" placeholder="Minimum Number" value="<?= $data->min; ?>" />
                                </div>
                                <div class="form-group">
                                    <label>Maximum: </label>
                                    <input type="number" min="0" max="100" class="form-control" name="max" placeholder="Maximum Number" value="<?= $data->max; ?>" />
                                </div>
                                <div class="form-group">
                                    <label>Remark: </label>
                                    <input type="text" class="form-control" name="remark" placeholder="Eg: Excellent" value="<?= $data->remark; ?>" />
                                </div>
                                <div class="form-group">
                                    <input type="submit" class="btn btn-outline-facebook" value="Update remark" />
                                </div>
                            </form>
                            <a href="?deleteRemark=<?= $rid; ?>" class="btn btn-danger delete">Delete</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>