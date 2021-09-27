<?php

use App\Core\Helper;

include_once './includes/header.php';

if (Helper::isActivated($schoolId)) {
        Helper::to("index.php");
}

?>
<div class="container-scroller">
        <?php include_once './includes/navbar.php'; ?>

        <div class="main-panel">
                <div class="content-wrapper pb-0">
                        <h3 class="text-center">You are seeing this page because you are not fully activated.</h3>
                        <div class="card">
                                <div class="card-body">
                                        <p>If you are seeing this after a while of subscription, it means you have to
                                                renew your
                                                subscription plan in
                                                order to continue using these services. Your data will be safe and handed back to you upon renewal.
                                                Thank you!.</p>
                                        <p>
                                                If this is a misunderstanding please call the <a href="tel:+2207024725">Admin</a> or
                                                send an e-mail to
                                                <a href="contact.php">Help Desk</a> so as to activate you, otherwise
                                                please pay for
                                                the subscription fee to fully enjoy our services.
                                                <br>

                                                While we may be forgiving in handling tighter restrictions, it does not mean you can use all of the
                                                potential services if you have not paid the due to be fully enlisted as our potential customer or
                                                continuing customer. <br> The
                                                following features have been withheld till you have been activated:
                                        </p>

                                        <div class="row mx-auto ">
                                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                        <ul style="font-size: 17px !important; list-style:none;">
                                                                <li>Tuition Fee Management <i class="mdi
                        mdi-check-circle text-success"></i></li>
                                                                <li>Tuition Fee Range Generator <i class="mdi
                        mdi-check-circle text-success"></i></li>
                                                                <li>Staff Attendance Collection <i class="mdi
                        mdi-check-circle text-success"></i></li>
                                                                <li>Daily Statistics <i class="mdi
                        mdi-check-circle text-success"></i></li>
                                                                <li>Weekly Statistics <i class="mdi
                        mdi-check-circle text-success"></i></li>
                                                                <li>Lists of Students <i class="mdi
                        mdi-check-circle text-success"></i></li>
                                                        </ul>
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                                        <ul style="font-size: 17px !important; list-style:none;">
                                                                <li>Result Management <i class="mdi
                        mdi-check-circle text-success"></i></li>
                                                                <li>Positional Grading <i class="mdi
                        mdi-check-circle text-success"></i></li>
                                                                <li>Progress Report <i class="mdi
                        mdi-check-circle text-success"></i></li>
                                                                <li>Grade Book <i class="mdi
                        mdi-check-circle text-success"></i></li>
                                                                <li>In-House Mailing and Messaging <i class="mdi
                        mdi-check-circle text-success"></i></li>
                                                                <li>Students Attendance Collection <i class="mdi
                        mdi-check-circle text-success"></i></li>
                                                                <li>Editing a subject <i class="mdi
                        mdi-check-circle text-success"></i></li>
                                                                <li>Editing a class once created <i class="mdi
                        mdi-check-circle text-success"></i></li>
                                                                <li>Parents/Guardian - School Relationship <i class="mdi
                        mdi-check-circle text-success"></i></li>
                                                        </ul>
                                                </div>
                                        </div>
                                        <div class="text-center">
                                                <a href="activate.php" class="btn btn-primary">Activate Here</a>
                                        </div>
                                </div>
                        </div>




                </div>
                <?php include_once './includes/footer.php'; ?>