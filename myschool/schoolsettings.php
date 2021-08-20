<?php

use App\Core\Helper;
use App\Media\FileUpload;
use App\Notifiable\Components\Components;

include_once './includes/header.php';

if ($sms_role != "Super-Admin") {
    Helper::showNotPermittedPage();
}

if (isset($_POST['updateschool'])) {
    $name = $_POST['school'];
    if (!Helper::isEmpty($name)) {
        Helper::updateSchoolName($name, $schoolId);
        Helper::to("schoolsettings.php");
    }
}

if (isset($_POST['updatebadge'])) {
    if (isset($_FILES['badge']) && $_FILES['badge']['name'] != "") {
        FileUpload::uploadImage("assets/images/", $_FILES['badge']);
        $img = FileUpload::$file;
        Helper::changeBadge($schoolId, $img);
        Helper::to("schoolsettings.php");
    }
}

?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0 body">
            <div class="row">
                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <?= Components::header("Change School Display Name", "h4"); ?>
                            <form action="" method="POST">
                                <div class="form-group">
                                    <label>School Name: </label>
                                    <input type="text" placeholder="School Name" name="school" class="form-control" value="<?= Helper::getSchoolName($schoolId) ?>" />
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="Update" name="updateschool" class="btn btn-github" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card">
                        <div class="card-body">
                            <?= Components::header("Change School Display Badge", "h4"); ?>
                            <form action="" method="POST" enctype="multipart/form-data">
                                <label for="">Change display badge:</label>
                                <div class="form-group">
                                    <input type="file" name="badge" class="form-file" title="Change school's Image" />
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="Update Badge" name="updatebadge" class="btn btn-behance" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6 m-2">
                    <div class="card">
                        <div class="card-body">
                            <?= Components::header("Monetary Settings <i class='mdi mdi-cash-multiple'></i>", "h4"); ?>
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">$</div>
                                </div>
                                <input type="text" name="currency" id="currency" placeholder="Enter your currency" class="form-control" value="<?= Helper::moneySign($schoolId); ?>" minlength="1" maxlength="3">
                                <div class="input-group-prepend">
                                    <button class="btn btn-primary" id="changeMoneyBtn">Change</button>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <a href="addfeerange.php" class="btn btn-facebook my-3">Add Fee Range</a>
                                <table class="table table-bordered table-hover table-striped dt">
                                    <thead>
                                        <tr>
                                            <th>Class Range</th>
                                            <th>Fee</th>
                                            <th class="text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        try {
                                            $data = Helper::getFeeRange($schoolId);
                                            foreach ($data as $d) {
                                                $classes = explode(",", $d->class);
                                                $temp = [];
                                                foreach ($classes as $cls) {
                                                    $temp[] = Helper::classEncode($cls);
                                                }
                                                $class = "";
                                                foreach ($temp as $t) {
                                                    $class = implode(",", $temp);
                                                }
                                                echo "<tr>";
                                                echo "<td>" . Helper::undoList($class) . "</td>";
                                                echo "<td>" . Helper::moneySign($schoolId) . number_format($d->fee, 2) . "</td>";

                                                echo "<td class='text-center'><a href='editRange.php?id=$d->id' class='btn btn-success'>Edit</a></td>";
                                                echo "</tr>";
                                            }
                                        } catch (Exception $ex) {
                                            echo "<tr>";
                                            echo "<td colspan=3 class='text-center'>" . $ex->getMessage() . "</td>";
                                            echo "</tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <?= Components::header("Internal Settings", "h4"); ?>
                            <?php
                            if (!Helper::isActivated($schoolId)) {
                            ?>
                                <p>Use SMS service?
                                    <br>
                                    <span class="text-small text-info">Using this service adds to your activation fee. This option won't be available after activation except on special request.</span><br>
                                    <input type="checkbox" name="sms-use" id="sms-use" <?= (Helper::isUsingSMS($schoolId)) ? "checked" : "" ?>>
                                    <i style="font-size: 1.3rem; display:none;" id="sms-spin" class="mdi mdi-loading mdi-spin text-small">
                                        <span class="text-small text-info"> processing...</span></i>
                                </p>
                            <?php
                            }
                            ?>
                            <p>Allow Online Assessment?
                                <br>
                                <input type="checkbox" name="online-assess-use" id="online-assess-use" <?= (Helper::isUsingOnlineAssessment($schoolId)) ? "checked" : ""; ?>>
                            </p>
                            <div>
                                <p>Change School Type</p>

                                <p class="text-info text-small">Current Type:
                                    <b><i id="type"><?= SCHOOL_TYPES[Helper::getSchoolType($schoolId)]; ?></i></b>
                                </p>

                                <input type="radio" name="school-type" id="nursery-primary" value="<?= NURSERY_PRIMARY; ?>" class="school-type">
                                <span class="text-small">Nursery & Primary School</span>
                                <br>
                                <input type="radio" name="school-type" id="nursery-primary-junior" value="<?= NURSERY_PRIMARY_JUNIOR; ?>" class="school-type">
                                <span class="text-small">Nursery & Primary & Junior School</span>
                                <br>
                                <input type="radio" name="school-type" id="junior-senior" value="<?= JUNIOR_SENIOR; ?>" class="school-type">
                                <span class="text-small">Junior & Senior School</span>
                                <br>
                                <input type="radio" name="school-type" id="junior" value="<?= JUNIOR; ?>" class="school-type">
                                <span class="text-small">Junior School</span>
                                <br>
                                <input type="radio" name="school-type" id="senior" value="<?= SENIOR; ?>" class="school-type">
                                <span class="text-small">Senior School</span>
                                <br>
                                <input type="radio" name="school-type" id="all" value="<?= ALL; ?>" class="school-type">
                                <span class="text-small">All</span>
                                <br>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>

        <script>
            try {
                let useSMS = document.getElementById('sms-use');

                let useOnlineAssessment = document.getElementById('online-assess-use');
                const sms_spin = document.getElementById('sms-spin');
                let Ajax;
                let typeSchool = document.getElementById("type");
                let currency = document.getElementById('currency');
                let changeMoneyBtn = document.getElementById('changeMoneyBtn');

                changeMoneyBtn.onclick = function() {
                    if (currency.value.trim() != "") {
                        changeMoneyBtn.disabled = true;
                        changeMoneyBtn.innerHTML = "<i class='mdi mdi-loading mdi-spin'></i>";
                        Ajax = new XMLHttpRequest();
                        Ajax.onreadystatechange = function() {
                            const response = Ajax.response;
                            if (response == "OK") {
                                changeMoneyBtn.disabled = false;
                                changeMoneyBtn.innerHTML = "<i class='mdi mdi-check-all'></i>";
                            } else {
                                changeMoneyBtn.disabled = false;
                                changeMoneyBtn.innerHTML = "Try again";
                            }
                        };
                        Ajax.open("GET", `ajax/schoolsettings_ajax.php?type=currency_change&value=${currency.value}&school=<?= $schoolId; ?>`, true);
                        Ajax.send();
                    } else {
                        alert("Please specify a currency");
                    }
                };

                // change school type
                $('.school-type').on('change', function() {
                    let types = [
                        "Nursery & Primary School", "Nursery, Primary & Junior Secondary School", "Junior & Senior Secondary School", "Junior Secondary School", "Senior Secondary School", "All"
                    ];
                    let status = this.value;
                    let school = '<?= $schoolId; ?>';
                    Ajax = new XMLHttpRequest();
                    Ajax.onreadystatechange = function() {
                        if (Ajax.readyState == 4 && Ajax.status == 200) {
                            let response = Ajax.response;
                            if (response == "OK") {
                                typeSchool.textContent = types[status];
                            } else {
                                alert("Something went wrong");
                            }
                        }
                    };
                    Ajax.open("GET", `ajax/schoolsettings_ajax.php?type=school_change&value=${status}&school=${school}`, true);
                    Ajax.send();
                });

                // change online assessment 
                useOnlineAssessment.onchange = function() {
                    if (useOnlineAssessment.checked) {
                        changeAssessmentStatus("true", '<?= $schoolId; ?>');
                    } else {
                        changeAssessmentStatus("false", '<?= $schoolId; ?>');
                    }
                };

                // change SMS status
                useSMS.onchange = function() {
                    if (useSMS.checked) {
                        useSMS.disabled = true;
                        sms_spin.style.display = "block";

                        // open ajax request
                        Ajax = new XMLHttpRequest();
                        Ajax.onreadystatechange = function() {
                            if (Ajax.readyState == 4 && Ajax.status == 200) {
                                let response = Ajax.response;
                                if (response == "OK") {
                                    useSMS.disabled = false;
                                    sms_spin.style.display = "none";
                                    alert("SMS Feature Enabled!");
                                } else {
                                    alert("Something went wrong\nTry again!");
                                    useSMS.disabled = false;
                                    sms_spin.style.display = "none";
                                }
                            }
                        };

                        Ajax.open("GET", `ajax/schoolsettings_ajax.php?type=sms_change&value=${useSMS.checked}&school=<?= $schoolId; ?>`, true);
                        Ajax.send();

                    } else {
                        useSMS.disabled = true;
                        sms_spin.style.display = "block";

                        // open ajax request
                        Ajax = new XMLHttpRequest();
                        Ajax.onreadystatechange = function() {
                            if (Ajax.readyState == 4 && Ajax.status == 200) {
                                let response = Ajax.response;
                                if (response == "OK") {
                                    useSMS.disabled = false;
                                    sms_spin.style.display = "none";
                                    alert("SMS Feature Disabled!");
                                } else {
                                    alert("Something went wrong\nTry again!");
                                    useSMS.disabled = false;
                                    sms_spin.style.display = "none";
                                }
                            }
                        };

                        Ajax.open("GET", `ajax/schoolsettings_ajax.php?type=sms_change&value=${useSMS.checked}&school=<?= $schoolId; ?>`, true);
                        Ajax.send();
                    }
                }

            } catch (error) {

            }

            function changeAssessmentStatus(status, school) {
                Ajax = new XMLHttpRequest();
                Ajax.onreadystatechange = function() {
                    if (Ajax.readyState == 4 && Ajax.status == 200) {
                        let response = Ajax.response;
                        if (response == "OK") {
                            if (status == "true") {
                                alert("Thank you for opting for online assessment.\nOnline Assessment has been activated.");
                            } else {
                                alert("Online Assessment has been deactivated");
                            }
                        } else {
                            alert(response);
                        }
                    }
                };
                Ajax.open("GET", `ajax/schoolsettings_ajax.php?type=assess_change&value=${status}&school=${school}`, true);
                Ajax.send();
            }
        </script>