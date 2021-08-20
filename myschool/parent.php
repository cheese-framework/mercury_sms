<?php

use App\Core\Helper;
use App\School\AcademicYear;
use App\School\SMSParent;

include_once './includes/header.php';

$error = [];
$name = $email = "";
$success = "";


// delete parent

if (isset($_GET['delete']) && $_GET['delete'] != "") {
    $id = $_GET['delete'];
    $page = $_GET['page'] ?? 0;
    SMSParent::removeParent($id, $schoolId);
    Helper::to("parent.php?page=" . $page);
}


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    if (!Helper::isEmpty($name, $email, $phone)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if (strlen($phone) === 11) {
                $parent = new SMSParent($name, $email, $schoolId, $phone);
                try {
                    $parent->addParent();
                    $connectedTo = $parent->getLinked();
                    if ($connectedTo > 1) {
                        $success = "$name has been added to the lists of Parents. Connected to: $connectedTo students.";
                    } else {
                        $success = "$name has been added to the lists of Parents. Connected to: $connectedTo student.";
                    }
                    $name = "";
                    $email = "";
                } catch (Exception $e) {
                    $error[] = $e->getMessage();
                }
            } else {
                $error[] = "Please enter a valid phone number with country code (+220)";
            }
        } else {
            $error[] = "E-mail is invalid";
        }
    } else {
        $error[] = "Fill all fields up";
    }
}

?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">

            <div class="modal" tabindex="-1" role="dialog" id="exampleModal">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Modal title</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>Modal body text goes here.</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row my-2 mx-auto">
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <h4>Add Parent</h4>
                            <p>Just add the parent and we will do the linking to student(s) automatically.</p>
                        </div>
                        <div class="card-body">
                            <?php
                            if (!empty($error)) {
                                echo "<div class='alert alert-danger'>";
                                foreach ($error as $e) {
                                    echo $e . "<br>";
                                }
                                echo "</div>";
                            }

                            if ($success != "") {
                                echo "<div class='alert alert-success' id='success'>$success</div>";
                            ?>
                                <script>
                                    setTimeout(() => {
                                        document.getElementById('success').style.display = 'none';
                                    }, 3000);
                                </script>
                            <?php
                            }
                            ?>
                            <form action="" method="post" autocomplete="off">
                                <div class="form-group">
                                    <label for="name">Full name *</label>
                                    <input type="text" name="name" id="name" placeholder="Full name" class="form-control" required value="<?= $name ?>">
                                </div>
                                <div class="form-group">
                                    <label for="email">E-mail Address *</label>
                                    <input type="email" name="email" id="email" placeholder="E-mail Address" class="form-control" required value="<?= $email ?>">
                                </div>
                                <div class="form-group">
                                    <label for="phone">Mobile Phone *</label>
                                    <input type="text" name="phone" id="phone" placeholder="Phone Number (Include country code)" class="form-control" required value="<?= $email ?>" minlength="11" maxlength="11">
                                </div>
                                <div class="form-group">
                                    <input type="submit" value="Save" class="btn btn-success">
                                </div>
                            </form>
                        </div>
                        <div class="card-footer text-small text-right">Powered by QHITECH Engine</div>
                    </div>
                </div><br>


                <div class="col-lg-7">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead>
                                <th>Full name</th>
                                <th style="display: none;">Connections</th>
                                <th>Connected to</th>
                                <th>View More</th>
                                <th>Delete</th>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $parents = SMSParent::getParents($schoolId, $page, DEFAULT_RECORD_PER_PAGE);
                                    if ($parents != null) {
                                        foreach ($parents as $parent) {
                                            echo "<tr>";
                                            echo "<td>" . $parent->name . "</td>";
                                            try {
                                                $myStudents = $parent->getMyStudents(AcademicYear::getCurrentYearId($schoolId), "custom");
                                                if ($myStudents != null) {
                                                    echo "<td style='display:none;'> Student(s):<br><ul>";
                                                    foreach ($myStudents as $student) {
                                                        echo "<li style='list-style: georgian;'><br>Name: " . $student->fullname . ".<br>Class: " . Helper::classEncode($student->class) . "</li>";
                                                    }
                                                    echo "<li>" . $student->phone . "</li>";
                                                    echo "</ul></td>";
                                                } else {
                                                    echo "<td style='display:none;'>Nothing here</td>";
                                                }
                                            } catch (Exception $e) {
                                                echo "<td style='display:none;'>" . $e->getMessage() . "</td>";
                                            }
                                            echo "<td>" . $parent->getLinked() . " student(s)</td>";
                                            echo "<td><button class='btn btn-facebook view-more'  data-toggle='modal' data-target='#exampleModal'>View More</button></td>";
                                            echo "<td><a href='parent.php?delete=$parent->id&page=$page' class='btn btn-google delete'>Delete</a></td>";
                                        }
                                    } else {
                                        echo "<tr><td colspan='4' class='text-center text-warning'>No record found</td></tr>";
                                    }
                                } catch (Exception $e) {
                                    echo "<tr><td colspan='4' class='text-center text-warning'>No record found</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-12 mx-auto my-3">
                        <?php if ($page > 0) : ?>
                            <a href="?page=<?= $page - 1; ?>">Prev &laquo;</a>
                        <?php else : ?>
                            Prev
                        <?php endif; ?>
                        |
                        <a href="?page=<?= $page + 1; ?>">Next &raquo;</a>
                    </div>
                </div>
            </div>
        </div>
        <?php include_once './includes/footer.php'; ?>
        <script>
            $(".view-more").on("click", function() {
                let contents = this.parentElement.parentElement.children;
                $(".modal-header").text("More details");
                $(".modal-body").html(contents[1].innerHTML);
                $('#myModal').on('shown.bs.modal', function() {
                    $('#myInput').trigger('focus')
                });
            });
        </script>