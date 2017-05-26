<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/3/2017
 * Time: 3:52 AM
 */
session_start();
include '../app/init.php';

if(isset($_SESSION['logged'])){
    mysql_query("UPDATE fmo_users SET user_last_location='".mysql_real_escape_string(basename(__FILE__, '.php')).".php?".$_SERVER['QUERY_STRING']."' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'");
    $location = mysql_fetch_array(mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    ?>
    <div class="page-content">
        <h3 class="page-title">
            Accidents
        </h3>
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a><?php echo $location['location_name']; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>">Dashboard</a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a class="load_page" data-href="assets/pages/accidents.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Accidents">Accidents</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title tabbable-line">
                        <div class="caption caption-md">
                            <i class="icon-fire theme-font bold"></i>
                            <span class="caption-subject font-red bold uppercase"><?php echo $location['location_name']; ?></span> <span class="font-red">|</span>  <small>Accidents</small>
                        </div>
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#accidents_tab" data-toggle="tab">Accidents</a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="accidents_tab">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
