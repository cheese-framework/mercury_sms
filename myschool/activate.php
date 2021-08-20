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
    $voucher = $_POST['voucher-text'] ?? "";

    if ($voucher) {
        if (strlen($voucher) < 15 || strlen($voucher) > 15) {
            $error[] = "The voucher should be 15 characters";
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
            <div class="col-lg-12 mx-auto my-3">
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
                    <p>Don't have a voucher?<br>Contact <a href="contact.php">us here</a> </p>
                    <!-- Or get from any of Trust Bank, Reliance Financial Services Branches.</p> -->
                    <br>
                    <form action="" method="POST">
                        <div class="form-group">
                            <label for="voucher">Enter your <?= VOUCHER_CHARACTER_COUNT ?> character voucher: </label>
                            <div id="voucher"></div>
                        </div>
                        <input type="hidden" name="voucher-text" id="voucher-text">
                        <div class="form-group">
                            <input type="submit" value="Activate Subscription" class="btn btn-success">
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/pincode-input@0.4.2/dist/pincode-input.min.js"></script>
        <script>
            new PincodeInput('#voucher', {
                count: 15,
                numeric: false,
                onInput: (value) => {
                    document.getElementById('voucher-text').value = value.trim();
                }
            })
        </script>