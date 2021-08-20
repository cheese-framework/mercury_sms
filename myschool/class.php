<?php

use App\Core\Helper;
use App\School\AcademicYear;
use App\School\Subject;

include_once './includes/header.php';

if ($sms_role != "Super-Admin") {
    Helper::to("index.php");
}

$class = "";
$error = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $class = $_POST['class'];
    $teachers = (isset($_POST['teachers']) ? $_POST['teachers'] : []);
    $subjects = (isset($_POST['subjects']) ? $_POST['subjects'] : []);
    $levels = $_POST['levels'] ?? "";
    $acadId = AcademicYear::getCurrentYearId($schoolId);
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

    if ($acadId == "") {
        $error[] = "Academic Year has not been defined";
    }

    if (!Helper::isEmpty($class, $teacherId, $subjectId, $acadId, $levels)) {
        try {
            Helper::addClass($class, $teacherId, $acadId, $subjectId, $schoolId, $choose, $levels);
            Helper::to("class.php?page=" . $page);
        } catch (Exception $ex) {
            $error[] = $ex->getMessage();
        }
    } else {
        $error[] = "All fields are needed.";
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
                    <h2>Add Class</h2>
                    <p class="text-info text-small">Please note that you cannot delete a class once it has been
                        created.</p>
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
                                    <label><b class="text-danger">*</b> Class Name: </label>
                                    <input type="text" name="class" class="form-control" placeholder="Class Name. Ex: Grade 4" value="<?= $class; ?>" />
                                </div>

                                <div class="form-group">
                                    <label><b class="text-danger">*</b> Hold down to multi-select class teachers</label>
                                    <select multiple="true" class="form-control" name="teachers[]">
                                        <?php
                                        try {
                                            $data = Helper::getTeachers($schoolId);
                                            foreach ($data as $d) {
                                                echo "<option value='$d->staffId'>$d->staff_name</option>";
                                            }
                                        } catch (Exception $ex) {
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="choose">Can choose subject? </label>
                                    <input type="checkbox" name="choose" id="choose" class="form-check">
                                </div>

                                <div class="form-group">
                                    <label for="levels">Level <br><small class="text-info">Levels helps with promotion and serving of right class to the right discipline</small></label>
                                    <select name="levels" id="levels" class="form-control">
                                        <?php
                                        foreach (LEVELS as $levels => $value) {
                                            if ($levels != "ALL") {
                                                echo "<option value='$levels'>$value</option>";
                                            }
                                        }

                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label id="subject-label"><b class="text-danger">*</b> Hold down to multi-select subjects</label>
                                    <select multiple="true" class="form-control" name="subjects[]">
                                        <?php
                                        try {
                                            $data = Subject::loadSubjectsF($schoolId);
                                            foreach ($data as $d) {
                                                echo "<option value='$d->subjectId'>$d->subject</option>";
                                            }
                                        } catch (Exception $ex) {
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <input type="submit" value="Add Class" class="btn btn-dark" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <h2>Manage Classes</h2>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-striped dt">
                            <thead>
                                <tr>
                                    <th>Class Name</th>
                                    <th>Teachers</th>
                                    <th>Subjects</th>
                                    <th class="text-center">Operations</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $data = Helper::loadClasses($schoolId);
                                    if (!empty($data)) {
                                        foreach ($data as $d) {
                                            echo "<tr>";
                                            echo "<td>$d->className</td>";
                                            echo "<td>" . Helper::undoList(Helper::getTeacher($d->classTeacher)) . "</td>";
                                            echo "<td>" . Helper::undoList(Subject::getSubjectList($d->classSubjects)) . "</td>";
                                            echo "<td><a href='editclass.php?classId=$d->classId' class='btn btn-primary btn-rounded text-center'><i class='mdi mdi-pencil'></i></a></td>";
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
        <script>
            const choose = document.getElementById("choose");
            const label = document.getElementById("subject-label");

            choose.addEventListener('change', function() {
                if (choose.checked) {
                    label.innerHTML = `<b class="text-danger">*</b> Select core subjects`;
                } else {
                    label.innerHTML = `<b class="text-danger">*</b> Hold down to multi-select subjects`;
                }
            });
        </script>