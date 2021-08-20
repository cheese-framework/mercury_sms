<?php

use App\Core\Helper;
use App\Media\PDFLib;

include_once './includes/header.php';

if (!Helper::isActivated($schoolId)) {
    Helper::to("index.php");
}

if (isset($_GET['class']) && $_GET['class'] != "") {
    $class = $_GET['class'];
} else {
    Helper::showErrorPage();
}
?>
<div class="container-scroller">
    <?php include_once './includes/navbar.php'; ?>

    <div class="main-panel">
        <div class="content-wrapper pb-0">
            <?php
            if ((isset($_GET['year']) && $_GET['year'] != "") && isset($_GET['term']) && $_GET['term'] != "" && (isset($_GET['avenue']) && $_GET['avenue'] != "") && (isset($_GET['class']) && $_GET['class'] != "")) {

                include './recordbook2.php';
                $year = $_GET['year'];
                $term = $_GET['term'];
                recordBook($year, $term, $class, $schoolId, $sms_userId);
            } else {
                Helper::showErrorPage();
            }

            ?>
        </div>
        <?php include_once './includes/footer.php'; ?>
        <script>
            // function exportTableToCSV(fileName) {
            //     var csv = [];
            //     var rows = document.querySelectorAll("table tr");

            //     for (var i = 0; i < rows.length; i++) {
            //         var row = [],
            //             cols = rows[i].querySelectorAll("td, th");

            //         for (var j = 0; j < cols.length; j++) row.push(cols[j].innerText);

            //         csv.push(row.join(","));
            //     }

            //     // Download CSV file
            //     downloadCSV(csv.join("\n"), fileName);
            // }

            // function downloadCSV(csv, filename) {
            //     var csvFile;
            //     var downloadLink;

            //     // CSV file
            //     csvFile = new Blob([csv], {
            //         type: "text/csv"
            //     });

            //     // Download link
            //     downloadLink = document.createElement("a");

            //     // File name
            //     downloadLink.download = filename;

            //     // Create a link to the file
            //     downloadLink.href = window.URL.createObjectURL(csvFile);

            //     // Hide download link
            //     downloadLink.style.display = "none";

            //     // Add the link to DOM
            //     document.body.appendChild(downloadLink);

            //     // Click download link
            //     downloadLink.click();
            // }
            // const btn = document.getElementById('export');
            // btn.addEventListener('click', function() {
            //     exportTableToCSV('gradebookforgradefour.csv');
            // })
            // document.getElementById('export').onclick = exportTableToCSV('gradebook.csv');
        </script>