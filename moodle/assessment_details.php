<?php

use App\Core\Helper;
use App\Extra\Assessment\WorkshopAssessment;
use App\Media\FileUpload;
use App\Notifiable\Components\Components;
use App\School\AcademicYear;
use App\School\Subject;

include_once "./pages/header.pages.php";

$id = $_GET['id'] ?? "";

if (!$id) {
    Helper::to("dashboard.php");
}

$workshopAssessment = new WorkshopAssessment();
$data = $workshopAssessment->getAssessment($id);

if (!$data) {
    Helper::to("manage-assessments.php?page=$page");
}

// getSubmitted assignment if any

$submittedAssessment = Assessment::getSubmittedAssessment($id);

$submittedText = "";
$submittedFile = "";
$submittedFileExtension = "pdf";
$delId = 0;

if ($submittedAssessment) {
    $submittedText = $submittedAssessment->details;
    $submittedFile = $submittedAssessment->file;
    $delId = $submittedAssessment->id;
    if ($submittedFile != "") {
        $extensionArray = explode(".", $submittedFile);
        $submittedFileExtension = strtolower(end($extensionArray));
    }
} else {
    // create a simple submit class to affect has_been_graded
    class Submit
    {
        public $has_been_graded;
    }
    $submittedAssessment = new Submit();
    $submittedAssessment->has_been_graded = 0;
}

// add assessment

$submission = "";
$errors = [];

if (isset($_POST['addAssessment'])) {
    $file = $_FILES['file'];
    $submission = $_POST['submission'];

    if (!$submission && !$file['name']) {
        $errors[] = "Please upload a file or fill in the submission area";
    }

    $fileuploaded = "";

    try {
        if ($file['name']) {
            $dir = "./../workshop/files/submissions/";
            $fileuploaded = FileUpload::uploadFile($dir, $file);
            if (!$fileuploaded) {
                $errors[] = "Could not upload file";
                // delete whatever file uploaded
                if (file_exists($dir . $fileuploaded)) {
                    @@unlink($dir . $fileuploaded);
                }
            }
        }

        if (empty($errors)) {
            $bool = Assessment::submitAssessment($submission, $fileuploaded, $s_id, $class, $schoolId, $id, AcademicYear::getCurrentYearId($schoolId), $data->subject, $data->term);
            if ($bool) {
                $_SESSION['success_msg'] = "Submission has been created successfully";
                Helper::to("assessment_details.php?id=$id&success=1");
            } else {
                $errors[] = "Something went wrong";
            }
        }
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        // delete whatever file uploaded
        if (file_exists("./../workshop/files/submissions/$fileuploaded")) {
            if (!is_dir("./../workshop/files/submissions/$fileuploaded")) {
                @@unlink("./../workshop/files/submissions/$fileuploaded");
            }
        }
    }
}


// update assessment

if (isset($_POST['edit_submission'])) {
    $file = $_FILES['file'];
    $submission = $_POST['submission'];

    if (!$submission && !$file['name']) {
        $errors[] = "Please upload a file or fill in the submission area";
    }

    $fileuploaded = "";

    try {
        if ($file['name']) {
            $dir = "./../workshop/files/submissions/";
            $fileuploaded = FileUpload::uploadFile($dir, $file);
            if (!$fileuploaded) {
                $errors[] = "Could not upload file";
                // delete whatever file uploaded
                if (file_exists($dir . $fileuploaded)) {
                    @@unlink($dir . $fileuploaded);
                }
            }
        }

        if (empty($errors)) {

            if (trim($fileuploaded) == "") {
                $fileuploaded = $submittedFile;
            }

            $bool = Assessment::updateSubmission($submission, $fileuploaded, $s_id, $id);
            if ($bool) {
                $_SESSION['success_msg'] = "Submission has been updated successfully";
                Helper::to("assessment_details.php?id=$id&success=1");
            } else {
                $errors[] = "Something went wrong";
            }
        }
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
        // delete whatever file uploaded
        if (file_exists("./../workshop/files/submissions/$fileuploaded")) {
            if (!is_dir("./../workshop/files/submissions/$fileuploaded")) {
                @@unlink("./../workshop/files/submissions/$fileuploaded");
            }
        }
    }
}

// delete assessment

if (isset($_GET['delete_id']) && $_GET['delete_id'] != "") {
    $assessmentId = $_GET['delete_id'];
    $didDelete = Assessment::deleteSubmission($assessmentId, $id);
    if ($didDelete) {
        $_SESSION['success_msg'] = "Submission was deleted successfully";
        Helper::to("assessment_details.php?id=$id&success=1");
    } else {
        $errors[] = "Could not delete assessment";
    }
}


