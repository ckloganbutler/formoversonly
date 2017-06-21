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
            <strong>Resource Library</strong>
        </h3>
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>"><?php echo $location['location_name']; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a class="load_page" data-href="assets/pages/resource.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Resource Library">Resource Library</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title tabbable-line">
                        <div class="caption caption-md">
                            <i class="icon-folder theme-font bold"></i>
                            <span class="caption-subject font-red bold uppercase"><?php echo $location['location_name']; ?></span> <span class="font-red">|</span>  <small>Resources</small>
                        </div>
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#tab" data-toggle="tab">Documentation</a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content">
                            <div class="tab-pane active" id="tab">
                                <div class="portlet">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-gift"></i>General
                                        </div>
                                        <div class="tools">
                                            <a href="javascript:;" class="collapse" data-original-title="" title="">
                                            </a>
                                            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
                                            </a>
                                            <a href="javascript:;" class="reload" data-original-title="" title="">
                                            </a>
                                            <a href="javascript:;" class="remove" data-original-title="" title="">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h3>Sample text with lead body</h3>
                                                <p class="lead">
                                                    Lead body. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                                                </p>
                                                <p>
                                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.
                                                </p>
                                                <p>
                                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <h3>Sample text</h3>
                                                <p>
                                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.
                                                </p>
                                                <p>
                                                    Nullam quis risus eget urna mollis ornare vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula ut id elit.
                                                </p>
                                                <p>
                                                    Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Donec sed odio dui.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h3>Texts</h3>
                                                <p class="muted">
                                                    Fusce dapibus, tellus ac cursus commodo, tortor mauris nibh.
                                                </p>
                                                <p class="text-warning">
                                                    Etiam porta sem malesuada magna mollis euismod.
                                                </p>
                                                <p class="text-error">
                                                    Donec ullamcorper nulla non metus auctor fringilla.
                                                </p>
                                                <p class="text-info">
                                                    Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis.
                                                </p>
                                                <p class="text-success">
                                                    Duis mollis, est non commodo luctus, nisi erat porttitor ligula.
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <h3>Headings</h3>
                                                <h1>h1. Heading 1</h1>
                                                <h2>h2. Heading 2</h2>
                                                <h3>h3. Heading 3</h3>
                                                <h4>h4. Heading 4</h4>
                                                <h5>h5. Heading 5</h5>
                                                <h6>h6. Heading 6</h6>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h3>Address</h3>
                                                <div class="well">
                                                    <address>
                                                        <strong>Loop, Inc.</strong><br>
                                                        795 Park Ave, Suite 120<br>
                                                        San Francisco, CA 94107<br>
                                                        <abbr title="Phone">P:</abbr> (234) 145-1810 </address>
                                                    <address>
                                                        <strong>Full Name</strong><br>
                                                        <a href="mailto:#">
                                                            first.last@email.com </a>
                                                    </address>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h3>Some more text here</h3>
                                                <p>
                                                    Nullam quis risus eget urna mollis ornare vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula ut id elit.
                                                </p>
                                                <p>
                                                    Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Donec sed odio dui.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div><div class="portlet">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-gift"></i>General
                                        </div>
                                        <div class="tools">
                                            <a href="javascript:;" class="collapse" data-original-title="" title="">
                                            </a>
                                            <a href="#portlet-config" data-toggle="modal" class="config" data-original-title="" title="">
                                            </a>
                                            <a href="javascript:;" class="reload" data-original-title="" title="">
                                            </a>
                                            <a href="javascript:;" class="remove" data-original-title="" title="">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h3>Sample text with lead body</h3>
                                                <p class="lead">
                                                    Lead body. Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                                                </p>
                                                <p>
                                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.
                                                </p>
                                                <p>
                                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <h3>Sample text</h3>
                                                <p>
                                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur bibendum ornare dolor, quis ullamcorper ligula sodales at. Nulla tellus elit, varius non commodo eget, mattis vel eros. In sed ornare nulla.
                                                </p>
                                                <p>
                                                    Nullam quis risus eget urna mollis ornare vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula ut id elit.
                                                </p>
                                                <p>
                                                    Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Donec sed odio dui.
                                                </p>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h3>Texts</h3>
                                                <p class="muted">
                                                    Fusce dapibus, tellus ac cursus commodo, tortor mauris nibh.
                                                </p>
                                                <p class="text-warning">
                                                    Etiam porta sem malesuada magna mollis euismod.
                                                </p>
                                                <p class="text-error">
                                                    Donec ullamcorper nulla non metus auctor fringilla.
                                                </p>
                                                <p class="text-info">
                                                    Aenean eu leo quam. Pellentesque ornare sem lacinia quam venenatis.
                                                </p>
                                                <p class="text-success">
                                                    Duis mollis, est non commodo luctus, nisi erat porttitor ligula.
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <h3>Headings</h3>
                                                <h1>h1. Heading 1</h1>
                                                <h2>h2. Heading 2</h2>
                                                <h3>h3. Heading 3</h3>
                                                <h4>h4. Heading 4</h4>
                                                <h5>h5. Heading 5</h5>
                                                <h6>h6. Heading 6</h6>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h3>Address</h3>
                                                <div class="well">
                                                    <address>
                                                        <strong>Loop, Inc.</strong><br>
                                                        795 Park Ave, Suite 120<br>
                                                        San Francisco, CA 94107<br>
                                                        <abbr title="Phone">P:</abbr> (234) 145-1810 </address>
                                                    <address>
                                                        <strong>Full Name</strong><br>
                                                        <a href="mailto:#">
                                                            first.last@email.com </a>
                                                    </address>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h3>Some more text here</h3>
                                                <p>
                                                    Nullam quis risus eget urna mollis ornare vel eu leo. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam id dolor id nibh ultricies vehicula ut id elit.
                                                </p>
                                                <p>
                                                    Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor. Duis mollis, est non commodo luctus, nisi erat porttitor ligula, eget lacinia odio sem nec elit. Donec sed odio dui.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
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
