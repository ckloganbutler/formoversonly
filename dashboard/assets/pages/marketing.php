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
            Marketing
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
                    <a class="load_page" data-href="assets/pages/marketing.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Marketing">Marketing</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title tabbable-line">
                        <div class="caption caption-md">
                            <i class="icon-earphones-alt theme-font bold"></i>
                            <span class="caption-subject font-red bold uppercase"><?php echo $location['location_name']; ?></span> <span class="font-red">|</span>  <small>Marketing</small>
                        </div>
                        <div class="actions btn-set">
                            <a class="btn default red-stripe load_page" data-href="assets/pages/create_marketer.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Add new marketer">
                                <i class="fa fa-plus"></i> Add new marketer
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="employees_tab">
                                <div class="table-container">
                                    <table class="table table-striped table-bordered table-hover" id="employees">
                                        <thead>
                                        <tr role="row" class="heading">
                                            <th width="18%">
                                                <input type="checkbox" class="group-checkable"> Marketer Contacted
                                            </th>
                                            <th>
                                                Marketer Name & ID
                                            </th>
                                            <th>
                                                Marketer Phone
                                            </th>
                                            <th>
                                                Marketer Email
                                            </th>
                                            <th width="10%">
                                                Contact or Edit
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
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
