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
            <div class="col-lg-9 col-md-10 col-sm-12 col-xs-12 mx-auto">
                <?php
                $day = "";
                $monp = 0;
                $monab = 0;
                $monlt = 0;
                $tuep = 0;
                $tueab = 0;
                $tuelt = 0;
                $wedp = 0;
                $wedab = 0;
                $wedlt = 0;
                $thurp = 0;
                $thurab = 0;
                $thurlt = 0;
                $frip = 0;
                $friab = 0;
                $frilt = 0;
                try {
                    $monp = Helper::loadStaffWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "present", "Monday");
                    $monab = Helper::loadStaffWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "absent", "Monday");
                    $monlt = Helper::loadStaffWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "late", "Monday");

                    $tuep = Helper::loadStaffWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "present", "Tuesday");
                    $tueab = Helper::loadStaffWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "absent", "Tuesday");
                    $tuelt = Helper::loadStaffWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "late", "Tuesday");

                    $wedp = Helper::loadStaffWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "present", "Wednesday");
                    $wedab = Helper::loadStaffWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "absent", "Wednesday");
                    $wedlt = Helper::loadStaffWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "late", "Wednesday");

                    $thurp = Helper::loadStaffWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "present", "Thursday");
                    $thurab = Helper::loadStaffWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "absent", "Thursday");
                    $thurlt = Helper::loadStaffWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "late", "Thursday");

                    $frip = Helper::loadStaffWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "present", "Friday");
                    $friab = Helper::loadStaffWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "absent", "Friday");
                    $frilt = Helper::loadStaffWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "late", "Friday");
                } catch (Exception $ex) {
                    // echo "<h1 class='text-center'>" . $ex->getMessage() . "</h1>";
                }
                ?>
                <canvas width="350" height="200" id="myCanvas"></canvas>
            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>
        <script>
            let ctx = document.getElementById('myCanvas').getContext('2d');
            let mixedChart = new Chart(ctx, {

                type: "bar",
                data: {
                    datasets: [{
                            label: 'Present',
                            data: [<?= $monp ?>, <?= $tuep ?>, <?= $wedp ?>, <?= $thurp ?>, <?= $frip ?>],
                            order: 1,
                            backgroundColor: [
                                "seagreen", "seagreen", "seagreen", "seagreen", "seagreen"
                            ],
                            borderColor: [
                                "seagreen", "seagreen", "seagreen", "seagreen", "seagreen"
                            ],
                            borderWidth: 1
                        },
                        {
                            label: 'Late',
                            data: [<?= $monlt ?>, <?= $tuelt ?>, <?= $wedlt ?>, <?= $thurlt ?>, <?= $frilt ?>],
                            type: 'bar',
                            order: 2,
                            backgroundColor: [
                                "orange", "orange", "orange", "orange", "orange"
                            ],
                            borderColor: [
                                "orange", "orange", "orange", "orange", "orange"
                            ],
                            borderWidth: 1
                        }, {
                            label: 'Absent',
                            data: [<?= $monab ?>, <?= $tueab ?>, <?= $wedab ?>, <?= $thurab ?>, <?= $friab ?>],
                            type: 'bar',
                            order: 2,
                            backgroundColor: [
                                "red", "red", "red", "red", "red"
                            ],
                            borderColor: [
                                "red", "red", "red", "red", "red"
                            ],
                            borderWidth: 1
                        },
                    ],
                    labels: ['Monday', 'Tuesday', 'Wednesday', 'Thurdsay', 'Friday']
                }
            });
        </script>