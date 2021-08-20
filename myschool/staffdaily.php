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
            <div class="col-lg-8 col-md-10 col-sm-12 col-xs-12 mx-auto">
                <?php
                $day = date("l");
                $today = date("dS F, Y");
                $p = 0;
                $ab = 0;
                $lt = 0;
                try {
                    $present = Helper::loadStaffDaily(AcademicYear::getCurrentYearId($schoolId), date("d-m-Y"), $schoolId, "present");

                    $absent = Helper::loadStaffDaily(AcademicYear::getCurrentYearId($schoolId), date("d-m-Y"), $schoolId, "absent");
                    $late = Helper::loadStaffDaily(AcademicYear::getCurrentYearId($schoolId), date("d-m-Y"), $schoolId, "late");
                    if ($present != null) {
                        $p = $present;
                    }

                    if ($absent != null) {
                        $ab = $absent;
                    }

                    if ($late != null) {
                        $lt = $late;
                    }
                } catch (Exception $ex) {
                    echo "<h1 class='text-center'>" . $ex->getMessage() . "</h1>";
                }
                ?>
                <canvas width="350" height="235" id="myCanvas"></canvas>
            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>
        <script>
            let ctx = document.getElementById('myCanvas').getContext('2d');
            let chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Present', 'Late', 'Absent'],
                    datasets: [{
                        label: "Attendance for <?= $day . " " . $today; ?>",
                        data: [<?= $p ?>, <?= $lt ?>, <?= $ab ?>],
                        backgroundColor: [
                            'darkcyan',
                            'orange',
                            'red'
                        ],
                        borderColor: [
                            'darkcyan',
                            'orange',
                            'red'
                        ]
                    }]
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