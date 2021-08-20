<?php

use App\Core\Helper;
use App\School\AcademicYear;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

if ($sms_role != "Super-Admin") {
    Helper::to("index.php");
}

$today = date("dS F, Y");

?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <h3 class="text-center">Collect staff attendance for today: <?=
                                                                        $today
                                                                        ?></h3>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered dt">
                    <thead>
                        <!-- <tr> -->
                        <th>Name</th>
                        <th style="display: none;"></th>
                        <th>Department</th>
                        <th>Status</th>
                        <th>Submit</th>
                        <th>Update</th>
                        <!-- </tr> -->
                    </thead>
                    <tbody id="table">
                        <?php
                        try {
                            $data = Helper::getStaffRecords(
                                $schoolId
                            );
                            if ($data != null) {
                                foreach ($data as $datum) {
                                    echo "<tr>";
                                    echo "<td id='staff_id' style='display: none'>$datum->staffId</td>";
                                    echo "<td>$datum->staff_name</td>";
                                    echo "<td></td>";
                                    $status = Helper::hasBeenMarked(date("d-m-Y"), AcademicYear::getCurrentYearId($schoolId), $schoolId, $datum->staffId);
                                    if ($status !== false) {
                                        echo "<td disabled='true'>" . ucfirst($status) . "</td>";
                                        echo "<td><button class='btn btn-success btn-block' disabled='true' id='mark'><i class='mdi mdi-check-all'></i></button></td>";
                                    } else {
                                        echo "<td><select class='form-control' id='status'><option>Present</option><option>Absent</option><option>Late</option></select></td>";
                                        echo "<td><button class='btn btn-primary btn-block' id='mark'>Mark</button></td>";
                                    }
                                    echo "<td><button class='btn btn-primary btn-block' id='update'>Update</button></td>";
                                    echo "</tr>";
                                }
                            }
                        } catch (Exception $e) {
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>

        <script>
            const table = document.getElementById('table');
            table.addEventListener('click', function(e) {
                if (e.target.id == "mark") {
                    let tar = e.target.parentNode.parentNode;
                    let btn = tar.children[4].children[0];
                    btn.innerHTML = '<i class="mdi mdi-loading mdi-spin"></i>';
                    btn.disabled = true;
                    let staffId = tar.children[0]
                        .textContent;
                    let status = tar.children[3].children[0].value;
                    let xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function() {
                        if (xhr.readyState == 4 && xhr.status == 200) {
                            const response = xhr.response;
                            if (response == "OK") {
                                btn.className = "btn btn-success btn-block";
                                btn.innerHTML = '<i class="mdi mdi-check-all"></i>';
                                btn.disabled = true;
                                status.disabled = true;
                            } else {
                                btn.className = "btn btn-danger btn-block";
                                btn.innerHTML = '<i class="mdi mdi-loading" style="font-size: 12px">Error</i>';
                                btn.disabled = false;
                            }
                        }
                    };
                    xhr.open('GET', `takeattendance.php?type=create_staff&staffId=${staffId}&status=${status
                    }`, true);
                    xhr.send();
                } else if (e.target.id == 'update') {
                    let tar = e.target.parentNode.parentNode;
                    let btn = tar.children[4].children[0];
                    let select = tar.children[3];
                    select.innerHTML =
                        "<td><select class='form-control' id='status'><option>Present</option><option>Absent</option><option>Late</option></select></td>";
                    btn.className = "btn btn-github";
                    btn.innerHTML = "Ready for change";
                    btn.disabled = false;
                }
            });
        </script>