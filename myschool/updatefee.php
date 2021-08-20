<?php

use App\Core\Helper;
use App\Notifiable\Components\Components;
use App\School\Student;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

$error = [];
$paid = 0;
$specialFee = 0;

if (isset($_GET['studentId']) && isset($_GET['year']) && isset($_GET['term']) && isset($_GET['class'])) {
    $stuId = $_GET['studentId'];
    $year = $_GET['year'];
    $term = $_GET['term'];
    $class = $_GET['class'];
    if (Helper::isEmpty($stuId, $year, $term, $class)) {
        Helper::showErrorPage();
    } else {
        try {
            $data = Helper::getFee($stuId, $year, $schoolId, $term);
        } catch (Exception $ex) {
            Helper::showErrorPage();
        }
    }

    if ($_SERVER['REQUEST_METHOD'] == "POST") {
        $paid = (isset($_POST['paid']) ? $_POST['paid'] : 0);
        $isSpecial = (isset($_POST['special']) ? 1 : 0);
        $specialFee = (isset($_POST['special-fee']) && is_numeric($_POST['special-fee']) && $_POST['special-fee'] > 0) ? $_POST['special-fee'] : 0;
        if (is_numeric($paid) && is_numeric($specialFee)) {
            if (($paid == 0 || $paid == 0.00) && $isSpecial == 0) {
                $error[] = "Please specify an amount less than or greater than 0";
            } else if ($paid != "" && $paid >= 0) {
                try {
                    // if ($data->feeToPay != $paid && $isSpecial == 1) {
                    Helper::updateFeePayment($stuId, $term, $year, $paid, $schoolId, $isSpecial, $specialFee);
                    // add to incomes
                    $feePaid = doubleval($paid);
                    if (is_numeric($feePaid) && doubleval($feePaid) > 0) {
                        try {
                            $date = date("Y-m-d");
                            $type = "Tuition Fee";
                            Helper::addIncomes($feePaid, $date, $type, $year, $schoolId);
                        } catch (Exception $e) {
                            // ignore if things goes south
                        }
                    }
                    Helper::to("feegenerate.php?year=$year&class=$class&term=$term");
                } catch (Exception $ex) {
                    $error[] = $ex->getMessage();
                }
            } else {
                // subtract from paid
                Helper::subtractFeePayment($stuId, $term, $year, $paid, $schoolId, $isSpecial, $specialFee);
                // add to expenses
                $feePaid = abs(doubleval($paid));
                if (is_numeric($feePaid) && doubleval($feePaid) > 0) {
                    try {
                        $date = date("Y-m-d");
                        $type = "Tuition Fee Refunded";
                        Helper::addExpenses($feePaid, $date, $type, $year, $schoolId);
                    } catch (Exception $e) {
                        // ignore if things goes south
                    }
                }
                Helper::to("feegenerate.php?year=$year&class=$class&term=$term");
            }
        } else {
            $error[] = "Please specify a valid paid amount";
        }
    }
}
?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="col-lg-9">
                <?php
                if (!empty($error)) {
                    echo "<div class='alert alert-danger'>";
                    foreach ($error as $e) {
                        echo $e . "<br>";
                    }
                    echo "</div>";
                }
                ?>
                <?= Components::header("Update " . Student::returnStudentName($stuId) . "'s Fee", "h4", "center"); ?>
                <form method="POST" action="" autocomplete="off">

                    <div class="form-group">
                        <label>Total Amount Paid: *</label>
                        <input type="text" name="paid" class="form-control" placeholder="Amount Paid" value="<?= $data->paid; ?>" disabled="" />
                    </div>
                    <div class="form-group">
                        <label>Amount Paid: * <i>(specify a minus sign to subtract from payment. Ex: -20)</i></label>
                        <input type="text" name="paid" class="form-control" placeholder="Amount Paid. Ex: 20 or -20" value="<?= $paid; ?>" />
                    </div>
                    <div class="form-group">
                        <label for="special">Special case: <i>Tick this if there is a sponsorship or deduction of price or any other reason</i></label>
                        <input type="checkbox" name="special" id="special" class="form-check" <?php
                                                                                                if ($data->hasSpecial == 1) {
                                                                                                    echo "checked";
                                                                                                } else {
                                                                                                    echo "";
                                                                                                }
                                                                                                ?>>
                    </div>
                    <div class="form-group" id="show-special" <?php
                                                                if ($data->hasSpecial != 1) {
                                                                    echo "style='display: none;'";
                                                                }
                                                                ?>>
                        <label for="special-fee">Special Fee: <i>Can be 0,
                                (less than or greater than the tuition fee). Other values will be converted to '0'.</i></label>
                        <input type="text" name="special-fee" id="special-fee" class="form-control" placeholder="Special Amount Paid." value="<?= $data->feeToPay; ?>" />
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-behance" value="Update" />
                    </div>
                </form>
            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>
        <script>
            const specialTick = document.getElementById("special");
            const specialFee = document.getElementById("show-special");
            specialTick.addEventListener("change", function() {
                if (specialTick.checked == true) {
                    specialFee.style.display = "block";
                } else {
                    specialFee.style.display = "none";
                }
            });
        </script>