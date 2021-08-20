<?php

use App\Core\Helper;
use App\Database\Database;
use App\School\AcademicYear;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="card col-lg-9 mx-auto my-3">
                <h3 class="text-center p-3">Generate data</h3>
                <form action="gradebook.php" method="get">
                    <div class="form-group">
                        <label for="">Term</label>
                        <select name="term" id="" class="form-control">
                            <option value="term-1">Term 1</option>
                            <option value="term-2">Term 2</option>
                            <option value="term-3">Term 3</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="text" name="avenue" id="" value="result" class="form-control" style="display: none;">
                    </div>
                    <div class="form-group">
                        <label for="">Academic year</label>
                        <select name="year" id="" class="form-control">
                            <?php
                            try {
                                $data = AcademicYear::loadAcademicYears($schoolId);
                                foreach ($data as $d) {
                                    echo "<option value='$d->academicYearId'>$d->academicYear</option>";
                                }
                            } catch (Exception $e) {
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Class</label>
                        <select name="class" id="" class="form-control">
                            <?php
                            $db = Database::getInstance();
                            $db->query("SELECT classId FROM classes WHERE school=?");
                            $db->bind(1, $schoolId);
                            $result = $db->resultset();
                            $classList = [];
                            if ($result != null) {
                                foreach ($result as $res) {
                                    $classList[] = $res->classId;
                                }
                            } else {
                                $classList = [];
                            }

                            if ($classList != null) {
                                foreach ($classList as $cls) {
                                    echo "<option value='$cls'>" . Helper::classEncode($cls) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <input type="submit" value="Generate" class="btn btn-primary">
                    </div>
                </form>
            </div>

        </div>
        <?php include_once './includes/footer.php'; ?>