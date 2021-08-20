<?php

use App\Database\CSVUploader;

include __DIR__ . "/init.php";

$csvFile = __DIR__ . "/names.csv";

$fields = ['name', 'age', 'class'];

$csvUploader = new CSVUploader($csvFile, 'names', $fields);

$csvUploader->uploadToDB();
