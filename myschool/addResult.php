<?php

use App\Core\Helper;
use App\Notifiable\Components\Components;
use App\School\AcademicYear;
use App\School\Student;
use App\School\Subject;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

if (isset($_GET['class']) && $_GET['class'] != "") {
    $id = $_GET['class'];
    if (Subject::hasBeenAssignedASubject($sms_userId, $schoolId)) {
        if (!Helper::isATeacherInClass($id, $sms_userId, $schoolId)) {
            $list = Subject::getMySubjects($sms_userId, $schoolId);
            $lists = [];
            foreach ($list as $l) {
                $lists[] = $l->subjectId;
            }
            $lists = implode(",", $lists);
        } else {
            try {
                $lists = Subject::getSubjectsByClass($id, $schoolId);
            } catch (Exception $ex) {
                Helper::to("chooseClass.php");
            }
        }
    } else {
        Helper::showNotPermittedPage();
    }
} else {
    Helper::showErrorPage();
}

$error = [];
$term = "";

if (isset($_POST['addResult'])) {
    $grades = Helper::getGradeTypes($_POST['subject']);
    $term = (isset($_POST['term']) ? $_POST['term'] : "");
    $subject = (isset($_POST['subject']) ? $_POST['subject'] : "");
    $student = (isset($_POST['student']) ? $_POST['student'] : "");
    $exam = $_POST['exam'];
    $year = AcademicYear::getCurrentYearId($schoolId);
    if (!Helper::isEmpty($term, $subject, $student, $year)) {
        if (trim($grades) != "") {
            $grades = explode(",", $grades);
            $tempGrade = [];
            $total = 0;
            if (trim($exam) == "") {
                $exam = 0;
            } else {
                if (!is_numeric($exam)) {
                    $error[] = "Please specify a valid grade value";
                } else {
                    foreach ($grades as $g) {
                        $temp = explode(" ", $g);
                        $clean = implode("_", $temp);
                        if (is_numeric($_POST[$clean])) {
                            $total += $_POST[$clean];
                        } else {
                            $error[] = "A non numeric value was received.<br> $g : " . $_POST[$clean];
                        }
                        array_push($tempGrade, "$g:" . floatval($_POST[$clean]));
                    }
                    $total += $exam;
                    $grades = implode(",", $tempGrade);
                    try {
                        if (empty($error)) {
                            Helper::addResult(
                                $term,
                                $year,
                                $subject,
                                $student,
                                $id,
                                $grades,
                                $total,
                                $exam,
                                $schoolId
                            );
                        } else {
                            $error[] = "Something went wrong";
                        }
                    } catch (Exception $e) {
                        $error[] = $e->getMessage();
                    }
                }
            }
        } else {
            $total = 0;
            if (trim($exam) == "") {
                $exam = 0;
            } else {
                if (!is_numeric($exam)) {
                    $error[] = "Please specify a valid grade value";
                } else {
                    $total += $exam;
                    try {
                        Helper::addResult(
                            $term,
                            $year,
                            $subject,
                            $student,
                            $id,
                            $grades,
                            $total,
                            $exam,
                            $schoolId
                        );
                    } catch (Exception $e) {
                        $error[] = $e->getMessage();
                    }
                }
            }
        }
    } else {
        $error[] = "Fill the required fields";
    }
}

