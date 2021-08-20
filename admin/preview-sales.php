<?php

use App\Core\Helper;

include_once "pages/header.pages.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $type = $_POST['type'] ?? "";
    $duration = $_POST['duration'] ?? "";
    $email = $_POST['email'] ?? "";
    $phone = $_POST['phone'] ?? "";
    $sms = $_POST['sms'] ?? false;

    if (!Helper::isEmpty($type, $duration, $email, $phone)) {
        // use sms = 1020
        // no sms = 870
        $date = "";
        $amountToPay = 0;
        $year = (int) date("Y");
        $month = (int) date("m");
        $day = (int) date("d");
        if ($type == "MONTH") {
            if ($sms) {
                $amountToPay = MONTH_CHARGE_WITH_SMS * $duration;
            } else {
                $amountToPay = MONTH_CHARGE_WITHOUT_SMS * $duration;
            }

            $tempMonth = $month + $duration;
            if ($tempMonth > 12) {
                $tempMonth = $tempMonth - 12;
                $date = ($year + 1) . "-" . $tempMonth . "-" . $day;
            } else {
                $date = ($year) . "-" . $tempMonth . "-" . $day;
            }
            $date = strtotime($date);
            $date = date("Y-m-d", $date);
        } else {
            if ($sms) {
                $amountToPay = MONTH_CHARGE_WITH_SMS * ($duration * 12);
            } else {
                $amountToPay = MONTH_CHARGE_WITHOUT_SMS * ($duration * 12);
            }

            $date = ($year + $duration) . '-' . $month . '-' . $day;
            $date = strtotime($date);
            $date = date("Y-m-d", $date);
        }
    } else {
        Helper::to("voucher-sales.php?error=All_Fields_Are_Required");
    }
} else {
    Helper::to("voucher-sales.php?error=You_Are_Not_Permitted_There");
}

include_once "pages/navbar.pages.php";

?>

<div class="container-fluid">
    <div class="row justify-content-center">
        <h3>Invoice</h3>
        <div class="table-responsive">
            <table class="table table-hover table-striped">
                <thead>
                    <tr>
                        <th>Charges: </th>
                        <th><?= "D" . number_format($amountToPay, 2); ?></th>
                    </tr>
                    <tr>
                        <th>Email: </th>
                        <th><?= $email; ?></th>
                    </tr>
                    <tr>
                        <th>Phone: </th>
                        <th><?= $phone; ?></th>
                    </tr>
                    <tr>
                        <th>Type of Voucher: </th>
                        <th><?= $type; ?></th>
                    </tr>
                    <tr>
                        <th>Using SMS: </th>
                        <th><?= ($sms) ? "TRUE" : "FALSE"; ?></th>
                    </tr>
                    <tr>
                        <th>Duration: </th>
                        <th><?php
                            if ($type == "MONTH") {
                                echo $duration . " Months";
                            } else {
                                if ($duration > 1) {
                                    echo $duration . " Years";
                                } else {
                                    echo $duration . " Year";
                                }
                            }
                            ?></th>
                    </tr>
                    <tr>
                        <th>Start Date: </th>
                        <th><?= date("Y-m-d"); ?></th>
                    </tr>
                    <tr>
                        <th>Valid To: </th>
                        <th><?= $date; ?></th>
                    </tr>
                    <tr>
                        <th></th>
                        <th><button id="generate-voucher" class="btn btn-success">Generate Voucher</button></th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
<?php include_once "pages/footer.pages.php"; ?>

<script>
    const amountToPay = '<?= $amountToPay; ?>';
    const usingSMS = '<?= $sms ? "TRUE" : "FALSE" ?>';
    const email = '<?= $email ?>';
    const phone = '<?= $phone ?>';
    const duration = '<?= $duration; ?>';
    const type = '<?= $type ?>';
    const date = '<?= $date ?>';

    const data = {
        amountToPay,
        usingSMS,
        email,
        phone,
        duration,
        type,
        date
    };

    const generateVoucher = document.getElementById('generate-voucher');
    generateVoucher.addEventListener('click', function() {
        console.log(data);
        generateVoucher.textContent = "Generating...";
        generateVoucher.disabled = true;
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                const response = xhr.responseText;
                if (response === "OK") {
                    generateVoucher.textContent = "Voucher Generated. Email Sent!";
                    generateVoucher.disabled = true;
                    setTimeout(() => {
                        window.location('voucher-sales.php');
                    }, 5000);
                } else {
                    alert(response);
                    generateVoucher.textContent = "Generate Voucher";
                    generateVoucher.disabled = false;
                }
            }
        };
        xhr.open("GET", `pay.php?data=${JSON.stringify(data)}`, true);
        xhr.send();
    });
</script>