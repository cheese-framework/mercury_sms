<?php

function addSuffix($num)
{
    if ($num == 2 || $num == 22 || $num == 32) {
        return $num . "nd";
    } else if ($num == 3 || $num == 23 || $num == 33) {
        return $num . "rd";
    } else if ($num == 1 || $num == 21 || $num == 31) {
        return $num . "st";
    } else {
        return $num . "th";
    }
}

$data = [850, 630, 836, 546, 615, 660, 747, 802, 596, 811, 762, 829, 426, 775, 845, 839, 788, 811, 888, 801, 681, 645, 638, 676, 433, 489, 740, 825, 668, 742, 714];
rsort($data);
$prev = 0;
$i = 0;
$count = 1;
$prevI = $i;
foreach ($data as $d) {
    if ($prev == round($d)) {
        $count++;
        $pos = addSuffix(($prevI));
        $i++;
    } else {
        $pos = addSuffix(($count));
        $i++;
        $count++;
        $prevI = $i;
        $prev = round($d);
    }

    if ($pos == "1st") {
        $col = "text-success";
    } else if ($pos == "2nd") {
        $col = "text-primary";
    } else if ($pos == "3rd") {
        $col = "text-danger";
    } else {

        $col = "";
    }
    $tot = round($d);
    echo $tot . " - " . $pos;
    echo "<br>";
}