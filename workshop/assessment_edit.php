<?php

use App\Core\Helper;
use App\Database\Database;
use App\Extra\Assessment\WorkshopAssessment;
use App\Helper\DateLib;
use App\Media\FileUpload;
use App\Notifiable\Components\Components;
use App\Queue\AssessmentQueuePublisher;
use App\School\AcademicYear;


include_once "./pages/header.pages.php";

// get assessment ID
$assessmentId = $_GET['id'] ?? "";
if (!$assessmentId) {
  Helper::to("dashboard.php");
}

// init workshop-assessment class
$workshop = new WorkshopAssessment();

$assessmentData = $workshop->getAssessment($assessmentId);

if (!$assessmentData) {
  Helper::to("dashboard.php");
}

$error = [];
$title = "";
$details = "";
$due = "";
$grade = "";
$message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
}

?>
<?php include_once "./pages/navbar.pages.php"; ?>
<div class="container-fluid">
  <div class="row justify-content-center">
    <div class="col-lg-10 mx-auto">
      <div class="card">
        <div class="card-body">
          <?php
          if (!empty($error)) {
            echo "<div class='alert alert-danger col-lg-12' id='msg'>";
            foreach ($error as $e) {
              echo $e . "<br>";
            }
            echo "</div>";

          ?>

            <script>
              let msg = document.getElementById('msg');
              setTimeout(() => {
                msg.style.display = "none";
              }, 10000);
            </script>
          <?php
          }

          if (trim($message) != "") {
            echo "<div class='alert alert-success col-lg-12' id='msg'>" . $message . "</div>";
          ?>
            <script>
              let msg2 = document.getElementById('msg');
              setTimeout(() => {
                msg2.style.display = "none";
              }, 15000);
            </script>
          <?php
          }

          ?>
          <?= Components::header("Update Assessment", "h4", "center"); ?>
          <form action="" method="post" autocomplete="off" enctype="multipart/form-data">
            <div class="form-group">
              <label for="title">* Title</label>
              <input type="text" name="title" id="title" placeholder="Title" class="form-control" value="<?= $assessmentData->title; ?>">
            </div>
            <div class="form-group">
              <label for="details">Details <i class="text-small text-primary">(Optional: only if a file version is included)</i></label>
              <textarea name="details" id="details" cols="10" rows="8" class="form-control editor" placeholder="Assessment Details"><?= $assessmentData->details; ?></textarea>
            </div>
            <div class="form-group">
              <label for="details-pdf">File Version <i class="text-small text-primary">(Optional: only if a text version is included)</i></label><br>
              <input type="file" name="details-pdf" id="details-pdf">
              <br>
              <label>Accepted formats: .pdf, .docx, .odt, .xls, .txt, .html <br>Max size: 5MB</label>
            </div>

            <div class="form-group" id="subjects-space">
            </div>
            <div class="form-group">
              <label for="due">* Due Date</label>
              <input type="date" name="due" id="due" placeholder="Due Date" class="form-control" value="<?= $assessmentData->due; ?>">
            </div>
            <div class="form-group">
              <label for="grade">* Grade</label>
              <input type="text" name="grade" id="grade" placeholder="Grade. Eg: 100" class="form-control" value="<?= $assessmentData->grade; ?>">
            </div>
            <div class="form-group">
              <input type="submit" value="Update" class="btn btn-primary">
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>


<?php include_once "./pages/footer.pages.php"; ?>
<script src="../myschool/assets/custom/ckeditor.js"></script>
<script>
  let subjectSpace = document.getElementById('subjects-space');
  (function() {
    let selectedClass = document.getElementById('class');
    getSubjects(selectedClass.value, '<?= $sms_userId; ?>', '<?= $schoolId; ?>');
    selectedClass.addEventListener('change', function() {
      let classId = this.value;
      getSubjects(classId, '<?= $sms_userId; ?>', '<?= $schoolId; ?>');
    });
  })();

  function getSubjects(classId, user, school) {
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
      if (xhr.readyState == 4 && xhr.status == 200) {
        const response = xhr.response;
        subjectSpace.innerHTML = response;
      }
    };
    xhr.open("GET", `ajax/getSubjects.php?class=${classId}&user=${user}&school=${school}`, true);
    xhr.send();
  }


  try {
    ClassicEditor
      .create(document.querySelector('.editor'), {
        toolbar: ['Heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote'],
        heading: {
          options: [{
            model: 'paragraph',
            title: 'Mercury-Paragraph',
            class: 'ck-heading_paragraph'
          }, {
            model: 'heading1',
            view: 'h1',
            title: 'Mercury-Heading-1',
            class: 'ck-heading_heading1'
          }, {
            model: 'heading2',
            view: 'h2',
            title: 'Mercury-Heading-2',
            class: 'ck-heading_heading2'
          }, {
            model: 'heading3',
            view: 'h3',
            title: 'Mercury-Heading-3',
            class: 'ck-heading_heading3'
          }]
        }
      })
      .catch(error => {
        console.log(error);
      });
  } catch (err) {

  }
</script>