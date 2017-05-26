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
        $phone  = $_POST['number'];
        $findCustomers = mysql_query("SELECT user_id, user_pic, user_fname, user_lname, user_token, user_email, user_phone, user_creation FROM fmo_users WHERE user_phone='".mysql_real_escape_string($phone)."' AND (user_group='3' AND user_creator='".mysql_real_escape_string($_SESSION['cuid'])."') ORDER BY user_id ASC");
        $found         = mysql_num_rows($findCustomers);
        $findLocation  = mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'");
        $location      = mysql_fetch_array($findLocation);

        if($found > 0){
            while($customer = mysql_fetch_array($findCustomers)){
                ?>
                <div class="row">
                    <div class="col-md-12 page-404">
                        <div class="number font-red">
                            <i style="font-size: 100px;" class="icon-user-following"></i>
                        </div>
                        <div class="details">
                            <h3>Logan found <strong><?php echo $customer['user_fname']." ".$customer['user_lname']; ?></strong> in <?php echo $location['location_name']; ?></h3>
                            <p>
                                Matched information: <strong><?php echo clean_phone($_POST['number']); ?></strong><br>
                                <strong>How would you like to continue with this customer?</strong><br/>
                            </p>
                            <div class="input-group input-medium">
                            <span class="input-group-btn">
                                <button type="button" class="btn red-stripe load_page" data-href="assets/pages/profile.php?uuid=<?php echo $customer['user_token']; ?>&luid=<?php echo $_GET['luid']; ?>" data-page-title="Profile">View their profile</button>
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <br/><br/><br/>
                    </div>
                </div>
                <?
            }
        } else {
            ?>
            <div class="row">
                <div class="col-md-12 page-404">
                    <div class="number font-red">
                        <i style="font-size: 100px;" class="icon-user-unfollow"></i>
                    </div>
                    <div class="details">
                        <h3>No results found.</h3>
                        <p>
                            No results were found for <?php echo $location['location_name']; ?>.<br>
                            <strong>Click below if you need to add a new customer</strong>.<br/>
                        </p>
                        <div class="input-group input-medium">
                            <span class="input-group-btn">
                                <button type="button" class="btn red-stripe load_page" data-href="assets/pages/create_customer.php?luid=<?php echo $_GET['luid']; ?>&p=<?php echo $_POST['number']; ?>" data-page-title="Create Customer">Add new customer</button>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <br/><br/><br/>
                </div>
            </div>
            <?php
        }
    }
}