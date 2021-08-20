<?php

use App\Core\Helper;
use App\School\Student;
use App\School\Subject;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

if ((isset($_GET['studentId']) && $_GET['studentId'] != "") && (isset($_GET['subject']) && $_GET['subject'] != "") && (isset($_GET['resultId']) && $_GET['resultId'] != "") && (isset($_GET['class']) && $_GET['class'] != "")) {
    $id = $_GET['studentId'];
    $subject = $_GET['subject'];
    $class = $_GET['class'];
    $resId = $_GET['resultId'];
    $year = $_GET['year'];
    $term = $_GET['term'];
    $data = Student::getFullName($id);

    $sheets = Helper::getResult($year, $term, $subject, $id, $schoolId);
    $gradeTypesAdvent = Helper::getGradeTypes($subject);

    if ($sheets[0] != null) {
        $prev = 0;
        foreach ($sheets[0] as $sht) {
            $value = ((array_key_exists(1, $sht)) ? $sht[1] : 0);
            $prev += $value;
        }
        $prev += $sheets[1];
    } else {
        $prev = 0;
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $gradeTypes = explode(",", $gradeTypesAdvent);
        $new = 0;
        $tempGrade = [];
        $exam = $_POST['exam'];
        if (is_numeric($exam)) {
            foreach ($gradeTypes as $grds) {
                $temp = explode(" ", $grds);
                $clean = implode("_", $temp);
                if (isset($_POST[$clean]) && is_numeric($_POST[$clean])) {
                    $new += $_POST[$clean];
                    array_push($tempGrade, "$grds:" . floatval($_POST[$clean]));
                } else {
                    $error[] = "A non numeric value was received.<br>$grds : " . ($_POST[$clean] ?? "unknown");
                }
            }
            $new += $exam;
            $grades = implode(",", $tempGrade);
            // exit;
            try {
                if (empty($error)) {
                    Helper::changeResult($id, $resId, $grades, $prev, $year, $term, $new, $exam, $subject);
                    Helper::to("viewprogress.php?year=$year&term=$term&studentId=$id&class=$class");
                } else {
                    $error[] = "Something went wrong";
                }
            } catch (Exception $ex) {
                $error[] = $ex->getMessage();
            }
        } else {
            $error[] = "Exam is not a valid numeric";
        }
    }
} else {
    Helper::showErrorPage();
}
?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-lg-7">
                    <h5>Update <?= $data; ?>'s result for: <?php
                                                            try {
                                                                echo Subject::getSubject($subject, $schoolId)->subject;
                                                            } catch (Exception $e) {
                                                            }
                                                            ?></h5>
                    <div class="card">
                        <div class="card-body">
                            <?php
                            if (!empty($error)) {
                                echo "<div class='alert alert-danger'>";
                                foreach ($error as $e) {
                                    echo $e . "<br>";
                                }
                                echo "</div>";
                            } else {
                                echo "<div class='alert alert-info'>The Final Grade is stacked at: <b>" . Helper::getFinalMark($resId) . "</b></div>";
                            }
                            ?>
                            <form method="POST" action="" autocomplete="off">

                                <div class="form-group">
                                    <label>Student: </label>
                                    <select class="form-control" name="student" disabled>
                                        <?php
                                        echo "<option>$data</option>";
                                        ?>
                                    </select>
                                </div>

                                <?php

                                $explodedGrades = explode(",", $gradeTypesAdvent);

                                if ($sheets[0] != null) {
                                    $cleanNames = [];

                                    foreach ($sheets[0] as $sheet) {
                                        $name = $sheet[0];
                                        $cleanNames[] = $name;
                                    }

                                    foreach ($sheets[0] as $sheet) {
                                        $name = $sheet[0];
                                        $value = ((array_key_exists(1, $sheet) ? $sheet[1] : 0));
                                        echo "<div class='form-group'>";
                                        echo "<label>$name:</label>";
                                        echo "<input type='text' class='form-control' name='$name' value='$value'>";
                                        echo "</div>";
                                    }

                                    foreach ($explodedGrades as $exploded) {
                                        if (!in_array($exploded, $cleanNames)) {
                                            echo "<div class='form-group'>";
                                            echo "<label>$exploded:</label>";
                                            echo "<input type='text' class='form-control' name='$exploded' value='$value'>";
                                            echo "</div>";
                                        }
                                    }

                                    echo "<div class='form-group'>
                                    <label>Exam:</label>
                                    <input type='text' class='form-control' value=" . $sheets[1] . " name='exam'>    
                                    </div>";

                                    echo '<div class="form-group">
                                        <input type="submit" value="Change Result"  class="btn btn-dark" />
                                    </div>';
                                } else {
                                    echo "<div class='alert alert-danger'>No grading sheets available. Try again later.</div>";
                                }

                                ?>


                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>