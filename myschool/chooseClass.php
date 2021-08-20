<?php

use App\Core\Helper;
use App\Database\Database;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

?>
<div class="container-scroller">
    <?php

    include_once './includes/navbar.php';
    ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <h3>Choose Class</h3>
            <?php
            $db = Database::getInstance();
            $db->query(
                "SELECT classId FROM classes WHERE school=? ORDER BY className ASC"
            )->bind(
                1,
                $schoolId
            );
            $result = $db->resultset();
            $classList = [];
            if ($result != null) {
                foreach ($result as $res) {
                    $classList[] = $res->classId;
                }
            } else {
                $classList = [];
            }

            if ($classList != null) {
                echo "<div class='row my-3'>";
                foreach ($classList as $cls) {
                    echo "<div class='col-lg-4 col-md-4 col-sm-5 col-sm-5 card m-2'><div class='card-body'>
                        <h5><a href='addResult.php?class=$cls'>" .
                        Helper::classEncode($cls) . "</a></h5>
                        </div></div>";
                }
                echo "</div>";
            } else {
                echo "<h2 class='text-center'>No record found!</h2>";
            }
            ?>
        </div>
        <?php

        include_once './includes/footer.php';
        ?>