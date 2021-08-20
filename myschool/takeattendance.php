<?php

use App\Core\Helper;
use App\School\AcademicYear;
use App\School\Student;

date_default_timezone_set('Africa/Banjul');
include_once '../init.php';

if (isset($_GET['type']) && $_GET['type'] == 'create_staff') {
    if (isset($_GET['status']) && isset($_GET['staffId'])) {
        $staffId = $_GET['staffId'];
        $status = strtolower($_GET['status']);
        $day = date('l');
        $date = date('d-m-Y');
        $week = date('W');
        $schoolId = $_SESSION['school'];
        $year = AcademicYear::getCurrentYearId($schoolId);
        $value = Helper::addStaffAttendance(
            $day,
            $date,
            $week,
            $status,
            $year,
            $staffId,
            $schoolId
        );
        if ($value == true) {
            echo "OK";
        } else {
            echo "ERROR";
        }
    } else {
        echo "ERROR";
    }
}


if (isset($_GET['type']) && $_GET['type'] == 'create_student') {
    $studentsData = $_GET['studentsData'] ?? null;
    if ($studentsData) {
        $studentsData = json_decode($studentsData, true);
        foreach ($studentsData as $singleStudent) {
            $studentId = $singleStudent['studentId'];
            $status = $singleStudent['status'];
            $day = $_GET['day'];
            $date = $_GET['date'];
            $week = $_GET['week'];
            $schoolId = $_SESSION['school'];
            $year = $_GET['year'];
            $class = $singleStudent['klass'];
            $time = $singleStudent['time'];
            Helper::addStudentAttendance($day, $date, $week, $status, $year, $studentId, $schoolId, $class, $time);
        }
        echo "OK";
    } else {
        echo "";
    }
} else {
    echo "";
}


if (isset($_GET['type']) && $_GET['type'] == 'refresh') {
    $class = $_GET['class'];
    $schoolId = $_GET['schoolId'];
    $date = $_GET['date'];
    $time = $_GET['time'];

    try {
        $data = Student::getMyStudents($class, AcademicYear::getCurrentYearId($schoolId));
        if ($data != null) {
            $tableData = "";
            foreach ($data as $datum) {
                $tableData .= "<tr>";
                $tableData .= "<td id='staff_id' style='display: none'>" .
                    $datum->getStudentId() . "</td>";
                $tableData .= "<td>" . $datum->getStudentFullName() .
                    "</td>";
                $tableData .= "<td>" .
                    Helper::classEncode(
                        $datum->getStudentClass()
                    ) . "</td>";
                $tableData .= "<td>$time</td>";
                $status = Helper::hasBeenMarkedStudent(
                    $date,
                    AcademicYear::getCurrentYearId($schoolId),
                    $schoolId,
                    $datum->getStudentId(),
                    $time
                );

                if ($status != false) {
                    if ($status == "Present") {
                        $tableData .= "<td><select class='form-control' id='status'><option>Present</option><option>Absent</option><option>Late</option></select></td>";
                    } elseif ($status == "Absent") {
                        $tableData .= "<td><select class='form-control' id='status'><option>Absent</option><option>Present</option><option>Late</option></select></td>";
                    } else {
                        $tableData .= "<td><select class='form-control' id='status'><option>Late</option><option>Absent</option><option>Present</option></select></td>";
                    }
                } else {
                    $tableData .= "<td><select class='form-control' id='status'><option>Present</option><option>Absent</option><option>Late</option></select></td>";
                }

                $tableData .= "</tr>";
            }
            $tableData .= "<tr><td></td><td></td><td></td><td><button class='btn btn-success update-attendance' onclick='saveOrUpdateAttendance()'>Update</button></td></tr>";
            echo $tableData;
        } else {
            echo "";
        }
    } catch (\Throwable $th) {
        //throw $th;
        echo "";
    }
}
