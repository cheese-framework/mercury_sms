<?php
include_once './includes/header.php';

use App\Core\Helper;
use App\Helper\Voucher\Voucher;

if (Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}
$voucher = "";
$error = [];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // validate voucher code
    $voucher = $_POST['voucher'] ?? "";

    if ($voucher) {
        $voucher = str_replace('-', '', $voucher);
        if (strlen($voucher) < 15 || strlen($voucher) > 15) {
            $error[] = "The voucher should be at most 15 characters";
        }
        if (empty($error)) {
            $voucher = strtoupper($voucher);
            // check if voucher is valid
            if (Voucher::isVoucherValid($voucher)) {
                Voucher::activateWithVoucher($voucher, $schoolId, $sms_email);
                Helper::to("index.php");
            } else {
                $error[] = "Voucher is not valid.<br>Probably has been used or never existed in the first place.";
            }
        }
    } else {
        $error[] = "Please specify a voucher";
    }
}

?>

<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="col-lg-10 mx-auto my-3">
                <div class="card p-2">
                    <?php
                    if (!empty($error)) {
                        echo "<div class='alert alert-danger'>";
                        foreach ($error as $e) {
                            echo $e . "<br>";
                        }
                        echo "</div>";
                    }
                    ?>
                    <h3 class="text-center">Activate Now</h3>
                    <p>Don't have a voucher?<br>Contact <a href="contact.php">us here</a> Or get from any of Trust Bank, Reliance Financial Services Branches.</p>
                    <br>
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="voucher">Enter your <?= VOUCHER_CHARACTER_COUNT ?> character voucher: </label>
                            <input type="text" name="voucher" value="<?= $voucher; ?>" id="voucher" class="form-control" placeholder="XXX-XXX-XXX-XXX-XXX" minlength="19" maxlength="19" required>
                        </div>
                        <!-- <div class="form-group">
                            <label for="">Subscription Plan</label>
                            <select name="billing" id="billingType" class="form-control">
                                <option value="third">3 Months Plan &mdash; $50.25 <?= Helper::isUsingSMS($schoolId) ? " + SMS Service: $" . THIRD_SMS_SERVICE_CHARGE  : "" ?></option>
                                <option value="six">6 Months Plan &mdash; $90.25 <?= Helper::isUsingSMS($schoolId) ? " + SMS Service: $" . HALF_SMS_SERVICE_CHARGE  : "" ?></option>
                                <option value="year">Yearly Plan &mdash; $140.25 <?= Helper::isUsingSMS($schoolId) ? " + SMS Service: $" . ANNUAL_SMS_SERVICE_CHARGE  : "" ?></option>
                            </select>
                        </div> -->
                        <!-- <div class="form-group">
                            <input type="hidden" name="activation" id="activation" value="2.75" class="form-control">
                        </div> -->
                        <!-- <div class="form-group">
                            <label for="">Billing Cycles (How long do you wish to subscribe for using the selected plan)</label>
                            <select name="cycles" id="cycles" class="form-control">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                        </div> -->
                        <div class="form-group">
                            <input type="submit" value="Activate Subscription" class="btn btn-success">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>
        <script>
            const voucherField = document.getElementById('voucher');
            let lengthOfText = 0;
            let usedLengthOfText = false;
            voucherField.addEventListener('keyup', function(e) {
                voucherField.value = e.target.value.toUpperCase();
                lengthOfText = e.target.value.length;

                if (e.key != "Backspace") {
                    if (lengthOfText == 3 && voucherField.value.length < 19) {
                        voucherField.value += "-";
                    } else if (lengthOfText == 7 && voucherField.value.length < 19) {
                        voucherField.value += "-";
                    } else if (lengthOfText == 11 && voucherField.value.length < 19) {
                        voucherField.value += "-";
                    } else if (lengthOfText == 15 && voucherField.value.length < 19) {
                        voucherField.value += "-";
                    }
                } else {
                    lengthOfText = e.target.value.length;
                }

            });

            voucherField.addEventListener('paste', function() {
                alert("Pasting is not allowed. Type it out please");
                voucherField.addEventListener('change', () => voucherField.value = "");
            });
        </script>