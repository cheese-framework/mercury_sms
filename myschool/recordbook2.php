<?php

use App\Core\Helper;
use App\School\Student;
use App\School\Subject;



function recordBook($year, $term, $class, $school, $teacher)
{

  try {
    if (!Helper::isATeacherInClass($class, $teacher, $school)) {
      $data = Subject::getMySubjectsInClass($class, $teacher, $school);
    } else {
      $data = Subject::returnSubject($class, $school);
    }

    $stu = Student::getStudents($class, $school);
    if ($data != NULL && $stu != null) {
      $subjectId = [];
      if (Helper::canChooseSubject($class)) {
        echo "<p>Click on view progress to view a student's electives.</p>";
      }
      // echo "<a class='btn btn-primary' href='gradebook.php?export&term={$term}&avenue=result&year={$year}&class={$class}' id='export'>Export To CSV</a>";
      echo "<p class='text-info'>You may need to scroll left and right to view the whole content.</p>";
      echo "<div class='table-responsive'>";
      echo "<table class='table table-striped table-hover table-bordered dt'>";
      echo "<thead style='position: sticky;top:0'>";
      echo "<tr>";
      echo "<th><b>Student Name</b></th>";
      foreach ($data as $d) {
        array_push($subjectId, $d->subjectId);
        echo "<th class='text-center'><b>$d->subject</b></th>";
      }
      echo "<th><b>Total</b></th>";
      echo "<th>Progress</th>";
      echo "</tr>";
      echo "</thead>";
      echo "<tbody>";
      foreach ($stu as $s) {
        $grandTotal = 0;
        echo "<tr>";
        echo "<td>$s->fullname</td>";
        for ($i = 0; $i < count($subjectId); $i++) {
          $sheets = Helper::getResult($year, $term, $subjectId[$i], $s->studentId, $school);
          if (!is_null($sheets)) {
            echo "<td>";
            echo "<table class='table table-bordered table-striped table-hover'><thead><tr>";
            if ($sheets[0] != null) {
              foreach ($sheets[0] as $sheet) {
                if (key_exists(1, $sheet)) {
                  echo "<th class='text-center'><b>" . $sheet[0] . "</b></th>";
                } else {
                  echo "<th></th>";
                }
              }
            }
            echo "<th class='text-center'><b>Exam</b></th>";
            echo "</tr></thead><tbody><tr>";
            $total = 0;
            foreach ($sheets[0] as $sheet) {
              $value = (array_key_exists(1, $sheet) ? $sheet[1] : 0);
              $total += number_format($value);
              if (key_exists(1, $sheet)) {
                echo "<td class='text-center'>$value</td>";
              } else {
                echo "<td></td>";
              }
            }
            $final = Helper::getFinalMark(Helper::$resId);
            $total += $sheets[1];
            $originalGrade = round(($total / $final) * 100, 2);
            $grandTotal += $originalGrade;
            $total = 0;
            echo "<td class='text-center'>" . $sheets[1] . "</td>";
            echo "</tr></tbody></table></td>";
          } else {
            echo "<td class='text-center'><b>...</b></td>";
          }
        }
        echo "<td><b>" . round($grandTotal) . "</b></td>";

        // viewprogress.php?class=1&year=1&term=term-3&studentId=32
        echo "<td><a href='viewprogress.php?class=$class&year=$year&term=$term&studentId=" . $s->studentId . "' class='btn btn-primary' target='_blank'>View Progress</a></td>";
        echo "</tr>";
      }

      echo "</tbody>";
      echo "</table>";
      echo "</div>";
    } else {

      echo "<h4>No record found!</h4>";
    }
  } catch (Exception $ex) {
    echo "<h4>" . $ex->getMessage() . "</h4>";
  }
}
