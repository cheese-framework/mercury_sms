<?php

use App\Core\Helper;
use App\School\AcademicYear;
use App\School\Student;

include_once './includes/header.php';

$classList = Helper::returnClassList($sms_userId, $schoolId);
?>
<div class="container-scroller">
    <?php

    include_once './includes/navbar.php';
    ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <h1>School Enrollment. <?= AcademicYear::getCurrentYear($schoolId); ?></h1>
            <div class="col-lg-12 my-3 card">
                <div class="card-body">
                    <div class="table-responsive">
                        <?php
                        if ($classList != null) {
                        ?>
                            <table class="table table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>Class</th>
                                        <th>Male</th>
                                        <th>Female</th>
                                        <th>Total</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($classList as $cls) {
                                        $m = Student::getNumGender(
                                            $cls,
                                            AcademicYear::getCurrentYearId($schoolId)
                                        )["male"];
                                        $f = Student::getNumGender(
                                            $cls,
                                            AcademicYear::getCurrentYearId($schoolId)
                                        )["female"];
                                        $t = $m + $f;
                                        echo "<tr>";
                                        echo "<td>" . Helper::classEncode($cls) . "</td>";
                                        echo "<td>" . $m . "</td>";
                                        echo "<td>" . $f . "</td>";
                                        echo "<td>" . $t . "</td>";
                                        echo "<td><a href='chooseAcad.php?classId=$cls' class='btn btn-facebook' target='_blank'>View More</a></td>";
                                        echo "</tr>";
                                    }
                                    ?>
                                </tbody>
                            </table>
                        <?php
                        } else {
                        ?>
                            <?php
                            if ($sms_role == $ADMIN) {

                                try {
                                    $data = Helper::loadClass($schoolId);
                            ?>
                                    <table class="table table-striped table-hover table-bordered dt">
                                        <thead>
                                            <tr>
                                                <th>Class</th>
                                                <th>Male</th>
                                                <th>Female</th>
                                                <th>Total</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $mTotal = 0;
                                            $fTotal = 0;
                                            $tTotal = 0;
                                            foreach ($data as $d) {
                                                $m = Student::getNumGender(
                                                    $d->classId,
                                                    AcademicYear::getCurrentYearId($schoolId)
                                                )["male"];
                                                $f = Student::getNumGender(
                                                    $d->classId,
                                                    AcademicYear::getCurrentYearId($schoolId)
                                                )["female"];
                                                $total = $m + $f;
                                                $mTotal += $m;
                                                $fTotal += $f;
                                                $tTotal += $total;
                                                echo "<tr>";
                                                echo "<td>$d->className</td>";
                                                echo "<td>" . $m . "</td>";
                                                echo "<td>" . $f . "</td>";
                                                echo "<td>" . $total . "</td>";
                                                echo "<td><a href='chooseAcad.php?classId=$d->classId' class='btn btn-facebook' target='_blank'>View More</a></td>";
                                                echo "</tr>";
                                            }
                                            ?>
                                            <tr>
                                                <td><b>Total</b></td>
                                                <td><b><?= $mTotal ?></b></td>
                                                <td><b><?= $fTotal ?></b></td>
                                                <td><b><?= $tTotal ?></b></td>
                                                <td></td>
                                            </tr>
                                        </tbody>

                                    </table>
                                <?php
                                } catch (Exception $ex) {
                                    echo "<h4 class='text-center'>" .
                                        $ex->getMessage() . "</h4>";
                                }
                                ?>
                        <?php
                            } else {
                                echo "<div class='text-center'><h3>You have no class assigned to you</h3></div>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php

        include_once './includes/footer.php';
        ?>