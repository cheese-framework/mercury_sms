<?php

use App\Core\Helper;
use App\School\AcademicYear;
use App\School\Student;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

if ($sms_role == $ADMIN) {
    Helper::to("index.php");
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $class = $_POST['class'];
    $time = $_POST['time'];
    $date = strtotime($_POST['date']);
    $week = date('W', $date);
    $day = date("l", $date);
    $today = date("dS F, Y", $date);
    $date = date('d-m-Y', $date);
} else {
    Helper::to("chooseClassAttendance.php");
}

?>
<div class="container-scroller">
    <?php

    include_once './includes/navbar.php';
    ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <h3 class="text-center" style="text-transform: uppercase;">Take students attendance for today: <?= $today ?></h3>
            <p id="class" style="display: none;"><?= $class; ?></p>
            <p id="year" style="display: none;"><?= AcademicYear::getCurrentYearId($schoolId); ?></p>
            <div class="table-responsive">
                <button class="btn btn-primary" onclick="refreshTable(document.getElementById('class').textContent, '<?= $schoolId ?>')" id="refresh" style="display: none;">Refresh Table</button>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <!-- <tr> -->
                        <th>Name</th>
                        <th style="display: none;"></th>
                        <th>Class</th>
                        <th>Time</th>
                        <th>Status</th>
                        <!-- </tr> -->
                    </thead>
                    <tbody id="table">
                        <?php
                        try {
                            $data = Student::getMyStudents(
                                $class,
                                AcademicYear::getCurrentYearId($schoolId)
                            );
                            if ($data != null) {
                                foreach ($data as $datum) {
                                    echo "<tr>";
                                    echo "<td id='staff_id' style='display: none'>" .
                                        $datum->getStudentId() . "</td>";
                                    echo "<td>" . $datum->getStudentFullName() .
                                        "</td>";
                                    echo "<td>" .
                                        Helper::classEncode(
                                            $datum->getStudentClass()
                                        ) . "</td>";
                                    echo "<td>$time</td>";
                                    $status = Helper::hasBeenMarkedStudent(
                                        $date,
                                        AcademicYear::getCurrentYearId($schoolId),
                                        $schoolId,
                                        $datum->getStudentId(),
                                        $time
                                    );

                                    if ($status != false) {
                                        if ($status == "Present") {
                                            echo "<td><select class='form-control' id='status'><option>Present</option><option>Absent</option><option>Late</option></select></td>";
                                        } elseif ($status == "Absent") {
                                            echo "<td><select class='form-control' id='status'><option>Absent</option><option>Present</option><option>Late</option></select></td>";
                                        } else {
                                            echo "<td><select class='form-control' id='status'><option>Late</option><option>Absent</option><option>Present</option></select></td>";
                                        }
                                    } else {
                                        echo "<td><select class='form-control' id='status'><option>Present</option><option>Absent</option><option>Late</option></select></td>";
                                    }
                                    echo "</tr>";
                                }
                                echo "<tr><td></td><td></td><td></td><td><button class='btn btn-success save-attendance' onclick='saveOrUpdateAttendance()'>Save</button></td></tr>";
                            }
                        } catch (Exception $e) {
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php

        include_once './includes/footer.php';
        ?>

        <script>
            const attendanceTable = document.querySelector('#table');
            const saveAttendanceButton = document.querySelector('.save-attendance') || document.querySelector('.update-attendance');
            const attendanceDataset = [];
            const year = document.getElementById('year').textContent;
            const date = '<?= $date; ?>';
            const day = '<?= $day ?>';
            const week = '<?= $week ?>';
            const timeAttend = '<?= $time ?>';

            function saveOrUpdateAttendance() {
                const dataset = attendanceTable.children;
                for (let i = 0; i < dataset.length - 1; i++) {
                    const studentId = dataset[i].children[0].textContent;
                    const status = dataset[i].children[4].children[0].value;
                    const time = dataset[i].children[3].textContent;
                    const klass = document.getElementById('class').textContent;
                    const currentStudentData = {
                        studentId,
                        status,
                        time,
                        klass
                    };
                    attendanceDataset.push(currentStudentData);
                }

                // open an ajax request to the backend

                const xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        const response = xhr.responseText;
                        if (response === 'OK') {
                            saveAttendanceButton.textContent = "Saved!";
                            document.getElementById('refresh').style.display = "block";
                        } else {
                            alert("Something went wrong while saving attendance");
                        }
                    }
                };
                xhr.open('GET', `takeattendance.php?type=create_student&studentsData=${JSON.stringify(attendanceDataset)}&year=${year}&day=${day}&date=${date}&week=${week}`, true);
                xhr.send();
            }


            // refresh table function
            function refreshTable(klass, schoolId) {
                const xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        const response = xhr.responseText;
                        if (response.trim() != "") {
                            attendanceTable.innerHTML = response;
                        }
                    }
                };
                xhr.open("GET", `takeattendance.php?type=refresh&class=${klass}&schoolId=${schoolId}&date=${date}&time=${timeAttend}`, true);
                xhr.send();
            }
        </script>