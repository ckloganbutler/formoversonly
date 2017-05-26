<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/4/2017
 * Time: 7:25 AM
 */
session_start();

session_unset();
session_destroy();
header("Location: ../../../index.php?err=goodbye");