<?php

use App\Notifiable\Components\Components;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/bootstrap.css">
    <title>Document</title>
    <style>
        table,
        th,
        td {
            border: 0 solid black;
            border-radius: 5px;
            width: fit-content;
            font-family: 'Courier New', Courier, monospace;
            font-size: 16px;

        }

        th,
        td {
            padding: 5px;
        }

        th {
            text-align: left;
        }

        table {
            border-spacing: 5px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row mx-auto my-5">
            <div class="col-lg-10">
                <?= Components::header("Mini statement of your activation subscription"); ?>
                <p>Thank you for subscribing to Mercury School Management System.<br>We truly appreciate you. Below is the details of your subscription: </p>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered">
                        <thead>
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
                                    <th>Voucher: </th>
                                    <th><?= $voucher; ?></th>
                                </tr>
                            </thead>
                    </table>
                </div>
                <p>CEO: <i>Caleb Okpara</i></p>
                <p><small>Mercury - School Management System</small></p>
            </div>
        </div>
    </div>
</body>

</html>