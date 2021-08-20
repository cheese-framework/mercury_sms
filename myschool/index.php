<?php

use App\Database\Database;
use App\School\AcademicYear;
use App\School\Student;
use App\Core\Helper;
use App\Helper\Voucher\Voucher;

include_once './includes/header.php'; ?>
<div class="container-scroller">
    <!-- partial:partials/_sidebar.html -->
    <?php include_once './includes/navbar.php'; ?>
    <!-- partial -->
    <div class="main-panel">
        <?php
        // Voucher::addVoucher('YEAR', '2022-07-20', 7650, TRUE);
        // Voucher::addVoucher('MONTH', '2021-09-21', 3450, TRUE);

        ?>
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
                            'rgba(45,199,32)',
                            'rgba(255,206,86)',
                            'rgba(75,12,200)'
                        ],
                        borderColor: [
                            'rgba(45,199,32)',
                            'rgba(255,206,86)',
                            'rgba(75,12,200)'
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



            ctx = document.getElementById('staff-floyd').getContext('2d');
            chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Male', 'Female'],
                    datasets: [{
                        label: "Staff Statistics",
                        data: [<?= $m ?>, <?= $f ?>],
                        backgroundColor: [
                            'rgba(45,199,32)',
                            'rgba(255,206,86)'
                        ],
                        borderColor: [
                            'rgba(45,199,32)',
                            'rgba(255,206,86)'
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