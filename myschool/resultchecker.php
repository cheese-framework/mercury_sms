<?php

use App\Core\Helper;
use App\Database\Database;
use App\School\AcademicYear;
use App\School\Student;

function addSuffix($num)
{
    if ($num == 2 || $num == 22 || $num == 32) {
        return $num . "nd";
    } else if ($num == 3 || $num == 23 || $num == 33) {
        return $num . "rd";
    } else if ($num == 1 || $num == 21 || $num == 31) {
        return $num . "st";
    } else {
        return $num . "th";
    }
}

function showResults($year, $term, $class, $school)
{
    $db = Database::getInstance();
    $db->query(
        "SELECT * FROM totals WHERE academicYear=? AND term=? AND class=? AND school=? ORDER BY total DESC"
    );
    $db->bind(1, $year);
    $db->bind(2, $term);
    $db->bind(3, $class);
    $db->bind(4, $school);
    $data = $db->resultset();
    if ($db->rowCount() > 0) {
?>
        <h1>Results for: <?= AcademicYear::getAcademicYearById($year); ?>, <?= ucfirst($term); ?></h1>
        <p class="text-info text-small">Please scroll left or right for full content view</p>
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered">
                <thead>
                    <tr>
                        <th>Fullname</th>
                        <th>Mark</th>
                        <?= (!Helper::canChooseSubject($class)) ? "<th>Position</th>" : ""; ?>
                        <th>Academic Progress</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $prev = 0;
                    $i = 0;
                    $count = 1;
                    $prevI = $i;
                    if (count($data) <= 1) {
                        foreach ($data as $d) {
                            if (Student::getFullName($d->studentId) != "") {
                                $pos = "1st";
                                if ($pos == "1st") {
                                    $col = "text-success";
                                } else if ($pos == "2nd") {
                                    $col = "text-primary";
                                } else if ($pos == "3rd") {
                                    $col = "text-danger";
                                } else {

                                    $col = "";
                                }
                                $tot = round($d->total);
                                echo "<tr>";
                                echo "<td>" . Student::getFullName($d->studentId) . "</td>";
                                echo "<td><b>$tot</b></td>";
                                if (!Helper::canChooseSubject($class)) {

                                    echo "<td class='$col'><b>$pos</b></td>";
                                }
                                echo "<td><a target='_blank' href='viewprogress.php?class=$class&year=$year&term=$term&studentId=$d->studentId' class='btn btn-behance'>View Progress</a></b></td>";
                                echo "</tr>";
                            }
                        }
                    } else {
                        foreach ($data as $d) {
                            if (Student::getFullName($d->studentId) != "") {
                                if ($prev == round($d->total)) {
                                    $count++;
                                    $pos = addSuffix(($prevI));
                                    $i++;
                                } else {
                                    $pos = addSuffix(($count));
                                    $i++;
                                    $count++;
                                    $prevI = $i;
                                    $prev = round($d->total);
                                }

                                if ($pos == "1st") {
                                    $col = "text-success";
                                } elseif ($pos == "2nd") {
                                    $col = "text-primary";
                                } elseif ($pos == "3rd") {
                                    $col = "text-danger";
                                } else {
                                    $col = "";
                                }
                                $tot = round($d->total);
                                echo "<tr>";
                                echo "<td>" . Student::getFullName($d->studentId) . "</td>";
                                echo "<td><b>$tot</b></td>";
                                echo "<td class='$col'><b>$pos</b></td>";
                                echo "<td><a target='_blank' href='viewprogress.php?class=$class&year=$year&term=$term&studentId=$d->studentId' class='btn btn-behance'>View Progress</a></b></td>";
                                echo "</tr>";
                            }
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
<?php
    } else {
        echo "<div class='alert alert-danger text-center'>Nothing to show here!</div>";
    }
}
