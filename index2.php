<?php
include 'dashboard/assets/app/init.php'
?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" style="background: #272626!important;">
<!--<![endif]-->
<head>
    <meta charset="utf-8">
    <title>#1 Moving Management Software - www.FORMOVERSONLY.com</title>

    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

    <meta content="#1 Moving Management Software - www.FORMOVERSONLY.com" name="description">
    <meta content="#1 Moving Management Software - www.FORMOVERSONLY.com" name="keywords">
    <meta content="Capital Kingdom" name="author">

    <meta property="og:site_name" content="#1 Moving Management Software - www.FORMOVERSONLY.com">
    <meta property="og:title" content="#1 Moving Management Software - www.FORMOVERSONLY.com">
    <meta property="og:description" content="Complete management software for moving companies.">
    <meta property="og:type" content="website">
    <meta property="og:image" content=""><!-- link to image for socio -->
    <meta property="og:url" content="https://www.formoversonly.com">

    <link rel="shortcut icon" href="favicon.ico">

    <!-- Fonts START -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700|PT+Sans+Narrow|Source+Sans+Pro:200,300,400,600,700,900&amp;subset=all" rel="stylesheet" type="text/css">
    <!-- Fonts END -->

    <!-- Global styles START -->
    <link href="dashboard/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <link href="dashboard/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Global styles END -->

    <!-- Page level plugin styles START -->
    <link href="dashboard/assets/global/plugins/fancybox/source/jquery.fancybox.css" rel="stylesheet">
    <link href="dashboard/assets/global/plugins/carousel-owl-carousel/owl-carousel/owl.carousel.css" rel="stylesheet">
    <link href="dashboard/assets/global/plugins/slider-revolution-slider/rs-plugin/css/settings.css" rel="stylesheet">
    <link href="dashboard/assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css"/>
    <!-- Page level plugin styles END -->

    <!-- Theme styles START -->
    <link href="dashboard/assets/global/css/components.css" rel="stylesheet">
    <link href="dashboard/assets/frontend/layout/css/style.css" rel="stylesheet">
    <link href="dashboard/assets/frontend/pages/css/style-revolution-slider.css" rel="stylesheet"><!-- metronic revo slider styles -->
    <link href="dashboard/assets/frontend/layout/css/style-responsive.css" rel="stylesheet">
    <link href="dashboard/assets/frontend/layout/css/themes/red.css" rel="stylesheet" id="style-color">
    <link href="dashboard/assets/frontend/layout/css/custom.css" rel="stylesheet">
    <!-- Theme styles END -->
</head>
<body class="corporate">
<div class="pre-header">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-sm-6 additional-shop-info">
                <ul class="list-unstyled list-inline">
                    <li><i class="fa fa-phone"></i><span>+1 317 671 6774</span></li>
                    <li><i class="fa fa-envelope-o"></i><span>info@formoversonly.com</span></li>
                </ul>
            </div>
            <div class="col-md-6 col-sm-6 additional-nav">
                <ul class="list-unstyled list-inline pull-right">
                    <li><a href="">Log In</a></li>
                    <li><a href="">Registration</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>
<div class="header">
    <div class="container">
        <a class="site-logo linker" data-href="app/home.php" data-title="Home" style="color: white; text-transform: uppercase; font-size: 23px; letter-spacing: .01em; word-spacing: 1px; width: auto; margin-top: 2px; font-weight: 300; margin-bottom: 0px;">
            <span style='color: #444'>FOR</span><span style='color: #cb5a5e'>MOVERS</span><span style='color: #444'>ONLY&trade;</span>
        </a>
        <a href="javascript:void(0);" class="mobi-toggler"><i class="fa fa-bars"></i></a>
        <div class="header-navigation pull-right font-transform-inherit">
            <ul>
                <li class="active anc">
                    <a class="linker" data-href="app/home.php" data-title="Home">Home</a>
                </li>
                <li class="dropdown anc">
                    <a class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="javascript:;">
                        About Us

                    </a>

                    <ul class="dropdown-menu">
                        <li><a class="linker" data-href="app/team.php" data-title="The Team">The Team</a></li>
                        <li><a class="linker" data-href="app/features.php" data-title="Software Features">Software Features</a></li>
                        <li><a class="linker" data-href="app/pricing.php" data-title="Pricing">Pricing</a></li>
                    </ul>
                </li>
                <li class="dropdown dropdown-megamenu anc">
                    <a class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="javascript:;">
                        Company Directory
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <div class="header-navigation-content">
                                <div class="row">
                                    <div class="col-md-4 header-navigation-col">
                                        <h4>Featured</h4>
                                        <ul>
                                            <li><a href=""><i class="fa fa-star font-yellow-gold"></i> Here To There Movers</a></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4 header-navigation-col">
                                        <h4><i class="fa fa-location-arrow"></i> Near you</h4>
                                        <ul>
                                            <li class="text-muted"><a>(none found)</a></li>
                                        </ul>
                                    </div>
                                    <div class="col-md-4 header-navigation-col">
                                        <h4>Commercial</h4>
                                        <ul>
                                            <li class="text-muted"><a>(none found)</a></li>
                                        </ul>

                                        <h4>Residential</h4>
                                        <ul>
                                            <li class="text-muted"><a>(none found)</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </li>
                <li class="dropdown anc">
                    <a class="dropdown-toggle" data-toggle="dropdown" data-target="#" href="javascript:;">
                        Documentation
                    </a>

                    <ul class="dropdown-menu">
                        <li><a href="">Blah</a></li>
                    </ul>
                </li>
                <li class="menu-search">
                    <span class="sep"></span>
                    <i class="fa fa-search search-btn"></i>
                    <div class="search-box">
                        <form action="#">
                            <div class="input-group">
                                <input type="text" placeholder="Search" class="form-control">
                                <span class="input-group-btn">
                      <button class="btn btn-primary" type="submit">Search</button>
                    </span>
                            </div>
                        </form>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
