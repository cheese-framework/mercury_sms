<?php

use App\Core\Helper;
use App\School\AcademicYear;
use App\School\Student;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId) || $sms_role != $ADMIN) {
  Helper::showNotPermittedPage();
}


$showStudents = false;
$students = null;
$level = null;
$currentYear = AcademicYear::getCurrentYearId($schoolId);

if ((isset($_GET['classes']) && $_GET['classes'] != "") && (isset($_GET['year']) && $_GET['year'] != "")) {
  $showStudents = true;
  [$classId, $level] = explode(",", $_GET['classes']);
  $nextYear = $_GET['year'];
  $students = Student::getStudents($classId, $schoolId);
} else {
  $showStudents = false;
}


?>
<div class="container-scroller">
  <?php include_once './includes/navbar.php'; ?>

  <div class="main-panel">
    <div class="content-wrapper pb-0">
      <div class="col-lg-12">
        <div class="card my-3">
          <div class="card-body">
            <?php if (!$showStudents) : ?>
              <h3>Generate Data</h3>
              <div class="col-lg-6 mt-3">
                <form action="" autocomplete="off" method="get">
                  <div class="form-group">
                    <label for="classes">Classes</label>
                    <select name="classes" id="classes" class="form-control">
                      <?php
                      $classes = Helper::loadClasses($schoolId);
                      foreach ($classes as $class) {
                        echo "<option value='" . $class->classId . "," . $class->levels . "'>{$class->className}</option>";
                      }
                      ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="classes">Next Academic Year</label>
                    <select name="year" id="year" class="form-control">
                      <?php
                      $years = AcademicYear::loadAcademicYears($schoolId);
                      foreach ($years as $year) {
                        if ($year->academicYearId != $currentYear) {
                          echo "<option value='" . $year->academicYearId . "'>{$year->academicYear}</option>";
                        }
                      }
                      ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <input type="submit" value="Generate" class="btn btn-primary">
                  </div>
                </form>
              </div>
            <?php else : ?>
              <h3>Students of <?= Helper::classEncode($classId) ?></h3>
              <div class="table-responsive">
                <table class="table table-hover table-striped">
                  <thead>
                    <tr>
                      <th>ID</th>
                      <th>Name</th>
                      <th>Gender</th>
                      <th>Action</th>
                      <th>Stepper</th>
                    </tr>
                  </thead>
                  <tbody id="students">
                    <?php
                    if ($students) {
                      foreach ($students as $student) {
                        echo "<tr>";
                        echo "<td>{$student->studentId}</td>";
                        echo "<td>{$student->fullname}</td>";
                        echo "<td>" . ($student->gender == "F" ? "Female" : "Male") . "</td>";
                        echo "<td><select class='form-control'>
                          <option value='up'>Promote</option>
                          <option value='stay'>Repeat</option>
                          <option value='down'>Demote</option>
                        </select></td>";
                        echo "<td><select class='form-control'>
                          <option value='1'>1</option>
                          <option value='2'>2</option>
                          <option value='3'>3</option>
                        </select></td>";
                        echo "</tr>";
                      }
                      echo "<tr>";
                      echo "<td></td>";
                      echo "<td></td>";
                      echo "<td></td>";
                      echo "<td><button class='btn btn-primary' id='proceed'>Proceed</button></td>";
                      echo "</tr>";
                    } else {
                      echo "<tr><td colspan=5 class='text-center'>No record found.</td></tr>";
                    }

                    ?>
                  </tbody>
                </table>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
    <?php include_once './includes/footer.php'; ?>
    <script>
      const proceedBtn = document.getElementById('proceed');
      const table = document.getElementById('students');


      proceedBtn.addEventListener('click', async () => {
        const data = [];
        const fragments = {
          classId: '<?= $classId ?>',
          level: '<?= $level ?>',
          school: '<?= $schoolId ?>',
          currentYear: '<?= $currentYear ?>',
          nextYear: '<?= $nextYear ?>'
        }
        for (let i = 0; i < table.children.length - 1; i++) {
          const id = table.children[i].children[0].textContent.trim();
          const decision = table.children[i].children[3].children[0].value.trim();
          const stepper = table.children[i].children[4].children[0].value.trim();
          data.push({
            id,
            decision,
            stepper
          })
        }
        const payload = {
          data,
          fragments
        }
        const response = await fetch('./ajax/levels.php', {
          method: 'POST',
          body: JSON.stringify(payload),
        });
        // window.location.reload();
      });
    </script>