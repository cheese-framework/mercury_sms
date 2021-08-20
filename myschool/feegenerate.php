<?php

use App\Core\Helper;
use App\Helper\DateLib;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

if ($sms_role != "Super-Admin") {
    Helper::showNotPermittedPage();
}

if (isset($_GET['year']) && isset($_GET['class']) && isset($_GET['term'])) {
    $year = $_GET['year'];
    $class = $_GET['class'];
    $term = $_GET['term'];
    if (Helper::isEmpty($year, $class, $term)) {
        Helper::to("fees.php");
    }
} else {
    Helper::to("fees.php");
}
?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <?php

            $notice = Helper::getNoticeDate($class, $schoolId);
            if ($notice == "") {
                echo "<button id='send-arrear-notice' style='display: none;' class='btn btn-primary'><i class='mdi mdi-bell-ring text-success'></i> Send Arrears Notice</button><br>";
            } else {
                $dateLib = new DateLib();
                $interval = $dateLib->interval($notice, date("d-m-Y h:i:s"));
                if ($interval->d > 5) {
                    echo "<button id='send-arrear-notice' style='display: none;' class='btn btn-primary'><i class='mdi mdi-bell-ring text-success'></i> Send Arrears Notice</button><br>";
                } else {
                    echo "<button class='btn btn-info disabled'><small>Will be available in 5 days</small></button><br><br>";
                }
            }

            ?>
            <div class="table-responsive" id="house">
                <h1 class="text-center">Please wait while we generate the list.</h1>
                <p class="text-center">Generating List <i class="mdi mdi-loading mdi-spin"></i></p>
                <p id="year" style="display: none;"><?= $year ?></p>
                <p id="class" style="display: none;"><?= $class ?></p>
                <p id="term" style="display: none;"><?= $term ?></p>
                <p id="school" style="display: none;"><?= $schoolId ?></p>
            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>
        <script>
            window.addEventListener('load', () => {
                const year = document.getElementById("year");
                const clas = document.getElementById("class");
                const term = document.getElementById("term");
                const school = document.getElementById("school");
                const house = document.getElementById("house");
                const xhr = new XMLHttpRequest();
                xhr.onreadystatechange = () => {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        const response = xhr.response;
                        setTimeout(() => {
                            house.innerHTML = response;
                            $(document).ready(function() {
                                $("table.dt").DataTable({
                                    "pageLength": 5
                                });
                            });

                            const arrearsBtn = document.getElementById("send-arrear-notice");
                            arrearsBtn.style.display = "block";
                            arrearsBtn.addEventListener("click", function() {
                                arrearsBtn.innerHTML = "Sending Notification <i class='mdi mdi-loading mdi-spin'></i>";
                                xhr.onreadystatechange = function() {
                                    if (xhr.readyState == 4 && xhr.status == 200) {
                                        const received = xhr.response;
                                        if (received == "OK") {
                                            window.location.reload();
                                        } else {
                                            alert("Could not send notification");
                                            arrearsBtn.innerHTML = "Try again";
                                        }
                                    }
                                };
                                xhr.open("GET", `sendarrears.php?class=${clas.textContent}&year=${year.textContent}&term=${term.textContent}&school=${school.textContent}`, true);
                                xhr.send();
                            });

                        }, 500);
                    }
                };
                xhr.open("GET",
                    `processor.php?year=${year.textContent}&class=${clas.textContent}&term=${term.textContent}`,
                    true);
                xhr.send();


            });
        </script>