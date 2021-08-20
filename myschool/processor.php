<?php

use App\Core\Helper;
use App\School\Student;

include '../init.php';

if (isset($_GET['class']) && isset($_GET['year']) && isset($_GET['term'])) {
    $class = $_GET['class'];
    $year = $_GET['year'];
    $term = $_GET['term'];

    try {
        Helper::generateFeesTable($class, $year, $term, $_SESSION['school']);
    } catch (Exception $e) {
        echo "<div class='text-center'><h3>" . $e->getMessage() . "</h3></div>";
    }
}


if (isset($_GET['delstu'])) {
    $id = $_GET['delstu'];
    Student::deleteStudent($id, $_SESSION['school']);
    Helper::to("mystudents.php?classId=$classId&year=$year&page=" . $pager->getPage());
}
