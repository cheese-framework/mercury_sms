<?php

use App\Core\Helper;
use App\School\AcademicYear;

include_once './includes/header.php';

if ($sms_role != "Super-Admin") {
    Helper::showNotPermittedPage();
}

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <h3>Fee Management - Choose a Class</h3>
            <form action="feegenerate.php" method="GET">
                <div class="form-group">
                    <label>Academic Year *</label>
                    <select name="year" class="form-control">
                        <?php
                        try {
                            $data = AcademicYear::loadAcademicYears($schoolId);
                            if ($data != null) {
                                foreach ($data as $d) {
                                    echo "<option value='$d->academicYearId'>$d->academicYear</option>";
                                }
                            }
                        } catch (Exception $ex) {
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Class *</label>
                    <select name="class" class="form-control">
                        <?php
                        try {
                            $data = Helper::loadClass($schoolId);
                            if ($data != null) {
                                foreach ($data as $d) {
                                    echo "<option value='$d->classId'>$d->className</option>";
                                }
                            }
                        } catch (Exception $ex) {
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Term *</label>
                    <select name="term" class="form-control">
                        <option value="term-1">Term 1</option>
                        <option value="term-2">Term 2</option>
                        <option value="term-3">Term 3</option>
                    </select>
                </div>

                <div class="form-group">
                    <input type="submit" value="Generate" class="btn btn-facebook" />
                </div>
            </form>

        </div>
        <?php include_once './includes/footer.php'; ?>