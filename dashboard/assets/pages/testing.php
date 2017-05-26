<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/9/2017
 * Time: 3:42 AM
 */

function startDate($date) {
    $knownDate = strtotime('May 7, 2018');
    $diff = $date - $knownDate;
    $weeks = 2 * ceil($diff / (60*60*24*7*2));
    return strtotime("$weeks weeks", $knownDate);
}

$startDate = startDate(strtotime('May 7, 2017'));

echo "Pay Periods +1 year: <br/>";
for($i = 0; $i <= 52; $i+=2) {
    echo date('m-d-y', strtotime("+$i weeks", $startDate))."<br/> - ".date('m-d-y', strtotime("-$i weeks", $startDate));
}
echo "Pay Periods -1 year: <br/>";
for($i = 0; $i <= 52; $i+=2) {
    echo date('m-d-y', strtotime("-$i weeks", $startDate)), "<br/> - ".date('m-d-y', strtotime("+$i weeks", $startDate));
}