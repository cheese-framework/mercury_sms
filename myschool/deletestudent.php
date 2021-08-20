<?php

use App\Core\Helper;
use App\School\Student;

include "./includes/header.php";

if (isset($_GET['delstu'])) {
    $classId = $_GET['classId'];
    $year = $_GET['year'];
    $id = $_GET['delstu'];
    Student::deleteStudent($id, $schoolId);
    Helper::to("mystudents.php?classId=$classId&year=$year&page=" . $page);
}
