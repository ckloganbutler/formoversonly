<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/10/2017
 * Time: 5:41 PM
 */
session_start();
include 'init.php';

if(isset($_SESSION['uuid'])){
    if(isset($_POST)){
        $phone  = $_POST['phone'];
        $findCustomers = mysql_query("SELECT user_id, user_pic, user_fname, user_lname, user_token FROM fmo_users WHERE user_phone='".mysql_real_escape_string($phone)."' AND user_creator_location='".mysql_real_escape_string($_GET['luid'])."'");
        $found         = mysql_num_rows($findCustomers);
        $findLocation  = mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'");
        $location      = mysql_fetch_array($findLocation);
        ?>
        <div class="page-content">
            <h3 class="page-title">
                Search Results
            </h3>
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li>
                        <i class="fa fa-home"></i>
                        <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>">Dashboard</a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <a><?php echo $location['location_name']; ?></a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <a class="load_page" data-href="assets/pages/customers.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Customers">Customers</a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <a class="load_page" data-href="assets/pages/customers.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Customers">Search Results</a>
                    </li>
                </ul>
            </div>
            <?php
            if($found > 0){
                while($customer = mysql_fetch_array($findCustomers)){
                    ?>
                    <div class="row portfolio-block">
                        <div class="col-md-5 col-sm-12 portfolio-text">
                            <img src="<?php echo $customer['user_pic']; ?>" alt="">
                            <div class="portfolio-text-info">
                                <h4><?php echo $customer['user_fname']." ".$customer['user_lname']; ?></h4>
                                <p>
                                    Lorem ipsum dolor sit consectetuer adipiscing elit.
                                </p>
                            </div>
                        </div>
                        <div class="col-md-5 portfolio-stat">
                            <div class="portfolio-info">
                                Moves Booked <span>0</span>
                            </div>
                            <div class="portfolio-info">
                                Returning <span class="label label-sm label-success">YES</span>
                            </div>
                            <div class="portfolio-info">
                                Money Spent <span>$7,060.25</span>
                            </div>
                        </div>
                        <div class="col-md-2 col-sm-12 portfolio-btn btn-red">
                            <a href="javascript:;" class="btn bigicn-only btn-red"><span>View Profile</span></a>
                        </div>
                    </div>
                    <?
                }
            } else {
                ?>
                <div class="row">
                    <div class="col-md-12 page-404">
                        <div class="number font-red">
                            <i style="font-size: 128px;" class="fa fa-times"></i>
                        </div>
                        <div class="details">
                            <h3>No results found.</h3>
                            <p>
                                No results were found for <?php echo $location['location_name']; ?>.<br>
                                <strong><a href="">Click here</a> if you need to add a new customer.</strong>.
                            </p>
                        </div>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
       <?php
    }
}