<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 10/1/2017
 * Time: 1:34 AM
 */
require '../app/init.php';
$event    = mysql_fetch_array(mysql_query("SELECT event_company_token, event_id, event_token, event_location_token, event_booking, event_user_token, event_name, event_date_start, event_date_end, event_time, event_zip, event_truckfee, event_laborrate, event_countyfee, event_status, event_email, event_phone, event_type, event_subtype, event_additions, event_comments, event_by_user_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));
$location = mysql_fetch_array(mysql_query("SELECT location_name, location_token, location_max_trucks, location_max_men, location_max_counties, location_creditcard_fee FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));
$user     = mysql_fetch_array(mysql_query("SELECT user_id, user_fname, user_lname, user_email, user_phone, user_token FROM fmo_users WHERE user_token='".mysql_real_escape_string($event['event_user_token'])."'"));

?>
<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<?php
?>
<html lang="en">
<!--<![endif]-->
<head>
    <meta charset="utf-8"/>
    <title>Claim form | <?php echo $event['event_name']; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta content="For Movers Only UI Description" name="description">
    <meta content="For Movers Only Keywords" name="keywords">
    <meta content="loganck" name="author">
    <meta property="og:site_name" content="https://www.formoversonly.com">
    <meta property="og:title" content="For Movers Only">
    <meta property="og:description" content="Automated Fleet Software for Moving Companies">
    <meta property="og:type" content="website">
    <meta property="og:image" content="">
    <meta property="og:url" content="https://www.formoversonly.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="../global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="../global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="../global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link href="../global/plugins/select2/select2.css" rel="stylesheet" type="text/css"/>
    <link href="../global/plugins/dropzone/css/dropzone.css" rel="stylesheet"/>
    <link href="../admin/pages/css/login3.css" rel="stylesheet" type="text/css"/>
    <link href="../global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="../global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="../admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
    <link href="../admin/layout/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="../admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="../../favicon.ico"/>
</head>
<body class="login">
<div class="logo"  style="text-align: center; color: white; text-transform: uppercase; font-size: 23px; letter-spacing: .01em; word-spacing: 1px; width: auto; margin-top: 7px; font-weight: 300; margin-bottom: 0px;">
    <?php
    if(empty($_GET['cuid'])){
        $_GET['cuid'] = $event['event_company_token'];
    }
    $name = companyName($_GET['cuid']);
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
</div>
<div class="menu-toggler sidebar-toggler">
</div>
<div class="content" style="min-width: 930px!important;">
    <div class="login-form" >
        <table class="table table-striped table-hover table-bordered">
            <thead>
            <tr>
                <th width="50%" class="text-center">
                    <img class="img-responsive" src="../../assets/global/img/htt.png" style="display: block; margin: 0 auto!important;"/>
                </th>
                <th width="50%" class="text-center">
                    <?php echo companyName($event['event_company_token']); ?> <br/>
                    <?php echo companyAddress($event['event_company_token']); ?> <br/>
                    <?php echo clean_phone(locationPhone($event['event_location_token'])); ?> - <?php echo clean_phone(companyPhone3($event['event_company_token'])); ?> <br/>
                    <?php echo companyLicenses($event['event_company_token']); ?> <br/>
                </th>
            </tr>
            <tr>
                <th colspan="2" class="text-center uppercasel" style="font-size: 20px;">RECEIPT / ORDER FOR SERVICE - Event #<?php echo $event['event_id']; ?> (<?php echo $event['event_type']." ".$event['event_subtype']; ?>)</th>
            </tr>
            <tr>
                <th width="50%">
                    <i class="fa fa-user"></i> <strong><?php echo name($event['event_user_token']); ?></strong> / <i class="fa fa-tag"></i> <strong><?php echo $event['event_name']; ?></strong>, <?php echo $event['event_type']; ?> <?php echo $event['event_subtype']; ?> <br/>
                    <i class="fa fa-phone"></i> <strong><?php echo clean_phone($event['event_phone']); ?></strong> or <strong><?php echo clean_phone($user['user_phone']); ?></strong>
                    <hr style="width: 25%; margin-top: 5px; margin-bottom: 8px;"/>
                    Pick up location(s): <br/>
                    <?php
                    $pickups = mysql_query("SELECT address_id, address_address, address_suite, address_city, address_state, address_zip, address_suite, address_closest_intersection, address_county, address_bedrooms, address_stairs, address_distance, address_comments FROM fmo_locations_events_addresses WHERE address_event_token='".mysql_real_escape_string($event['event_token'])."' AND address_type=1");
                    if(mysql_num_rows($pickups) > 0){
                        $pk = 0;
                        while($pickup = mysql_fetch_assoc($pickups)){
                            $pk++;
                            ?>
                            <strong>
                                <?php echo $pickup['address_address']; ?>,
                                <?php echo $pickup['address_city']; ?>,
                                <?php echo $pickup['address_state']; ?>,
                                <?php echo $pickup['address_zip']; ?> <?php echo $pickup['address_suite']; ?></strong> <br/>
                            <?php
                            $extt = 0;
                            if(!empty($pickup['address_stairs'])){
                                echo "<strong>Stairs</strong>: ".$pickup['address_stairs']." ";
                                $extt++;
                            }
                            if(!empty($pickup['address_bedrooms'])){
                                echo "<strong>Bedrooms</strong>: ".$pickup['address_bedrooms']." ";
                                $extt++;
                            }
                            if(!empty($pickup['address_distance'])){
                                echo "<strong>Distance</strong>: ".$pickup['address_distance']." ";
                                $extt++;
                            }
                            if($extt > 0){
                                echo "<br/>";
                            }
                        }
                    } else {
                        ?>
                        (no pickup locations have been added)
                        <?php
                    }
                    ?>
                </th>
                <th width="50%">
                    Destination location(s): <br/>
                    <?php
                    $dests = mysql_query("SELECT address_address, address_suite, address_city, address_state, address_zip, address_suite, address_closest_intersection, address_county, address_stairs, address_distance, address_comments FROM fmo_locations_events_addresses WHERE address_event_token='".mysql_real_escape_string($event['event_token'])."' AND address_type=2");
                    if(mysql_num_rows($dests) > 0){
                        $pk = 0;
                        while($dest = mysql_fetch_assoc($dests)){
                            $pk++
                            ?>
                            <strong>
                                <?php echo $dest['address_address']; ?>,
                                <?php echo $dest['address_city']; ?>,
                                <?php echo $dest['address_state']; ?>,
                                <?php echo $dest['address_zip']; ?> <?php echo $dest['address_suite']; ?></strong> <br/>
                            <?php
                            $extt = 0;
                            if(!empty($dest['address_stairs'])){
                                echo "<strong>Stairs</strong>: ".$dest['address_stairs']." ";
                                $extt++;
                            }
                            if(!empty($dest['address_distance'])){
                                echo "<strong>Distance</strong>: ".$dest['address_distance']." ";
                                $extt++;
                            }
                            if($extt > 0){
                                echo "<br/>";
                            }
                        }
                    } else {
                        ?>
                        (no destination locations have been added)
                        <?php
                    }
                    ?>
                </th>
            </tr>
            <tr>
                <th width="50%" class="text-center" style="font-size: 15px;">
                    <strong>Agreed Start</strong>: <?php echo date('d-m-Y', strtotime($event['event_date_start'])); ?>
                </th>
                <th width="50%" class="text-center" style="font-size: 15px;">
                    <strong>Agreed Finish</strong>: <?php echo date('d-m-Y', strtotime($event['event_date_end'])); ?>
                </th>
            </tr>
            <tr>
                <th colspan="2"><strong>Comments:</strong> <?php echo $event['event_comments']; ?></th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td colspan="2">
                    <table class="table table-striped table-hover table-bordered">
                        <thead>
                        <tr>
                            <th>Item</th>
                            <th>Description</th>
                            <th>Unit Cost</th>
                            <th>Quantity</th>
                            <th class="text-right">Line Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        $findItems = mysql_query("SELECT item_id, item_item, item_desc, item_qty, item_cost, item_total, item_redeemable FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."'");
                        $iTotalRecords = mysql_num_rows($findItems);

                        $records = array();
                        $records["data"] = array();

                        while($items = mysql_fetch_assoc($findItems)) {
                            if($items['item_redeemable'] == 2){
                                $records["data"][] = array(
                                    '<span class="text-success">'.$items['item_item'].'</span>',
                                    '<span class="text-success">'.$items['item_desc'].'</span>',
                                    '<span class="text-success">'.$items['item_qty'].'</span>',
                                    '<span class="text-success">'.$items['item_cost'].'</span>',
                                    '<strong class="text-danger pull-right">'.$items['item_total'].'</strong>'
                                );
                            } else {
                                $records["data"][] = array(
                                    ''.$items['item_item'].'',
                                    '<a>'.$items['item_desc'].'</a>',
                                    '<a>'.$items['item_qty'].'</a>',
                                    '<a>'.$items['item_cost'].'</a>',
                                    '<strong class="text-danger pull-right">'.$items['item_total'].'</strong>'
                                );
                            }
                        }
                        $i = 0;
                        foreach($records['data'] as $data){
                            ?>
                            <tr>
                                <td><?php echo $data[0]; ?></td>
                                <td><?php echo $data[1]; ?></td>
                                <td><?php echo $data[2]; ?></td>
                                <td><?php echo $data[3]; ?></td>
                                <td><?php echo $data[4]; ?></td>
                            </tr>
                            <?php
                            $i++;
                        }
                        for($b = 0; $b <= 8 - $i; $i++){
                            ?>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>
                    <table class="table table-striped table-hover table-bordered">
                        <tr>
                            <th style="width: 14%">Crew</th>
                            <th style="width: 6%" class="text-center">On</th>
                            <th style="width: 6%" class="text-center">Lunch</th>
                            <th style="width: 6%" class="text-center">Off</th>
                            <th style="width: 6%" class="text-center">Total</th>
                            <th rowspan="5" class="text-center" style="width: 33%">
                                <br/><br/>
                                <strong>CC # _________-_________-_________-_________</strong><br/><br/>
                                <strong>Expiration Date _____/_____ CVC ________</strong><br/><br/>
                                <strong>Name On Card ___________________________</strong>
                            </th>
                            <th rowspan="2" colspan="2"  style="width: 20%">
                                Sub Total: <span class="pull-right bold font-red">$<span id="PLPAP_SUBTOTAL"></span></span> <br/>
                                Taxes: <span class="pull-right bold font-red">$<span id="PLPAP_TAXES"></span></span> <br/>
                                Total: <span class="pull-right bold font-red">$<span id="PLPAP_TOTAL"></span></span>
                            </th>
                        </tr>
                        <?php
                        $findLabor = mysql_query("SELECT laborer_user_token FROM fmo_locations_events_laborers WHERE laborer_event_token='".mysql_real_escape_string($event['event_token'])."'");
                        $iTotalRecords = mysql_num_rows($findLabor);

                        $records = array();
                        $records["data"] = array();

                        while($lb = mysql_fetch_assoc($findLabor)) {
                            $records["data"][] = array(''.name($lb['laborer_user_token']).'');
                        }
                        $i = 0;
                        foreach($records['data'] as $crew){
                            ?>
                            <tr>
                                <td><?php echo $crew[0]; ?></td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <?php
                                if($i == 1){
                                    ?>
                                    <td rowspan="3" colspan="2" class="text-center">I authorize Here To There Movers to charge me for the charges listed above.</td>
                                    <?php
                                } $i ++;
                                ?>
                            </tr>
                            <?php
                        }
                        for($c = 0; $c <= 4 - $i; $c++){
                            ?>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                                <?php
                                if($i == 1){
                                    ?>
                                    <td rowspan="3" colspan="2" class="text-center">I authorize Here To There Movers to charge me for the charges listed above.</td>
                                    <?php
                                } $i ++;
                                ?>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td colspan="3" style="font-size: 10px;" class="text-muted text-center">Consumer Must Personally Initial Choice</td>
                            <td colspan="4"  style="font-size: 10px;" class="text-muted text-center">This contract is subject to all terms and conditions, rates, and disclaimers contained here and within the tarrif filed with the State.</td>
                        </tr>
                        <tr>
                            <td colspan="7" style="font-size: 12px;">
                                _______ <strong class="font-red">I agree to minimal reimbursement for lost or damaged goods. I understand and accept that I will be reimbursed for lost or damaged goods at a minimal amount not exceeding sixty cents per pound per article.</strong>

                                <hr/>

                                _______ <strong class="font-red">I accept reimbursement equal to the replacement cost of lost or damaged goods. I declare a total replacement value of $ ______________ or a minimum of six dollars per pound times the weight of the shipment, whichever is greater. I understand that total reimbursement for lost or damaged goods shall not exceed this declared value. I understand that failure to disclose any article valued at greater than one hundred dollars per pound may limit the carrier's reimbursement liability to this maximum per article.</strong>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5">
                                Lost or Damaged Items:

                            </td>
                            <td rowspan="2" colspan="2">
                                <br/><br/>
                                <strong style="font-size: 20px;">X</strong> <span class="text-muted" style="font-size: 8px;">signature</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="5" class="text-center">
                                www.HERETOTHEREMOVERS.com
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
<!-- END LOGIN -->
<!-- BEGIN COPYRIGHT -->
<div class="copyright">
    2017 &copy; For Movers Only | <strong>Powered by Logan</strong> <br/>  <a target="_blank" href="https://www.fmcsa.dot.gov/protect-your-move"><strong>Your rights & responsibilities.</strong></a>
</div>
<!--[if lt IE 9]>
<script src="../global/plugins/respond.min.js"></script>
<script src="../global/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="../global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="../global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<script src="../global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="../global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="../global/plugins/dropzone/dropzone.js"></script>
<script type="text/javascript" src="../global/plugins/select2/select2.min.js"></script>
<script src="../global/scripts/metronic.js" type="text/javascript"></script>
<script src="../admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="../admin/pages/scripts/login.js" type="text/javascript"></script>
<script>
    jQuery(document).ready(function() {
        Metronic.init();
        Layout.init();
        Login.init();

        function updateI(){
            $.ajax({
                url: '../../assets/app/api/event.php?type=inv&luid=<?php echo $event['event_location_token']; ?>',
                type: 'POST',
                data: {
                    event: '<?php echo $event['event_token']; ?>'
                },
                success: function(m){
                    var owe = JSON.parse(m);
                    $(document).find('#PLPAP_SUBTOTAL').html(parseFloat(owe.sub_total).toFixed(2));
                    $(document).find('#PLPAP_TAXES').html(parseFloat(owe.tax).toFixed(2));
                    $(document).find('#PLPAP_TOTAL').html(parseFloat(owe.total).toFixed(2));
                },
                error: function(e){

                }
            });
        }
        updateI();
    });
</script>
</body>
</html>