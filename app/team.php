<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 9/19/2017
 * Time: 7:31 AM
 */
include '../dashboard/assets/app/init.php'
?>
<div class="main">
    <div class="container">
        <ul class="breadcrumb">
            <li><a class="linker" data-href="app/home.php" data-title="Home">Home</a></li>
            <li>About Us</li>
            <li class="active"><a class="linker" data-href="app/team.php" data-title="The Team">The Team</a></li>
        </ul>
        <!-- BEGIN SIDEBAR & CONTENT -->
        <div class="row margin-bottom-40">
            <!-- BEGIN CONTENT -->
            <div class="col-md-12 col-sm-12">
                <h1>About Us</h1>
                <div class="content-page">

                    <div class="row margin-bottom-30">
                        <!-- BEGIN INFO BLOCK -->
                        <div class="col-md-12">
                            <h2 class="no-top-space">Vero eos et accusamus</h2>
                            <p>At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi.</p>
                            <p>Idest laborum et dolorum fuga. Et harum quidem rerum et quas molestias excepturi sint occaecati facilis est et expedita distinctio lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut non libero consectetur adipiscing elit magna. Sed et quam lacus.</p>
                        </div>
                        <!-- END INFO BLOCK -->
                    </div>
                    <div class="row front-team">
                        <ul class="list-unstyled">
                            <?php
                            $joshua = mysql_fetch_array(mysql_query("SELECT user_token FROM fmo_users WHERE user_id=1000002"));
                            ?>
                            <li class="col-md-3">
                                <div class="thumbnail">
                                    <img alt="" src="<?php echo picture($joshua['user_token']); ?>">
                                    <h3>
                                        <strong><?php echo name($joshua['user_token']); ?></strong>
                                        <small>Chief Executive Officer / CEO</small>
                                    </h3>
                                    <p>Personally investing his own company into For Movers Only was just the beginning. Now helping other movers is his mindset.</p>
                                    <ul class="social-icons social-icons-color">
                                        <li><a class="facebook" data-original-title="Facebook" href="javascript:;"></a></li>
                                        <li><a class="twitter" data-original-title="Twitter" href="javascript:;"></a></li>
                                        <li><a class="googleplus" data-original-title="Goole Plus" href="javascript:;"></a></li>
                                        <li><a class="linkedin" data-original-title="Linkedin" href="javascript:;"></a></li>
                                    </ul>
                                </div>
                            </li>
                            <?php
                            $logina = mysql_fetch_array(mysql_query("SELECT user_token FROM fmo_users WHERE user_id=1000064"));
                            ?>
                            <li class="col-md-3">
                                <div class="thumbnail">
                                    <img alt="" src="<?php echo picture($logina['user_token']); ?>">
                                    <h3>
                                        <strong><?php echo name($logina['user_token']); ?></strong>
                                        <small>Chief Technology Officer / CTO</small>
                                    </h3>
                                    <p>The brains behind the software. Through software engineering, Logan plans to make business powerful & efficient--without complications.</p>
                                    <ul class="social-icons social-icons-color">
                                        <li><a class="facebook" data-original-title="Facebook" href="javascript:;"></a></li>
                                        <li><a class="twitter" data-original-title="Twitter" href="javascript:;"></a></li>
                                        <li><a class="googleplus" data-original-title="Goole Plus" href="javascript:;"></a></li>
                                        <li><a class="linkedin" data-original-title="Linkedin" href="javascript:;"></a></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>
            <!-- END CONTENT -->
        </div>
        <!-- END SIDEBAR & CONTENT -->
    </div>
</div>

