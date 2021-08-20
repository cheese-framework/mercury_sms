<?php

use App\Core\Helper;
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
            <h1>Daily Statistics</h1>
            <div class="table-responsive">
                <table class="table table-bordered table-hover table-striped dt">
                    <thead>
                        <tr>
                            <th>Class</th>
                            <th>Present<br>(Morning)</th>
                            <th>Present<br>(Afternoon)</th>
                            <th>Absent<br>(Morning)</th>
                            <th>Absent<br>(Afternoon)</th>
                            <th>Late<br>(Morning)</th>
                            <th>Late<br>(Afternoon)</th>
                        </tr>
                    </thead>
                    <?php
                    try {
                        $classes = Helper::loadClasses($schoolId);
                        $mPt = $aPt = $mAbt = $aAbt = $mLt = $aLt = 0;
                        foreach ($classes as $class) {
                            $morningPresent = Helper::getStatistics(date("d-m-Y"), AcademicYear::getCurrentYearId($schoolId), $class->classId, "present", "Morning", $schoolId);
                            $morningAbsent = Helper::getStatistics(date("d-m-Y"), AcademicYear::getCurrentYearId($schoolId), $class->classId, "absent", "Morning", $schoolId);
                            $morningLate = Helper::getStatistics(date("d-m-Y"), AcademicYear::getCurrentYearId($schoolId), $class->classId, "late", "Morning", $schoolId);

                            $afternoonPresent = Helper::getStatistics(date("d-m-Y"), AcademicYear::getCurrentYearId($schoolId), $class->classId, "present", "Afternoon", $schoolId);
                            $afternoonAbsent = Helper::getStatistics(date("d-m-Y"), AcademicYear::getCurrentYearId($schoolId), $class->classId, "absent", "Afternoon", $schoolId);
                            $afternoonLate = Helper::getStatistics(date("d-m-Y"), AcademicYear::getCurrentYearId($schoolId), $class->classId, "late", "Afternoon", $schoolId);

                            $mPt += $morningPresent;
                            $aPt += $afternoonPresent;

                            $mAbt += $morningAbsent;
                            $aAbt += $afternoonAbsent;

                            $mLt += $morningLate;
                            $aLt += $afternoonLate;


                            echo "<tr>";
                            echo "<td>" . Helper::classEncode($class->classId) . "</td>";
                            echo "<td>$morningPresent</td>";
                            echo "<td>$afternoonPresent</td>";
                            echo "<td>$morningAbsent</td>";
                            echo "<td>$afternoonAbsent</td>";
                            echo "<td>$morningLate</td>";
                            echo "<td>$afternoonLate</td>";
                            echo "</tr>";
                        }
                    } catch (Exception $ex) {
                        echo "<tr>";
                        echo "<td colspan=4 class='text-center'>" . $ex->getMessage() . "</td>";
                        echo "</tr>";
                    }
                    echo "<tr style='font-weight:bold'>
                        <td>Total</td>
                        <td>$mPt</td>
                        <td>$aPt</td>
                        <td>$mAbt</td>
                        <td>$aAbt</td>
                        <td>$mLt</td>
                        <td>$aLt</td>
                    </tr>";
                    ?>
                </table>
            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>