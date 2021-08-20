<?php

use App\Core\Helper;
use App\School\AcademicYear;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

if (isset($_GET['class']) && $_GET['class'] != 0) {
    $sms_class = $_GET['class'];
} else {
    $sms_class = 0;
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
                    $monp = Helper::loadWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "present", "Monday", $sms_class);
                    $monab = Helper::loadWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "absent", "Monday", $sms_class);
                    $monlt = Helper::loadWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "late", "Monday", $sms_class);

                    $tuep = Helper::loadWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "present", "Tuesday", $sms_class);
                    $tueab = Helper::loadWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "absent", "Tuesday", $sms_class);
                    $tuelt = Helper::loadWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "late", "Tuesday", $sms_class);

                    $wedp = Helper::loadWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "present", "Wednesday", $sms_class);
                    $wedab = Helper::loadWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "absent", "Wednesday", $sms_class);
                    $wedlt = Helper::loadWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "late", "Wednesday", $sms_class);

                    $thurp = Helper::loadWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "present", "Thursday", $sms_class);
                    $thurab = Helper::loadWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "absent", "Thursday", $sms_class);
                    $thurlt = Helper::loadWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "late", "Thursday", $sms_class);

                    $frip = Helper::loadWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "present", "Friday", $sms_class);
                    $friab = Helper::loadWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "absent", "Friday", $sms_class);
                    $frilt = Helper::loadWeekly(date("W"), AcademicYear::getCurrentYearId($schoolId), $schoolId, "late", "Friday", $sms_class);
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
                            label: 'Present (Morning and Afternoon)',
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
                            label: 'Late (Morning and Afternoon)',
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
                            label: 'Absent (Morning and Afternoon)',
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
                },
                options: {
                    responsive: true,
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true
                            }
                        }]
                    }
                }
            });
        </script>