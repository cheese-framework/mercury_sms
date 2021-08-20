<?php

use App\Core\Helper;

include_once './includes/header.php';

// if ($sms_role != "Super-Admin") {
Helper::to("index.php");
// }

$success = "";
$errors = [];
$title = "";
$desc = "";
$long = 0;
$occurs = 1;

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $title = $_POST['title'];
    $desc = $_POST['description'];
    $long = $_POST['long'];
    $occurs = $_POST['interval'];
    $start = $_POST['start'];
    $end = $_POST['end'];

    if (!Helper::isEmpty($title, $desc, $long, $occurs, $start)) {
        if (strlen($title) > 15 || strlen($title) < 1) {
            $errors[] = "Title must be between 1 and 15 characters in length";
        }
        if (strlen($desc) > 100 || strlen($desc) < 1) {
            $errors[] = "Description must be between 1 and 100 characters in length";
        }

        if (empty($errors)) {
            try {
                $desc = nl2br($desc);
                Helper::addEvent($title, $desc, $start, $end, $long, $occurs, $schoolId, intval(date("Y")));
                $success = "Event created!";
                $errors = [];
                $title = "";
                $desc = "";
                $long = 0;
                $occurs = 1;
            } catch (Throwable $e) {
                $errors[] = $e->getMessage();
            }
        }
    } else {
        $errors[] = "All fields are required";
    }
}

?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="col-lg-11">
                <?php
                if (!empty($errors)) {
                    echo "<div class='alert alert-danger col-lg-9' id='msg'>";
                    foreach ($errors as $e) {
                        echo $e . "<br>";
                    }
                    echo "</div>";

                ?>
                    <script>
                        const msg = document.getElementById('msg');
                        setTimeout(() => {
                            msg.style.display = "none";
                        }, 3000);
                    </script>
                <?php
                }


                if (trim($success) != "") {
                    echo "<div class='alert alert-success col-lg-9' id='msg'>" . $success . "</div>";
                ?>
                    <script>
                        const msg = document.getElementById('msg');
                        setTimeout(() => {
                            msg.style.display = "none";
                        }, 3000);
                    </script>
                <?php
                }

                ?>


                <div class="card my-3">
                    <div class="card-header">
                        <h3>Add an event</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="form-group">
                                <label for="title">Event Title*</label>
                                <input type="text" name="title" id="title" placeholder="Event Title" class="form-control" maxlength="20" value="<?= $title; ?>">
                                <p id="remind" class="text-small text-info p-1">0/15</p>
                            </div>
                            <div class="form-group">
                                <label for="title">Event Description*</label>
                                <textarea name="description" id="description" class="form-control" placeholder="Event Description" maxlength="100"><?= $desc; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label for="start">Start Date*</label>
                                <input type="date" name="start" id="start" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="end">End Date</label>
                                <input type="date" name="end" id="end" class="form-control">
                            </div>
                            <div class="form-group">
                                <label for="long">Interval*</label>
                                <input type="number" name="long" id="long" min="0" max="30" class="form-control" value="<?= $long; ?>">
                            </div>
                            <div class="form-group">
                                <label for="interval">Days*</label>
                                <input type="number" name="interval" id="interval" min="1" max="30" class="form-control" value="<?= $occurs; ?>">
                            </div>
                            <div class="form-group">
                                <input type="submit" class="btn btn-success" id="submit" value="Add Event">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>
        <script>
            const title = document.getElementById('title');
            const remind = document.getElementById('remind');
            const submitBtn = document.getElementById('submit');

            (function() {
                if (title.value.trim().length == 0) {
                    submitBtn.disabled = true;
                }
            })();

            title.addEventListener('keyup', function() {
                let length = title.value.trim().length;
                if (length == 0) {
                    submitBtn.disabled = true;
                    remind.textContent = length + "/15";
                } else {
                    if (length > 15) {
                        remind.className = "text-small text-danger";
                        remind.textContent = length + "/15";
                        submitBtn.disabled = true;
                    } else {
                        remind.className = "text-small text-info";
                        remind.textContent = length + "/15";
                        submitBtn.disabled = false;
                    }
                }
            });
        </script>