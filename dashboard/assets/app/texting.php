<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 5/28/2017
 * Time: 6:50 AM
 */
session_start();
include 'init.php';

if(isset($_GET['txt'])){
    if($_GET['txt'] == 'upr'){
        $who = $_POST['p'];
        $msg = "Your new password on FMO is: ".struuid();

        _sendText($who, $msg);
    }
}