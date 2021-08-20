<?php

use App\Core\Helper;
use App\School\Subject;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

if ($sms_role == $TEACHER) {
    Helper::to("index.php");
}

if (isset($_GET['subid']) && $_GET['subid'] != "") {
    $subId = $_GET['subid'];
    try {
        $sub = Subject::getSubject($subId, $schoolId);
    } catch (Exception $ex) {
        Helper::to("subjects.php?page=" . $page);
    }
} else {
    Helper::to("subjects.php?page=" . $page);
}

$error = [];
$subject = "";
$pass = "";
$final = "";
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $subject = $_POST['subject'];
    $teachers = (isset($_POST['teachers']) ? $_POST['teachers'] : []);
    $pass = $_POST['pass_mark'];
    $final = $_POST['final_mark'];
    $opt = $_POST['opt'];
    $teacherId = "";
    $assessment = (isset($_POST['assessment']) ? $_POST['assessment'] : 0);
    $levels = $_POST['levels'] ?? NULL;



    if ($teachers != null) {
        foreach ($teachers as $teacher) {
            $teacherId = implode(',', $teachers);
        }
    } else {
        $error[] = "Please select a teacher";
    }

    if (!Helper::isEmpty($subject, $teacherId, $pass, $final)) {
        if ($final <= $pass) {
            $error[] = "Final grade cannot be less or equal to the pass grade";
        } else if ($final <= $assessment) {
            $error[] = "Final grade cannot be less or equal to the online assessment grade";
        } else {
            if ($levels) {
                $levels = implode(",", $levels);
                try {
                    if (!is_numeric($assessment)) {
                        $assessment  = 0;
                    }
                    Subject::updateSubject(
                        $subject,
                        $teacherId,
                        $pass,
                        $final,
                        $subId,
                        $opt,
                        $assessment,
                        $levels
                    );
                    Helper::to("subjects.php?page=" . $page);
                } catch (Exception $ex) {
                    $error[] = $ex->getMessage();
                }
            } else {
                $error[] = "Please specify a level";
            }
        }
    } else {
        $error[] = "All fields are needed";
    }
}
?>
<div class="container-scroller">
    <?php

    include_once './includes/navbar.php';
    ?>

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
                                <label>Subject * </label>
                                <input type="text" name="subject" class="form-control" value="<?= $sub->subject; ?>" />
                            </div>

                            <div class="form-group">
                                <label>Teacher * </label>
                                <select name="teachers[]" class="form-control" multiple>
                                    <?php
                                    try {
                                        $data = Helper::getTeachers($schoolId);
                                        $arr = explode(",", $sub->teacherId);
                                        foreach ($data as $d) {
                                            $temp = explode(",", $d->staffId);
                                            foreach ($temp as $t) {
                                                if (in_array($t, $arr)) {
                                                    echo "<option value='$t' selected>" .
                                                        Helper::undoList(
                                                            Helper::getTeacher(
                                                                $d->staffId
                                                            )
                                                        ) .
                                                        "</option>";
                                                } else {
                                                    echo "<option value='$t'>" .
                                                        Helper::undoList(
                                                            Helper::getTeacher(
                                                                $d->staffId
                                                            )
                                                        ) .
                                                        "</option>";
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
                                <label>Pass Mark *</label>
                                <input type="number" value="<?= $sub->passGrade ?>" class="form-control" name="pass_mark">
                            </div>
                            <div class="form-group">
                                <label>Final Mark *</label>
                                <input type="number" value="<?= $sub->finalGrade ?>" class="form-control" name="final_mark">
                            </div>
                            <div class="form-group">
                                <label for="levels">Level</label>
                                <select name="levels[]" multiple id="levels" class="form-control">
                                    <?php
                                    $subjectLevels = Helper::getLevelsForSubject($subId);
                                    if ($subjectLevels) {
                                        foreach (LEVELS as $levels => $value) {
                                            if (in_array($levels, $subjectLevels)) {
                                                echo "<option value='$levels' selected>$value</option>";
                                            } else {
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
                                <label>Grade Field *</label>
                                <input type="text" value="<?= $sub->grade_fields ?>" class="form-control" name="opt">
                            </div>
                            <?php if (Helper::isUsingOnlineAssessment($schoolId)) : ?>
                                <div class="form-group">
                                    <label for="assessment">Grade for online assessment</label>
                                    <input type="text" name="assessment" id="assessment" class="form-control" placeholder="Eg: 20" value="<?= $sub->assessment; ?>">
                                </div>
                            <?php endif; ?>
                            <div class="form-group">
                                <input type="submit" class="btn btn-dark" value="Update Subject" />
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
        <?php

        include_once './includes/footer.php';
        ?>