<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/9/2017
 * Time: 3:42 AM
 */

$refStart = new DateTime('2017-01-02');
$periodLength = 14;

$now = new DateTime();
$cur = date('Y-m-d');

$daysIntoCurrentPeriod = (int)$now->diff($refStart)->format('%a') % $periodLength;
$currentPeriodStart = clone $now;
$currentPeriodStart->sub(new DateInterval('P'.$daysIntoCurrentPeriod.'D'));

$date  = date("Y-m-d", strtotime($cur." -".$daysIntoCurrentPeriod." days"));
$end    = date('Y-m-d', strtotime($date." +14 days"));
echo $date." - ".$end;