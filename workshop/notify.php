<?php

use App\Core\Helper;
use App\Extra\Assessment\WorkshopAssessment;
use App\Queue\TempStudent;

include_once "./pages/header.pages.php"; ?>
<?php include_once "./pages/navbar.pages.php";

// get assessment id
$id = $_GET['id'] ?? "";

if (!$id) {
    Helper::to("dashboard.php");
}

$error = "";

// send notification
$workshop = new WorkshopAssessment();
$data = $workshop->getAssessment($id);

if ($data) {
    $class = $data->class;
    $userId = $data->created_by;
    $teacher = $sms_username;
    $email = $sms_email;
    $gender = $myTitle;
    $due = strtotime($data->due);
    $due = date("dS F, Y", $due);
    $title = $data->title;
    try {
        $recepients = Helper::getStudentIds($class);
        if ($recepients != NULL) {
            foreach ($recepients as $student) {
                if (Helper::isUsingSMS($schoolId)) {
                    $temp = new TempStudent(['twilio', 'mail'], $student->studentId);
                } else {
                    $temp = new TempStudent(['mail'], $student->studentId);
                }

                $link = SCHOOL_URL . "/moodle/assessment_details.php?id=" . $id;
                $msg = "New Assessment Added.\r\nTitle: $title.\r\n" . $gender . " " . $teacher . " has posted a new assessment that is due on the $due.\n\rView more here $link.";
                // send notification to recepients
                $temp->notify($msg, $title, $teacher, $email);
            }
        }
        echo "<div class='alert alert-success'>Notification sent!</div>";
        echo "<button class='btn btn-primary' onclick='closeWindow()'>Close Window</button>";
    } catch (Exception $e) {
        $error = "Sending notification is not available right now.<br>Troubleshooting tips:<br>
        <ul>
        <li>Check your connection</li>
        <li>Contact the admin</li>
        <li>Try again</li>
        <li><button class='btn btn-primary' onclick='closeWindow()'>Close Window</button></li>
        </ul>";
        $error = $e->getMessage();
    }
} else {
    Helper::to("dashboard.php");
}


?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <?php
        if (!empty($error)) {
            echo "<div class='alert alert-danger'>$error</div>";
        }

        ?>
    </div>
</div>


<?php include_once "./pages/footer.pages.php"; ?>

<script>
    function closeWindow() {
        let new_window = open(location, '_self');

        new_window.close();

        return false;
    }
</script>