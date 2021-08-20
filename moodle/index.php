<?php

use App\Core\Helper;
use App\Notifiable\Components\Components;

include_once "./classes/Student.php";
include_once "../init.php";

if (isset($_SESSION['student_id'])) {
    $path = str_replace("moodle/", "", $_SESSION['moodle_redirect_path_mercury']);
    Helper::to($path);
}

$student_id = "";
$errors = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $student_id = $_POST['username'];
    $password = $_POST['password'];
    if (!Helper::isEmpty($student_id, $password)) {
        $sendTo = (isset($_SESSION['moodle_redirect_path_mercury']) ? str_replace("moodle/", "", $_SESSION['moodle_redirect_path_mercury']) : "dashboard.php");

        try {
            $data = Student::login($student_id, $password);
            $_SESSION['student_id'] = $data->admissionno;
            $_SESSION['student_username'] = $data->fullname;
            $_SESSION['student_email'] = $data->email;
            $_SESSION['school'] = $data->school;
            $_SESSION['class'] = $data->class;
            $_SESSION['year'] = $data->academicYear;
            $_SESSION['s_id'] = $data->studentId;
            Helper::to($sendTo);
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
    } else {
        $errors[] = "All fields are required";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Tell the browser to be responsive to screen width -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mercury - SMS |
        MOODLE
    </title>
    <link rel="stylesheet" href="../myschool/assets/css/style.css">
</head>

<body>
    <div class="container">
        <div class="col-lg-8 my-5 mx-auto">
            <div class="card">
                <div class="card-body">
                    <?php
                    if (!empty($errors)) {
                        echo "<div class='alert alert-danger col-lg-12 text-center' id='msg'>";
                        foreach ($errors as $e) {
                            echo $e . "<br>";
                        }
                        echo "</div>";

                    ?>

                        <script>
                            const msg = document.getElementById('msg');
                            setTimeout(() => {
                                msg.style.display = "none";
                            }, 10000);
                        </script>
                    <?php
                    } ?>
                    <?= Components::header("Login to Moodle", "h2", "center"); ?>
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="username">Student ID or Email</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="Student ID | E-mail Address" value="<?= $student_id; ?>">
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Password">
                        </div>

                        <div class="form-group text-center">
                            <input type="submit" value="Login" class="btn btn-primary">
                        </div>
                    </form>
                    <?= Components::body("<small><i>Powered by QHITECH</i></small>", true, "right") ?>
                </div>
            </div>
        </div>
    </div>
</body>
<!-- ============================================================== -->
<!-- All Jquery -->
<!-- ============================================================== -->
<script src="../myschool/assets/custom/jquery.min.js"></script>

</html>