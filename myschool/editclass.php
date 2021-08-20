<?php

use App\Core\Helper;
use App\School\Subject;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

if (isset($_GET['classId']) && $_GET['classId'] != "") {
    $classId = $_GET['classId'];
    try {
        $classObj = Helper::returnClass($classId);
    } catch (Exception $ex) {
        Helper::to("class.php?page=" . $page);
    }
} else {
    Helper::to("class.php?page=" . $page);
}

$error = [];
$class = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $class = $_POST['class'];
    $teachers = (isset($_POST['teachers']) ? $_POST['teachers'] : []);
    $subjects = (isset($_POST['subjects']) ? $_POST['subjects'] : []);
    $levels = $_POST['levels'] ?? "";
    $choose = $_POST['choose'] ?? 0;
    if ($choose === "on") {
        $choose = 1;
    }
    $teacherId = "";
    if ($teachers != null) {
        foreach ($teachers as $teacher) {
            $teacherId = implode(',', $teachers);
        }
    } else {
        $error[] = "Please select a teacher";
    }
    $subjectId = "";
    if ($subjects != null) {
        foreach ($subjects as $subject) {
            $subjectId = implode(',', $subjects);
        }
    } else {
        $error[] = "Please select a subject";
    }

    if (!Helper::isEmpty($class, $teacherId, $subjectId, $levels)) {
        try {
            Helper::updateClass($class, $teacherId, $subjectId, $classId, $choose, $levels);
            Helper::to("class.php?page=" . $page);
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
            <div class="row">
                <div class="col-lg-6 card">
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
                                <label><b class="text-danger">*</b> Class Name: </label>
                                <input type="text" name="class" class="form-control" placeholder="Class Name. Ex: Grade 4" value="<?= $classObj->className; ?>" />
                            </div>
                            <div class="form-group">
                                <label><b class="text-danger">*</b> Hold down to multi-select class teachers</label>
                                <select multiple="true" class="form-control" name="teachers[]">
                                    <?php
                                    try {
                                        $data = Helper::getTeachers($schoolId);
                                        $arr = explode(",", $classObj->classTeacher);
                                        foreach ($data as $d) {
                                            $temp = explode(",", $d->staffId);
                                            foreach ($temp as $t) {
                                                if (in_array($t, $arr)) {
                                                    echo "<option value='$t' selected>" . Helper::undoList(Helper::getTeacher($d->staffId)) . "</option>";
                                                } else {
                                                    echo "<option value='$t'>" . Helper::undoList(Helper::getTeacher($d->staffId)) . "</option>";
                                                }
                                            }
                                            //
                                        }
                                    } catch (Exception $ex) {
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="choose">Can choose subject? </label>
                                <input type="checkbox" name="choose" id="choose" class="form-check" <?= (Helper::canChooseSubject($classId) ? "checked" : ""); ?>>
                            </div>

                            <div class="form-group">
                                <label for="levels">Level <br><small class="text-info">Levels helps with promotion and serving of right class to the right discipline</small></label>
                                <select name="levels" id="levels" class="form-control">
                                    <?php

                                    if ($classObj->levels) {
                                        echo "<option value='" . $classObj->levels . "'>" . LEVELS[$classObj->levels] . " ~ current level</option>";
                                        foreach (LEVELS as $levels => $value) {
                                            if ($levels != "ALL" && $levels != $classObj->levels) {
                                                echo "<option value='$levels'>$value</option>";
                                            }
                                        }
                                    } else {
                                        foreach (LEVELS as $levels => $value) {
                                            if ($levels != "ALL") {
                                                echo "<option value='$levels'>$value</option>";
                                            }
                                        }
                                    }

                                    ?>
                                </select>
                            </div>

                            <div class="form-group">
                                <label><b class="text-danger">*</b> Hold down to multi-select subjects</label>
                                <select multiple="true" class="form-control" name="subjects[]">
                                    <?php
                                    try {
                                        $dat = Subject::loadSubjectsF($schoolId);
                                        $ar = explode(",", $classObj->classSubjects);
                                        foreach ($dat as $d) {
                                            $temp = explode(",", $d->subjectId);
                                            foreach ($temp as $t) {
                                                if (in_array($t, $ar)) {
                                                    echo "<option value='$t' selected>" . Helper::undoList(Subject::getSubjectList($d->subjectId)) . "</option>";
                                                } else {
                                                    echo "<option value='$t'>" . Helper::undoList(Subject::getSubjectList($d->subjectId)) . "</option>";
                                                }
                                            }
                                        }
                                    } catch (Exception $ex) {
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <input type="submit" value="Update Class" class="btn btn-dark" />
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>