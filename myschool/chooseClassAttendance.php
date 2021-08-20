<?php

use App\Core\Helper;
use App\Database\Database;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

if ($sms_role == $ADMIN) {
    Helper::showNotPermittedPage();
}

?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="card col-lg-10 mx-auto py-3">
                <h3 class="text-center p-3">Generate Data</h3>
                <form action="createattendance.php" method="post">
                    <div class="form-group">
                        <label for="">Grade * </label>
                        <select name="class" id="" class="form-control" required>
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
                                    echo "<option value='$cls'>" . Helper::classEncode($cls) . "</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="">Date *</label>
                        <input type="date" name="date" id="" class="form-control" value="<?= date("Y-m-d") ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="">Time *</label>
                        <select name="time" id="" class="form-control">
                            <option value="Morning">Morning</option>
                            <option value="Afternoon">Afternoon</option>
                        </select>
                    </div>
                    <div class="text-center">
                        <div class="form-group">
                            <input type="submit" value="Generate" class="btn btn-primary">
                        </div>
                    </div>
                </form>
            </div>

        </div>
        <?php include_once './includes/footer.php'; ?>