<div id="page_content">

</div>

<div class="pre-footer">
    <div class="container">
        <div class="row">
            <!-- BEGIN BOTTOM ABOUT BLOCK -->
            <div class="col-md-4 col-sm-6 pre-footer-col">
                <h2>About us</h2>
                <p>Our software takes the everyday tasks of your moves and makes them fast, efficient, and powerful. By utilizing the tools provided by <strong>For Movers Only™</strong> you're already putting yourself at an advantage.</p>
            </div>
            <!-- END BOTTOM ABOUT BLOCK -->

            <!-- BEGIN BOTTOM CONTACTS -->
            <div class="col-md-4 col-sm-6 pre-footer-col">
                <h2>Our Contacts</h2>
                <address class="margin-bottom-40">
                    6800 E 30th Street<br>
                    Indianapolis, Indiana, US<br>
                    Phone: +1 317 671 6774<br>
                    Email: <a href="mailto:info@formoversonly.com">info@formoversonly.com</a><br>
                </address>

                <div class="pre-footer-subscribe-box pre-footer-subscribe-box-vertical">
                    <h2>Newsletter</h2>
                    <p>Subscribe to our newsletter and stay up to date with the latest news and deals!</p>
                    <form action="#">
                        <div class="input-group">
                            <input type="text" placeholder="youremail@mail.com" class="form-control">
                            <span class="input-group-btn">
                    <button class="btn btn-primary" type="submit">Subscribe</button>
                  </span>
                        </div>
                    </form>
                </div>
            </div>
            <!-- END BOTTOM CONTACTS -->

            <!-- BEGIN TWITTER BLOCK -->
            <div class="col-md-4 col-sm-6 pre-footer-col">
                <h2 class="margin-bottom-0">Latest Tweets</h2>
                <a class="twitter-timeline" href="https://twitter.com/twitterapi" data-tweet-limit="2" data-theme="dark" data-link-color="#57C8EB" data-widget-id="" data-chrome="noheader nofooter noscrollbar noborders transparent">Loading tweets by @NASA...</a>
            </div>
            <!-- END TWITTER BLOCK -->
        </div>
    </div>
</div>
<div class="footer">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-sm-6 padding-top-10">
                &copy; 2017-2017 For Movers Only&trade;. Created with <i class="fa fa-heart font-red"></i> by <a href="https://www.captialkingdom.com/" target="_blank">Captial Kingdom</a>.
            </div>
            <div class="col-md-6 col-sm-6">
                <ul class="social-footer list-unstyled list-inline pull-right">
                    <li><a href="javascript:;"><i class="fa fa-facebook"></i></a></li>
                    <li><a href="javascript:;"><i class="fa fa-google-plus"></i></a></li>
                    <li><a href="javascript:;"><i class="fa fa-dribbble"></i></a></li>
                    <li><a href="javascript:;"><i class="fa fa-linkedin"></i></a></li>
                    <li><a href="javascript:;"><i class="fa fa-twitter"></i></a></li>
                    <li><a href="javascript:;"><i class="fa fa-skype"></i></a></li>
                    <li><a href="javascript:;"><i class="fa fa-github"></i></a></li>
                    <li><a href="javascript:;"><i class="fa fa-youtube"></i></a></li>
                    <li><a href="javascript:;"><i class="fa fa-dropbox"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Load javascripts at bottom, this will reduce page load time -->
<!-- BEGIN CORE PLUGINS (REQUIRED FOR ALL PAGES) -->
<!--[if lt IE 9]>
<script src="dashboard/assets/global/plugins/respond.min.js"></script>
<![endif]-->
<script src="dashboard/assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="dashboard/assets/global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<script src="dashboard/assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="dashboard/assets/frontend/layout/scripts/back-to-top.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->

<!-- BEGIN PAGE LEVEL JAVASCRIPTS (REQUIRED ONLY FOR CURRENT PAGE) -->
<script src="dashboard/assets/global/plugins/fancybox/source/jquery.fancybox.pack.js" type="text/javascript"></script><!-- pop up -->
<script src="dashboard/assets/global/plugins/carousel-owl-carousel/owl-carousel/owl.carousel.min.js" type="text/javascript"></script><!-- slider for products -->
<script src="dashboard/assets/global/plugins/bootstrap-toastr/toastr.min.js"></script>
<!-- BEGIN RevolutionSlider -->

