<?php
session_start();
if(!isset($_SESSION['logged']) && $_SESSION['logged'] != true){
    header("Location: ../index.php?err=no_access");
} else {
    include 'assets/app/init.php';
    $lastlocation = mysql_query("UPDATE fmo_users SET user_last_ext_location='".mysql_real_escape_string($_GET['luid'])."' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'");
    $user = mysql_fetch_array(mysql_query("SELECT user_company_name, user_pic, user_setup, user_group, user_last_location, user_fname, user_lname, user_token, user_company_token FROM fmo_users WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'"));
    $location = mysql_fetch_array(mysql_query("SELECT location_name, location_state FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."' AND location_owner_company_token='".mysql_real_escape_string($_GET['cuid'])."'"));
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
    <script src="assets/global/plugins/pace/pace.min.js" type="text/javascript"></script>
    <link href="assets/global/plugins/pace/themes/pace-theme-minimal.css" rel="stylesheet" type="text/css"/>
    <link rel="stylesheet" type="text/css" href="assets/global/plugins/bootstrap-wysihtml5/bootstrap-wysihtml5.css"/>
	<link href="assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
	<link href="assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
	<link href="assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
	<link href="assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
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
    <link href="assets/admin/pages/css/invoice.css" rel="stylesheet" type="text/css"/>
    <link href="assets/global/plugins/select2/select2.css" rel="stylesheet" type="text/css"/>
	<link href="assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="assets/admin/pages/css/pricing-table.css" rel="stylesheet" type="text/css">
    <link href="assets/global/plugins/fullcalendar/fullcalendar.min.css" rel="stylesheet"/>
    <link href="assets/global/plugins/fullcalendar/scheduler.min.css" rel="stylesheet"/>
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
        .check {
            opacity:0.5;
            color:#996;
        }
    </style>

</head>
<body class="page-header-fixed page-quick-sidebar-over-content">
<div id="loader-wrapper">
    <div id="loader"></div>
    <div class="loader-section section-left"></div>
    <div class="loader-section section-right"></div>
</div>
<div class="page-header -i navbar navbar-fixed-top">
	<div class="page-header-inner">
		<div class="page-logo">
			<a href="">
				<img src="assets/admin/layout/img/logo.png" alt="logo" class="logo-default"/>
			</a>
			<div class="menu-toggler sidebar-toggler hide">
			</div>
		</div>
			<a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
		</a>
		<div class="top-menu">
			<ul class="nav navbar-nav pull-right">
                <li class="dropdown dropdown-dark dropdown-language">
                    <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="true">
                        <img alt="" src="assets/global/img/flags/us.png">
                        <span class="langname"> <?php echo $location['location_name']; ?> (<?php echo $location['location_state']; ?>) </span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-default">
                        <?php
                        $findLocations = mysql_query("SELECT location_name, location_token, location_state FROM fmo_locations WHERE location_owner_company_token='".$_SESSION['cuid']."' ORDER BY location_name ASC");
                        if(mysql_num_rows($findLocations) > 0){
                            while($loc = mysql_fetch_assoc($findLocations)){
                                ?>
                                <li>
                                    <a class="change_location" data-new-location="<?php echo $loc['location_token']; ?>" data-new-location-name="<?php echo $loc['location_name']; ?>" data-new-location-state="<?php echo $loc['location_state']; ?>"><img alt="" src="assets/global/img/flags/us.png"> <?php echo $loc['location_name']; ?> (<?php echo $loc['location_state']; ?>) </a>
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                </li>
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
							<a class="load_page" data-href="assets/pages/profile.php?uuid=<?php echo $_GET['uuid']; ?>&luid=<?php echo $_GET['luid']; ?>" data-page-title="My Profile">
                                <i class="icon-user"></i> My Profile
                            </a>
						</li>
						<li>
							<a href="assets/app/logout.php">
								<i class="icon-key"></i> Log Out </a>
						</li>
					</ul>
				</li>
				<li class="dropdown dropdown-quick-sidebar-toggler">
					<a href="javascript:;" class="dropdown-toggle">
						<i class="icon-logout"></i>
					</a>
				</li>
			</ul>
		</div>
	</div>
</div>
<div class="clearfix">
</div>
<div class="page-container">
	<div class="page-sidebar-wrapper">
		<div class="page-sidebar navbar-collapse collapse">
			<ul class="page-sidebar-menu" id="nav" data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200">
				<li class="sidebar-toggler-wrapper">
					<div class="sidebar-toggler">
					</div>
				</li>
                <br/>
                <?php
                if(!empty($_GET['luid'])){
                    if($user['user_group'] >= 1){
                        ?>
                        <li class="start" style='margin-top: 7px'>
                            <a class="load_page nav-a" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Dashboard">
                                <i class="icon-home"></i>
                                <span class="title">Dashboard</span>
                                <span class="selected"></span>
                                <span class="arrow "></span>
                            </a>
                        </li>
                        <li class="">
                            <a class="load_page" data-href="assets/pages/time_clock.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Time Clock">
                                <i class="icon-clock"></i>
                                <span class="title">Time Clock</span>
                                <span class="selected"></span>
                                <span class="arrow "></span>
                            </a>
                        </li>
                        <li class="">
                            <a class="load_page" data-href="assets/pages/customers.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Customers">
                                <i class="icon-users"></i>
                                <span class="title">Customers</span>
                                <span class="selected"></span>
                                <span class="arrow "></span>
                            </a>
                        </li>
                        <li class="">
                            <a class="load_page" data-href="assets/pages/marketing.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Marketing">
                                <i class="icon-graph"></i>
                                <span class="title">Marketing</span>
                                <span class="selected"></span>
                                <span class="arrow "></span>
                            </a>
                        </li>
                        <li class="">
                            <a class="load_page" data-href="assets/pages/employees.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Employees">
                                <i class="icon-earphones-alt"></i>
                                <span class="title">Employees</span>
                                <span class="selected"></span>
                                <span class="arrow "></span>
                            </a>
                        </li>
                        <li class="">
                            <a class="load_page" data-href="assets/pages/reports.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Reports">
                                <i class="icon-layers"></i>
                                <span class="title">Reports</span>
                                <span class="selected"></span>
                                <span class="arrow "></span>
                            </a>
                        </li>
                        <li class="">
                            <a class="load_page" data-href="assets/pages/assets.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Assets">
                                <i class="fa fa-truck"></i>
                                <span class="title">Assets</span>
                                <span class="selected"></span>
                                <span class="arrow "></span>
                            </a>
                        </li>
                        <li class="">
                            <a class="load_page" data-href="assets/pages/vendors.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Vendors">
                                <i class="icon-tag"></i>
                                <span class="title">Vendors</span>
                                <span class="selected"></span>
                                <span class="arrow "></span>
                            </a>
                        </li>
                        <li class="">
                            <a class="load_page" data-href="assets/pages/inventory.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Inventory">
                                <i class="icon-eye"></i>
                                <span class="title">Inventory</span>
                                <span class="selected"></span>
                                <span class="arrow "></span>
                            </a>
                        </li>
                        <li class="">
                            <a class="load_page" data-href="assets/pages/resource.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Resource Library">
                                <i class="icon-folder"></i>
                                <span class="title">Resource Library</span>
                                <span class="selected"></span>
                                <span class="arrow "></span>
                            </a>
                        </li>
                        <li class="">
                            <a class="load_page" data-href="assets/pages/manage_location.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Location Settings">
                                <i class="icon-settings"></i>
                                <span class="title">Location Settings</span>
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
                    <center><h5 style="color: white;">You need to create your first location.</h5></center>
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
	<a href="javascript:;" class="page-quick-sidebar-toggler"><i class="icon-call-in"></i></a>
	<div class="page-quick-sidebar-wrapper" style="overflow-y: scroll;">
		<div class="page-quick-sidebar">
            <?php
            $events = mysql_query("SELECT event_user_token, event_name, event_date_start, event_date_end, event_time, event_token, event_status, event_type, event_subtype, event_phone, event_email FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND event_status=0");
            ?>
			<div class="nav-justified">
				<ul class="nav nav-tabs nav-justified">
					<li class="active">
						<a href="#quick_sidebar_tab_1" data-toggle="tab">
							Call Catcher
						</a>
					</li>
					<li>
						<a href="#quick_sidebar_tab_2" data-toggle="tab">
							HOT LEADS <span class="badge badge-danger pull-left"><?php echo mysql_num_rows($events); ?></span>
						</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active page-quick-sidebar-chat" id="quick_sidebar_tab_1">
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
                                            <label>Now, we need their name:</label>
                                            <div class="input-icon">
                                                <i class="fa fa-user"></i>
                                                <input type="text" class="form-control" placeholder="enter persons name.." id="catcher_name" name="name">
                                                <span class="help-block text-danger" id="response_2"></span>
                                            </div>
                                        </div>
                                        <div class="form-group catcher-items-hide" id="zipcodeinput" style="display: none;">
                                            <label>Next, we need their zipcode:</label>
                                            <div class="input-icon">
                                                <i class="fa fa-crop"></i>
                                                <input type="text" min="3" max="5" class="form-control" placeholder="enter zipcode.." id="catcher_zipcode" name="catcher_zipcode">
                                            </div>
                                        </div>
                                        <div class="form-group catcher-items-hide" id="locationinput" style="display: none">
                                            <label>And select serviceable location:</label>
                                            <div class="input-icon">
                                                <i class="fa fa-compass"></i>
                                                <select class="form-control" name="location">
                                                    <?php
                                                    $findLocations = mysql_query("SELECT location_name, location_token, location_state FROM fmo_locations WHERE location_owner_token='".$user['user_token']."' ORDER BY location_name ASC");
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
                                                <div class="date-picker" data-date-format="mm/dd/yyyy" style="background-color: white; margin: auto;"></div>
                                                <input class="hide" name="date" id="date">
                                            </div>
                                        </div>
                                        <div class="form-group catcher-items-hide" id="jobtypeinput" style="display: none">
                                            <label>Now, select the job type..</label>
                                            <div class="input-icon">
                                                <i class="fa fa-tags"></i>
                                                <select class="form-control" name="type" id="catcher_jobtype">
                                                    <option disabled selected value="">Select one..</option>
                                                    <option>Local Move</option>
                                                    <option disabled>Out Of State Move</option>
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
                                        <div class="form-group catcher-items-hide" id="email" style="display: none;">
                                            <label>Customer's email</label>
                                            <div class="input-icon">
                                                <i class="fa fa-envelope"></i>
                                                <input type="text" class="form-control" placeholder="enter customers email.." id="catcher_email" name="email">
                                            </div>
                                        </div>
                                        <div class="form-group catcher-items-hide" id="other_options" style="display: none;">
                                            <label>
                                                Other options for the customer
                                            </label>
                                            <br/>
                                            <label class="btn btn-block">
                                                <img src="assets/global/img/catcher/hottub.gif" alt="..." class="img-thumbnail img-check check" style="vertical-align: top;">
                                                <label style="padding-top: 5px;">Hot Tub <br/>$398<br/>$350 w/ move <br/> <small>click image <br/>to add</small> </label>
                                                <input type="checkbox" name="addition[]" id="hot_tub" value="hot_tub" class="hidden" autocomplete="off">
                                            </label>
                                            <br/>
                                            <label class="btn btn-block">
                                                <img src="assets/global/img/catcher/babygrand.gif" alt="..." class="img-thumbnail img-check check" style="vertical-align: top;">
                                                <label style="padding-top: 5px;">Piano <br/>$398<br/>$350 w/ move <br/> <small>click image <br/>to add</small> </label>
                                                <input type="checkbox" name="addition[]" id="piano" value="piano" class="hidden" autocomplete="off">
                                            </label>
                                            <br/>
                                            <label class="btn btn-block">
                                                <img src="assets/global/img/catcher/pooltable.gif" alt="..." class="img-thumbnail img-check check" style="vertical-align: top;">
                                                <label style="padding-top: 5px;">Pool Table <br/>$398<br/>$350 w/ move <br/> <small>click image <br/>to add</small> </label>
                                                <input type="checkbox" name="addition[]" id="pool_table" value="pool_table" class="hidden" autocomplete="off">
                                            </label>
                                            <br/>
                                            <label class="btn btn-block">
                                                <img src="assets/global/img/catcher/playset.gif" alt="..." class="img-thumbnail img-check check" style="vertical-align: top;">
                                                <label style="padding-top: 5px;">Play Set <br/>$378<br/>$300 w/ move <br/> <small>click image <br/>to add</small> </label>
                                                <input type="checkbox" name="addition[]" id="play_set" value="play_set" class="hidden" autocomplete="off">
                                            </label>
                                            <br/>
                                            <label class="btn btn-block">
                                                <img src="assets/global/img/catcher/safe.gif" alt="..." class="img-thumbnail img-check check" style="vertical-align: top;">
                                                <label style="padding-top: 5px;">Safe <br/>$298<br/>$200 w/ move <br/> <small>click image <br/>to add</small> </label>
                                                <input type="checkbox" name="addition[]" id="safe" value="safe" class="hidden" autocomplete="off">
                                            </label>
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
                                            <a href="javascript:;" class="btn default blue-stripe pull-right create_event">Hot Lead </a>
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
					<div class="tab-pane page-quick-sidebar-alerts" id="quick_sidebar_tab_2">
                        <div class="row" style="padding: 15px;">
                            <div class="col-md-12">
                                <div class="todo-tasklist">
                                    <?php
                                    if(mysql_num_rows($events) > 0){
                                        while($event = mysql_fetch_assoc($events)){
                                            if($event['event_status'] != 0){
                                                continue;
                                            }
                                            ?>
                                            <div class="todo-tasklist-item todo-tasklist-item-border-red lead" data-ev="<?php echo $event['event_token']; ?>" data-uuid="<?php echo $event['event_user_token']; ?>">
                                                <div class="todo-tasklist-item-title">
                                                    <?php echo $event['event_name']; ?> <span class="todo-tasklist-badge badge badge-roundless badge-danger">HOT LEAD</span>
                                                </div>
                                                <div class="todo-tasklist-item-text">
                                                    <i class="fa fa-phone"></i> <?php echo $event['event_phone']; ?><br/>
                                                    <i class="fa fa-envelope"></i> <?php echo $event['event_email']; ?><br/>
                                                    <small><i class="fa fa-info"></i> click to book</small>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="page-footer">
	<div class="page-footer-inner">
		2017 &copy; For Movers Only | All Rights Reserved. <strong>Powered by Logan</strong>
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
<script src="assets/global/plugins/flot/jquery.flot.resize.min.js" type="text/javascript"></script>
<script src="assets/global/plugins/flot/jquery.flot.categories.min.js" type="text/javascript"></script>
<script type="text/javascript" src="assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/jquery.input-ip-address-control-1.0.min.js"></script>
<script src="assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
<script type="text/javascript" src="assets/global/plugins/select2/select2.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/jquery-mixitup/jquery.mixitup.min.js"></script>
<script type="text/javascript" src="assets/global/plugins/fancybox/source/jquery.fancybox.pack.js"></script>
<script src="http://maps.google.com/maps/api/js?key=AIzaSyBg2MfengOuhtRA-39qVbm8vA7n7pf5ES8&sensor=false" type="text/javascript"></script>
<script src="assets/global/plugins/gmaps/gmaps.min.js" type="text/javascript"></script>
<script src="assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="assets/admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
<script src="assets/admin/pages/scripts/ui-idletimeout.js"></script>
<script src="assets/global/plugins/xeditable/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script src="assets/admin/pages/scripts/form-validation.js"></script>
<script src="assets/admin/pages/scripts/tasks.js" type="text/javascript"></script>
<script src="assets/admin/pages/scripts/index.js" type="text/javascript"></script>
<script src="assets/global/scripts/datatable.js"></script>
<script src="https://checkout.stripe.com/checkout.js"></script>
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
        $.ajax({
            url: 'assets/pages/<?php $url = explode('?', $user['user_last_location']); echo $url[0]; ?>?luid=<?php echo $_GET['luid']; if($url[0] == 'profile.php'){echo "&".$url[1];};?><?php if($url[0] == 'event.php'){echo "&".$url[1];};?>',
            success: function(data) {
                $('#page_content').html(data);
            },
            error: function() {
                toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
            }
        });
        $(document).on('click', '.change_location', function(){
            var luid = $(this).attr('data-new-location');
            window.location.replace("//www.formoversonly.com/dashboard/index.php?uuid=<?php echo $_GET['uuid']; ?>&cuid=<?php echo $_GET['cuid']; ?>&luid="+luid);
        });
        $(document).on('click', '.load_page', function(){
            var act = $(this).attr('data-act');
            if(act == 'breadcrumb'){
                $(".active").removeClass("active");
                $('.nav-a').addClass("active");
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
        $(document).on('click', '.add_item', function(){
            $.ajax({
                url: 'assets/app/api/actions.php?ty=ai',
                type: 'POST',
                data: {
                    srv_id: $(this).attr('data-id'),
                    srv_ev: $(this).attr('data-ev')
                },
                success: function(d){
                    var inf = JSON.parse(d);
                    toastr.success("<strong>Logan says</strong>:<br/> "+inf.item+" added to <?php echo $user['user_fname']; ?>'s invoice for "+inf.cost);
                    $('#sales').DataTable().ajax.reload();
                },
                error: function(e){
                    toastr.error("<strong>Logan says</strong>:<br/> An unexpected error has occurred. Please try again later.")
                }
            });
        });
        $(document).on('click', '.lead', function(){
            var dat = $(this).attr('data-uuid');
            var ev  = $(this).attr('data-ev');
            $.ajax({
                url: 'assets/pages/profile.php?uuid='+dat,
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

                },
                error: function() {
                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                }
            });
        });
        $(document).on('click', '.edit', function(){
            var line   = $(this).attr('data-edit');
            var reload = $(this).attr('data-reload');
            $('.'+line).editable({
                success: function() {
                    if(reload.length > 0){
                        $('.datatable').DataTable().ajax.reload();
                    }
                }
            });
            toastr.info("<strong>Logan says</strong>:<br/>You can now edit that information. If you change a value, remember that you may need to refresh the page to see the changes.")
        });
    });
    $('#catcher_phone').focusout(function() {
        if($(this).val().length > 0){
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
                                                url: 'assets/app/api/catcher.php?luid=<?php echo $_GET['luid']; ?>&p=jdk',
                                                type: 'POST',
                                                data: {
                                                    zipcode: $(this).val()
                                                },
                                                success: function(data){
                                                    $('#locationinput').show(function(){
                                                        var luid = data;
                                                        $('option[id=' + luid + ']').attr('selected', true);
                                                    });
                                                    $('#calenderinput').show(function(){
                                                        var date = new Date();
                                                        date.setDate(date.getDate());
                                                        $('.date-picker').datepicker({
                                                            startDate: date
                                                        }).on("changeDate", function (e) {
                                                            var luid = $('select[name="location"]').val();
                                                            var date = $(this).datepicker('getDate');
                                                            $('#date').attr('value', date);
                                                            $.ajax({
                                                                url: 'assets/pages/dashboard.php?luid='+luid,
                                                                type: 'POST',
                                                                data: {
                                                                    date: date
                                                                },
                                                                success: function(data) {
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
                                                                    $('#page_content').html(data);
                                                                    $('#jobtypeinput').show(function(){
                                                                        $('#catcher_jobtype').on('change', function(){
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
                                                                                    $('#fees').show();
                                                                                    $('#email').show(function(){
                                                                                        $('#catcher_email').focusout(function(){
                                                                                            $('#other_options').show(function(){
                                                                                                $(".img-check").click(function(){
                                                                                                    $(this).toggleClass("check");
                                                                                                    $(this).parent().toggleClass("red");
                                                                                                });
                                                                                            });
                                                                                            $('#storage').show();
                                                                                            $('#referer').show(function(){
                                                                                                $('#catcher_referer').on('change', function() {
                                                                                                    $('#comments').show(function() {
                                                                                                        $('#catcher_comments').focusout(function() {
                                                                                                            $('#submit').show();
                                                                                                            $('.create_event').click(function(ee){
                                                                                                                var me = $(this);
                                                                                                                ee.preventDefault();

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
                                                                                                                        me.data('requestRunning', false);
                                                                                                                        toastr.success("Customer has been added to our database, you can now further configure their booking.");
                                                                                                                        $.ajax({
                                                                                                                            url: 'assets/app/add_event.php?ev=plk&uuid='+dat+'&luid='+luid+'&e=<?php echo struuid(true); ?>',
                                                                                                                            type: 'POST',
                                                                                                                            data: $('#catcher').serialize(),
                                                                                                                            success: function(ev){
                                                                                                                                $.ajax({
                                                                                                                                    url: 'assets/pages/profile.php?uuid='+dat,
                                                                                                                                    success: function(vat) {
                                                                                                                                        $('#page_content').html(vat);
                                                                                                                                        $('body.page-quick-sidebar-open').removeClass("page-quick-sidebar-open");
                                                                                                                                        $.ajax({
                                                                                                                                            url: 'assets/pages/sub/profile_event_wizard.php?&conf='+ev,
                                                                                                                                            success: function(data) {
                                                                                                                                                $('#profile-content').html(data);
                                                                                                                                                /*$('input[name="startdate"]').val($.datepicker.formatDate("mm/dd/yy", new Date(date)));
                                                                                                                                                 $('input[name="enddate"]').val($.datepicker.formatDate("mm/dd/yy", new Date(date)));
                                                                                                                                                 $('input[name="email"]').val($("#catcher_email").val());
                                                                                                                                                 $('input[name="phone"]').val($("#catcher_phone").val());
                                                                                                                                                 $('#ev_TR').val($("#TR").val()); $('input[name="event_truckfee"]').val($("#catcher_truckfee").val()); $('#ev_TR > span').html($("#TR").val());
                                                                                                                                                 $('#ev_LR').val($("#LR").val()); $('input[name="event_laborrate"]').val($("#catcher_laborrate").val()); $('#ev_LR > span').html($("#LR").val());
                                                                                                                                                 $('#ev_CR').val($("#CR").val()); $('input[name="event_countyfee"]').val($("#catcher_countyfee").val()); $('#ev_CR > span').html($("#CR").val());*/
                                                                                                                                                $('#catcher')[0].reset();
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
                                                                                                            $('.hot_lead').on('click', function() {
                                                                                                                var truckfee = $("#catcher_truckfee");
                                                                                                                $.ajax({
                                                                                                                    url: 'assets/app/register.php?gr=3&c=<?php echo $_SESSION['cuid']; ?>',
                                                                                                                    type: 'POST',
                                                                                                                    data: {
                                                                                                                        fullname: $('input[name="catcher_name"').val(),
                                                                                                                        phone: $('input[name="catcher_phone"]').val().replace(/[^a-z0-9\s]/gi, '').replace(/[_\s]/g, ''),
                                                                                                                        email: $('input[name="catcher_email"]').val(),
                                                                                                                        luid: luid
                                                                                                                    },
                                                                                                                    success: function(dat){
                                                                                                                        toastr.success("Customer has been added to our database, you can now further configure their booking.");
                                                                                                                        $.ajax({
                                                                                                                            url: 'assets/pages/profile.php?uuid='+dat,
                                                                                                                            success: function(vat) {
                                                                                                                                $('#page_content').html(vat);
                                                                                                                                $('body.page-quick-sidebar-open').removeClass("page-quick-sidebar-open");

                                                                                                                            },
                                                                                                                            error: function() {
                                                                                                                                toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
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
                                                                        })
                                                                    });
                                                                },
                                                                error: function() {
                                                                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                                                }
                                                            });
                                                        });
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