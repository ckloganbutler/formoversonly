<?php
/**
 * Created by PhpStorm.
 * User: gameroom
 * Date: 6/14/2017
 * Time: 12:40 PM
 */
session_start();
include '../app/init.php';

if(isset($_SESSION['logged'])){
    ?>
    // todo: make a bol?
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}