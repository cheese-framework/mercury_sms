<?php

use App\Core\Helper;
use App\School\Subject;

include_once "../../init.php";

if (isset($_GET['class'])) {
    $class = $_GET['class'];
    $user = $_GET['user'];
    $school = $_GET['school'];

    $subjects = "<label for='subject'>* Subject</label>
    <select name='subject' id='subject' class='form-control'>";
    if (Helper::canChooseSubject($class)) {
        $data = Subject::getSubjectsByLevelExplicit($school, $class);
        if ($data) {
            foreach ($data as $datum) {
                $temp = Subject::getSubject($datum, $school);
                $subjects .= "<option value='" . $temp->subjectId . "'>" . $temp->subject . "</option>";
            }
        }
    } else if (Helper::isATeacherInClass($class, $user, $school)) {
        $data = Subject::returnSubject($class, $school);
        if ($data != NULL) {
            foreach ($data as $d) {
                $subjects .= "<option value='" . $d->subjectId . "'>" . $d->subject . "</option>";
            }
        }
    } else {
        $data = Subject::getMySubjectsInClass($class, $user, $school);
        if ($data != NULL) {
            foreach ($data as $d) {
                $subjects .= "<option value='" . $d->subjectId . "'>" . $d->subject . "</option>";
            }
        }
    }

    $subjects .= "</select>";

    echo $subjects;
}
