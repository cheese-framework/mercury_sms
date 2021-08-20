<?php

use App\Core\Helper;
use App\School\AcademicYear;

include_once './includes/header.php';

if ($sms_role != "Super-Admin") {
    Helper::to("index.php");
}

$amount = "";
$date = "";
$type = "";
$error = [];


if (isset($_GET['delinid']) && $_GET['delinid'] != "") {
    $id = $_GET['delinid'];
    Helper::remove("incomes", "incomeId", $id);
    Helper::to("incomes.php");
}

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $amount = $_POST['amount'];
    $date = $_POST['date'];
    $type = $_POST['type'];
    if (!Helper::isEmpty($amount, $date)) {
        if (is_numeric($amount)) {
            try {
                Helper::addIncomes($amount, $date, $type, AcademicYear::getCurrentYearId($schoolId), $schoolId);
                Helper::to("incomes.php");
            } catch (Exception $ex) {
                $error[] = $ex->getMessage();
            }
        } else {
            $error[] = "Please specify a valid amount";
        }
    } else {
        $error[] = "All fields are needed";
    }
}
?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-lg-5">
                    <h2>Manage your incomes</h2>
                    <div class="card">
                        <div class="card-body">
                            <?php
                            if (!empty($error)) {
                                echo "<div class='alert alert-danger'>";
                                foreach ($error as $e) {
                                    echo $e . "<br>";
                                }
                                echo "</div>";
                            }
                            ?>
                            <form method="POST" action="" autocomplete="off">
                                <div class="form-group">
                                    <label>Amount</label>
                                    <input type="text" name="amount" class="form-control" placeholder="Amount Received" value="<?= $amount ?>" />
                                </div>

                                <div class="form-group">
                                    <label>Date: </label>
                                    <input type="date" name="date" class="form-control" value="<?= $date ?>" />
                                </div>
                                <div class="form-group">
                                    <label>Received On: </label>
                                    <input type="text" name="type" class="form-control" placeholder="Ex: Tuition Fee" value="<?= $type ?>">
                                </div>


                                <div class="form-group">
                                    <input type="submit" value="Add Income" class="btn btn-dark" />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-7">
                    <h2>All Incomes</h2>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-striped dt">
                            <thead>
                                <tr>
                                    <th>Received</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Operation</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $data = Helper::getIncomes($schoolId);
                                    if (!empty($data)) {
                                        foreach ($data as $d) {
                                            $da = strtotime($d->date);
                                            $day = date("l dS F, Y", $da);
                                            echo "<tr>";
                                            echo "<td>$d->type</td>";
                                            echo "<td>" . Helper::moneySign($schoolId)  . number_format($d->amount, 2) . "</td>";
                                            echo "<td>" . $day . "</td>";
                                            echo "<td><a class='btn btn-danger delete' href='?delinid=$d->incomeId'>Remove</a></td>";
                                            echo "</tr>";
                                        }
                                    }
                                } catch (Exception $ex) {
                                    echo "<tr>";
                                    $msg = $ex->getMessage();
                                    echo "<td colspan=4 class='text-center'>$msg</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-5 mx-auto">
                        <?php if (Helper::getNumRecords("incomes") > 10) : ?>
                            <div class="my-2 mx-auto">
                                <ul class="pagination">
                                    <li class="page-item">
                                        <a href="?page=1" class="page-link">1</a>
                                    </li>
                                    <li class="page-item">
                                        <?php if ($pager->getPrev() > 0) : ?>
                                            <a href="?page=<?php echo $pager->getPrev(); ?>" class="page-link">Prev</a>
                                        <?php else : ?>
                                            <a class="page-link">Prev</a>
                                        <?php endif; ?>
                                    </li>
                                    <li class="page-item">
                                        <a href="?page=<?php echo $pager->getNext(); ?>" class="page-link">Next</a>
                                    </li>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

        </div>
        <?php include_once './includes/footer.php'; ?>