<?php

use App\Core\Helper;
use App\School\AcademicYear;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

if (isset($_GET['class']) && $_GET['class'] != "") {
    $sms_class = $_GET['class'];
} else {
    Helper::to("chooseclassdaily.php");
}
?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="col-lg-9 col-md-10 col-sm-12 col-xs-12 mx-auto">
                <?php
                $day = date("l");
                $today = date("dS F, Y");
                $pm = 0;
                $pa = 0;
                $abm = 0;
                $aba = 0;
                $ltm = 0;
                $lta = 0;
                try {
                    $presentMorning = Helper::loadDaily(AcademicYear::getCurrentYearId($schoolId), date("d-m-Y"), $schoolId, "present", "Morning", $sms_class);
                    $presentAfternoon = Helper::loadDaily(AcademicYear::getCurrentYearId($schoolId), date("d-m-Y"), $schoolId, "present", "Afternoon", $sms_class);

                    $absentMorning = Helper::loadDaily(AcademicYear::getCurrentYearId($schoolId), date("d-m-Y"), $schoolId, "absent", "Morning", $sms_class);
                    $absentAfternoon = Helper::loadDaily(AcademicYear::getCurrentYearId($schoolId), date("d-m-Y"), $schoolId, "absent", "Afternoon", $sms_class);

                    $lateMorning = Helper::loadDaily(AcademicYear::getCurrentYearId($schoolId), date("d-m-Y"), $schoolId, "late", "Morning", $sms_class);
                    $lateAfternoon = Helper::loadDaily(AcademicYear::getCurrentYearId($schoolId), date("d-m-Y"), $schoolId, "late", "Afternoon", $sms_class);


                    if ($presentMorning != null) {
                        $pm = $presentMorning;
                    }

                    if ($presentAfternoon != null) {
                        $pa = $presentAfternoon;
                    }

                    if ($absentMorning != null) {
                        $abm = $absentMorning;
                    }

                    if ($absentAfternoon != null) {
                        $aba = $absentAfternoon;
                    }

                    if ($lateMorning != null) {
                        $ltm = $lateMorning;
                    }

                    if ($lateAfternoon != null) {
                        $lta = $lateAfternoon;
                    }
                } catch (Exception $ex) {
                    echo "<h1 class='text-center'>" . $ex->getMessage() . "</h1>";
                }
                ?>
                <canvas width="350" height="200" id="myCanvas"></canvas>
            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>
        <script>
            let ctx = document.getElementById('myCanvas').getContext('2d');
            let chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    datasets: [{
                            label: 'Present',
                            data: [<?= $pm ?>, <?= $pa ?>],
                            order: 1,
                            backgroundColor: [
                                "seagreen", "seagreen"
                            ],
                            borderColor: [
                                "seagreen", "seagreen"
                            ],
                            borderWidth: 1
                        },
                        {
                            label: 'Late',
                            data: [<?= $ltm ?>, <?= $lta ?>],
                            type: 'bar',
                            order: 2,
                            backgroundColor: [
                                "orange", "orange"
                            ],
                            borderColor: [
                                "orange", "orange"
                            ],
                            borderWidth: 1
                        }, {
                            label: 'Absent',
                            data: [<?= $abm ?>, <?= $aba ?>],
                            type: 'bar',
                            order: 2,
                            backgroundColor: [
                                "red", "red"
                            ],
                            borderColor: [
                                "red", "red"
                            ],
                            borderWidth: 1
                        },
                    ],
                    labels: ['Morning', 'Afternoon']
                },
                options: {
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