<?php

use App\Core\Helper;
use App\Helper\DateLib;
use App\Helper\Logger;

include_once './includes/header.php';

if ($sms_role != $ADMIN) {
    Helper::showNotPermittedPage();
}


if (isset($_GET['deleteLog']) && $_GET['deleteLog'] != "") {
    $id = $_GET['deleteLog'];
    if (Logger::delete($id, $schoolId)) {
        Helper::to("logs.php");
    }
}

if (isset($_GET['clearLogs'])) {
    $id = "all";
    if (Logger::delete($id, $schoolId)) {
        Helper::to("logs.php");
    } else {
        Helper::showErrorPage();
    }
}

?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <h2 class="text-center">Recent Issues and Logs</h2>
            <div class="modal" tabindex="-1" role="dialog" id="exampleModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Modal title</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Modal body text goes here.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <a href="?clearLogs" class="btn btn-danger">Clear All</a>
                <hr>
                <table class="dt table table-striped table-hover">
                    <thead>
                        <th style="display: none;" data-toggle="modal" data-target="#exampleModal">ID</th>
                        <th style="display: none;">Log</th>
                        <th>Event</th>
                        <th style='display:none'>Level</th>
                        <th>Time</th>
                        <th>View</th>
                        <th>Delete</th>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            $data = Logger::logs($schoolId);
                            if ($data != null) {
                                $dateLib = new DateLib();
                                foreach ($data as $datum) {
                                    echo "<tr>";
                                    echo "<td style='display:none'>$datum->id</td>";
                                    echo "<td style='display:none'>" . $datum->log . "</td>";
                                    echo "<td>" . $datum->event . "</td>";
                                    echo "<td style='display:none'>" . $datum->level . "</td>";
                                    echo "<td>" . $dateLib->fullDateInterval($datum->time, date("d-m-Y h:i:s")) . "</td>";
                                    echo '<td><button type="button" class="btn btn-primary data-log" data-toggle="modal" data-target="#exampleModal">View</button></td>';
                                    echo "<td><a href='logs.php?deleteLog=$datum->id' class='btn btn-danger delete'>Delete</a></td>";
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
            $(".data-log").on("click", function() {
                let parent = this.parentElement.parentElement;
                $(".modal-title").text(parent.children[2].textContent);
                $(".modal-body").html("<b>Log: </b>" + parent.children[1].innerHTML +
                    "<br><b>Time:</b> " + parent.children[4].textContent +
                    "<br><b>Level:</b> " + parent.children[3].textContent);
                $('#myModal').on('shown.bs.modal', function() {
                    $('#myInput').trigger('focus')
                });
            });
        </script>