?>
<div class="container-scroller">
    <!-- partial:partials/_sidebar.html -->
    <?php

    include_once './includes/navbar.php';
    ?>
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-lg-9 mx-auto">
                    <?= Components::header(Helper::classEncode($id), "h1", "center"); ?>
                    <?= Components::header("Add Students Result", "h4", "center"); ?>
                    <?php
                    if (Helper::hasBeenAssignedAClass($sms_userId, $schoolId, $sms_role)) {
                        echo '<p class="text-center">This section is for adding a new student\'s result. If you wish to update
                        students grade click <a href="resultclass.php">here</a>.</p>';
                    }

                    ?>

                    <div class="card">
                        <div class="card-body">
                            <p class='text-primary text-small'>* Change the subject to get grading sheets.
                                <?php
                                if (Helper::canChooseSubject($id)) {
                                    echo "<br><small><span class='text-primary text-small'>* Change the student to get his/her subjects.</span></small>";
                                }
                                ?></p>
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
                                    <label>Term: </label>
                                    <select class="form-control" name="term">
                                        <?php if ($term) : ?>
                                            <option value="<?= $term; ?>"><?= $term; ?></option>
                                        <?php endif; ?>
                                        <option value="term-1">Term 1</option>
                                        <option value="term-2">Term 2</option>
                                        <option value="term-3">Term 3</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Academic Year: </label>
                                    <select class="form-control" name="year" id="year">
                                        <?php
                                        try {
                                            $curr = AcademicYear::getCurrentYearId(
                                                $schoolId
                                            );
                                            $data = AcademicYear::loadAcademicYears(
                                                $schoolId
                                            );
                                            if (!empty($data)) {
                                                foreach ($data as $d) {
                                                    if (
                                                        $curr ==
                                                        $d->academicYearId
                                                    ) {
                                                        echo "<option value='$d->academicYearId'>$d->academicYear - {current}</option>";
                                                    } else {
                                                        echo "<option value='$d->academicYearId'>$d->academicYear</option>";
                                                    }
                                                }
                                            }
                                        } catch (Exception $ex) {
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Subject: </label>
                                    <select class="form-control" name="subject" id="subjects">
                                        <?php
                                        if (!Helper::canChooseSubject($id)) {
                                            $mySubjects = explode(",", $lists);

                                            if (!Helper::isATeacherInClass(
                                                $id,
                                                $sms_userId,
                                                $schoolId
                                            )) {
                                                foreach ($mySubjects as $subject) {
                                                    try {
                                                        if (Subject::isSubjectInClass(
                                                            $subject,
                                                            $id,
                                                            $schoolId
                                                        )) {
                                                            $data = Subject::getSubject(
                                                                $subject,
                                                                $schoolId
                                                            );
                                                            echo "<option value=" .
                                                                $data->subjectId .
                                                                ">" . $data->subject .
                                                                "</option>";
                                                        }
                                                    } catch (Exception $e) {
                                                    }
                                                }
                                            } else {
                                                foreach ($mySubjects as $list) {
                                                    try {
                                                        $data = Subject::getSubject(
                                                            $list,
                                                            $schoolId
                                                        );
                                                        echo "<option value=" .
                                                            $data->subjectId . ">" .
                                                            $data->subject .
                                                            "</option>";
                                                    } catch (Exception $e) {
                                                    }
                                                }
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Student: </label>
                                    <select class="form-control" id="student" name="student">
                                        <?php
                                        $students = Student::getStudents(
                                            $id,
                                            $schoolId
                                        );
                                        if ($students != null) {
                                            foreach ($students as $stu) {
                                                echo "<option value='$stu->studentId'>$stu->fullname</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <p style="display: none;" id="can_choose"><?= (Helper::canChooseSubject($id) ? "true" : "false") ?></p>
                                <div id="opt"></div>

                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <?php

        include_once "includes/footer.php";
        ?>

        <script>
            let subjects = document.getElementById("subjects");
            let student = document.getElementById("student");
            const can_choose = document.getElementById("can_choose");
            let year = document.getElementById("year");
            let school = '<?= $schoolId; ?>';
            let teacher = '<?= $sms_userId; ?>';
            let cls = '<?= $id; ?>';

            if (can_choose.textContent == "true") {
                (() => {
                    subjects.innerHTML =
                        "<div class='text-center'><i class='mdi mdi-loading mdi-spin text-info' style='font-size: 50px'></i></div>";
                    getSubjectsFromStudent(student.value, year.value, school, cls, teacher);
                })();
            } else {
                (() => {
                    let o = document.getElementById("opt");
                    o.innerHTML =
                        "<div class='text-center'><i class='mdi mdi-loading mdi-spin text-info' style='font-size: 50px'></i></div>";
                    getSubjects(subjects.value);
                })();
            }


            subjects.addEventListener('change', () => {
                let op = document.getElementById("opt");
                op.innerHTML =
                    "<div class='text-center'><i class='mdi mdi-loading mdi-spin text-info' style='font-size: 50px'></i></div>";
                getSubjects(subjects.value);
            });

            student.addEventListener('change', () => {
                if (can_choose.textContent == "true") {
                    getSubjectsFromStudent(student.value, year.value, school, cls, teacher);
                }
            });

            function getSubjects(sub) {
                let xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    const opt = document.getElementById("opt");
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        let response = xhr.response;
                        setTimeout(() => {
                            opt.innerHTML = response;
                        }, 500);
                    }
                };
                xhr.open("GET", "getgradestype.php?sub_id=" + sub, true);
                xhr.send();
            }

            function getSubjectsFromStudent(student, year, school, cls, teacher) {
                let xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    const opt = document.getElementById("subjects");
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        let response = xhr.response;
                        setTimeout(() => {
                            opt.innerHTML = response;
                            subjects = document.getElementById("subjects");
                            let o = document.getElementById("opt");
                            o.innerHTML =
                                "<div class='text-center'><i class='mdi mdi-loading mdi-spin text-info' style='font-size: 50px'></i></div>";
                            getSubjects(subjects.value);
                        }, 500);
                    }
                };
                xhr.open("GET", `getgradestype.php?student=${student}&class=${cls}&teacher=${teacher}&year=${year}&school=${school}`, true);
                xhr.send();
            }
        </script>