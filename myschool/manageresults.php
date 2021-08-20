<?php

use App\Core\Helper;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

if (isset($_GET['class']) && $_GET['class'] != "") {
    $class = $_GET['class'];
} else {
    Helper::showErrorPage();
}
?>
<div class="container-scroller">
    <?php

    include_once './includes/navbar.php';
    ?>


    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <?php
            if ((isset($_GET['year']) && $_GET['year'] != "") &&
                isset($_GET['term']) && $_GET['term'] != "" &&
                (isset($_GET['avenue']) && $_GET['avenue'] != "") &&
                (isset($_GET['class']) && $_GET['class'] != "")
            ) {
                include './resultchecker.php';
                $year = $_GET['year'];
                $term = $_GET['term'];

                showResults($year, $term, $class, $schoolId);
            } else {
                Helper::showErrorPage();
            }

            ?>
        </div>
        <?php

        include_once './includes/footer.php';
        ?>