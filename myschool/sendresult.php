<?php

use App\Core\Helper;
use App\Media\PDFLib;
use App\Notifiable\Mail;
use App\Notifiable\Notifiable;
use App\School\AcademicYear;
use App\School\SMSParent;
use App\School\Student;
use App\School\Subject;

include_once "includes/header.php";

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

if ((isset($_GET['year']) && $_GET['year'] != "") &&
    (isset($_GET['term']) && $_GET['term'] != "") &&
    (isset($_GET['studentId']) && $_GET['studentId'] != "") &&
    (isset($_GET['class']) && $_GET['class'] != "")
) {
    $year = $_GET['year'];
    $term = $_GET['term'];
    $id = $_GET['studentId'];
    $class = $_GET['class'];
} else {
    Helper::showErrorPage();
}

?>

<div class="container-scroller">

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <div class="row">
                <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12 mx-auto" id="panel">


                    <div class="card my-3 p-3">
                        <?php
                        $str = "";
                        $str .=
                            "
                                <!DOCTYPE html>
                                <head>
                                <style>                        
                                    table, th,td {
                                        border: 1px solid black;
                                        border-radius: 5px;
                                        width: fit-content;
                                        font-family: 'Courier New', Courier, monospace;
                                        font-size: 18px;
                                        
                                    }
                                    th,td {
                                        padding: 15px;
                                    }
                                    th {
                                        text-align: left;
                                    }
                                    table {
                                        border-spacing: 5px;
                                    }
                                    h5 {
                                        font-family: 'Courier New', Courier, monospace;
                                        font-size: 22px;
                                    }
                                </style></head><body>";

                        $str .= "<h5>School: " . Helper::getSchoolName($schoolId) . "</h5>
                        <h5>Student: " . Student::getFullName($id) . "</h5>
                        <h5>Class: " . Helper::classEncode($class) . "</h5>
                        <h5>Term: " . ucfirst($term) . "</h5>
                        <h5>Academic year: " . AcademicYear::getAcademicYearById($year) . "</h5>"; ?>
                        <?php
                        try {
                            $data = Subject::returnSubject($class, $schoolId);
                            if ($data != null) {
                                $str .= "<div class='col-lg-12 mx-auto mb-3 mt-4'>";
                                $str .= "<div class='table-responsive mt-3'>";
                                $str .= "<table>";
                                $str .= "<thead>";
                                $str .= "<tr>";
                                $str .= "<th><b>Subject</b></th>";
                                $str .= "<th class='text-center'><b>Continuous Assessment</b></th>";
                                $str .= "<th>Exam</th>";
                                $str .= "<th><b>Total %</b></th>";
                                $str .= "<th><b>Remark</b></th>";
                                $str .= "</tr>";
                                $str .= "</thead>";
                                $str .= "<tbody>";
                                $grandTotal = 0;
                                $subCount = 0;
                                $grandTotalAlt = 0;
                                foreach ($data as $d) {

                                    $record = Helper::getResult(
                                        $year,
                                        $term,
                                        $d->subjectId,
                                        $id,
                                        $schoolId
                                    );
                                    $resId = Helper::$resId;
                                    $total = 0;
                                    $str .= "<tr>";
                                    $str .= "<td>" . $d->subject . "</td>";
                                    if ($record != null) {
                                        $subCount += 100;
                                        $str .= "<td>";
                                        $str .= "<table>
                                    <thead><tr>";
                                        $gradingSheet = Helper::getGradeTypes($d->subjectId);
                                        $explodedSheet = explode(",", $gradingSheet);
                                        foreach ($explodedSheet as $rec) {
                                            $str .= "<th class='text-center'><b>" .
                                                $rec . "</b></th>";
                                        }
                                        $str .= "</tr></thead>
                                            <tbody>
                                            <tr>";
                                        foreach ($record[0] as $r) {
                                            if (in_array($r[0], $explodedSheet)) {
                                                $str .= "<th class='text-center'><b>" .
                                                    $r[1] . "</b></th>";
                                                $total += $r[1];
                                            }
                                        }
                                        $total += $record[1];
                                        $total += $record[2];
                                        $str .= "</tr>
                                            </tbody>
                                        </table>";
                                        $str .= "</td>";
                                        $final = Helper::getFinalMark($resId);
                                        $originalGrade = round(
                                            ($total / $final) * 100,
                                            2
                                        );
                                        $grandTotalAlt += $originalGrade;
                                        $str .= "<td>" . $record[1] . "</td>";
                                        $str .= "<td><b>" . $originalGrade .
                                            "</b></td>";
                                        $str .= "<td>" .
                                            Helper::getRemark(
                                                $originalGrade,
                                                $schoolId
                                            ) . "</td>";
                                    } else {
                                        $str .= "<td class='text-center'><b>...</b></td>";
                                    }

                                    $str .= "</tr>";
                                }
                                if ($subCount > 0) {
                                    $avr = number_format(
                                        ($grandTotalAlt / $subCount) * 100,
                                        2
                                    );
                                } else {
                                    $avr = 0;
                                }
                                $str .= "<tr>";
                                $str .= "<td><b>Total:</b></td>";
                                $str .= "<td class='text-center'><b><i>...</i></b></td>";
                                $str .= "<td class='text-center'><b><i>...</i></b></td>";
                                $str .= "<td class='text-center'><b> ~ $grandTotalAlt ~ </b></td>";
                                $str .= "<td><b>Average: $avr%</b></td>";
                                $str .= "</tr>";
                                $str .= "</tbody>";

                                $str .= "</table>";
                                $str .= "</div>";
                                $str .= "</div></body></html>";
                            }
                        } catch (Exception $ex) {
                            $str .= "</body></html>";
                        }

                        $pdf = new PDFLib();
                        $filename = uniqid("SMS") . ".pdf";
                        $html = uniqid("SMS") . ".html";
                        file_put_contents($html, $str);
                        $contents = file_get_contents($html);
                        $pdf->generatePdf($contents, $filename);

                        $parent = SMSParent::getParentEmail($id);
                        if ($parent == "") {
                            echo "No parent attached to this student";
                            @@unlink($html);
                            @@unlink($filename);
                        } else {
                            // send mail and delete attachment
                            $name =  Student::getFullName($id) . "'s Report";
                            $mail = new Mail();
                            $mail->sendMailWithAttachments([$parent => $parent], $name, "This email contains an attachment with a report.", $sms_username, DEFAULT_FROM, ["Report" => $filename]);
                            $parId = SMSParent::getParentIdByEmail($parent);
                            if ($mail->sent) {
                                if (Helper::isUsingSMS($schoolId)) {
                                    $notification = new Notifiable(['twilio'], $parId);
                                    $notification->notify("You have mail from " . Helper::getSchoolName($schoolId) . ". In regards to your ward's result.");
                                }
                                echo "Sent attachment. You can close this page now";
                                @@unlink($html);
                                @@unlink($filename);
                            } else {
                                echo "Something went wrong.<br>Could not send attachment.";
                                @@unlink($html);
                                @@unlink($filename);
                            }
                        }

                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
        include_once './includes/footer.php';
        ?>