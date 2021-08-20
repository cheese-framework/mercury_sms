<?php

use App\Core\Helper;
use App\School\AcademicYear;

include_once './includes/header.php';
if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}
$error = [];
$fee = "";
$classes = [];
$success = [];
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $fee = $_POST['fee'];
    $classes = (isset($_POST['classes']) ? $_POST['classes'] : []);
    $classId = "";
    if ($classes != null) {
        foreach ($classes as $class) {
            $classId = implode(",", $classes);
        }
    }
    if (!Helper::isEmpty($fee, $classId)) {
        if (is_numeric($fee)) {
            try {
                Helper::addFeeRange($classId, $fee, AcademicYear::getAcademicYearId(AcademicYear::getCurrentYear($schoolId), $schoolId), $schoolId);
                $success[] = "Fee added successfully";
            } catch (Exception $ex) {
                $error[] = $ex->getMessage();
            }
        } else {
            $error[] = "Fee must be a valid number";
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
            <div class="col-lg-9">
                <?php
                if (!empty($error)) {
                    echo "<div class='alert alert-danger'>";
                    foreach ($error as $e) {
                        echo $e . "<br>";
                    }
                    echo "</div>";
                }

                if (!empty($success)) {
                    echo "<div class='alert alert-success'>";
                    foreach ($success as $e) {
                        echo $e . "<br>";
                    }
                    echo "</div>";
                }
                ?>
                <h1>Add Fee Range</h1>
                <form action="" method="POST" autocomplete="off">
                    <div class="form-group">
                        <label>Fee * </label>
                        <input type="text" name="fee" placeholder="Fee" class="form-control" value="<?= $fee; ?>">
                    </div>
                    <div class="form-group">
                        <label>Class *</label>
                        <select name="classes[]" multiple class="form-control">
                            <?php
                            try {
                                $data = Helper::loadClass($schoolId);
                                $tempClasses = (isset($classes) ? $classes : []);
                                foreach ($data as $d) {
                                    if (in_array($d->classId, $tempClasses)) {
                                        echo "<option value='$d->classId' selected>$d->className</option>";
                                    } else {
                                        echo "<option value='$d->classId'>$d->className</option>";
                                    }
                                }
                            } catch (Exception $ex) {
                            }
                            ?>

                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Add Fee" class="btn btn-behance" />
                    </div>
                </form>

            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>