<script src="dashboard/assets/global/plugins/slider-revolution-slider/rs-plugin/js/jquery.themepunch.revolution.min.js" type="text/javascript"></script>
<script src="dashboard/assets/global/plugins/slider-revolution-slider/rs-plugin/js/jquery.themepunch.tools.min.js" type="text/javascript"></script>
<script src="dashboard/assets/frontend/pages/scripts/revo-slider-init.js" type="text/javascript"></script>
<!-- END RevolutionSlider -->

<script src="dashboard/assets/frontend/layout/scripts/layout.js" type="text/javascript"></script>
<script type="text/javascript">
    jQuery(document).ready(function() {
        Layout.init();
        Layout.initOWL();
        Layout.initFixHeaderWithPreHeader(); /* Switch On Header Fixing (only if you have pre-header) */
        Layout.initNavScrolling();
        var hash = window.location.hash;
        <?php
        $arr[] = array();
        $companies = mysql_query("SELECT user_company_name, user_company_token FROM fmo_users WHERE user_group=1");
        if(mysql_num_rows($companies) > 0){
            while($company = mysql_fetch_assoc($companies)){
                $arr['companies'][] = array(
                    str_replace(" ", "", strtolower($company['user_company_name'])),
                    ''.$company['user_company_token'].''
                );
            }
        }else{
            $arr[] = NULL;
        }
        ?>
        function getIndexOf(a,v) {
            var l = a.length;
            for (var k=0;k<l;k++) {
                if (a[k].color==v) {
                    return k;
                }
            }
            return false;
        }
        var companies = <?php echo json_encode($arr['companies']); ?>;
        if(companies.indexOf(companies, hash.replace('#', ''))){
            var index = getIndexOf(companies, hash.replace('#', ''));
            console.log(index);
            /*$.ajax({
                url: 'app/company.php?cuid='+ companies[index],
                success: function(data) {
                    $('#page_content').html(data);
                    window.history.pushState("Details", "#1 Moving Management Software - www.FORMOVERSONLY.com", hash);
                },
                error: function() {
                    $.ajax({
                        url: 'app/404.php',
                        success: function(data) {
                            $('#page_content').html(data);
                            window.history.pushState("Details", "#1 Moving Management Software - www.FORMOVERSONLY.com", "#app/404.php");
                        },
                        error: function() {
                            toastr.error("<strong>Oops!</strong><br/>An unexpected error has occured. Error #10211.");
                        }
                    });
                }
            });*/
        } else if(hash.length > 0){
            $.ajax({
                url: hash.replace('#', ''),
                success: function(data) {
                    $('#page_content').html(data);
                    window.history.pushState("Details", "#1 Moving Management Software - www.FORMOVERSONLY.com", hash);
                },
                error: function() {
                    $.ajax({
                        url: 'app/404.php',
                        success: function(data) {
                            $('#page_content').html(data);
                            window.history.pushState("Details", "#1 Moving Management Software - www.FORMOVERSONLY.com", "#app/404.php");
                        },
                        error: function() {
                            toastr.error("<strong>Oops!</strong><br/>An unexpected error has occured. Error #10211.");
                        }
                    });
                }
            });
        } else {
            $.ajax({
                url: 'app/home.php',
                success: function(data) {
                    $('#page_content').html(data);
                    window.history.pushState("Details", "#1 Moving Management Software - www.FORMOVERSONLY.com", "#app/home.php");
                },
                error: function() {
                    $.ajax({
                        url: 'app/404.php',
                        success: function(data) {
                            $('#page_content').html(data);
                            window.history.pushState("Details", "#1 Moving Management Software - www.FORMOVERSONLY.com", "#app/404.php");
                        },
                        error: function() {
                            toastr.error("<strong>Oops!</strong><br/>An unexpected error has occured. Error #10211.");
                        }
                    });
                }
            });
        }


        $(document).on('click', '.linker', function(){
            var url   = $(this).data('href');
            var title = $(this).data('title');
            $('.anc').removeClass('active');
            $('li').removeClass('active');
            $(this).closest('.anc').addClass('active');
            $(this).closest('li').addClass('active');
            $.ajax({
                url: url,
                type: 'POST',
                success: function(data){
                    $('#page_content').html(data);
                    window.history.pushState("Details", title + " - #1 Moving Management Software - www.FORMOVERSONLY.com", "#" + url);
                }, error: function(data){
                    $.ajax({
                        url: 'app/500.php',
                        success: function(data) {
                            $('#page_content').html(data);
                            window.history.pushState("Details", "#1 Moving Management Software - www.FORMOVERSONLY.com", "#api/500.php");
                        },
                        error: function() {
                            toastr.error("<strong>Oops!</strong><br/>An unexpected error has occured. Error #10211.");
                        }
                    });
                }
            });
        });
    });
</script>
<!-- END PAGE LEVEL JAVASCRIPTS -->
</body>
</html>