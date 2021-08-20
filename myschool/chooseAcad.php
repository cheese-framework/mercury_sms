<?php

use App\Core\Helper;
use App\School\AcademicYear;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

if (isset($_GET['classId']) && $_GET['classId'] != "") {
    $classId = $_GET['classId'];
} else {
    Helper::showErrorPage();
}
?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <h1>Choose Academic Year</h1>
            <?php
            try {
                $data = AcademicYear::loadAcademicYears($schoolId);
                if ($data != null) {
                    echo "<div class='row col-lg-12 col-md-12 col-sm-12 col-xs-12 mx-auto'>";
                    foreach ($data as $d) {
                        $curr = "";
                        if (
                            $d->academicYearId == AcademicYear::getCurrentYearId($schoolId)
                        ) {
                            $curr = "<i class='mdi mdi-check-circle text-success' style='text-shadow: 2px 2px 8px #000000;font-size: 27px'></i>";
                        } else {
                            $curr = "";
                        }
                        echo "<div class='card col-lg-3 col-md-4 col-sm-5 col-xs-12 m-2'>
                                <div class='card-body'>
                                
                                <h5 class='text-center'><a href='mystudents.php?classId=$classId&year=$d->academicYear'>$d->academicYear</a> $curr</h5>
                              
</div>
                                </div>";
                    }
                    echo "</div>";
                }
            } catch (Exception $ex) {
                echo "<h4 class='text-center'>" . $ex->getMessage() . "</h4>";
            }
            ?>
        </div>
        <?php include_once './includes/footer.php'; ?>