<?php
session_start();
if(!isset($_SESSION['logged']) && $_SESSION['logged'] != true){
    header("Location: ../index.php?err=no_access");
} else {
    include 'assets/app/init.php';
    $lastlocation = mysql_query("UPDATE fmo_users SET user_last_ext_location='".mysql_real_escape_string($_GET['luid'])."' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'");
    $user = mysql_fetch_array(mysql_query("SELECT user_company_name, user_employer, user_employer_location, user_pic, user_status, user_creator, user_setup, user_group, user_last_location, user_fname, user_lname, user_token, user_company_token, user_last_ext_location, user_permissions, user_esc_permissions FROM fmo_users WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'"));
    $location = mysql_fetch_array(mysql_query("SELECT location_name, location_quote, location_state, location_nickname FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."' AND location_owner_company_token='".mysql_real_escape_string($_SESSION['cuid'])."'"));
    $exp      = mysql_fetch_array(mysql_query("SELECT user_license_exp FROM fmo_users WHERE user_company_token='".mysql_real_escape_string($_SESSION['cuid'])."'"));
    if(!empty($location['location_nickname'])){
        $nickname = "(".$location['location_nickname'].")";
    } else {
        $nickname = "(".$location['location_state'].")";
    }
    $quote = $location['location_quote'];
}
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" data-uuid="<?php echo $_SESSION['uuid']; ?>">
<!--<![endif]-->
<head>
	<meta charset="utf-8"/>
	<title>Dashboard - For Movers Only</title>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
	<meta http-equiv="Content-type" content="text/html; charset=utf-8">
	<meta content="" name="description"/>
	<meta content="" name="author"/>

    <!-- INCLUDE THE CORE COMPONENTS ON LOAD -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.9.1/sweetalert2.min.css" rel="stylesheet" type="text/css"/>
    <script src="assets/global/plugins/pace/pace.min.js" type="text/javascript"></script>
    <link href="assets/global/plugins/pace/themes/pace-theme-minimal.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css"/>
	<link href="assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link href="assets/global/plugins/font-awesome/css/font-awesome-animation.min.css" rel="stylesheet" type="text/css"/>
	<link href="assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
	<link href="assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.css">
	<link href="assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css"/>
    <link href="assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="assets/global/plugins/jquery-tags-input/jquery.tagsinput.css"/>
    <link href="assets/global/plugins/xeditable/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet" />
    <link href="assets/global/plugins/clockface/css/clockface.css" rel="stylesheet" type="text/css"/>
    <link href="assets/global/plugins/bootstrap-datepicker/css/datepicker3.css" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-colorpicker/css/colorpicker.css"/>
    <link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"/>
    <link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css"/>
    <link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-markdown/css/bootstrap-markdown.min.css">
    <link rel="stylesheet" type="text/css" href="assets/global/plugins/typeahead/typeahead.css">
    <link href="assets/admin/pages/css/timeline.css" rel="stylesheet" type="text/css"/>
    <link href="assets/global/plugins/icheck/skins/all.css" rel="stylesheet"/>
    <link href="assets/admin/pages/css/invoice.css" rel="stylesheet" type="text/css"/>
    <link href="assets/global/plugins/select2/select2.css" rel="stylesheet" type="text/css"/>
	<link href="assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="assets/admin/pages/css/pricing-table.css" rel="stylesheet" type="text/css">
    <link href="assets/global/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet"/>
    <link href="assets/admin/pages/css/profile-old.css" rel="stylesheet" type="text/css"/>
    <link href="assets/admin/pages/css/error.css" rel="stylesheet" type="text/css"/>
	<link href="assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
    <link href="assets/admin/pages/css/tasks.css" rel="stylesheet" type="text/css"/>
    <link href="assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css" rel="stylesheet" type="text/css"/>
    <link href="assets/admin/pages/css/profile.css" rel="stylesheet" type="text/css"/>
    <link href="assets/admin/pages/css/todo.css" rel="stylesheet" type="text/css">
	<link id="style_color" href="assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"/>
	<link href="assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
    <link href="assets/global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
	<link rel="shortcut icon" href="favicon.ico"/>
    <link href="assets/admin/pages/css/portfolio.css" rel="stylesheet" type="text/css">
    <link href="assets/global/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet" type="text/css">
    <style type="text/css">
        .datepicker-inline {
            margin: auto !important;
        }
        .checking {
            color: grey;
        }
        .che {
            opacity:0.5;
        }
        .checking:hover {
            color: white;
        }
        .popover-title {
            color: black !important;
        }
        .dataTables_filter {
            float: right !important;
        }
        .dataTables_paginate {
            float: right !important;
        }
        .filter {
            background-color: #FFFFC1;
        }
        .filter .form-control {
            background-color: white;
        }

        @media print
        {
            table { page-break-after:auto }
            tr    { page-break-inside:avoid; page-break-after:auto }
            td    { page-break-inside:avoid; page-break-after:auto }
            thead { display:table-header-group }
            tfoot { display:table-footer-group }
            .no_print { display: none !important; }
        }
        .info-btn:hover {
            background-color: #cb5a5e;
            color: white;
        }
    </style>

</head>
<body class="page-header-fixed page-quick-sidebar-over-content page-footer-fixed">
<div id="loader-wrapper">
    <div id="loader"></div>
    <div class="loader-section section-left"></div>
    <div class="loader-section section-right"></div>
</div>
<div class="page-header -i navbar navbar-fixed-top nigga" id="nigga">
	<div class="page-header-inner">
		<div class="page-logo" style="color: white; text-transform: uppercase; font-size: 23px; letter-spacing: .01em; word-spacing: 1px; width: auto; margin-top: 7px; font-weight: 300;">
            <?php
            $name = companyName($_SESSION['cuid']);
            if(!empty($name)){
                $cool = explode(" ", $name);
                $white = true; $red = false;
                foreach($cool as $word){
                    if($white == true){
                        echo "<span style='color: #FFFFFF'>".$word."</span>";
                        $white = false;
                        $red   = true;
                    } elseif($red == true){
                        echo "<span style='color: #cb5a5e'>".$word."</span>";
                        $red   = false;
                        $white = true;
                    }
                }
            } else {

            }
            ?>
			<div class="menu-toggler sidebar-toggler hide">

			</div>
		</div>
			<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
		</a>
		<div class="top-menu">
			<ul class="nav navbar-nav pull-right">
                <?php
                if(!empty($_GET['luid'])){
                    ?>
                    <li class="dropdown dropdown-dark dropdown-language">
                        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="true">
                            <img alt="" src="assets/global/img/flags/us.png">
                            <span class="langname"> <?php echo $location['location_name']; ?> <?php echo $nickname; ?> </span>
                            <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-default">
                            <?php
                            $perms = explode(',', $user['user_permissions']);
                            $findLocations = mysql_query("SELECT location_name, location_token, location_state, location_nickname FROM fmo_locations WHERE location_owner_company_token='".$_SESSION['cuid']."' ORDER BY location_name ASC");
                            if(mysql_num_rows($findLocations) > 0){
                                while($loc = mysql_fetch_assoc($findLocations)){
                                    if(in_array($loc['location_token'], $perms) || $_SESSION['group'] == 1){
                                        // Nothing. Let it show!
                                        if(!empty($loc['location_nickname'])){
                                            $nickname = "(".$loc['location_nickname'].")";
                                        } else {
                                            $nickname = "(".$loc['location_state'].")";
                                        }
                                    } else {
                                        continue;
                                    }

                                    ?>
                                    <li>
                                        <a class="change_location" data-new-location="<?php echo $loc['location_token']; ?>" data-new-location-name="<?php echo $loc['location_name']; ?>" data-new-location-state="<?php echo $loc['location_state']; ?>"><img alt="" src="assets/global/img/flags/us.png"> <?php echo $loc['location_name']; ?> <?php echo $nickname; ?> </a>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                    </li>
                    <?php
                }
                ?>
				<li class="dropdown dropdown-dark dropdown-user">
					<a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                        <?php
                        if(!empty($user['user_pic'])){
                            ?>
                            <img height="25px" width="25px" alt="" class="pp img-circle" src="<?php echo $user['user_pic']; ?>"/>
                            <?php
                        } else {
                            ?>
                            <img alt="" class="pp img-circle" src="assets/admin/layout/img/default.png"/>
                            <?php
                        }
                        ?>

						<span class="username">
					    <?php echo $user['user_fname']; ?> </span>
						<i class="fa fa-angle-down"></i>
					</a>
					<ul class="dropdown-menu dropdown-menu-default">
						<li>
							<a class="load_page" data-href="assets/pages/profile.php?uuid=<?php echo $_SESSION['uuid']; ?>&luid=<?php echo $_GET['luid']; ?>" data-page-title="My Profile">
                                <i class="icon-user"></i> My Profile
                            </a>
						</li>
                        <?php
                        if($_SESSION['group'] == 1){
                            ?>
                            <li>
                                <a class="load_page" data-href="assets/pages/subscriptions.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="My Subscriptions">
                                    <i class="fa fa-dollar"></i> My Subscripions
                                </a>
                            </li>
                            <?php
                        }
                        if($_SESSION['group'] != 3){
                            ?>
                            <li>
                                <a class="load_page" data-href="assets/pages/my_reports.php?uuid=<?php echo $_SESSION['uuid']; ?>&luid=<?php ?>" data-page-title="My Reports">
                                    <i class="icon-docs"></i> My Reports
                                </a>
                            </li>
                            <?php
                        }
                        ?>
						<li>
							<a href="assets/app/logout.php">
								<i class="icon-key"></i> Log Out </a>
						</li>
					</ul>
				</li>
                <?php
                if($_SESSION['group'] <= 5.0 && $_SESSION['group'] != 3.0){
                    ?>
                    <li class="dropdown dropdown-quick-sidebar-toggler">
                        <a href="javascript:;" class="dropdown-toggle">
                            <i class="icon-call-in"></i>
                        </a>
                    </li>
                    <?php
                }
                ?>
			</ul>
		</div>
	</div>
</div>
<div class="clearfix">
</div>
<div class="page-container">
	<div class="page-sidebar-wrapper <?php if(empty($_GET['luid'])){echo "hide";} ?>">
		<div class="page-sidebar navbar-collapse collapse">
			<ul class="page-sidebar-menu" id="nav" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
				<li class="sidebar-toggler-wrapper">
					<div class="sidebar-toggler">
					</div>
				</li>
                <?php
                if($_SESSION['group'] == 1 || $_SESSION['group'] == 2 || $_SESSION['group'] == 4){
                    ?>
                    <li class="sidebar-search-wrapper">
                        <!-- BEGIN RESPONSIVE QUICK SEARCH FORM -->
                        <!-- DOC: Apply "sidebar-search-bordered" class the below search form to have bordered search box -->
                        <!-- DOC: Apply "sidebar-search-bordered sidebar-search-solid" class the below search form to have bordered & solid search box -->
                        <div class="sidebar-search">
                            <a href="javascript:;" class="remove">
                                <i class="icon-close"></i>
                            </a>
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Search globally..." id="search" style="color: #d9d9d9!important;">
                                <span class="input-group-btn">
                                    <a href="javascript:;" class="btn submit"><i class="icon-magnifier"></i></a>
                                </span>
                                <button class="hidden hidden-submit"></button>
                            </div>
                        </div>
                        <!-- END RESPONSIVE QUICK SEARCH FORM -->
                    </li>
                    <?php
                }
                ?>

                <?php
                if(!empty($_GET['luid'])){
                    if($user['user_group'] >= 1 && $user['user_group'] != 3 ){
                        if($_SESSION['group'] == 1 || strpos($user['user_esc_permissions'], "view_dashboard") !== false || $_SESSION['group'] == 3 || $_SESSION['group'] == 5.1){
                            ?>
                            <li class="start" style='margin-top: 7px'>
                                <a class="load_page nav-arr" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Dashboard">
                                    <i class="icon-home faa-pulse"></i>
                                    <span class="title">Dashboard</span>
                                    <span class="selected"></span>
                                    <span class="arrow "></span>
                                </a>
                            </li>
                            <?php
                        }
                        if($_SESSION['group'] == 1 || strpos($user['user_esc_permissions'], "view_timeclock") !== false){
                            ?>
                            <li class="">
                                <a class="load_page" data-href="assets/pages/time_clock.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Time Clock">
                                    <i class="icon-clock faa-pulse"></i>
                                    <span class="title">Time Clock</span>
                                    <span class="selected"></span>
                                    <span class="arrow "></span>
                                </a>
                            </li>
                            <?php
                        }
                        if($_SESSION['group'] == 1 || strpos($user['user_esc_permissions'], "view_customers") !== false){
                            ?>
                            <li class="">
                                <a class="load_page" data-href="assets/pages/customers.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Customers">
                                    <i class="icon-users faa-pulse"></i>
                                    <span class="title">Customers</span>
                                    <span class="selected"></span>
                                    <span class="arrow "></span>
                                </a>
                            </li>
                            <?php
                        }
                        if($_SESSION['group'] == 1 || strpos($user['user_esc_permissions'], "view_tickets") !== false){
                            ?>
                            <li class="">
                                <a class="load_page" data-href="assets/pages/tickets.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Support Tickets">
                                    <i class="icon-envelope-letter faa-pulse"></i>
                                    <span class="title">Support Tickets</span>
                                    <span class="selected"></span>
                                    <span class="arrow "></span>
                                </a>
                            </li>
                            <?php
                        }
                        if($_SESSION['group'] == 1 || strpos($user['user_esc_permissions'], "view_marketing") !== false){
                            ?>
                            <li class="">
                                <a class="load_page" data-href="assets/pages/marketing.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Marketing">
                                    <i class="icon-graph"></i>
                                    <span class="title">Marketing</span>
                                    <span class="selected"></span>
                                    <span class="arrow "></span>
                                </a>
                            </li>
                            <?php
                        }
                        if($_SESSION['group'] == 1 || strpos($user['user_esc_permissions'], "view_employees") !== false){
                            ?>
                            <li class="">
                                <a class="load_page" data-href="assets/pages/employees.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Employees">
                                    <i class="icon-earphones-alt"></i>
                                    <span class="title">Employees</span>
                                    <span class="selected"></span>
                                    <span class="arrow "></span>
                                </a>
                            </li>
                            <?php
                        }
                        if($_SESSION['group'] == 1 || strpos($user['user_esc_permissions'], "view_reports") !== false){
                            ?>
                            <li class="">
                                <a class="load_page" data-href="assets/pages/reports.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Reports">
                                    <i class="icon-layers"></i>
                                    <span class="title">Reports</span>
                                    <span class="selected"></span>
                                    <span class="arrow "></span>
                                </a>
                            </li>
                            <?php
                        }
                        if($_SESSION['group'] == 1 || strpos($user['user_esc_permissions'], "view_assets") !== false){
                            ?>
                            <li class="">
                                <a class="load_page" data-href="assets/pages/assets.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Assets">
                                    <i class="fa fa-truck"></i>
                                    <span class="title">Assets</span>
                                    <span class="selected"></span>
                                    <span class="arrow "></span>
                                </a>
                            </li>
                            <?php
                        }
                        if($_SESSION['group'] == 1 || strpos($user['user_esc_permissions'], "view_vendors") !== false){
                            ?>
                            <li class="">
                                <a class="load_page" data-href="assets/pages/vendors.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Vendors">
                                    <i class="icon-tag"></i>
                                    <span class="title">Vendors</span>
                                    <span class="selected"></span>
                                    <span class="arrow "></span>
                                </a>
                            </li>
                            <?php
                        }
                        if($_SESSION['group'] == 1 || strpos($user['user_esc_permissions'], "view_storage") !== false){
                            ?>
                            <li class="">
                                <a class="load_page" data-href="assets/pages/storage.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Storage">
                                    <i class="fa fa-cubes"></i>
                                    <span class="title">Storage</span>
                                    <span class="selected"></span>
                                    <span class="arrow "></span>
                                </a>
                            </li>
                            <?php
                        }
                        if($_SESSION['group'] == 1 || strpos($user['user_esc_permissions'], "view_library") !== false){
                            ?>
                            <li class="">
                                <a class="load_page" data-href="assets/pages/resource.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Resource Library">
                                    <i class="icon-folder"></i>
                                    <span class="title">Resource Library</span>
                                    <span class="selected"></span>
                                    <span class="arrow "></span>
                                </a>
                            </li>
                            <?php
                        }
                        if($_SESSION['group'] == 1 || strpos($user['user_esc_permissions'], "view_locationsettings") !== false){
                            ?>
                            <li class="">
                                <a class="load_page" data-href="assets/pages/manage_location.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Location Settings">
                                    <i class="icon-settings"></i>
                                    <span class="title">Location Settings</span>
                                    <span class="selected"></span>
                                    <span class="arrow "></span>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                        <?php
                    } elseif($user['user_group'] == 3) {
                        ?>
                        <li class="">
                            <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Dashboard">
                                <i class="icon-home"></i>
                                <span class="title">Dashboard</span>
                                <span class="selected"></span>
                                <span class="arrow "></span>
                            </a>
                        </li>
                        <?php
                    } else {
                        ?>
                            <center><h5 style="color: white;">You do not have <br/> permission to navigate.</h5></center>
                        <?php
                    }
                } else {
                    ?>
                    <center><h5 style="color: white;">You need to create your first location. <?php echo $user['user_last_ext_location']; ?></h5></center>
                    <?php
                }
                ?>
			</ul>
		</div>
	</div>
	<div class="page-content-wrapper" id="page_content">
        <div class="page-content">

        </div>
	</div>
    <form method="POST" action="" role="form" id="new_tickets">
        <div class="modal fade bs-modal-lg" id="add_ticket" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content box red">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h3 class="modal-title font-bold">Add new ticket</h3>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label class="control-label">Select Department <span class="font-red">*</span></label>
                                <div class="input-icon">
                                    <i class="fa fa-tag"></i>
                                    <select class="form-control" name="department" id="department">
                                        <option disabled selected value="">Select department...</option>
                                        <option value="Accounts">Accounts</option>
                                        <option value="Services">Services</option>
                                        <option value="Claims">Claims</option>
                                        <option value="Billing">Billing</option>
                                        <option value="Software Issues">Software Issues</option>
                                        <option value="Misc/Information">Misc/Information</option>
                                    </select>
                                    <span class="help-block">Department.</span>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label class="control-label">Select Priority <span class="font-red">*</span></label>
                                <div class="input-icon">
                                    <i class="fa fa-tag"></i>
                                    <select class="form-control" name="priority" id="priority">
                                        <option disabled selected value="">Select priority...</option>
                                        <option value="High">High</option>
                                        <option value="Medium">Medium</option>
                                        <option value="Low">Low</option>
                                        <option value="Non-issue/Lowest">Non-issue/Lowest</option>
                                    </select>
                                    <span class="help-block">Priority.</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="control-label">Message <span class="font-red">*</span></label>
                                <textarea class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Detailed message goes here.." name="message" style="height: 200px;"></textarea>
                                <span class="help-block">This will be the first message on the support ticket. Please make it detailed & explain well.</span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-12">
                                <label class="control-label">Select attachment</label>
                                <div class="input-icon">
                                    <i class="fa fa-tag"></i>
                                    <input class="form-control placeholder-no-fix" type="file" name="file" autocomplete="off"/>
                                    <span class="help-block">Attachment.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn red pull-right submitter">Save ticket</button>
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <?php
    if($_SESSION['group'] <= 5.0 && $_SESSION['group'] != 3){
        ?>
        <a href="javascript:;" class="page-quick-sidebar-toggler"><i class="icon-call-in"></i></a>
        <div class="page-quick-sidebar-wrapper" style="overflow-y: scroll;">
            <div class="page-quick-sidebar">
                <div class="row" style="padding: 15px;">
                    <div class="col-md-12">
                        <form id="catcher" role="form">
                            <div class="form-body">
                                <div class="form-group " style="margin-bottom: 7px;">
                                    <label>First, provide phone #:</label>
                                    <div class="input-icon">
                                        <i class="fa fa-phone"></i>
                                        <input type="text" min="1" max="10" class="form-control" placeholder="enter phone number.." id="catcher_phone" name="phone">
                                        <span class="help-block text-danger" id="response_1"></span>
                                    </div>
                                </div>
                                <div class="form-group catcher-items-hide" id="nameinput" style="display: none;">
                                    <label>Now, we need their name</label>
                                    <div class="input-icon">
                                        <i class="fa fa-user"></i>
                                        <input type="text" class="form-control" placeholder="enter persons name.." id="catcher_name" name="name">
                                        <span class="help-block text-danger" id="response_2"></span>
                                    </div>
                                </div>
                                <div class="form-group catcher-items-hide" id="zipcodeinput" style="display: none;">
                                    <label>Next, enter starting zipcode</label>
                                    <div class="input-icon">
                                        <i class="fa fa-crop"></i>
                                        <input type="text" min="3" max="5" class="form-control" placeholder="enter zipcode.." id="catcher_zipcode" name="catcher_zipcode">
                                    </div>
                                </div>
                                <div class="form-group catcher-items-hide" id="locationinput" style="display: none">
                                    <label>Select dispatch location:</label>
                                    <div class="input-icon">
                                        <i class="fa fa-compass"></i>
                                        <select class="form-control" name="location" id="locationinputtt">
                                            <option disabled selected value="">Select location...</option>
                                            <?php
                                            $findLocations = mysql_query("SELECT location_name, location_token, location_state FROM fmo_locations WHERE location_owner_company_token='".mysql_real_escape_string($_SESSION['cuid'])."' ORDER BY location_name ASC");
                                            if(mysql_num_rows($findLocations) > 0){
                                                while($loc = mysql_fetch_assoc($findLocations)){
                                                    ?>
                                                    <option id="<?php echo $loc['location_token']; ?>" value="<?php echo $loc['location_token']; ?>"> <?php echo $loc['location_name']; ?> (<?php echo $loc['location_state']; ?>) </option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                        <span class="help-block">I've auto-selected the city based on the zip code you've given me.</span>
                                    </div>
                                </div>
                                <div class="form-group catcher-items-hide" id="calenderinput" style="display: none; width: 100%;">
                                    <label>Which day do they prefer?</label>
                                    <div class="input-group col-md-12">
                                        <div class="date-picker" style="background-color: white; margin: auto;"></div>
                                        <input class="hide" name="date" id="date">
                                    </div>
                                </div>
                                <div class="form-group catcher-items-hide" id="jobtypeinput" style="display: none">
                                    <label>Now, select the job type..</label>
                                    <div class="input-icon">
                                        <i class="fa fa-tags"></i>
                                        <select class="form-control" name="type" id="catcher_jobtype">
                                            <option disabled selected value="">Select one..</option>
                                            <option value="Local">Local</option>
                                            <option value="Out Of State">Out Of State</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group catcher-items-hide" id="fees" style="display: none">
                                    <label># of trucks needed</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control doMath" name="truckfee" value="1" id="catcher_truckfee" data-a="#catcher_truckfee" data-b="#catcher_laborrate" data-c="#catcher_countyfee">
                                        <span class="input-group-btn">
                                        <button class="btn red" type="button" id="TR" name="t_r" value="">$<span></span></button>
                                    </span>
                                    </div>
                                    <br/>
                                    <label># of crewmen needed</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control doMath" name="laborrate" value="2" id="catcher_laborrate" data-a="#catcher_truckfee" data-b="#catcher_laborrate" data-c="#catcher_countyfee">
                                        <span class="input-group-btn">
                                        <button class="btn red" type="button" id="LR" name="l_r" value="">$<span></span></button>
                                    </span>
                                    </div>
                                    <br/>
                                    <label># of counties</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control doMath" name="countyfee" value="0" id="catcher_countyfee" data-a="#catcher_truckfee" data-b="#catcher_laborrate" data-c="#catcher_countyfee">
                                        <div class="input-group-btn open">
                                            <button class="btn red" type="button" id="CR" name="c_r" value="">$<span></span></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group catcher-items-hide" id="osb_fees" style="display: none">
                                    <label>Next, enter ending zipcode</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control osb" name="osb_endingzip" id="catcher_osb_endingzip" data-a="#catcher_osb_endingzip" data-b="#catcher_osb_truckfee" data-c="#catcher_zipcode" data-d="#catcher_osb_labor">
                                        <span class="input-group-btn">
                                            <button class="btn red" type="button" id="total_miles">..</button>
                                        </span>
                                    </div>
                                    <span class="help-block" id="ending_name">Enter zip to see quote</span>
                                    <label># of trucks needed</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control osb" name="truckfee" value="1" id="catcher_osb_truckfee" data-a="#catcher_osb_endingzip" data-b="#catcher_osb_truckfee" data-c="#catcher_zipcode" data-d="#catcher_osb_labor">
                                        <span class="input-group-btn">
                                            <button class="btn red" type="button" id="osb_truckfee">..</button>
                                         </span>
                                    </div>
                                    <br/>
                                    <label># of crewmen needed</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control osb" name="laborrate" value="2" id="catcher_osb_labor" data-a="#catcher_osb_endingzip" data-b="#catcher_osb_truckfee" data-c="#catcher_zipcode" data-d="#catcher_osb_labor">
                                        <span class="input-group-btn">
                                            <button class="btn red" type="button" id="osb_labor">..</button>
                                         </span>
                                    </div>
                                    <h2 class="text-center" style="color: white!important;">Rough Bid:<br/><span id="osb_bid"></span></h2>
                                </div>
                                <div class="form-group catcher-items-hide" id="email" style="display: none;">
                                    <label>Customer's email</label>
                                    <div class="input-icon">
                                        <i class="fa fa-envelope"></i>
                                        <input type="email" class="form-control" placeholder="enter customers email.." id="catcher_email" name="email">
                                    </div>
                                </div>
                                <div class="form-group catcher-items-hide" id="other_options" style="display: none;">
                                    <label>
                                        Other options for the customer
                                    </label>
                                    <br/>
                                    <?php
                                    if(strpos($quote, "view_quote_oversized_hottub") !== false){
                                        ?>
                                        <label class="btn btn-block checking">
                                            <img src="assets/global/img/catcher/hottub.gif" alt="..." class="img-thumbnail img-che che" style="vertical-align: top;">
                                            <label style="padding-top: 5px;">Hot Tub <br/>$398<br/>$350 w/ move <br/> <small>click image <br/>to add</small> </label>
                                            <input type="checkbox" name="addition[]" id="hot_tub" value="hot_tub" class="hidden" autocomplete="off">
                                        </label><br/>
                                        <?php
                                    }if(strpos($quote, "view_quote_oversized_piano") !== false){
                                        ?>
                                        <label class="btn btn-block checking">
                                            <img src="assets/global/img/catcher/babygrand.gif" alt="..." class="img-thumbnail img-che che" style="vertical-align: top;">
                                            <label style="padding-top: 5px;">Piano <br/>$398<br/>$350 w/ move <br/> <small>click image <br/>to add</small> </label>
                                            <input type="checkbox" name="addition[]" id="piano" value="piano" class="hidden" autocomplete="off">
                                        </label>
                                        <br/>
                                        <?php
                                    }if(strpos($quote, "view_quote_oversized_pooltable") !== false){
                                        ?>
                                        <label class="btn btn-block checking">
                                            <img src="assets/global/img/catcher/pooltable.gif" alt="..." class="img-thumbnail img-che che" style="vertical-align: top;">
                                            <label style="padding-top: 5px;">Pool Table <br/>$398<br/>$350 w/ move <br/> <small>click image <br/>to add</small> </label>
                                            <input type="checkbox" name="addition[]" id="pool_table" value="pool_table" class="hidden" autocomplete="off">
                                        </label>
                                        <br/>
                                        <?php
                                    }if(strpos($quote, "view_quote_oversized_playset") !== false){
                                        ?>
                                        <label class="btn btn-block checking">
                                            <img src="assets/global/img/catcher/playset.gif" alt="..." class="img-thumbnail img-che che" style="vertical-align: top;">
                                            <label style="padding-top: 5px;">Play Set <br/>$378<br/>$300 w/ move <br/> <small>click image <br/>to add</small> </label>
                                            <input type="checkbox" name="addition[]" id="play_set" value="play_set" class="hidden" autocomplete="off">
                                        </label>
                                        <br/>
                                        <?php
                                    }if(strpos($quote, "view_quote_oversized_safe") !== false){
                                        ?>
                                        <label class="btn btn-block checking">
                                            <img src="assets/global/img/catcher/safe.gif" alt="..." class="img-thumbnail img-che che" style="vertical-align: top;">
                                            <label style="padding-top: 5px;">Safe <br/>$298<br/>$200 w/ move <br/> <small>click image <br/>to add</small> </label>
                                            <input type="checkbox" name="addition[]" id="safe" value="safe" class="hidden" autocomplete="off">
                                        </label>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="pricing catcher-items-hide hover-effect" id="storage" style="display: none !important; border: none;">
                                    <div class="pricing-head" style="background-color: #cb5a5e;">
                                        <h3 style="background-color: #cb5a5e; border-bottom: 1px solid white;">Storage Units
                                            <span>
                                    Available for use
                                    </span>
                                        </h3>
                                    </div>
                                    <ul class="pricing-content list-unstyled text-center">
                                        <li>
                                            <i class="fa fa-cubes font-red"></i> 5x5 - $<strong>37.00</strong>/month
                                        </li>
                                        <li>
                                            <i class="fa fa-cubes font-red"></i> 5x10 - $<strong>45.00</strong>/month
                                        </li>
                                        <li>
                                            <i class="fa fa-cubes font-red"></i> 10x10 - $<strong>69.00</strong>/month
                                        </li>
                                        <li>
                                            <i class="fa fa-cubes font-red"></i> 10x15 - $<strong>85.00</strong>/month
                                        </li>
                                        <li>
                                            <i class="fa fa-cubes font-red"></i> 10x20 - $<strong>120.00</strong>/month
                                        </li>
                                    </ul>
                                    <div class="pricing-footer p-b-none" style="padding-bottom: 0;">
                                        <p>
                                            Prices at different locations may vary. See your manager for details.
                                        </p>
                                    </div>
                                </div>
                                <div class="form-group catcher-items-hide" id="referer" style="display: none;">
                                    <label>Who refererred the customer?</label>
                                    <div class="input-icon">
                                        <i class="fa fa-users"></i>
                                        <select class="form-control" name="referer" id="catcher_referer">
                                            <option disabled selected value="">Select one..</option>
                                            <?php
                                            $findRefs = mysql_query("SELECT howhear_name FROM fmo_locations_howhears WHERE howhear_location_token='".$_GET['luid']."' ORDER BY howhear_name ASC");
                                            if(mysql_num_rows($findRefs) > 0){
                                                while($ref = mysql_fetch_assoc($findRefs)){
                                                    ?>
                                                    <option value="<?php echo $ref['howhear_name']; ?>"> <?php echo $ref['howhear_name']; ?> </option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group catcher-items-hide" id="comments" style="display: none;">
                                    <label>Comments about this call..</label>
                                    <div class="input-icon">
                                        <i class="fa fa-comments"></i>
                                        <input type="text" class="form-control" placeholder="enter comments.." id="catcher_comments" name="comments">
                                    </div>
                                </div>
                                <div id="submit" class="form-group catcher-items-hide" style="display: none; padding-top: 15px;">
                                    <a href="javascript:;" class="btn default red-stripe pull-left create_event">Create Event </a>
                                    <a href="javascript:;" class="btn default blue-stripe pull-right hot_lead">Hot Lead </a>
                                </div>
                                <br/>
                                <hr style="background-color: grey;"/>
                            </div>
                        </form>
                        <h4>What is the call catcher?</h4>
                        <p class="help-block">The call catcher is for exactly what it says--catching calls. Use this simple tool to easily book a customers move with a <strong>5 minute call</strong>.</p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>

</div>
<div class="page-footer text-center">
	<div class="page-footer-inner" style="float: none!important">
        <strong>For Movers Only&trade;</strong> - Moving Management Software | &copy; 2016-2017 <a target="_blank" href="//www.captialkingdom.com">CK, Inc.</a> | <a data-toggle="modal" href="#add_ticket">Open a support ticket</a> for help from staff. | <span id="countdown_footer"></span>
	</div>
	<div class="scroll-to-top">
		<i class="icon-arrow-up"></i>
	</div>
</div>
<!--[if lt IE 9]>
<script src="assets/global/plugins/respond.min.js"></script>
<script src="assets/global/plugins/excanvas.min.js"></script>
<![endif]-->

<script src="assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/moment.min.js"></script>
<script src="assets/global/plugins/fullcalendar/fullcalendar.min.js"></script>
<script src="assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/rateYo/2.3.2/jquery.rateyo.min.js"></script>
<script src="assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery-idle-timeout/jquery.idletimeout.js" type="text/javascript"></script>
<script src="assets/global/plugins/jquery-idle-timeout/jquery.idletimer.js" type="text/javascript"></script>
<script src="assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js" type="text/javascript"></script>
<script src="assets/global/plugins/bootstrap-toastr/toastr.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/clockface/js/clockface.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-colorpicker/js/bootstrap-colorpicker.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/jquery-validation/js/additional-methods.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-wizard/jquery.bootstrap.wizard.min.js"></script>
<script src="assets/admin/pages/scripts/ui-toastr.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/media/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js"></script>
<script src="assets/global/plugins/jquery.pulsate.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/flot/jquery.flot.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/flot/jquery.flot.pie.min.js"></script>
<script src="assets/global/plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
<script type="text/javascript" src="assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/jquery.input-ip-address-control-1.0.min.js"></script>
<script src="assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
<script type="text/javascript" src="assets/global/plugins/select2/select2.min.js"></script>
<script src="assets/global/plugins/icheck/icheck.min.js"></script>
<script src="assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js" type="text/javascript"></script>
<script type="text/javascript" src="assets/global/plugins/jquery-mixitup/jquery.mixitup.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
<script type="text/javascript" src="assets/global/plugins/printThis/printThis.js"></script>
<script async defer src="https://maps.google.com/maps/api/js?v=3.exp&key=AIzaSyBg2MfengOuhtRA-39qVbm8vA7n7pf5ES8&sensor=false" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.9.1/sweetalert2.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/gmaps/gmaps.min.js" type="text/javascript"></script>
<script src="assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="assets/admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
<script src="assets/admin/pages/scripts/ui-idletimeout.js"></script>
<script src="assets/global/plugins/xeditable/bootstrap3-editable/js/bootstrap-editable.js"></script>
<script src="assets/admin/pages/scripts/form-validation.js"></script>
<script src="assets/admin/pages/scripts/index.js" type="text/javascript"></script>
<script src="assets/global/scripts/datatable.js"></script>
<script src="https://checkout.stripe.com/checkout.js"></script>
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-wysihtml5/wysihtml5-0.3.0.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.js"></script>
<script type="text/javascript" src="assets/global/plugins/bootstrap-editable/inputs-ext/wysihtml5/wysihtml5.js"></script>

<script>
    $(document).ready(function() {
        $(document).ajaxStart(function() { Pace.restart(); });
        Metronic.init();
        Layout.init();
        QuickSidebar.init();
        UIToastr.init();
        UIIdleTimeout.init();
        FormValidation.init();
        Index.init();

        <?php
        if(isset($_GET['x'])) {
            ?>
            $.ajax({
                url: 'assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>',
                success: function(data) {
                    $('#page_content').html(data);
                },
                error: function() {
                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                }
            });
            <?php
        } elseif(empty($_GET['luid']) && $_SESSION['group'] == 1){
            ?>
            $.ajax({
                url: 'assets/pages/new.php',
                success: function(data) {
                    $('#page_content').html(data);
                },
                error: function() {
                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                }
            });
            <?php
        } elseif(isset($_GET['navigate']) && $_GET['navigate'] == 'TxN'){
            ?>
            $.ajax({
                url: 'assets/pages/<?php echo $_GET['pop']; ?>',
                success: function(data) {
                    $('#page_content').html(data);
                    document.title = "<?php urldecode($_GET['tt']); ?> - For Movers Only";
                },
                error: function() {
                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                }
            });
            <?php
        } else {
            ?>
            $.ajax({
                url: 'assets/pages/<?php $url = explode('?', $user['user_last_location']); echo $url[0]; ?>?luid=<?php echo $_GET['luid']; if($url[0] == 'profile.php'){echo "&".$url[1];};?><?php if($url[0] == 'event.php'){echo "&".$url[1];};?><?php if($url[0] == 'tickets.php'){ $parts2 = explode("=", $url[1]); if($parts2[0] != 'luid') { echo "&".$url[1]; } };?>',
                success: function(data) {
                    $('#page_content').html(data);
                },
                error: function() {
                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                }
            });
            <?php
        }
        ?>
        $(document).on('click', '.popout', function(){
            var pop = $(this).data('pop');
            var tt  = encodeURI($(this).data('page-title'));
            window.open("//www.formoversonly.com/dashboard/index.php?uuid=<?php echo $_SESSION['uuid']; ?>&cuid=<?php echo $_SESSION['cuid']; ?>&luid=<?php echo $_GET['luid']; ?>&navigate=TxN&tt="+tt+"&pop="+pop);
        });
        $(document).on('click', '.tab_print', function(t){
            var p = $(this).data('print');
            $('.print').attr('data-print', p);
        });
        $(document).on('click', '.print', function(p){
            var id = $(this).data('print');
            $(id).printThis();
        });
        $(document).on('click', '.change_location', function(){
            var luid = $(this).attr('data-new-location');
            window.location.replace("//www.formoversonly.com/dashboard/index.php?luid="+luid);
        });
        $(document).on('click', '.edit_inf', function(f) {
            var inf = $(this).attr('data-edit');
            f.stopPropagation();
            $('#'+inf).editable('toggle');
        });
        $(document).on('click', '.edit_line', function(){
            var line = $(this).val();
            $('.'+line).editable({
                step: 'any',
                inputclass: 'form-control'
            });
            $('.'+line+'_percentage').editable({
                source: [
                    {value: 1, text: 'Yes'},
                    {value: 0, text: 'No'}
                ]
            });
            $('.'+line+'_taxable').editable({
                source: [
                    {value: 1, text: 'Yes'},
                    {value: 0, text: 'No'}
                ]
            });
            $('.'+line+'_commissionable').editable({
                source: [
                    {value: 1, text: 'Yes'},
                    {value: 0, text: 'No'}
                ]
            });
            $('.'+line+'_redeemable').editable({
                source: [
                    {value: 1, text: 'Yes'},
                    {value: 0, text: 'No'}
                ]
            });
            $('.'+line+'_type').editable({
                source: [
                    {value: 'Supplies', text: 'Supplies'},
                    {value: 'Labor', text: 'Labor'},
                    {value: 'Discount', text: 'Discount'},
                    {value: 'Extras', text: 'Extras'},
                    {value: 'Storage', text: 'Storage'},
                    {value: 'Other', text: 'Other'}
                ]
            });
            toastr.info("<strong>Logan says</strong>:<br/>You can now edit that line. To edit, please click the blue underline under the value you'd like to update.")
        });
        $(document).on('click', '.load_page', function(){
            var act = $(this).attr('data-act');
            if(act == 'breadcrumb'){
                $(".active").removeClass("active");
                $('.nav-arr').addClass("active");
            }else{
                $(".active").removeClass("active");
                $(this).parent().addClass("active");
            }
            var url = $(this).attr('data-href');
            var tit = $(this).attr('data-page-title');

            Pace.track(function(){
                $.ajax({
                    url: url,
                    success: function(data) {
                        $('#page_content').html(data);
                        document.title = tit+" - For Movers Only";
                    },
                    error: function() {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                    }
                });
            });
        });
        $(document).on('click', '.sub_pl', function(){
            var ref = $(this).attr('data-href');
            var tit = $(this).attr('data-page-title');
            var ext = $(this).attr('data-ext');
            $('#sub_content').html('<center style="font-size:30px!important; margin-top: 100px;"><i class="fa fa-spinner fa-pulse fa-3x fa-fw margin-bottom" style="font-size:50px!important;"></i> <br/>Loading report..</center>')
            Pace.track(function(){
                $.ajax({
                    url: ref,
                    type: 'POST',
                    data: {
                        ext: ext
                    },
                    success: function(data) {
                        $('#sub_content').html(data);
                        document.title = tit+" - For Movers Only";
                    },
                    error: function() {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                    }
                });
            });
        });

        $(document).on('click', '.load_profile_tab', function(){
            var act = $(this).attr('data-act');
            var url = $(this).attr('data-href');
            var tit = $(this).attr('data-page-title');
            $(".active").removeClass("active");
            $(this).parent().addClass("active");
            Pace.track(function(){
                $.ajax({
                    url: url,
                    success: function(data) {
                        $('#profile-content').html(data);

                    },
                    error: function() {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                    }
                });
            });
        });
        $(document).on('click', '.load_reports_pull', function(){
            var typ = $(this).attr('data-type');
            var url = $(this).attr('data-href');
            var tit = $(this).attr('data-page-title');
            var ext = $(this).attr('data-ext');
            Pace.track(function(){
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        type: typ,
                        ext: ext
                    },
                    success: function(data) {
                        $('#reports-content').html(data);
                        document.title = tit;
                    },
                    error: function() {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                    }
                });
            });
        });

        <?php
        if($exp['user_license_exp'] == '0000-00-00 00:00:00'){
            $license = date('Y-m-d G:i:s', strtotime('today -1 days'));
        } else {
            $license = $exp['user_license_exp'];
        }
        ?>
        var countDownDate = new Date("<?php echo date('M d, Y G:i:s', strtotime($license));  ?>").getTime();
        var x = setInterval(function() {
            var now = new Date().getTime();
            var distance = countDownDate - now;
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);
            document.getElementById("countdown_footer").innerHTML = "<strong class='text-info'>" + days + "d</strong>, <strong class='text-info'>" + hours + "h</strong>, <strong class='text-info'>" + minutes + "m</strong> & <strong class='text-info'>" + seconds + "s</strong> remaining on company software license.";
            if (distance < 0) {
                clearInterval(x);
                document.getElementById("countdown_footer").innerHTML = "Your companies software license is expired. <strong class='font-red'>No new events can be booked!</strong>";
            }
        }, 1000);

        $(document).on('click', '.load_payments', function(){
            var act = $(this).attr('data-type');
            var url = $(this).attr('data-href');
            var tit = $(this).attr('data-page-title');
            $(this).html("Taking payment.. <i class='fa fa-money'></i>");
            $(this).removeClass("green");
            $(this).addClass("red");
            Pace.track(function(){
                $.ajax({
                    url: url,
                    type: 'POST',
                    data: {
                        type: act
                    },
                    success: function(data) {
                        $('#payments-content').html(data);
                        $('#payments-maked').hide();
                        document.title = tit;
                    },
                    error: function() {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                    }
                });
            });
        });
        $(document).on('click', '.update_settings', function(){
            var url = $(this).attr("data-form");
            var id  = $(this).attr("data-id");
            var reload = $(this).attr("data-reload");
            Pace.track(function(){
                $.ajax({
                    type: "POST",
                    url: url,
                    data: $(id).serialize(),
                    success: function(data) {
                        toastr.success("<strong>Logan says</strong>:<br/>Your changes have been saved to the database.");
                        if($(this).hasAttribute("data-no-reload")){

                        } else {
                            $.ajax({
                                url: reload,
                                success: function(data) {
                                    $('#page_content').html(data);
                                },
                                error: function() {
                                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                }
                            });
                        }
                    },
                    error: function() {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                    }
                });
            });
        });

        function updateInv(event, luid){
            $.ajax({
                url: 'assets/app/api/event.php?type=inv&luid='+luid,
                type: 'POST',
                data: {
                    event: '' + event + ''
                },
                success: function(m){
                    var owe = JSON.parse(m);
                    $(document).find('#owe_sub_total').html(parseFloat(owe.sub_total).toFixed(2));
                    $(document).find('#owe_tax').html(parseFloat(owe.tax).toFixed(2));
                    $(document).find('#owe_total').html(parseFloat(owe.total).toFixed(2));
                    $(document).find('#PLPAP_SUBTOTAL').html(parseFloat(owe.sub_total).toFixed(2));
                    $(document).find('#PLPAP_TAXES').html(parseFloat(owe.tax).toFixed(2));
                    $(document).find('#PLPAP_TOTAL').html(parseFloat(owe.total).toFixed(2));
                    $(document).find('#owe_total_unpaid').html(parseFloat(owe.unpaid).toFixed(2));
                    $(document).find('#owe_paid').html(parseFloat(owe.paid).toFixed(2));
                    if(parseFloat(owe.unpaid).toFixed(2) > 0){
                        $(document).find('#owe_alert').show();
                        $(document).find('#owe_alert').html("<i class='fa fa-exclamation-triangle'></i> UNPAID - $" + parseFloat(owe.unpaid).toFixed(2));
                    } else {
                        $(document).find('#owe_alert').hide();
                        $(document).find('#owe_alert').html("");
                    }
                    if(parseFloat(owe.cc_fees).toFixed(2) > 0){
                        $(document).find("#cc_fees").show();
                        $(document).find("#owe_cc_fees").html(parseFloat(owe.cc_fees).toFixed(2));
                        $(document).find(".load_payments").removeClass("margin-top-15");
                    } else {
                        $(document).find("#cc_fees").hide();
                        $(document).find("#owe_cc_fees").html("");
                        $(document).find(".load_payments").addClass("margin-top-15");
                    }
                    if(parseFloat(owe.taxable).toFixed(2) > 0){
                        $(document).find("#taxable_fees").show();
                        $(document).find("#commie_fees").show();
                        $(document).find("#taxable_fees").html("($"+ parseFloat(owe.taxable).toFixed(2) +" taxable)");
                        $(document).find("#commie_fees").html("($"+ parseFloat(owe.coms).toFixed(2) +" commissionable)");
                    } else {
                        $(document).find("#taxable_fees").hide();
                        $(document).find("#commie_fees").hide();
                    }
                },
                error: function(e){

                }
            });
        }

        function updateContractInv(ct, luid){
            var adj = $('input[name="rate_adj"]').val();
            var dpt = $('input[name="deposit"]').val();
            var real = $('.fake-news').text();
            var number0 = parseFloat(real).toFixed(2);
            var number2 = parseFloat(adj).toFixed(2);
            $('.fake-cnn').html(parseFloat(+number0 + +number2).toFixed(2));
            $.ajax({
                url: 'assets/app/api/storage.php?type=inv&luid=<?php echo $_GET['luid']; ?>',
                type: 'POST',
                data: {
                    contract: '' + ct + '',
                    mr: parseFloat(+number0 + +number2).toFixed(2),
                    date1: $('.d1').val(),
                    date2: $('.d2').val(),
                    deposit: dpt
                },
                success: function(m){
                    var owe = JSON.parse(m);
                    $(document).find('#owe_sub_total').html(parseFloat(owe.sub_total).toFixed(2));
                    $(document).find('#owe_tax').html(parseFloat(owe.tax).toFixed(2));
                    $(document).find('#owe_total').html(parseFloat(owe.total).toFixed(2));
                    $(document).find('#PLPAP_SUBTOTAL').html(parseFloat(owe.sub_total).toFixed(2));
                    $(document).find('#PLPAP_TAXES').html(parseFloat(owe.tax).toFixed(2));
                    $(document).find('#PLPAP_TOTAL').html(parseFloat(owe.total).toFixed(2));
                    $(document).find('#owe_total_unpaid').html(parseFloat(owe.unpaid).toFixed(2));
                    $(document).find('#owe_paid').html(parseFloat(owe.paid).toFixed(2));
                    $(document).find('#owe_rent').html(parseFloat(owe.total).toFixed(2));
                    $(document).find('.amt').val(parseFloat(owe.total).toFixed(2));
                    $(document).find('#amt_pay').trigger('change');
                    if(parseFloat(owe.cc_fees).toFixed(2) > 0){
                        $(document).find("#cc_fees").show();
                        $(document).find("#owe_cc_fees").html(parseFloat(owe.cc_fees).toFixed(2));
                        $(document).find(".load_payments").removeClass("margin-top-15");
                    } else {
                        $(document).find("#cc_fees").hide();
                        $(document).find("#owe_cc_fees").html("");
                        $(document).find(".load_payments").addClass("margin-top-15");
                    }
                    if(parseFloat(owe.taxable).toFixed(2) > 0){
                        $(document).find("#taxable_fees").show();
                        $(document).find("#taxable_fees").html("($"+ parseFloat(owe.taxable).toFixed(2) +" taxable)");
                    } else {
                        $(document).find("#taxable_fees").hide();
                    }
                },
                error: function(e){

                }
            });
        }

        function updateRetailInv(ct, luid){
            $.ajax({
                url: 'assets/app/api/storage.php?type=inv&luid=<?php echo $_GET['luid']; ?>&no_calc=true&rt=true',
                type: 'POST',
                data: {
                    ct: '' + ct + ''
                },
                success: function(m){
                    var owe = JSON.parse(m);
                    if(owe.unpaid < 0){
                        var due     = "Credit";
                        var unpaid  = owe.unpaid * -1;
                    } else {var due = "Due"; var unpaid = owe.unpaid; }
                    $(document).find('#owe_sub_total').html(parseFloat(owe.sub_total).toFixed(2));
                    $(document).find('#owe_tax').html(parseFloat(owe.tax).toFixed(2));
                    $(document).find('#owe_total').html(parseFloat(owe.total).toFixed(2));
                    $(document).find('#owe_total_unpaid').html(due + " $" +parseFloat(unpaid).toFixed(2));
                    if(parseFloat(owe.taxable).toFixed(2) > 0){
                        $(document).find("#taxable_fees").show();
                        $(document).find("#taxable_fees").html("($"+ parseFloat(owe.taxable).toFixed(2) +" taxable)");
                    } else {
                        $(document).find("#taxable_fees").hide();
                    }
                },
                error: function(e){

                }
            });
        }

        $(document).on('click', '.delete_labor',  function() {
            var del    = $(this).attr('data-delete');
            var that   = $(this);
            swal({
                title: 'Are you sure?',
                text: 'You will not be able to recover this labor record!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then(function() {


                $(that).closest('tr').remove();
                $.ajax({
                    url: 'assets/app/update_settings.php?setting=delete_labor',
                    type: 'POST',
                    data: {
                        del: del
                    },
                    success: function(s){
                        $('#laborers').DataTable().ajax.reload();
                    },
                    error: function(e){
                    }
                });

                swal(
                    'Deleted!',
                    'The labor record has been deleted.',
                    'success'
                );

            }, function(dismiss) {
                // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        'Your labor record are safe :)',
                        'error'
                    )
                }
            });
        });

        $(document).on('click', '.delete_item',  function() {
            var del    = $(this).attr('data-delete');
            var event  = $(this).attr('data-event');
            var luid   = $(this).attr('data-luid');
            var that   = $(this);
            swal({
                title: 'Are you sure?',
                text: 'You will not be able to recover this item!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then(function() {


                $(that).closest('tr').remove();
                $.ajax({
                    url: 'assets/app/update_settings.php?setting=delete_item',
                    type: 'POST',
                    data: {
                        del: del,
                        ev: event
                    },
                    success: function(s){
                        updateInv(event, luid);
                    },
                    error: function(e){
                        updateInv(event, luid);
                    }
                });

                swal(
                    'Deleted!',
                    'The item has been deleted.',
                    'success'
                );

            }, function(dismiss) {
                // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        'Your item is safe :)',
                        'error'
                    )
                }
            });

        });
        $(document).on('click', '.delete_item_str',  function() {
            var del    = $(this).attr('data-delete');
            var event  = $(this).attr('data-event');
            var luid   = $(this).attr('data-luid');
            var that   = $(this);
            swal({
                title: 'Are you sure?',
                text: 'You will not be able to recover this item!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then(function() {


                $(that).closest('tr').remove();
                $.ajax({
                    url: 'assets/app/update_settings.php?setting=delete_item_str',
                    type: 'POST',
                    data: {
                        del: del,
                        ev: event
                    },
                    success: function(s){
                        updateRetailInv(event, luid);
                    },
                    error: function(e){
                        updateRetailInv(event, luid);
                    }
                });

                swal(
                    'Deleted!',
                    'The item has been deleted.',
                    'success'
                );

            }, function(dismiss) {
                // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        'Your item is safe :)',
                        'error'
                    )
                }
            });

        });

        $(document).on('click', '.delete_payment',  function() {
            var del    = $(this).attr('data-void');
            var event  = $(this).attr('data-event');
            var luid   = $(this).attr('data-luid');
            swal({
                title: 'Are you sure?',
                text: 'You will not be able to recover this payment!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then(function() {


                $.ajax({
                    url: 'assets/app/update_settings.php?setting=delete_payment',
                    type: 'POST',
                    data: {
                        del: del,
                        ev: event
                    },
                    success: function(s){
                        updateInv(event, luid);
                        $('#paid').DataTable().ajax.reload();
                    },
                    error: function(e){
                        updateInv(event, luid);
                    }
                });

                swal(
                    'Deleted!',
                    'The payment has been deleted.',
                    'success'
                );

            }, function(dismiss) {
                // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        'Your payment is safe :)',
                        'error'
                    )
                }
            });


        });

        $(document).on('click', '.refund_payment',  function() {
            var del    = $(this).attr('data-refund');
            var event  = $(this).attr('data-event');
            var luid   = $(this).attr('data-luid');
            swal({
                title: 'Are you sure?',
                text: 'You will not be able to recover this payment!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then(function() {


                $.ajax({
                    url : 'assets/app/refund.php?cuid=<?php echo $_SESSION['cuid']; ?>',
                    type: 'POST',
                    data: {
                        ch : del
                    },
                    success: function(data){
                        toastr.success("<strong>Logan says:</strong><br/>Payment has been refunded..updating our database to match Stripe.");
                        $.ajax({
                            url: 'assets/app/update_settings.php?setting=refund_payment',
                            type: 'POST',
                            data: {
                                del: del,
                                ev: event
                            },
                            success: function(s){
                                updateInv(event, luid);
                                $('#paid').DataTable().ajax.reload();
                            },
                            error: function(e){
                                updateInv(event, luid);
                            }
                        });
                    },
                    error: function(error){
                        toastr.error("<strong>Logan says:</strong><br/>An unexpected error has occurred.");
                    }
                });

                swal(
                    'Refunded!',
                    'The payment has been refunded.',
                    'success'
                );

            }, function(dismiss) {
                // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        'Your refund is safe :)',
                        'error'
                    )
                }
            });


        });


        $(document).on('click', '.add_item', function(){
            var event = $(this).attr('data-ev');
            var luid  = $(this).attr('data-luid');
            var qty   = $(this).attr('data-qty');
            $.ajax({
                url: 'assets/app/api/actions.php?ty=ai',
                type: 'POST',
                data: {
                    srv_id: $(this).attr('data-id'),
                    srv_ev: $(this).attr('data-ev'),
                    srv_qt: qty
                },
                success: function(d){
                    var inf = JSON.parse(d);
                    toastr.success("<strong>Logan says</strong>:<br/> "+inf.item+" added to <?php echo $user['user_fname']; ?>'s invoice for "+inf.cost);
                    $('.sales').DataTable().destroy();
                    var url = $('.sales').attr('data-src');
                    $('.sales').DataTable({
                        "processing": true,
                        "serverSide": true,
                        //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                        "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                        "bPaginate": false,
                        "bFilter": false,
                        "info": false,
                        "ajax": {
                            "url": url, // ajax source
                        },
                        "lengthMenu": [
                            [10, 20, 50, 100, 150, -1],
                            [10, 20, 50, 100, 150, "All"] // change per page values here
                        ],
                        "pageLength": 10, // default record count per page
                        "order": [
                            [1, "asc"]
                        ]// set first column as a default sort by asc
                    });
                    updateInv(event, luid);
                },
                error: function(e){
                    toastr.error("<strong>Logan says</strong>:<br/> An unexpected error has occurred. Please try again later.")
                }
            });
        });
        $(document).on('click', '.add_contract_item', function(){
            var ct    = $(this).attr('data-ct');
            var luid  = $(this).attr('data-luid');
            var qty   = $(this).attr('data-qty');
            var tty   = $(this).attr('data-tty');
            var uuid  = $(this).attr('data-uuid');
            $.ajax({
                url: 'assets/app/api/actions.php?ty=ai&st_c='+ct+'&uuid='+uuid+'&luid='+luid,
                type: 'POST',
                data: {
                    srv_id: $(this).attr('data-id'),
                    srv_qt: qty
                },
                success: function(d){
                    var inf = JSON.parse(d);
                    toastr.success("<strong>Logan says</strong>:<br/> "+inf.item+" added to <?php echo $user['user_fname']; ?>'s invoice for "+inf.cost);
                    $('.sales').DataTable().destroy();
                    var url = $('.sales').attr('data-src');
                    $('.sales').DataTable({
                        "processing": true,
                        "serverSide": true,
                        //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                        "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                        "bPaginate": false,
                        "bFilter": false,
                        "info": false,
                        "ajax": {
                            "url": url, // ajax source
                        },
                        "lengthMenu": [
                            [10, 20, 50, 100, 150, -1],
                            [10, 20, 50, 100, 150, "All"] // change per page values here
                        ],
                        "pageLength": 10, // default record count per page
                        "order": [
                            [1, "asc"]
                        ]// set first column as a default sort by asc
                    });
                    if(tty != '1'){
                        updateContractInv(ct, luid);
                    } else {
                        updateRetailInv(ct, luid);
                    }

                },
                error: function(e){
                    toastr.error("<strong>Logan says</strong>:<br/> An unexpected error has occurred. Please try again later.")
                }
            });
        });
        $(document).on('click', '.hotlead', function(){
            var dat = $(this).attr('data-uuid');
            var ev  = $(this).attr('data-ev');
            var sv  = $(this).attr('data-sv');
            if(sv == 1){
                $.ajax({
                    url: 'assets/pages/profile.php?uuid='+dat+'&luid=<?php echo $_GET['luid']; ?>&wiz=true',
                    success: function(vat) {
                        $('#page_content').html(vat);
                        $('body.page-quick-sidebar-open').removeClass("page-quick-sidebar-open");
                        $.ajax({
                            url: 'assets/pages/sub/profile_event_wizard.php?&conf='+ev,
                            success: function(data) {
                                $('#profile-content').html(data);
                            },
                            error: function() {
                                toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                            }
                        });
                        toastr.success("<strong>Logan says:</strong><br/>Loading event wizard for you..");
                    },
                    error: function() {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                    }
                });
            } else {
                $.ajax({
                    url: 'assets/pages/event.php?ev='+ev+'&luid=<?php echo $_GET['luid']; ?>',
                    success: function(vat) {
                        $('#page_content').html(vat);
                    },
                    error: function() {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                    }
                });
            }

        });
        $(document).on('click', '.hotlead_remove', function(){
            var ev  = $(this).attr('data-ev');
            swal({
                title: 'Are you sure?',
                text: 'You will not be able to recover this hot lead!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'No, keep it'
            }).then(function() {


                $.ajax({
                    url: 'assets/app/update_settings.php?update=event_fly',
                    type: 'POST',
                    data: {
                        name: 'event_status',
                        value: 9,
                        pk: ev
                    },
                    success: function(s){
                        $.ajax({
                            url: 'assets/pages/dashboard.php?luid='+s,
                            success: function(data) {
                                $('#page_content').html(data);
                            },
                            error: function() {
                                toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                            }
                        });
                        toastr.success("<strong>Logan says:</strong><br/>Hot lead has been removed from the list.");
                    },
                    error: function(s){

                    }
                });

                swal(
                    'Removed!',
                    'The hotlead has been removed.',
                    'success'
                );

            }, function(dismiss) {
                // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        'Your hot lead is safe :)',
                        'error'
                    )
                }
            });

        });
        $(document).on('click', '.touch', function(){
            var ev  = $(this).attr('data-ev');
            $.ajax({
                url: 'assets/app/update_settings.php?update=event_fly',
                type: 'POST',
                data: {
                    name: 'event_date_touch',
                    value: 1,
                    pk: ev
                },
                success: function(s){
                    $.ajax({
                        url: 'assets/pages/dashboard.php?luid='+s,
                        success: function(data) {
                            $('#page_content').html(data);
                        },
                        error: function() {
                            toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                        }
                    });
                    toastr.success("<strong>Logan says:</strong><br/>Hot lead has been touched & sent to the bottom of that list.");
                },
                error: function(s){

                }
            });
        });
        $(document).on('click', '.review_stat', function(){
            var stat  = $(this).attr('data-stat');
            var r     = $(this).attr('data-r');
            $.ajax({
                url: 'assets/app/update_settings.php?update=review_stat',
                type: 'POST',
                data: {
                    value: stat,
                    pk: r
                },
                success: function(s){
                    if(stat == 1){
                        toastr.success("<strong>Logan says:</strong><br/>Review was approved and added to the <strong>review API</strong>.");
                        $('.review_btns_'+r).remove();
                    } else {
                        toastr.success("<strong>Logan says:</strong><br/>Review was removed from the list.");
                        $('.review_'+r).remove();
                    }

                },
                error: function(s){

                }
            });
        });
        $(document).on('click', '.edit', function(){
            var line   = $(this).attr('data-edit');
            var reload = $(this).attr('data-reload');
            var event  = $(this).attr('data-event');
            var luid   = $(this).attr('data-luid');
            var selec  = $(this).attr('data-selec');
            $('.'+line).editable({
                step: 'any',
                success: function(e) {
                    if(reload == "eve"){
                        updateInv(event, luid);
                    } if(reload == "ct") {
                        updateContractInv(event, luid);
                    } if(reload == "rt") {
                        updateRetailInv(event, luid);
                    } if(reload){
                        $('.datatable').DataTable().ajax.reload();
                    }
                }
            }).on("shown", function(e, editable) {
                if(selec == 'autoselect'){
                    editable.input.$input.get(0).select();
                }
                //console.log(type + " is the type!");
            });
            toastr.info("<strong>Logan says</strong>:<br/>Editable information has been underlined with blue dots.")
        });
        $(document).on('click', '.del_sendable', function(E){
            var token = $(this).attr('data-delete');
            var that  = $(this);
            swal({
                title: 'Are you sure?',
                text: 'You will not be able to recover this payment!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then(function() {


                $(that).closest('tr').remove();
                $.ajax({
                    url: 'assets/app/update_settings.php?adm=delete_sendable',
                    type: 'POST',
                    data: {
                        token: token
                    },
                    success: function(s){
                        toastr.info('<strong>Logan says</strong>:<br>That document has been deleted. Thanks for keeping me clean!');
                        $(this).closest('tr').remove();
                    },
                    error: function(e){
                        toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                    }
                });

                swal(
                    'Refunded!',
                    'The payment has been refunded.',
                    'success'
                );

            }, function(dismiss) {
                // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        'Your refund is safe :)',
                        'error'
                    )
                }
            });

        });
        $(document).on('click', '.del_daily_note', function(){
            var id = $(this).attr('data-delete');

            swal({
                title: 'Are you sure?',
                text: 'You will not be able to recover this record!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then(function() {

                $.ajax({
                    url: 'assets/app/update_settings.php?adm=daily_note',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function (d) {
                        toastr.success("<strong>Logan says</strong>:<br/>I deleted that note.");
                        $('.refresh').click();
                    },
                    error: function () {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                    }
                });

                swal(
                    'Deleted!',
                    'The record has been deleted.',
                    'success'
                );

            }, function(dismiss) {
                // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        'Your record is safe :)',
                        'error'
                    )
                }
            });
        });
        $(document).on('click', '.delete_tc', function(E){
            var token = $(this).attr('data-id');
            var that  = $(this);
            swal({
                title: 'Are you sure?',
                text: 'You will not be able to recover this record!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then(function() {


                $(that).closest('tr').remove();
                $.ajax({
                    url: 'assets/app/update_settings.php?adm=delete_tc',
                    type: 'POST',
                    data: {
                        token: token
                    },
                    success: function(s){
                        toastr.info('<strong>Logan says</strong>:<br>That record has been deleted. Thanks for keeping me clean!');
                        $(this).closest('tr').remove();
                    },
                    error: function(e){
                        toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                    }
                });

                swal(
                    'Deleted!',
                    'The record has been deleted.',
                    'success'
                );

            }, function(dismiss) {
                // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        'Your record is safe :)',
                        'error'
                    )
                }
            });

        });
        $(document).on('click', '.updt_pp', function () {
            $(this).off('click');
            var formData = new FormData($('form#pp_upload')[0]);
            var uuid     = $(this).attr('data-uuid');
            $.ajax({
                type: 'POST',
                url: 'assets/app/upload_image.php?uuid='+ uuid,
                data: formData,
                async: false,
                success: function(data) {
                    toastr.success("<strong>Logan says</strong>:<br/>Your new profile picture has been uploaded. Refreshing the dashboard to show your new image..");
                    $('.pp').attr("src", data);
                },
                error: function() {
                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                },
                cache: false,
                contentType: false,
                processData: false
            });
            return false;
        });
        $(document).on('click', '.updt_ll', function () {
            $(this).off('click');
            var formData = new FormData($('form#ll_upload')[0]);
            var luid     = $(this).attr('data-luid');
            $.ajax({
                type: 'POST',
                url: 'assets/app/upload_image.php?luid='+ luid,
                data: formData,
                async: false,
                success: function(data) {
                    toastr.success("<strong>Logan says</strong>:<br/>Your new profile picture has been uploaded. Refreshing the dashboard to show your new image..");
                    $('.ll').attr("src", data);
                },
                error: function() {
                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                },
                cache: false,
                contentType: false,
                processData: false
            });
            return false;
        });
        $(document).on('click', '.del_p_doc', function(E){

            var token = $(this).attr('data-id');
            var that  = $(this);
            swal({
                title: 'Are you sure?',
                text: 'You will not be able to recover this record!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then(function() {


                $(that).closest('tr').remove();
                $.ajax({
                    url: 'assets/app/update_settings.php?adm=delete_p_doc',
                    type: 'POST',
                    data: {
                        token: token
                    },
                    success: function(s){
                        toastr.info('<strong>Logan says</strong>:<br>That record has been deleted. Thanks for keeping me clean!');
                        $(this).closest('tr').remove();
                    },
                    error: function(e){
                        toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                    }
                });

                swal(
                    'Deleted!',
                    'The record has been deleted.',
                    'success'
                );

            }, function(dismiss) {
                // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        'Your record is safe :)',
                        'error'
                    )
                }
            });


        });
        $(document).on('click', '.del_labor', function() {
            var id   = $(this).attr('data-delete');
            var that = $(this);
            swal({
                title: 'Are you sure?',
                text: 'You will not be able to recover this record!',
                type: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it'
            }).then(function() {


                $(that).closest('tr').remove();
                $.ajax({
                    url: 'assets/app/update_settings.php?adm=del_usrlabor',
                    type: 'POST',
                    data: {
                        id: id
                    },
                    success: function (d) {
                        toastr.success("<strong>Logan says</strong>:<br/>I deleted that record from this users labor.");

                    },
                    error: function () {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                    }
                });

                swal(
                    'Deleted!',
                    'The record has been deleted.',
                    'success'
                );

            }, function(dismiss) {
                // dismiss can be 'overlay', 'cancel', 'close', 'esc', 'timer'
                if (dismiss === 'cancel') {
                    swal(
                        'Cancelled',
                        'Your record is safe :)',
                        'error'
                    )
                }
            });

        });
        $('.hidden-submit').on('click', function() {
            $.ajax({
                url: 'assets/pages/search.php?luid=<?php echo $_GET['luid']; ?>',
                type: 'POST',
                data: {
                    search: $('#search').val()
                },
                success: function(data) {
                    toastr.success("<strong>Logan says</strong>:<br/>Give me a second while I gather search results for you.")
                    $('#page_content').html(data);
                },
                error: function() {
                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                }
            });
        });
        $(document).on('click', '.search', function() {
            $.ajax({
                url: 'assets/pages/search.php?luid=<?php echo $_GET['luid']; ?>',
                type: 'POST',
                data: {
                    search: $('#search_deep').val()
                },
                success: function(data) {
                    toastr.success("<strong>Logan says</strong>:<br/>Give me a second while I gather search results for you.")
                    $('#page_content').html(data);
                },
                error: function() {
                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                }
            });
        });
        $('#new_tickets').validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "",
            rules: {
                department: {
                    required: true
                },
                priority: {
                    required: true
                },
                message: {
                    required: true
                }
            },

            invalidHandler: function (event, validator) { //display error alert on form submit

            },

            highlight: function (element) { // hightlight error inputs
                $(element)
                    .closest('.form-group').addClass('has-error'); // set error class to the control group
            },

            success: function (label) {
                label.closest('.form-group').removeClass('has-error');
                label.remove();
            },


            submitHandler: function (form) {
                var group = $('#role').val();
                $('.submitter').prop('disabled', true);
                $('.submitter').html("<i class='fa fa-spinner fa-spin'></i>");
                $.ajax({
                    url: 'assets/app/add_setting.php?setting=ticket&luid=<?php echo $_GET['luid']; ?>&uuid=<?php echo $_SESSION['uuid']; ?>&cuid=<?php echo $_SESSION['cuid']; ?>',
                    type: "POST",
                    data: new FormData($('#new_tickets')[0]),
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    encode: true,
                    success: function (data) {
                        $('.submitter').prop('disabled', false);
                        $('#new_tickets')[0].reset();
                        $('#add_ticket').modal('hide');
                        $('.submit-msg').html("Save Ticket Again");
                        toastr.success("<strong>Logan says</strong>:<br/>Magic. I'm taking you to your newly created support ticket now..");
                        $.ajax({
                            url: 'assets/pages/tickets.php?tk='+ data.tk,
                            type: "POST",
                            success: function (data) {

                                $('#page_content').html(data);
                            },
                            error: function () {
                                toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later. B");
                            }
                        });
                    },
                    error: function () {
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later. C");
                    }
                });
            }
        });
    });
    $('#catcher_phone').focusout(function() {
        var phone = $(this).val();

        phone = phone.replace(/\D/g,'');
        if(phone.length == 10){
            $.ajax({
                url: 'assets/app/api/catcher.php?luid=<?php echo $_GET['luid']; ?>&p=jre',
                type: 'POST',
                data: {
                    phone: $(this).val().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, ''),
                },
                success: function(data) {
                    $('.catcher-items-hide').hide();
                    $('#response_1').html("Customer found. We're loading this customers profile for you, you can continue with them from there.").attr('class', 'help-block text-success');
                    $('body.page-quick-sidebar-open').removeClass("page-quick-sidebar-open");
                    $('#catcher')[0].reset();
                    $.ajax({
                        url: 'assets/pages/profile.php?uuid='+data,
                        success: function(data) {
                            $('#page_content').html(data);
                        },
                        error: function() {
                            toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                        }
                    });
                },
                error: function() {
                    $('#response_1').html("Nobody found with that number.").attr('class', 'help-block text-danger');
                    $('#nameinput').show(function() {
                        $('#catcher_name').get(0).focus();
                        $('#catcher_name').focusout(function(){
                            if($(this).val().length > 0){
                                $('#response_2').html("").attr('class', 'help-block text-danger');
                                $('#zipcodeinput').show(function() {
                                    $('#catcher_zipcode').get(0).focus();
                                    $('#catcher_zipcode').focusout(function(){
                                        if($(this).val().length > 0){
                                            $.ajax({
                                                url: 'assets/app/api/catcher.php?cuid=<?php echo $_SESSION['cuid']; ?>&p=jdk',
                                                type: 'POST',
                                                data: {
                                                    zipcode: $(this).val()
                                                },
                                                success: function(data){
                                                    $('#locationinput').show(function(){
                                                        var luid = data;
                                                        $("#" + luid).attr('selected', true);
                                                        $('#locationinputtt').on('change', function(){
                                                           if($(this).val() != ''){
                                                               $('#calenderinput').show(function(){
                                                                   var date = new Date();
                                                                   date.setDate(date.getDate());
                                                                   $('.date-picker').datepicker({
                                                                       startDate: date,
                                                                       altField: '#date',
                                                                       dateFormat: 'yy-mm-dd'
                                                                   }).on("changeDate", function (e) {
                                                                       var luid = $('select[name="location"]').val();
                                                                       var date = $(this).datepicker("getDate");
                                                                       $('#date').attr('value', date);
                                                                       $.ajax({
                                                                           url: 'assets/app/update_settings.php?update=usr_prf',
                                                                           type: 'POST',
                                                                           data: {
                                                                               name: 'user_last_ext_date',
                                                                               value: ''+ date +'',
                                                                               pk: '<?php echo $_SESSION['uuid']; ?>'
                                                                           },
                                                                           success: function(){
                                                                               $.ajax({
                                                                                   url: 'assets/pages/dashboard.php?luid='+luid,
                                                                                   type: 'POST',
                                                                                   success: function(data) {
                                                                                       $('#page_content').html(data);
                                                                                       $('#jobtypeinput').show(function(){
                                                                                           $('#catcher_jobtype').on('change', function(){
                                                                                               if($(this).val() == 'Local'){
                                                                                                   $('.doMath').on('change', function() {
                                                                                                       var a = $(this).attr('data-a');
                                                                                                       var b = $(this).attr('data-b');
                                                                                                       var c = $(this).attr('data-c');
                                                                                                       $.ajax({
                                                                                                           url: 'assets/app/api/catcher.php?luid='+luid+'&p=doMath',
                                                                                                           type: 'POST',
                                                                                                           data: {
                                                                                                               day: date.getDay(),
                                                                                                               a: $(a).val(),
                                                                                                               b: $(b).val(),
                                                                                                               c: $(c).val()
                                                                                                           },
                                                                                                           success: function(d){
                                                                                                               var e = JSON.parse(d);
                                                                                                               $("#TR > span").html(e.truck_fee);
                                                                                                               $("#TR").val(e.truck_fee);
                                                                                                               $("#LR > span").html(e.total_labor_rate);
                                                                                                               $("#LR").val(e.total_labor_rate);
                                                                                                               $("#CR > span").html(e.county_fee);
                                                                                                           },
                                                                                                           error: function(e){

                                                                                                           }
                                                                                                       })
                                                                                                   });
                                                                                                   $('#fees').show();
                                                                                                   $('#osb_fees input').prop("disabled", true);
                                                                                                   $('#osb_fees').hide();
                                                                                                   $.ajax({
                                                                                                       url: 'assets/app/api/catcher.php?luid='+luid+'&p=jkv',
                                                                                                       type: 'POST',
                                                                                                       data: {
                                                                                                           day: date.getDay()
                                                                                                       },
                                                                                                       success: function(e){
                                                                                                           var inf = JSON.parse(e);
                                                                                                           $('#TR > span').html(inf.truck_fee);
                                                                                                           $('#TR').attr('value', parseInt(inf.truck_fee));
                                                                                                           $('#LR > span').html(inf.total_labor_rate);
                                                                                                           $('#LR').val(inf.total_labor_rate);
                                                                                                           $('#CR > span').html(inf.county_fee);
                                                                                                           $('#CR').val(inf.county_fee);
                                                                                                           $('#email').show(function(){
                                                                                                               $('#catcher_email').focusout(function(){
                                                                                                                   $('#other_options').show(function(){
                                                                                                                       $(".img-che").click(function(){
                                                                                                                           $(this).toggleClass("che");
                                                                                                                           $(this).parent().toggleClass("red");
                                                                                                                       });
                                                                                                                   });
                                                                                                                   $('#storage').show();
                                                                                                                   $('#referer').show(function(){
                                                                                                                       $('#catcher_referer').on('change', function() {
                                                                                                                           $('#comments').show(function() {
                                                                                                                               $('#catcher_comments').focusout(function() {
                                                                                                                                   $('#submit').show();
                                                                                                                                   $('.create_event').unbind().click(function(ee){
                                                                                                                                       var me = $(this);
                                                                                                                                       ee.preventDefault();

                                                                                                                                       if ( me.data('requestRunning') ) {
                                                                                                                                           return;
                                                                                                                                       }

                                                                                                                                       me.data('requestRunning', true);

                                                                                                                                       var truckfee = $("#catcher_truckfee");
                                                                                                                                       $.ajax({
                                                                                                                                           url: 'assets/app/register.php?gr=3&c=<?php echo $_SESSION['cuid']; ?>&luid='+luid,
                                                                                                                                           type: 'POST',
                                                                                                                                           data: {
                                                                                                                                               fullname: $('input[id="catcher_name"').val(),
                                                                                                                                               phone: $('input[id="catcher_phone"]').val().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, ''),
                                                                                                                                               email: $('input[id="catcher_email"]').val(),
                                                                                                                                               luid: luid
                                                                                                                                           },
                                                                                                                                           success: function(dat){
                                                                                                                                               $('#date').val(date.toISOString().slice(0,10));
                                                                                                                                               me.data('requestRunning', false);
                                                                                                                                               toastr.success("Customer has been added to our database, you can now further configure their booking.");
                                                                                                                                               $.ajax({
                                                                                                                                                   url: 'assets/app/add_event.php?ev=plk&uuid='+dat+'&luid='+luid+'',
                                                                                                                                                   type: 'POST',
                                                                                                                                                   data: $('#catcher').serialize(),
                                                                                                                                                   success: function(ev){
                                                                                                                                                       $.ajax({
                                                                                                                                                           url: 'assets/pages/profile.php?uuid='+dat+'&luid='+luid,
                                                                                                                                                           success: function(vat) {
                                                                                                                                                               $('#page_content').html(vat);
                                                                                                                                                               $('body.page-quick-sidebar-open').removeClass("page-quick-sidebar-open");
                                                                                                                                                               $.ajax({
                                                                                                                                                                   url: 'assets/pages/sub/profile_event_wizard.php?&conf='+ev+'&uuid='+dat,
                                                                                                                                                                   success: function(data) {
                                                                                                                                                                       $('#profile-content').html(data);
                                                                                                                                                                       $('#catcher')[0].reset();
                                                                                                                                                                       $('.catcher-items-hide').hide();
                                                                                                                                                                   },
                                                                                                                                                                   error: function() {
                                                                                                                                                                       toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                                                                                                                                                   }
                                                                                                                                                               });

                                                                                                                                                           },
                                                                                                                                                           error: function() {
                                                                                                                                                               toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                                                                                                                                           }
                                                                                                                                                       });
                                                                                                                                                   },
                                                                                                                                                   error: function(){

                                                                                                                                                   }
                                                                                                                                               });
                                                                                                                                           }
                                                                                                                                       });
                                                                                                                                   });
                                                                                                                                   $('.hot_lead').unbind().click(function(eh) {
                                                                                                                                       var me = $(this);
                                                                                                                                       eh.preventDefault();

                                                                                                                                       if ( me.data('requestRunning') ) {
                                                                                                                                           return;
                                                                                                                                       }

                                                                                                                                       me.data('requestRunning', true);
                                                                                                                                       var truckfee = $("#catcher_truckfee");
                                                                                                                                       $.ajax({
                                                                                                                                           url: 'assets/app/register.php?gr=3&c=<?php echo $_SESSION['cuid']; ?>',
                                                                                                                                           type: 'POST',
                                                                                                                                           data: {
                                                                                                                                               fullname: $('input[id="catcher_name"').val(),
                                                                                                                                               phone: $('input[id="catcher_phone"]').val().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, ''),
                                                                                                                                               email: $('input[id="catcher_email"]').val(),
                                                                                                                                               luid: luid
                                                                                                                                           },
                                                                                                                                           success: function(dat){
                                                                                                                                               $('#date').val(date.toISOString().slice(0,10));
                                                                                                                                               me.data('requestRunning', false);
                                                                                                                                               toastr.success("Customer has been added to our database, I'm showing you the hot lead on their profile now..");
                                                                                                                                               $.ajax({
                                                                                                                                                   url: 'assets/app/add_event.php?ev=plk&uuid='+dat+'&luid='+luid+'&e=<?php echo struuid(true); ?>&hot=lead',
                                                                                                                                                   type: 'POST',
                                                                                                                                                   data: $('#catcher').serialize(),
                                                                                                                                                   success: function(ev){
                                                                                                                                                       $.ajax({
                                                                                                                                                           url: 'assets/pages/profile.php?uuid='+dat+'&luid='+luid,
                                                                                                                                                           success: function(vat) {
                                                                                                                                                               $('#page_content').html(vat);
                                                                                                                                                               $('#catcher')[0].reset();
                                                                                                                                                               $('.catcher-items-hide').hide();
                                                                                                                                                               $('body.page-quick-sidebar-open').removeClass("page-quick-sidebar-open");
                                                                                                                                                           },
                                                                                                                                                           error: function() {
                                                                                                                                                               toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                                                                                                                                           }
                                                                                                                                                       });
                                                                                                                                                   },
                                                                                                                                                   error: function(){

                                                                                                                                                   }
                                                                                                                                               });
                                                                                                                                           }
                                                                                                                                       });
                                                                                                                                   });
                                                                                                                               });
                                                                                                                           })
                                                                                                                       });
                                                                                                                   });
                                                                                                               });
                                                                                                           });
                                                                                                       },
                                                                                                       error: function(e){

                                                                                                       }
                                                                                                   });
                                                                                               }

                                                                                               if($(this).val() == 'Out Of State'){
                                                                                                   $('#fees').hide();
                                                                                                   $('#osb_fees').show();
                                                                                                   $('#fees input').prop("disabled", true);
                                                                                                   $('.osb').on('change', function() {
                                                                                                       $('#ending_name').html('<i class="fa fa-spinner fa-spin"></i>');
                                                                                                       $('#total_miles').html('<i class="fa fa-spinner fa-spin"></i>');
                                                                                                       $('#osb_truckfee').html('<i class="fa fa-spinner fa-spin"></i>');
                                                                                                       $('#osb_labor').html('<i class="fa fa-spinner fa-spin"></i>');
                                                                                                       $('#osb_bid').html('<i class="fa fa-spinner fa-spin"></i>');
                                                                                                       var a = $(this).attr('data-a');
                                                                                                       var b = $(this).attr('data-b');
                                                                                                       var c = $(this).attr('data-c');
                                                                                                       var d = $(this).attr('data-d');
                                                                                                       $.ajax({
                                                                                                           url: 'assets/app/api/catcher.php?luid='+luid+'&p=osb',
                                                                                                           type: 'POST',
                                                                                                           data: {
                                                                                                               a: $(a).val(),
                                                                                                               b: $(b).val(),
                                                                                                               c: $(c).val(),
                                                                                                               d: $(d).val(),
                                                                                                               day: date.getDay()
                                                                                                           },
                                                                                                           success: function(d){
                                                                                                               var e = JSON.parse(d);
                                                                                                               $('#ending_name').html(e.wording);
                                                                                                               $('#total_miles').html(e.distance);
                                                                                                               $('#osb_truckfee').html("<strong>$" + e.truckfee + "</strong>");
                                                                                                               $('#osb_labor').html("<strong>$" + e.labor + "</strong>");
                                                                                                               $('#osb_bid').html("<strong>$" + e.bid + "</strong>");
                                                                                                               $('#email').show(function(){
                                                                                                                   $('#catcher_email').focusout(function(){
                                                                                                                       $('#other_options').show(function(){
                                                                                                                           $(".img-che").click(function(){
                                                                                                                               $(this).toggleClass("che");
                                                                                                                               $(this).parent().toggleClass("red");
                                                                                                                           });
                                                                                                                       });
                                                                                                                       $('#storage').show();
                                                                                                                       $('#referer').show(function(){
                                                                                                                           $('#catcher_referer').on('change', function() {
                                                                                                                               $('#comments').show(function() {
                                                                                                                                   $('#catcher_comments').focusout(function() {
                                                                                                                                       $('#submit').show();
                                                                                                                                       $('.create_event').unbind().click(function(ee){
                                                                                                                                           var me = $(this);
                                                                                                                                           ee.preventDefault();

                                                                                                                                           if ( me.data('requestRunning') ) {
                                                                                                                                               return;
                                                                                                                                           }

                                                                                                                                           me.data('requestRunning', true);

                                                                                                                                           var truckfee = $("#catcher_truckfee");
                                                                                                                                           $.ajax({
                                                                                                                                               url: 'assets/app/register.php?gr=3&c=<?php echo $_SESSION['cuid']; ?>&luid='+luid,
                                                                                                                                               type: 'POST',
                                                                                                                                               data: {
                                                                                                                                                   fullname: $('input[id="catcher_name"').val(),
                                                                                                                                                   phone: $('input[id="catcher_phone"]').val().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, ''),
                                                                                                                                                   email: $('input[id="catcher_email"]').val(),
                                                                                                                                                   luid: luid
                                                                                                                                               },
                                                                                                                                               success: function(dat){
                                                                                                                                                   $('#date').val(date.toISOString().slice(0,10));
                                                                                                                                                   me.data('requestRunning', false);
                                                                                                                                                   toastr.success("Customer has been added to our database, you can now further configure their booking.");
                                                                                                                                                   $.ajax({
                                                                                                                                                       url: 'assets/app/add_event.php?ev=plk&uuid='+dat+'&luid='+luid+'&e=<?php echo struuid(true); ?>',
                                                                                                                                                       type: 'POST',
                                                                                                                                                       data: $('#catcher').serialize(),
                                                                                                                                                       success: function(ev){
                                                                                                                                                           $.ajax({
                                                                                                                                                               url: 'assets/pages/profile.php?uuid='+dat+'&luid='+luid+'&wiz=true',
                                                                                                                                                               success: function(vat) {
                                                                                                                                                                   $('#page_content').html(vat);
                                                                                                                                                                   $('body.page-quick-sidebar-open').removeClass("page-quick-sidebar-open");
                                                                                                                                                                   $.ajax({
                                                                                                                                                                       url: 'assets/pages/sub/profile_event_wizard.php?&conf='+ev+'&uuid='+dat,
                                                                                                                                                                       success: function(data) {
                                                                                                                                                                           $('#profile-content').html(data);
                                                                                                                                                                           $('#catcher')[0].reset();
                                                                                                                                                                           $('.catcher-items-hide').hide();
                                                                                                                                                                       },
                                                                                                                                                                       error: function() {
                                                                                                                                                                           toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                                                                                                                                                       }
                                                                                                                                                                   });

                                                                                                                                                               },
                                                                                                                                                               error: function() {
                                                                                                                                                                   toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                                                                                                                                               }
                                                                                                                                                           });
                                                                                                                                                       },
                                                                                                                                                       error: function(){

                                                                                                                                                       }
                                                                                                                                                   });
                                                                                                                                               }
                                                                                                                                           });
                                                                                                                                       });
                                                                                                                                       $('.hot_lead').unbind().click(function(eh) {
                                                                                                                                           var me = $(this);
                                                                                                                                           eh.preventDefault();

                                                                                                                                           if ( me.data('requestRunning') ) {
                                                                                                                                               return;
                                                                                                                                           }

                                                                                                                                           me.data('requestRunning', true);
                                                                                                                                           var truckfee = $("#catcher_truckfee");
                                                                                                                                           $.ajax({
                                                                                                                                               url: 'assets/app/register.php?gr=3&c=<?php echo $_SESSION['cuid']; ?>',
                                                                                                                                               type: 'POST',
                                                                                                                                               data: {
                                                                                                                                                   fullname: $('input[id="catcher_name"').val(),
                                                                                                                                                   phone: $('input[id="catcher_phone"]').val().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, ''),
                                                                                                                                                   email: $('input[id="catcher_email"]').val(),
                                                                                                                                                   luid: luid
                                                                                                                                               },
                                                                                                                                               success: function(dat){
                                                                                                                                                   $('#date').val(date.toISOString().slice(0,10));
                                                                                                                                                   me.data('requestRunning', false);
                                                                                                                                                   toastr.success("Customer has been added to our database, I'm showing you the hot lead on their profile now..");
                                                                                                                                                   $.ajax({
                                                                                                                                                       url: 'assets/app/add_event.php?ev=plk&uuid='+dat+'&luid='+luid+'&e=<?php echo struuid(true); ?>&l=0',
                                                                                                                                                       type: 'POST',
                                                                                                                                                       data: $('#catcher').serialize(),
                                                                                                                                                       success: function(ev){
                                                                                                                                                           $.ajax({
                                                                                                                                                               url: 'assets/pages/profile.php?uuid='+dat+'&luid='+luid,
                                                                                                                                                               success: function(vat) {
                                                                                                                                                                   $('#page_content').html(vat);
                                                                                                                                                                   $('#catcher')[0].reset();
                                                                                                                                                                   $('.catcher-items-hide').hide();
                                                                                                                                                                   $('body.page-quick-sidebar-open').removeClass("page-quick-sidebar-open");
                                                                                                                                                               },
                                                                                                                                                               error: function() {
                                                                                                                                                                   toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                                                                                                                                               }
                                                                                                                                                           });
                                                                                                                                                       },
                                                                                                                                                       error: function(){

                                                                                                                                                       }
                                                                                                                                                   });
                                                                                                                                               }
                                                                                                                                           });
                                                                                                                                       });
                                                                                                                                   });
                                                                                                                               })
                                                                                                                           });
                                                                                                                       });
                                                                                                                   });
                                                                                                               });
                                                                                                           },
                                                                                                           error: function(e){

                                                                                                           }
                                                                                                       });
                                                                                                   });
                                                                                               }
                                                                                           });
                                                                                       });
                                                                                   },
                                                                                   error: function() {
                                                                                       toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                                                                   }
                                                                               });
                                                                           }, error: function() {

                                                                           }
                                                                       });

                                                                   });
                                                               });
                                                           }
                                                        });
                                                        if(luid != ""){
                                                            $('#locationinputtt').trigger('change');
                                                        }
                                                    });

                                                },
                                                error: function(data){

                                                }
                                            });
                                        } else {
                                            $('#response_3').html("Please enter a zip code.").attr('class', 'help-block text-danger');
                                        }
                                    }).inputmask("mask", {
                                        "mask": "99999"
                                    });
                                });
                            } else {
                                $('#response_2').html("Please enter a name.").attr('class', 'help-block text-danger');
                            }
                        });
                    });
                }
            });
        } else {
            $('#response_1').html("Please enter a complete phone number to continue.").attr('class', 'help-block text-danger');
        }
    }).inputmask("mask", {
        "mask": "(999) 999-9999"
    });
</script>
</body>
</html>