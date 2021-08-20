<?php

use App\Core\Helper;

include_once './includes/header.php';

if ($sms_role != "Super-Admin") {
    Helper::showNotPermittedPage();
}

if (isset($_GET['delete_id']) && $_GET['delete_id'] != "") {
    $id = $_GET['delete_id'];
    Helper::deleteStaff($id);
    Helper::to("staffsprofile.php");
}
?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <h1 class="text-center">Staff Profile</h1>
            <a href="addstaff.php" class="btn btn-primary">Add New Staff</a>
            <p class="text-info text-small">Please scroll left or right for full content view</p>
            <div class="table-responsive">
                <table class="table table-striped table-hover table-dark table-bordered dt">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Status</th>
                            <th>D.O.B</th>
                            <th>Professional<br>Qualification</th>
                            <th>Academic<br>Qualification</th>
                            <th>Year of<br>Appointment</th>
                            <th>Contact<br>Address</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $data = Helper::getStaffRecords($schoolId);
                            if (!empty($data)) {
                                foreach ($data as $d) {
                                    if ($d->dob != "") {

                                        $temp = strtotime($d->dob);
                                        $dob = date("dS M, Y", $temp);
                                    } else {
                                        $dob = "";
                                    }
                                    if ($d->yearappoint != "") {
                                        $temp = strtotime($d->yearappoint);
                                        $year = date("F Y", $temp);
                                    } else {
                                        $year = "";
                                    }
                                    if ($d->gender == "M") {
                                        $name = "Mr. " . $d->staff_name;
                                    } elseif ($d->gender == "F") {
                                        $name = "Ms. " . $d->staff_name;
                                    } else {
                                        $name = $d->staff_name;
                                    }
                                    echo "<tr>";
                                    echo "<td>$name</td>";
                                    echo "<td>$d->gender</td>";
                                    echo "<td>$d->staff_role</td>";
                                    echo "<td>$dob</td>";
                                    echo "<td>$d->profqual</td>";
                                    echo "<td>$d->acadqual</td>";
                                    echo "<td>$year</td>";
                                    echo "<td>$d->contact_address</td>";
                                    echo "<td><a href='editstaff.php?staff_id=$d->staffId' class='btn btn-behance'>Edit</a></td>";
                                    if ($d->staff_role != "Super-Admin") {
                                        echo "<td><a href='?delete_id=$d->staffId' class='btn btn-google delete'>Delete</a></td>";
                                    } else {
                                        echo "<td></td>";
                                    }
                                    echo "</tr>";
                                }
                            }
                        } catch (Exception $ex) {
                        }
                        ?>
                    </tbody>
                </table>

            </div>

        </div>
        <?php include_once './includes/footer.php'; ?>