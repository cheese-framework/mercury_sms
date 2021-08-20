<?php

use App\Core\Helper;
use App\School\AcademicYear;
use App\School\Subject;

include_once './includes/header.php';

if ($sms_role != "Super-Admin") {
    Helper::to("index.php");
}

$rando = rand(1, 3);
switch ($rando) {
    case 1:
        $sub = "Eg: Mathematics";
        break;
    case 2:
        $sub = "Eg: English";
        break;
    default:
        $sub = "Eg: Chemistry";
}

$error = [];
$subject = "";
$pass = "";
$final = "";
$opt = "";
$assessment = "";

if (isset($_GET['deletesub']) && $_GET['deletesub'] != "") {
    $id = $_GET['deletesub'];

    try {
        $year =  AcademicYear::getCurrentYearId($schoolId);
        if ($year != "") {
            Subject::deleteSubject($id, $year);
            Helper::to("subjects.php?page=" . $page);
        } else {
            $error[] = "Academic year is not set";
        }
    } catch (Exception $e) {
        $error[] = $e->getMessage();
    }
}

if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['subject'])) {
    $subject = $_POST['subject'];
    $teachers = (isset($_POST['teachers']) ? $_POST['teachers'] : []);
    $pass = $_POST['pass_mark'];
    $final = $_POST['final_mark'];
    $levels = $_POST['levels'] ?? "";
    $teacherId = "";
    $assessment = (isset($_POST['assessment']) ? $_POST['assessment'] : 0);
    $opt = $_POST['opt'];
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
                    Subject::addSubject(
                        new Subject($subject),
                        $teacherId,
                        $pass,
                        $final,
                        $opt,
                        $schoolId,
                        $assessment,
                        $levels
                    );
                    Helper::to("subjects.php?page=" . $page);
                } catch (Exception $ex) {
                    $error[] = $ex->getMessage();
                }
            } else {
                $error[] = "Please specify the right level";
            }
        }
    } else {
        $error[] = "All fields are needed";
    }
}
?>
<div class="container-scroller">
    <script>
        document.title = "Add | Manage Subjects";
    </script>
    <?php

    include_once './includes/navbar.php';
    ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <?php
            if (!empty($error)) {
                echo "<div class='alert alert-danger'>";
                foreach ($error as $e) {
                    echo $e . "<br>";
                }
                echo "</div>";
            }
            ?>
            <div class="row">
                <div class="col-lg-4 card">
                    <div class="card-body">
                        <h3>Add Subject</h3>


                        <form action="" method="POST" autocomplete="off">
                            <div class="form-group">
                                <label>Subject * </label>
                                <input type="text" name="subject" class="form-control" placeholder="<?= $sub; ?>" value="<?= $subject; ?>" />
                            </div>

                            <div class="form-group">
                                <label>Teacher * </label>
                                <select name="teachers[]" multiple class="form-control">
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
                                <label>Pass mark *</label>
                                <input type="number" name="pass_mark" class="form-control" value="<?= $pass; ?>" />
                            </div>
                            <div class="form-group">
                                <label>Final mark *</label>
                                <input type="number" name="final_mark" class="form-control" value="<?= $final; ?>" />
                            </div>
                            <div class="form-group">
                                <label for="levels">Level <br><small class="text-info">Select multiple if you may</small> </label>
                                <select name="levels[]" id="levels" class="form-control" multiple>
                                    <?php
                                    foreach (LEVELS as $levels => $value) {
                                        echo "<option value='$levels'>$value</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <?php if (Helper::isUsingOnlineAssessment($schoolId)) : ?>
                                <div class="form-group">
                                    <label for="assessment">Grade for online assessment</label>
                                    <input type="text" name="assessment" id="assessment" class="form-control" placeholder="Eg: 20" value="<?= $assessment; ?>">
                                </div>
                            <?php endif; ?>
                            <div id="opt" class="form-group">

                            </div>

                            <div class="row">

                                <div class="form-group col-lg-6">
                                    <button type="button" class="btn btn-youtube" id="addField">Add Grade</button>
                                </div>

                                <div class="form-group col-lg-6">
                                    <input type="submit" class="btn btn-dark" value="Add Subject" />
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-lg-8">

                    <h3>Manage Subjects</h3>
                    <p class="text-info">On deleting a subject, you are also clearing all the results associated with it
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dt">
                            <thead>
                                <tr>
                                    <th>Subject</th>
                                    <th>Teacher(s)</th>
                                    <th>Pass grade / Final grade</th>
                                    <th class='text-center'>Edit</th>
                                    <th class='text-center'>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $data = Subject::loadSubjects($schoolId);
                                    foreach ($data as $d) {
                                        echo "<tr>";
                                        echo "<td>$d->subject</td>";
                                        echo "<td>" .
                                            Helper::undoList(
                                                Helper::getTeacher(
                                                    $d->teacherId
                                                )
                                            ) . "</td>";
                                        echo "<td>" . $d->passGrade . " / " .
                                            $d->finalGrade . "</td>";
                                        echo "<td><a href='subjects.php?deletesub=$d->subjectId' class='btn btn-danger btn-rounded delete'><i class='mdi mdi-delete'></i></a></td>";
                                        echo "<td><a href='editsubject.php?subid=$d->subjectId' class='btn btn-primary btn-rounded'><i class='mdi mdi-pencil'></i></a></td>";
                                        echo "</tr>";
                                    }
                                } catch (Exception $ex) {
                                    echo "<tr>";
                                    echo "<td colspan=4 class='text-center'>" .
                                        $ex->getMessage() . "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php

        include_once './includes/footer.php';
        ?>
        <script>
            const btnAddGrade = document.getElementById("addField");
            let opt = document.getElementById("opt");

            const temp = document.getElementById("temp");

            // console.log(temp);
            if (temp == null) {
                let field = document.createElement("input");
                field.setAttribute("type", "text");
                field.className = "form-control";
                field.id = "temp";
                field.name = "opt";
                field.placeholder = "Example: Test 1";
                field.value = "<?php

                                echo ($opt == '') ? 'Test 1,Test 2' : $opt ?>";
                let label = document.createElement("label");
                label.innerHTML =
                    "Grade Fields  [ When typing manually here, separate by comma (,) ]<br><i class='text-info'>Do not include exam field.</i>";
                opt.appendChild(label);
                opt.appendChild(field);
            } else {
                temp.value += "," + dialog;
            }


            btnAddGrade.addEventListener('click', () => {
                let opt = document.getElementById("opt");
                let dialog = prompt("Enter the grade. Ex: Test 1 or Project Work.");
                if (dialog.trim() != "") {
                    const temp = document.getElementById("temp");
                    if (temp.value.trim() == "") {
                        temp.value += dialog;
                    } else {
                        temp.value += "," + dialog;
                    }
                } else {
                    alert("Please specify a value in the prompt");
                }
            });
        </script>