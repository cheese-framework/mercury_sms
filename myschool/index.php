<?php

use App\Database\Database;
use App\School\AcademicYear;
use App\School\Student;
use App\Core\Helper;

include_once './includes/header.php'; ?>
<div class="container-scroller">
    <!-- partial:partials/_sidebar.html -->
    <?php include_once './includes/navbar.php'; ?>
    <!-- partial -->
    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row my-2">
                <div class="card col-lg-5 mr-2">
                    <h4 class="text-center pt-3">Number of Students </h4>
                    <div class="card-body">
                        <canvas id="data-floyd" width="300" height="300"></canvas>
                        <?php
                        $male = 0;
                        $female = 0;
                        $total = 0;
                        if ($sms_role == $ADMIN || !Helper::hasBeenAssignedAClass($sms_userId, $schoolId, $sms_role)) {
                            try {
                                $male = Student::getNumGender(0, AcademicYear::getAcademicYearId(AcademicYear::getCurrentYear($schoolId), $schoolId))["male"];
                                $female = Student::getNumGender(0, AcademicYear::getAcademicYearId(AcademicYear::getCurrentYear($schoolId), $schoolId))["female"];
                                $total = $male + $female;
                            } catch (Exception $ex) {
                                echo $ex->getMessage();
                            }
                        } else {
                            $db = Database::getInstance();
                            $db->query("SELECT classTeacher,classId FROM classes");
                            $result = $db->resultset();
                            $classList = [];
                            if ($result != null) {
                                foreach ($result as $res) {
                                    $list = explode(",", $res->classTeacher);
                                    foreach ($list as $l) {
                                        if ($l == $sms_userId) {
                                            $classList[] = $res->classId;
                                        }
                                    }
                                }
                            } else {
                                $classList = [];
                            }

                            if ($classList != null) {
                                foreach ($classList as $cls) {
                                    $male += Student::getNumGender($cls, AcademicYear::getAcademicYearId(AcademicYear::getCurrentYear($schoolId), $schoolId))["male"];
                                    $female += Student::getNumGender($cls, AcademicYear::getAcademicYearId(AcademicYear::getCurrentYear($schoolId), $schoolId))["female"];
                                }
                                $total = $male + $female;
                            }
                        }
                        ?>

                    </div>
                </div>
                <div class="col-lg-1"></div>
                <div class="card col-lg-5 mr-2">
                    <h4 class="text-center pt-3">Number of Staffs</h4>
                    <div class="card-body">
                        <canvas id="staff-floyd" width="300" height="300"></canvas>
                        <?php
                        try {
                            $m = Helper::getNumStaffs("M", $schoolId);
                            $f = Helper::getNumStaffs("F", $schoolId);
                            $t = $m + $f;
                        } catch (Exception $ex) {
                            //                                echo $ex->getMessage();
                        }
                        ?>
                    </div>
                </div>
                <!-- This month -->

            </div>

        </div>

        <?php include_once "includes/footer.php"; ?>
        <script>
            let ctx = document.getElementById('data-floyd').getContext('2d');
            let chart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Male', 'Female', 'Total'],
                    datasets: [{
                        label: "Number of Enrollment for <?= AcademicYear::getCurrentYear($schoolId); ?>",
                        data: [<?= $male ?>, <?= $female ?>, <?= $total ?>],
                        backgroundColor: [
                            'rgba(16, 11, 173, 1)',
                            'rgba(218, 10, 177, 1)',
                            'rgba(96, 11, 174, 1)'
                        ],
                        borderColor: [
                            'rgba(16, 11, 173, 1)',
                            'rgba(218, 10, 177, 1)',
                            'rgba(75,12,200)'
                        ]
                    }]
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



            ctx = document.getElementById('staff-floyd').getContext('2d');
            chart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Male', 'Female'],
                    datasets: [{
                        label: "Staff Statistics",
                        data: [<?= $m ?>, <?= $f ?>],
                        backgroundColor: [
                            'rgba(16, 11, 173, 1)',
                            'rgba(172, 33, 145, 1)'
                        ],
                        borderColor: [
                            'rgba(16, 11, 173, 1)',
                            'rgba(172, 33, 145, 1)'
                        ]
                    }]
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