?>
<?php include_once "./pages/navbar.pages.php"; ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="mx-auto row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <?= Components::header($data->title, "h4"); ?>
                    </div>
                    <?php
                    if ($data->details) {
                        echo "<div class=\"card-body\" style=\"height:fit-content; max-height:250px;overflow-y:auto\" " . Components::body($data->details) . " 
                 </div>";
                    }
                    ?>
                    <div class="card-footer">
                        <?php
                        if ($data->file) {
                            $ext = explode(".", $data->file);
                            $extension = strtolower(end($ext));
                            if ($extension == "pdf") {
                                $class = "fas fa-file-pdf";
                            } else if ($extension == "html") {
                                $class = "fab fa-html5";
                            } else {
                                $class = "fas fa-file-word";
                            }
                            echo "<a href='../workshop/files/" . $data->file . "' class='btn btn-success' target='_blank'>View <i class='$class'></i></a>";
                            echo "&nbsp;&nbsp;&nbsp;";
                            echo "<a href='../workshop/files/" . $data->file . "' class='btn btn-primary' download>Download <i class='$class'></i></a>";
                        }

                        ?>
                    </div>
                </div>
                <?= Components::header("Due: " . date("dS F, Y", strtotime($data->due)), "h4"); ?>

            </div>
            <div class="col-lg-5">
                <?= Components::header("Subject: " . Subject::getSubject($data->subject, $schoolId)->subject, "h6"); ?>
                <?= Components::header("Status: " . (Helper::isAssessmentValid($data->created_date, $data->id) ? "Valid" : "Expired"), "h6"); ?>
                <?= Components::header("Grade Mark: " . $data->grade, "h6"); ?>
                <?= Components::header("Class: " . Helper::classEncode($data->class), "h6"); ?>
                <?= Components::header("Grading Status: " . ($submittedAssessment->has_been_graded == 1 ? "Graded" : "Not Graded"), "h6"); ?>
                <?php if (Helper::isAssessmentValid($data->created_date, $data->id) && $submittedAssessment->has_been_graded == 0) : ?>
                    <button class="btn btn-success" id="make-submit" onclick="showSubmit()">Make Submission &downarrow;</button>
                <?php else : ?>
                    <button class="btn btn-warning" id="expired" style="text-transform: uppercase;" disabled>You can no longer make submissions on this assessment</button>
                <?php endif; ?>
            </div>
        </div>


        <!-- show submission div if only assessment is still valid -->

        <?php if (Helper::isAssessmentValid($data->created_date, $data->id) && $submittedAssessment->has_been_graded == 0) : ?>

            <!-- submission div -->

            <div id="submit_div" class="mt-3" style="display: none;">
                <?php

                if (!empty($errors)) {
                    unset($_SESSION['success_msg']);
                    echo "<div class='alert alert-danger'>";
                    foreach ($errors as $e) {
                        echo $e . "<br>";
                    }
                    echo "</div>";
                }


                if (isset($_SESSION['success_msg']) && (isset($_GET['success']) && $_GET['success'] == "1")) {
                    echo "<div class='alert alert-success'>";
                    echo $_SESSION['success_msg'];
                    echo "</div>";
                }

                ?>

                <?php if (!Assessment::hasSubmitted($s_id, $id)) : ?>

                    <!-- display form if assessment has not been submitted -->

                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="submission">Submission <i class="text-small text-primary">(Optional: only if a file version is included)</i></label>
                            <textarea name="submission" placeholder="Submission goes here" id="submission" cols="5" rows="3" class="form-control editor"><?= $submission; ?></textarea>
                        </div>
                        <div class="form-group">
                            <label for="file">File Version <i class="text-small text-primary">(Optional: only if a text version is included)</i></label><br>
                            <input type="file" name="file" id="file">
                        </div>
                        <div class="form-group">
                            <input type="submit" value="Submit" class="btn btn-dark" name="addAssessment">
                        </div>
                    </form>

                    <!-- display form if assessment has been submitted -->

                <?php else : ?>
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="submission">Submission <i class="text-small text-primary">(Optional: only if a file version is included)</i></label>
                            <textarea name="submission" id="submission" cols="30" rows="10" class="form-control editor"><?= ($submission) ? $submission : $submittedText ?></textarea>
                        </div>

                        <!-- display file if only it was submitted -->
                        <?php if ($submittedFile) : ?>
                            <div class="form-group">
                                <?php
                                if ($submittedFileExtension == "pdf") {
                                    $class = "fas fa-file-pdf";
                                } else if ($submittedFileExtension == "html") {
                                    $class = "fab fa-html5";
                                } else {
                                    $class = "fas fa-file-word";
                                }
                                ?>
                                <label for="submitted_file">Submitted File: </label>
                                <a href="../workshop/files/submissions/<?= $submittedFile; ?>" id="submitted_file" class="btn btn-primary" target="_blank">View <i class="<?= $class; ?>"></i></a>
                            </div>
                        <?php endif; ?>

                        <div class="form-group">
                            <label for="file">File Version <i class="text-small text-primary">(Optional: only if a text version is included)</i></label><br>
                            <input type="file" name="file" id="file">
                        </div>
                        <div class="form-group">
                            <input type="submit" name="edit_submission" value="Edit Assessment" class="btn btn-success">
                            <a href="assessment_details.php?id=<?= $id ?>&delete_id=<?= $delId ?>" class="btn btn-danger">Delete Submission</a>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        <?php else : ?>

            <?php if (Assessment::hasSubmitted($s_id, $id)) : ?>
                <div class="card mt-3">
                    <div class="card-body">
                        <p>
                            <?= wordwrap($submittedText, 100, "<br>") ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</div>


<?php include_once "./pages/footer.pages.php"; ?>
<script src="../myschool/assets/custom/ckeditor.js"></script>

<script>
    try {
        ClassicEditor
            .create(document.querySelector('.editor'), {
                toolbar: ['Heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'undo', 'redo'],
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
                // console.log(error);
            });
    } catch (err) {

    }

    try {
        let open = false;

        (() => {
            showSubmit();
        })()

        function showSubmit() {
            if (open) {
                document.getElementById('submit_div').style.display = "none";
                open = false;
                document.getElementById('make-submit').innerHTML = "Make Submission &downarrow;";
                document.getElementById('make-submit').className = "btn btn-success";
            } else {
                document.getElementById('submit_div').style.display = "block";
                open = true;
                document.getElementById('make-submit').innerHTML = "Hide Submission &uparrow;";
                document.getElementById('make-submit').className = "btn btn-danger";
            }

        }
    } catch (error) {
        // console.log(error);
    }
</script>