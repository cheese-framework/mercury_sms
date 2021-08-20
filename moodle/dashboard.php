<?php include_once "./pages/header.pages.php"; ?>
<?php include_once "./pages/navbar.pages.php"; ?>
<div class="container-fluid">
    <div class="row justify-content-center">

        <div class="col-lg-4 col-md-12">
            <div class="white-box analytics-info">
                <h3 class="box-title">Pending Assessments</h3>
                <ul class="list-inline two-part d-flex align-items-center mb-0">

                    <li class="ms-auto"><span class="counter text-purple">
                            <?= Assessment::getPendingAssessments($s_id, $class); ?></span></li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="white-box analytics-info">
                <h3 class="box-title">Valid Assessments</h3>
                <ul class="list-inline two-part d-flex align-items-center mb-0">

                    <li class="ms-auto"><span class="counter text-info"><?= Assessment::getValidAssessments($s_id, $class); ?></span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-lg-4 col-md-12">
            <div class="white-box analytics-info">
                <h3 class="box-title">Expired Assessments</h3>
                <ul class="list-inline two-part d-flex align-items-center mb-0">
                    <li class="ms-auto"><span class="counter text-info"><?= Assessment::getExpiredAssessments($s_id, $class); ?></span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- ============================================================== -->
<!-- End Container fluid  -->
<!-- ============================================================== -->


<?php include_once "./pages/footer.pages.php"; ?>