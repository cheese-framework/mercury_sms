<?php

use App\Core\Helper;
use App\Database\Database;
use App\Extra\Assessment\WorkshopAssessment;
use App\Helper\DateLib;
use App\Media\FileUpload;
use App\Notifiable\Components\Components;
use App\Queue\AssessmentQueuePublisher;
use App\Queue\TempStudent;
use App\School\AcademicYear;


include_once "./pages/header.pages.php";

$error = [];
$title = "";
$details = "";
$due = "";
$grade = 100;
$message = "";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // init workshop-assessment class
    $workshop = new WorkshopAssessment();

    // get request data
    $title = $_POST['title'];
    $details = $_POST['details'];
    $file = isset($_FILES['details-pdf']) ? $_FILES['details-pdf'] : NULL;
    $due = $_POST['due'];
    $class = (isset($_POST['class']) ? $_POST['class'] : "");
    $subject = (isset($_POST['subject']) ? $_POST['subject'] : "");
    $notify = (isset($_POST['notify']) ? TRUE : FALSE);
    $term = $_POST['term'];

    $year = AcademicYear::getCurrentYearId($schoolId);
    $filename = "";

    if (!Helper::isEmpty($title, $due, $class, $subject, $grade, $year)) {
        if ($file['name'] == '' && trim($details) == "") {
            $error[] = "Please upload a file or fill in the details area";
        } else {
            if ($file['name'] != "") {
                try {
                    $filename = FileUpload::uploadFile("files/", $file);
                } catch (Exception $e) {
                    $error[] = $e->getMessage();

                    if ($filename != "") {
                        if (file_exists('files/' . $filename)) {
                            @@unlink('files/' . $filename);
                        }
                    }
                }
            }
            $created = date("Y-m-d");
            // check if due date is less than created date
            $isInvert = DateLib::isInvert($created, $due);
            if ($isInvert) {
                $error[] = "Due date cannot be less than today's date";
            }

            if (empty($error)) {
                $filename = ($filename != FALSE) ? $filename : "";
                $details = nl2br($details);
                try {
                    $added =  $workshop->addAssessment($title, $details, $filename, $class, $subject, $due, $grade, $schoolId, $sms_userId, $year, $created, $term);
                    if ($added != FALSE) {
                        $due = strtotime($due);
                        $due = date("dS F, Y", $due);

                        if ($notify) {
                            $message = "Assessment has been created.<br>Sending notification to students now";
                            // send notification to queue
                            try {
                                $teacher = $sms_username;
                                $email = $sms_email;
                                $gender = $myTitle;
                                $recepients = Helper::getStudentIds($class);

                                if ($recepients != NULL) {
                                    foreach ($recepients as $student) {
                                        if (Helper::isUsingSMS($schoolId)) {
                                            $temp = new TempStudent(['twilio', 'mail'], $student->studentId);
                                        } else {
                                            $temp = new TempStudent(['mail'], $student->studentId);
                                        }

                                        $link = SCHOOL_URL . "/moodle/assessment_details.php?id=" . $added;
                                        $msg = "New Assessment Added.\r\nTitle: $title.\r\n" . $gender . " " . $teacher . " has posted a new assessment that is due on the $due.\n\rView more here $link.";
                                        // send notification to recepients
                                        $temp->notify($msg, $title, $teacher, $email);
                                    }
                                }
                            } catch (Exception $e) {
                                $error[] = $e->getMessage();
                                $error[] = "Temporarily can't send notication.<br>Manually send it in the assessment <a href='manage-assessments.php'>lists</a>.";
                            }
                        } else {
                            $message = "Assessment has been created.";
                        }

                        $title = "";
                        $details = "";
                        $due = "";
                    } else {
                        if ($filename != "") {
                            if (file_exists('files/' . $filename)) {
                                @@unlink('files/' . $filename);
                            }
                        }
                        $error[] = "Could not create assessment";
                    }
                } catch (\Throwable $th) {
                    if ($filename != "") {
                        if (file_exists('files/' . $filename)) {
                            @@unlink('files/' . $filename);
                        }
                    }
                    $error[] = $th->getMessage();
                }
            } else {
                if ($filename != "") {
                    if (file_exists('files/' . $filename)) {
                        @@unlink('files/' . $filename);
                    }
                }
                $error[] = "Something went wrong";
            }
        }
    } else {
        $error[] = "Fill in the required fields";
    }
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
                    <?= Components::header("Add Assessment", "h4", "center"); ?>
                    <form action="" method="post" autocomplete="off" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="term">* Term</label>
                            <select name="term" id="term" class="form-control">
                                <option value="term-1">Term 1</option>
                                <option value="term-2">Term 2</option>
                                <option value="term-3">Term 3</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="title">* Title</label>
                            <input type="text" name="title" id="title" placeholder="Title" class="form-control" value="<?= $title; ?>">
                        </div>
                        <div class="form-group">
                            <label for="details">Details <i class="text-small text-primary">(Optional: only if a file version is included)</i></label>
                            <textarea name="details" id="details" cols="10" rows="8" class="form-control editor" placeholder="Assessment Details"><?= $details; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="details-pdf">File Version <i class="text-small text-primary">(Optional: only if a text version is included)</i></label><br>
                            <input type="file" name="details-pdf" id="details-pdf">
                            <br>
                            <label>Accepted formats: .pdf, .docx, .odt, .xls, .txt, .html <br>Max size: 5MB</label>
                        </div>
                        <div class="form-group">
                            <label for="class">* Class</label>
                            <select name="class" id="class" class="form-control">
                                <?php
                                $db = Database::getInstance();
                                $db->query("SELECT classId FROM classes WHERE school=?");
                                $db->bind(1, $schoolId);
                                $result = $db->resultset();
                                $classList = [];
                                if ($result != null) {
                                    foreach ($result as $res) {
                                        $classList[] = $res->classId;
                                    }
                                } else {
                                    $classList = [];
                                }

                                if ($classList != null) {
                                    foreach ($classList as $cls) {
                                        echo "<option value='$cls'>" . Helper::classEncode($cls) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group" id="subjects-space">
                        </div>
                        <div class="form-group">
                            <label for="due">* Due Date</label>
                            <input type="date" name="due" id="due" placeholder="Due Date" class="form-control" value="<?= $due; ?>">
                        </div>
                        <div class="form-group">
                            <label for="grade">* Grade</label>
                            <input type="text" name="grade" id="grade" class="form-control" disabled value="<?= $grade; ?>">
                        </div>
                        <div class="form-group">
                            <label for="notify">Send notification to students? </label>
                            <input type="checkbox" name="notify" id="notify" class="form-check">
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Save" class="btn btn-primary">
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