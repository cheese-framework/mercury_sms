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
    <title>Welcome to Mercury SMS</title>
</head>

<body>
    <div class="row mx-auto my-5">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-body">
                    <?= Components::header("Welcome to Mercury School Management System", "h1"); ?>
                    <?= Components::header($subject, "h4"); ?>
                    <p>
                        <?= $message; ?>
                    </p>
                    <?= Components::link($link, "Verify Email", Components::BTN_SUCCESS); ?>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer">
        <p>CEO: <i>Caleb Okpara</i></p>
        <p><small>Proudly powered by Mercury School Management System</small></p>
    </footer>
</body>

</html>