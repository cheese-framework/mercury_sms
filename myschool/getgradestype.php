<?php

use App\Core\Helper;
use App\School\Student;
use App\School\Subject;

include_once "../init.php";


if (isset($_GET['sub_id']) && $_GET['sub_id'] != "") {
    $id = $_GET['sub_id'];
    $data = Helper::getGradeTypes($id);
    if (trim($data) == "") {
        echo "<div class='form-group'>";
        echo "<label>Exam</label>";
        echo "<input type='text' name='exam' class='form-control' value='0' placeholder='Can be left blank'>";
        echo "</div>";
        echo '<div class="form-group">
                <input type="submit" value="Add Result" class="btn btn-dark" name="addResult" />
             </div>';
    } else {
        $newData = explode(",", $data);
        foreach ($newData as $nd) {
            echo "<div class='form-group'>";
            echo "<label>$nd (Generated Dynamically) </label>";
            echo "<input type='text' name='$nd' class='form-control' value='0' />";
            echo "</div>";
        }
        echo "<div class='form-group'>";
        echo "<label>Exam</label>";
        echo "<input type='text' name='exam' class='form-control' value='0' placeholder='Can be left blank'>";
        echo "</div>";
        echo '<div class="form-group">
                <input type="submit" value="Add Result" class="btn btn-dark" name="addResult" />
             </div>';
    }
}


if (isset($_GET['student']) && $_GET['student'] != "") {
    $student = $_GET['student'];
    $year = $_GET['year'];
    $class = $_GET['class'];
    $teacher = $_GET['teacher'];
    $school = $_GET['school'];

    // get students electives
    $mySubjects = Student::getMySubjects($student);

    // get core subjects
    if (!Helper::isATeacherInClass($class, $teacher, $school)) {
        $teachersSubject = [];
        $subjects = Subject::getMySubjectsByLevel($teacher, $school, $class);
        foreach ($subjects as $sub) {
            $teachersSubject[] = $sub;
        }
        $clean = [];
        $subjectTeacher = Subject::getMySubjects($teacher, $school);
        $subs = [];
        if ($subjectTeacher) {
            foreach ($subjectTeacher as $sub) {
                $subs[] = $sub->subjectId;
            }
        }
        foreach ($mySubjects as $subZero) {
            if (in_array($subZero, $subs)) {
                $clean[] = $subZero;
            }
        }
        $teachersSubject = implode(",", $teachersSubject);
        $teacherSubject = $teachersSubject;
        $mySubjects = $clean;
    } else {
        $teacherSubject = Subject::getSubjectsByLevel($school, $class);
        $teacherSubject = implode(",", $teacherSubject);
    }
    $teacherSubject = explode(",", $teacherSubject);
    if ($mySubjects != NULL) {
        $allSubjects = array_merge_recursive($mySubjects, $teacherSubject);
    } else {
        $allSubjects = $teacherSubject;
    }
    $uniqueSubjectsList = array_unique($allSubjects);

    // loop through and create a select field
    $select = "";
    if ($uniqueSubjectsList) {
        $select .= "<select name='subjects' id='subjects' class='form-control'>";
        foreach ($uniqueSubjectsList as $list) {
            $temp = Subject::getSubject($list, $school);
            if ($temp) {
                $select .= "<option value='$temp->subjectId'>$temp->subject</option>";
            }
        }
    }
    echo $select;
}
