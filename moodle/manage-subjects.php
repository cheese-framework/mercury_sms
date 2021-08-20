<?php

use App\Core\Helper;
use App\Notifiable\Components\Components;
use App\School\Student;
use App\School\Subject;

include_once "./pages/header.pages.php";


$error = [];

$mySubjects = Student::getMySubjects($moodle_userId);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $electives = $_POST['electives'] ?? [];

    if ((!$mySubjects || count($mySubjects) <= 0) && !$electives) {
        $error[] = "Please select at least 1 elective";
    }


    if (empty($error) && is_array($electives)) {
        $electives = implode(",", $electives);
        $bool = Student::addElectives($moodle_userId, $electives);
        if ($bool) {
            Helper::to("manage-subjects.php?success=electives_updated");
        } else {
            $error[] = "Your request was not processed";
        }
    } else {
        $error[] = "Something went wrong. Try again later";
    }
}


?>
<!-- ============================================================== -->
<!-- End Topbar header -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- Left Sidebar - style you can find in sidebar.scss  -->
<!-- ============================================================== -->
<?php include_once "./pages/navbar.pages.php"; ?>
<!-- ============================================================== -->
<!-- End Bread crumb and right sidebar toggle -->
<!-- ============================================================== -->
<!-- ============================================================== -->
<!-- Container fluid  -->
<!-- ============================================================== -->
<div class="container-fluid">
    <!-- ============================================================== -->
    <!-- Three charts -->
    <!-- ============================================================== -->
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <?php

            if (!empty($error)) {
                echo "<div class='alert alert-danger col-lg-12' id='msg'>";
                foreach ($error as $e) {
                    echo $e . "<br>";
                }
                echo "</div>";

            ?>

                <script>
                    const msg = document.getElementById('msg');
                    setTimeout(() => {
                        msg.style.display = "none";
                    }, 7000);
                </script>
            <?php
            } ?>
            <?= Components::header("Core Subjects", "h3") ?>
            <?php
            $coreSubjects = Subject::getSubjectsByClass($class, $schoolId);
            if ($coreSubjects) {
                $core = explode(",", $coreSubjects);
                if ($core) {
                    foreach ($core as $c) {
                        $subject = Subject::getSubject($c, $schoolId);
                        if ($subject) {
                            echo "<input type='checkbox' class='form-check' disabled checked> {$subject->subject}";
                        }
                    }
                }
            }

            ?>
            <form action="" method="post" autocomplete="off">
                <div class="form-group">

                    <?php
                    $subjectLevel = Subject::getSubjectsByLevelExplicit($schoolId, $class);
                    if ($subjectLevel) {

                        if (Helper::canChooseSubject($class)) {
                            echo Components::header("Choose Electives", "h4");
                            foreach ($subjectLevel as $sub) {
                                $subject = Subject::getSubject($sub, $schoolId);
                                if ($subject) {
                                    if ($core) {
                                        if (!in_array($subject->subjectId, $core)) {
                                            if ($mySubjects) {
                                                if (in_array($subject->subjectId, $mySubjects)) {
                                                    $label = md5($subject->subject);
                                                    echo "<input type='checkbox' checked     class='form-check' id='{$label}' value='{$subject->subjectId}' name='electives[]'><label for='$label'> {$subject->subject}</label>";
                                                } else {
                                                    $label = md5($subject->subject);
                                                    echo "<input type='checkbox' class='form-check' id='{$label}' value='{$subject->subjectId}' name='electives[]'><label for='$label'> {$subject->subject}</label>";
                                                }
                                            } else {
                                                $label = md5($subject->subject);
                                                echo "<input type='checkbox' class='form-check' id='{$label}' value='{$subject->subjectId}' name='electives[]'><label for='$label'> {$subject->subject}</label>";
                                            }
                                        }
                                    } else {
                                        $label = md5($subject->subject);
                                        echo "<input type='checkbox' class='form-check' id='{$label}' value='{$subject->subjectId}' name='electives'><label for='$label'> {$subject->subject}</label>";
                                    }
                                }
                            }

                            echo "<div class='form-group'>
                                <input type='submit' value='SAVE' class='btn btn-primary'>
                            </div>";
                        }
                    }

                    ?>
                </div>
            </form>
        </div>
        <div class="col-lg-7">

            <?= Components::header("Manage My Subjects", "h3") ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped table-bordered dt">
                    <thead class="text-center">
                        <tr>
                            <th>Subject</th>
                            <th>Type</th>
                            <th>View Progress</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <?php
                        if ($coreSubjects) {
                            $core = explode(",", $coreSubjects);
                            $data = $core;
                            if ($mySubjects) {
                                $data = array_merge_recursive($data, $mySubjects);
                            }
                            foreach ($data as $datum) {
                                $subject = Subject::getSubject($datum, $schoolId);
                                if ($subject) {
                                    $type = (in_array($subject->subjectId, $core)) ? "Core" : "Elective";
                                    echo "<tr>";
                                    echo "<td>{$subject->subject}</td>";
                                    echo "<td>{$type}</td>";
                                    echo "<td><a href='viewprogress.php?id={$subject->subjectId}' target='_blank' class='btn btn-primary'>View Progress</a></td>";
                                    echo "</tr>";
                                }
                            }
                        }

                        ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>
<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->

<?php include_once "./pages/footer.pages.php"; ?>