<?php

use App\Core\Helper;
use App\Database\Database;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <h3>Choose Class</h3>
            <?php
            $db = Database::getInstance();
            $db->query("SELECT classTeacher,classId FROM classes WHERE school=?");
            $db->bind(1, $schoolId);
            $result = $db->resultset();
            $classList = [];
            if ($result != null) {
                foreach ($result as $res) {
                    $list = explode(",", $res->classTeacher);
                    foreach ($list as $l) {
                        if ($l == $sms_userId) {
                            $classList[] = $res->classId;
                        }
                    }
                }
            } else {
                $classList = [];
            }

            if ($classList != null) {
                foreach ($classList as $cls) {
                    echo "<h5><a href='weekly.php?class=$cls'>" . Helper::classEncode($cls) . "</a></h5>";
                }
            }
            ?>
        </div>
        <?php include_once './includes/footer.php'; ?>