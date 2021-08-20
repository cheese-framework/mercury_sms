<?php

use App\Core\Helper;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

if (isset($_GET['id']) && $_GET['id'] != "") {
    $id = $_GET['id'];
    try {
        $range = Helper::getFeeR($id);
    } catch (Exception $ex) {
        Helper::showErrorPage();
    }
} else {
    Helper::showErrorPage();
}

$success = [];
$error = [];
$fee = "";
$classes = [];
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $fee = $_POST['fee'];
    $classes = (isset($_POST['classes']) ? $_POST['classes'] : []);
    if ($classes != null) {
        foreach ($classes as $class) {
            $classId = implode(",", $classes);
        }
    }
    if (!Helper::isEmpty($fee, $classId)) {
        if (is_numeric($fee)) {
            try {
                Helper::updateFeeRange($classId, $id, $fee, $schoolId);
                $success[] = "Fee updated!";
                // Helper::to("schoolsettings.php");
            } catch (Exception $ex) {
                $error[] = $ex->getMessage();
            }
        } else {
            $error[] = "Fee must be a valid numeric";
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
                        <input type="text" name="fee" placeholder="Fee" class="form-control" value="<?= $range->fee; ?>">
                    </div>
                    <div class="form-group">
                        <label>Class *</label>
                        <select name="classes[]" multiple class="form-control">
                            <?php
                            try {
                                $data = Helper::loadClass($schoolId);
                                $tempClasses = (isset($classes) ? $classes : []);
                                $temp = explode(",", $range->class);
                                foreach ($data as $d) {
                                    if (in_array($d->classId, $temp)) {
                                        echo "<option value='$d->classId' selected>$d->className</option>";
                                    } else if (in_array($d->classId, $tempClasses)) {
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
                        <input type="submit" value="Update Fee" class="btn btn-behance" />
                    </div>
                </form>

            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>