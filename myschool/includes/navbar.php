<?php

use App\Core\Helper;

?>

<nav class="sidebar sidebar-offcanvas id=" sidebar">
    <ul class="nav">
        <li class="nav-item nav-profile border-bottom">
            <a href="#" class="nav-link flex-column">

                <div class="nav-profile-text d-flex ml-0 mb-3 flex-column">
                    <div class="nav-profile-image">
                        <img src="assets/profile/<?= $pic; ?>" alt="profile" />
                        <!--change to offline or busy as needed-->
                    </div>
                    <span class="font-weight-semibold mb-1 mt-2 text-center">Hi, <?= $sms_username; ?></span>
                </div>
            </a>
        </li>
        <li class="pt-2 pb-1">
            <span class="nav-item-head">Menus</span>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="index.php">
                <i class="mdi mdi-compass-outline menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <?php if ($sms_role == "Super-Admin" && !Helper::isActivated($schoolId)) : ?>
            <li class="nav-item">
                <a class="nav-link" href="unactive.php">
                    <i class="mdi mdi-alert menu-icon text-danger"></i>
                    <span class="menu-title text-danger">Read Me! (Unactivated)</span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($sms_role == "Super-Admin" && !Helper::isActivated($schoolId)) : ?>
            <li class="nav-item">
                <a class="nav-link" href="activate.php">
                    <i class="mdi mdi-alert menu-icon text-warning"></i>
                    <span class="menu-title text-warning">Activate Now</span>
                </a>
            </li>
        <?php endif; ?>
        <li class="nav-item make-scroll">
            <a class="nav-link" data-toggle="collapse" href="#ui-basic" aria-expanded="false" aria-controls="ui-basic">
                <i class="mdi mdi-school menu-icon"></i>
                <span class="menu-title">School System</span>
                <i class="menu-arrow"></i>
            </a>
            <div class="collapse" id="ui-basic">
                <ul class="nav flex-column sub-menu">

                    <?php if ($sms_role == "Super-Admin" && Helper::isActivated($schoolId)) : ?>
                        <!-- <li class="nav-item">
                            <a class="nav-link" href="events.php">
                                <i class="mdi mdi-clock"></i>
                                <span class="">&nbsp; &nbsp;Events Management</span>
                            </a>
                        </li> -->
                    <?php endif; ?>
                    <?php if (Helper::hasBeenAssignedAClass($sms_userId, $schoolId, $sms_role)) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="addstudent.php">
                                <i class="mdi mdi-bag-personal"></i> &nbsp; &nbsp;Add Student</a>
                        </li>
                    <?php endif; ?>

                    <?php if ($sms_role == $ADMIN && Helper::isActivated($schoolId)) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="uploadstudent.php">
                                <i class="mdi mdi-bag-personal"></i> &nbsp; &nbsp;Upload Student</a>
                        </li>
                    <?php endif; ?>

                    <?php if ($sms_role == $ADMIN && Helper::isActivated($schoolId)) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="leveling.php">
                                <i class="mdi mdi-book"></i> &nbsp; &nbsp;Promotion Panel</a>
                        </li>
                    <?php endif; ?>

                    <?php if (Helper::isUsingOnlineAssessment($schoolId) && Helper::isActivated($schoolId) && $sms_role == $TEACHER) : ?>
                        <li class="nav-item">
                            <a class="nav-link" target="_blank" href="../workshop">
                                <i class="mdi mdi-book"></i> &nbsp; &nbsp;Online Assessment</a>
                        </li>
                    <?php endif; ?>

                    <?php if ($sms_role == $TEACHER && Helper::isActivated($schoolId)) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="chooseClass.php">
                                <i class="mdi mdi-database"></i> &nbsp; &nbsp;Add Result</a>
                        </li>
                    <?php endif; ?>

                    <?php if ($sms_role == $TEACHER && Helper::isActivated($schoolId) && Helper::hasBeenAssignedAClass($sms_userId, $schoolId, $sms_role)) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="resultclass.php">
                                <i class="mdi mdi-receipt"></i> &nbsp; &nbsp;Manage Results</a>
                        </li>
                    <?php endif; ?>

                    <?php if ($sms_role == $TEACHER && Helper::isActivated($schoolId)) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="gradeclass.php">
                                <i class="mdi mdi-book"></i> &nbsp; &nbsp;Grade Book</a>
                        </li>
                    <?php endif; ?>



                    <?php if ($sms_role == $TEACHER && Helper::hasBeenAssignedAClass($sms_userId, $schoolId, $sms_role)) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="allstudents.php">
                                <i class="mdi mdi-book-open"></i> &nbsp; &nbsp;View All Your Students</a>
                        </li>
                    <?php endif; ?>

                    <?php if ($sms_role == $TEACHER && Helper::isActivated($schoolId) && Helper::hasBeenAssignedAClass($sms_userId, $schoolId, $sms_role)) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="chooseClassAttendance.php">
                                <i class="mdi mdi-calendar"></i> &nbsp; &nbsp;Create Attendance</a>
                        </li>
                    <?php endif; ?>

                    <?php if ($sms_role == $TEACHER && Helper::isActivated($schoolId) && Helper::hasBeenAssignedAClass($sms_userId, $schoolId, $sms_role)) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="chooseclassdaily.php">
                                <i class="mdi mdi-calendar"></i> &nbsp; &nbsp;Daily Attendance</a>
                        </li>
                    <?php endif; ?>

                    <?php if ($sms_role == $TEACHER && Helper::isActivated($schoolId) && Helper::hasBeenAssignedAClass($sms_userId, $schoolId, $sms_role)) : ?>
                        <li class="nav-item">
                            <a class="nav-link" href="chooseclassweekly.php">
                                <i class="mdi mdi-calendar"></i> &nbsp; &nbsp;Weekly Attendance</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </li>

        <?php if ($sms_role == $ADMIN) : ?>
            <li class="nav-item make-scroll">
                <a class="nav-link" data-toggle="collapse" href="#admin-ui" aria-expanded="false" aria-controls="admin-ui">
                    <i class="mdi mdi-account-box menu-icon"></i>
                    <span class="menu-title">Admin Tasks</span>
                    <i class="menu-arrow"></i>
                </a>
                <div class="collapse" id="admin-ui">
                    <ul class="nav flex-column sub-menu">

                        <?php if ($sms_role == "Super-Admin") : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="staffsprofile.php">
                                    <i class="mdi mdi-worker"></i> &nbsp; &nbsp;Staffs</a>
                            </li>
                        <?php endif; ?>

                        <?php if ($sms_role == $ADMIN) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="parent.php">
                                    <i class="mdi mdi-account-child"></i> &nbsp; &nbsp;Manage Parents</a>
                            </li>
                        <?php endif; ?>

                        <?php if ($sms_role == "Super-Admin") : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="class.php">
                                    <i class="mdi mdi-book"></i> &nbsp; &nbsp;Manage Class</a>
                            </li>
                        <?php endif; ?>

                        <?php if ($sms_role == "Super-Admin") : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="academicyear.php">
                                    <i class="mdi mdi-pencil"></i> &nbsp; &nbsp;Academic Years</a>
                            </li>
                        <?php endif; ?>
                        <?php if ($sms_role == "Super-Admin") : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="subjects.php">
                                    <i class="mdi mdi-bus-school"></i> &nbsp; &nbsp;Subjects</a>
                            </li>
                        <?php endif; ?>

                        <?php if ($sms_role == "Super-Admin" && Helper::isActivated($schoolId)) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="fees.php">
                                    <i class="mdi mdi-cash-multiple"></i>
                                    <span class="">&nbsp; &nbsp;Fees Management</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($sms_role == "Super-Admin") : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="expenses.php">
                                    <i class="mdi mdi-cash-register"></i>&nbsp; &nbsp;Expenses
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($sms_role == "Super-Admin") : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="incomes.php">
                                    <i class="mdi mdi-cash-100"></i>
                                    <span class="">&nbsp; &nbsp;Incomes</span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <?php if ($sms_role == $ADMIN) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="remarks.php">
                                    <i class="mdi mdi-note"></i> &nbsp; &nbsp;Manage Remarks</a>
                            </li>
                        <?php endif; ?>

                        <?php if ($sms_role == $ADMIN) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="allstudents.php">
                                    <i class="mdi mdi-book-open"></i> &nbsp; &nbsp;View All Students</a>
                            </li>
                        <?php endif; ?>

                        <?php if ($sms_role == $ADMIN && Helper::isActivated($schoolId)) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="statistics.php">
                                    <i class="mdi mdi-calendar"></i> &nbsp; &nbsp;Daily Statistics</a>
                            </li>
                        <?php endif; ?>

                        <?php if ($sms_role == $ADMIN && Helper::isActivated($schoolId)) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="wstatistics.php">
                                    <i class="mdi mdi-calendar"></i> &nbsp; &nbsp;Weekly Statistics</a>
                            </li>
                        <?php endif; ?>

                        <?php if ($sms_role == $ADMIN && Helper::isActivated($schoolId) && Helper::hasBeenAssignedAClass($sms_userId, $schoolId, $sms_role)) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="weekly.php">
                                    <i class="mdi mdi-calendar"></i> &nbsp; &nbsp;Weekly Attendance</a>
                            </li>
                        <?php endif; ?>
                        <?php if ($sms_role == "Super-Admin" && Helper::isActivated($schoolId)) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="createstaffattendance.php">
                                    <i class="mdi mdi-calendar"></i> &nbsp; &nbsp;Staff Attendance</a>
                            </li>
                        <?php endif; ?>

                        <?php if ($sms_role == $ADMIN && Helper::isActivated($schoolId)) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="staffdaily.php">
                                    <i class="mdi mdi-calendar"></i> &nbsp; &nbsp;Staff Daily Attendance</a>
                            </li>
                        <?php endif; ?>
                        <?php if ($sms_role == $ADMIN && Helper::isActivated($schoolId)) : ?>
                            <li class="nav-item">
                                <a class="nav-link" href="staffweekly.php">
                                    <i class="mdi mdi-calendar"></i> &nbsp; &nbsp;Staffs Weekly Attendance</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </li>
        <?php endif; ?>

        <?php if ($sms_role == "Super-Admin") : ?>
            <li class="nav-item">
                <a class="nav-link" href="schoolsettings.php">
                    <i class="mdi mdi-flash menu-icon"></i>
                    <span class="menu-title">Settings</span>
                </a>
            </li>
        <?php endif; ?>
        <li class="nav-item">
            <a class="nav-link" href="contact.php">
                <i class="mdi mdi-phone menu-icon"></i>
                <span class="menu-title">Contact Us</span>
            </a>
        </li>

        <?php if ($sms_role == "Super-Admin") : ?>
            <li class="nav-item">
                <a class="nav-link" href="logs.php">
                    <i class="mdi mdi-alert-circle text-info menu-icon"></i>
                    <span class="menu-title text-info">Issues & Logs</span>
                </a>
            </li>
        <?php endif; ?>

        <li class="nav-item">
            <a class="nav-link" href="setting.php">
                <i class="mdi mdi-account menu-icon"></i>
                <span class="menu-title">Manage Profile</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="logout.php">
                <i class="mdi mdi-logout menu-icon"></i>
                <span class="menu-title">Logout</span>
            </a>
        </li>
    </ul>
</nav>

<!-- partial -->
<div class="container-fluid page-body-wrapper">
    <!-- partial:partials/_settings-panel.html -->

    <!-- partial -->
    <!-- partial:partials/_navbar.html -->
    <nav class="navbar default-layout-navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
        <div class="navbar-menu-wrapper d-flex align-items-stretch">

            <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                <span class="mdi mdi-chevron-double-left"></span>
            </button>

            <ul class="navbar-nav navbar-nav-right">

                <li class="nav-item nav-logout d-none d-lg-block">
                    <a class="nav-link" href="logout.php" title="logout">
                        <i class="mdi mdi-logout"></i>
                    </a>
                </li>
                <li class="nav-item nav-logout d-none d-lg-block">
                    <a class="nav-link" href="index.php">
                        <i class="mdi mdi-home-circle"></i>
                    </a>
                </li>

            </ul>
            <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button" data-toggle="offcanvas">
                <span class="mdi mdi-menu"></span>
            </button>
        </div>
    </nav>