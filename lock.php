<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/4/2017
 * Time: 3:07 PM
 */
include 'dashboard/assets/app/init.php';


?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title>Locked [+] For Movers Only</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta http-equiv="Content-type" content="text/html; charset=utf-8">
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
    <link href="dashboard/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="dashboard/assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="dashboard/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="dashboard/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL STYLES -->
    <link href="dashboard/assets/admin/pages/css/lock2.css" rel="stylesheet" type="text/css"/>
    <!-- END PAGE LEVEL STYLES -->
    <!-- BEGIN THEME STYLES -->
    <link href="dashboard/assets/global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="dashboard/assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="dashboard/assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
    <link href="dashboard/assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="dashboard/assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
    <!-- END THEME STYLES -->
    <link rel="shortcut icon" href="dashboard/favicon.ico"/>
</head>
<!-- END HEAD -->
<!-- BEGIN BODY -->
<body class="login">
<?php
if(isset($_GET['uuid'])){
    $user = mysql_query("SELECT user_fname, user_lname, user_email, user_pic FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'");
    if(mysql_num_rows($user) > 0){
        $info = mysql_fetch_array($user);
        ?>
        <div class="page-lock">
            <div class="page-logo">
                <a class="brand" href="//www.formoversonly.com/">
                    <img src="dashboard/assets/admin/layout/img/logo-big.png" alt="logo"/>
                </a>
            </div>
            <div class="page-body">
                <img class="page-lock-img" src="<?php echo $info['user_pic']; ?>" alt="">
                <div class="page-lock-info">
                    <h1> <?php echo $info['user_fname']." ".$info['user_lname']; ?></h1>
                    <span class="email"><?php echo secret_mail($info['user_email']); ?></span>
                    <br/>
                    <form class="form-inline" action="dashboard/assets/app/login.php?t=aXn" method="POST" style="padding-top: 0; margin-top: 0;">
                        <div class="input-group input-medium input-icon">
                            <i class="fa fa-lock font-red"></i>
                            <input class="form-control" type="password" autocomplete="off" placeholder="Password" name="password">
                            <span class="input-group-btn">
                             <button type="submit" class="btn red icn-only"><i class="m-icon-swapright m-icon-white"></i></button>
                            </span>
                        </div>
                        <!-- /input-group -->
                        <div class="relogin">
                            <a href="index.php">
                                Not <?php echo $info['user_fname']." ".$info['user_lname']; ?>?</a>
                        </div>
                        <input style="visibility: hidden;" type="text" autocomplete="off" value="<?php echo $info['user_email']; ?>" name="email"/>
                    </form>
                </div>
            </div>
            <div class="page-footer">
                2017 &copy; For Movers Only | Powered by <strong>HTT 5.0</strong> | <a href="index.php">Back to login <i class="fa fa-arrow-right"></i></a>
            </div>
        </div>
        <?php
    } else {
        header("Location: index.php?err=lock_uuid");
    }
}
?>
<!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
<!-- BEGIN CORE PLUGINS -->
<!--[if lt IE 9]>
<script src="dashboard/assets/global/plugins/respond.min.js"></script>
<script src="dashboard/assets/global/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="dashboard/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="dashboard/assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<script src="dashboard/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="dashboard/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="dashboard/assets/global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="dashboard/assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="dashboard/assets/global/plugins/backstretch/jquery.backstretch.min.js" type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<script src="dashboard/assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="dashboard/assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="dashboard/assets/admin/pages/scripts/lock.js"></script>
<script>
    jQuery(document).ready(function() {
        Metronic.init(); // init metronic core components
        Layout.init(); // init current layout
        Lock.init();
    });
</script>
<!-- END JAVASCRIPTS -->
</body>
<!-- END BODY -->
</html>