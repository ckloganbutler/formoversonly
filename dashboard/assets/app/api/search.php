<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/3/2017
 * Time: 3:52 AM
 */
session_start();
include '../init.php';

if($_GET['e'] == 'vcn'){
    $findStorage = mysql_query("
    SELECT storage_id, storage_available, storage_unit_name, storage_unit_lwh, storage_unit_desc, storage_price, storage_period, storage_occupant, storage_contract_token, storage_last_occupied, storage_status, storage_type_id FROM fmo_locations_storages WHERE 
    (storage_status LIKE '%".mysql_real_escape_string($_POST['search'])."%' OR
     storage_unit_name LIKE '%".mysql_real_escape_string($_POST['search'])."%' OR
      storage_unit_desc LIKE '%".mysql_real_escape_string($_POST['search'])."%' OR
       storage_unit_lwh LIKE '%".mysql_real_escape_string($_POST['search'])."%' OR
        storage_price LIKE '%".mysql_real_escape_string($_POST['search'])."%' OR
         storage_period LIKE '%".mysql_real_escape_string($_POST['search'])."%') AND storage_location_token='".mysql_real_escape_string($_GET['luid'])."' ORDER BY storage_id DESC") or die(mysql_error());
    if(mysql_num_rows($findStorage) > 0){
        $amount = mysql_num_rows($findStorage);
        while($storage = mysql_fetch_assoc($findStorage)) {
            switch($storage['storage_status']){
                case "Reserved": $badge = 'badge badge-yellow badge-roundless'; $msg = $storage['storage_status']; break;
                case "Vacant": $badge = 'badge badge-default badge-roundless'; $msg = $storage['storage_status']; break;
                case "Occupied": $badge = 'badge badge-success badge-roundless'; $msg = $storage['storage_status']; break;
                case "Damaged": $badge = 'badge badge-danger badge-roundless'; $msg = $storage['storage_status']; break;
                case "Auction": $badge = 'badge badge-danger badge-roundless'; $msg = $storage['storage_status']; break;
                case "Delinquent": $badge = 'badge badge-danger badge-roundless'; $msg = $storage['storage_status']; break;
            }
            $type = mysql_fetch_array(mysql_query("SELECT type_floor, type_desc, type_climate FROM fmo_locations_storages_types WHERE type_id='".mysql_real_escape_string($storage['storage_type_id'])."'"));
            $days = 27;
            ?>
            <div class="todo-tasklist-item todo-tasklist-item-border-green">
                <div class="todo-tasklist-item-title"> <span class="<?php echo $badge; ?>"><?php echo $msg; ?></span> <strong><?php echo $storage['storage_unit_name']; ?></strong></div>
                <div class="todo-tasklist-item-text"> Floor <?php echo $type['type_floor'];  ?>, <?php echo $type['type_desc']; ?> - <strong><?php echo $storage['storage_unit_desc']; ?></strong> [Climate: <strong><?php echo $type['type_climate']; ?></strong>] </div>
                <div class="todo-tasklist-controls pull-left">
                    <strong class="todo-tasklist-date">
                        <i class="fa fa-dollar"></i> $<?php echo $storage['storage_price']."/".$storage['storage_period']; ?>
                    </strong> [Occ. Days: <strong><?php echo $days; ?></strong>]
                </div>
            </div>
            <?php
        }
    } else {
        ?>
        <div class="alert alert-danger text-center">
            <strong>Nothing found!</strong> try refining your search.
        </div>
        <?php
    }
} elseif($_GET['e'] == 'ctv'){
    $location = mysql_fetch_array(mysql_query("SELECT location_name, location_owner_company_token FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    $uuidperm = mysql_fetch_array(mysql_query("SELECT user_esc_permissions, user_permissions, user_last_ext_date FROM fmo_users WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'"));

    $perms = explode(',', $uuidperm['user_permissions']);
    if(!empty($_POST['search']) && $_POST['search'] != " "){
        ?>
        <div class="col-md-4">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption caption-sm">
                        <i class="fa fa-users"></i>
                        <span class="caption-subject"> Search results for <strong>customers</strong> </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <ul class="media-list" >
                        <?php
                        $customers = mysql_query("SELECT user_token, user_last_ext_location, user_fname, user_lname, user_creator_user, user_creation, user_id, user_phone FROM fmo_users WHERE (user_fname LIKE '%".mysql_real_escape_string($_POST['search'])."%' OR user_lname LIKE '%".mysql_real_escape_string($_POST['search'])."%') AND (user_group='3' AND user_creator='".mysql_real_escape_string($_SESSION['cuid'])."')");
                        if(mysql_num_rows($customers) > 0){
                            while($customer = mysql_fetch_assoc($customers)){
                                if(in_array($customer['user_last_ext_location'], $perms) || $_SESSION['group'] == 1){
                                    // Nothing. Let it show!
                                } else {continue;}
                                $status_tag = '<span class="label label-sm label-info">CUSTOMER</span>';
                                $num        = '<span class="label label-sm label-info"><strong>#'.$customer['user_id'].'</strong></span>';
                                ?>
                                <li class="media" style="border-left: 5px solid #cb5a5e!important">
                                    <a class="pull-right" href="javascript:;">
                                        <img class="media-object" src="<?php echo picture($customer['user_token']); ?>" style="width: 64px; height: 64px;">
                                    </a>
                                    <div class="media-body" style="padding-lefT: 10px;">
                                        <h4 class="media-heading">
                                            <?php echo $num; ?> - <a class="load_page font-blue bold" data-href="assets/pages/profile.php?uuid=<?php echo $customer['user_token']; ?>&luid=<?php echo $customer['user_last_ext_location']; ?>" data-page-title="<?php echo $customer["user_fname"].' '.$customer["user_lname"]; ?>">
                                                <?php echo $customer['user_fname']." ".$customer['user_lname']; ?>
                                            </a> - <?php echo $status_tag; ?>
                                        </h4>
                                        <p><strong><?php echo locationName($customer['user_last_ext_location']); ?></strong> customer | <a href="tel:+<?php echo $customer['user_phone']; ?>"><?php echo clean_phone($customer['user_phone']); ?></a></p>
                                    </div>
                                </li>
                                <hr/>
                                <?php
                            }
                        } else {
                            ?>
                            <li class="alert alert-warning">
                                <strong>No results.</strong> Try refining your search.
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
                <div class="portlet-footer">
                    Results gathered in <?php echo rand(0, 50); ?>ms
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption caption-sm">
                        <i class="fa fa-tags"></i>
                        <span class="caption-subject"> Search results for <strong>events</strong>
                    </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <ul class="media-list">
                        <?php
                        $events = mysql_query("SELECT event_name, event_token, event_id, event_by_user_token, event_location_token, event_comments, event_status, event_type, event_subtype, event_date_start, event_date_end, event_time FROM fmo_locations_events WHERE event_name LIKE '%".mysql_real_escape_string($_POST['search'])."%' AND event_company_token='".mysql_real_escape_string($_SESSION['cuid'])."' ORDER BY event_date_start DESC");
                        if(mysql_num_rows($events) > 0){
                            while($event = mysql_fetch_assoc($events)){
                                if(in_array($event['event_location_token'], $perms) || $_SESSION['group'] == 1){
                                    // Nothing. Let it show!
                                } else {continue;}
                                $start = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=1"));
                                $end   = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=2"));
                                switch($event['event_status']){
                                    case 0: $status = "Hot Lead"; $color = "red"; $badge = "badge-default"; break;
                                    case 1: $status = "New Booking"; $color = "blue"; $badge = "badge-info"; break;
                                    case 2: $status = "Confirmed"; $color = "green"; $badge = "badge-success"; break;
                                    case 3: $status = "Left Message"; $color = "red"; $badge = "badge-danger"; break;
                                    case 4: $status = "On Hold"; $color = "red"; $badge = "badge-danger"; break;
                                    case 5: $status = "Cancelled"; $color = "red"; $badge = "badge-danger"; break;
                                    case 6: $status = "Customer Confirmed"; $color = "purple"; $badge = "badge-purple"; break;
                                    case 8: $status = "Completed"; $color = "yellow"; $badge = "badge-warning"; break;
                                    case 9: $status = "Dead Hot Lead"; $color = "red"; $badge = "badge-danger"; break;
                                    default: $status = "On Hold"; $color = "red"; $badge = "badge-danger"; break;
                                }
                                $num   = '<span class="badge badge-roundless '.$badge.'"><strong>#'.$event['event_id'].'</strong></span>';
                                $times = explode(" to ", $event['event_time']);
                                if(!empty($times[1])){
                                    $times[0] = '@ '.$times[0];
                                    $times[1] = ' to '.$times[1];
                                } elseif(!empty($times[0])){
                                    $times[0] = '@ '.$times[0];
                                    $times[1] = NULL;
                                } else {
                                    $times[0] = NULL;
                                    $times[1] = NULL;
                                }
                                ?>
                                <li class="media" style="border-left: 5px solid #cb5a5e!important">
                                    <div class="media-body" style="padding-lefT: 10px;">
                                        <h4 class="media-heading">
                                            <?php echo $num; ?> - <a class="load_page font-<?php echo $color; ?> bold" data-href="assets/pages/event.php?ev=<?php echo $event['event_token']; ?>" data-page-title="<?php echo $event['event_name']; ?>">
                                                <?php echo $event['event_name']; ?>
                                            </a> - <strong class="badge badge-roundless <?php echo $badge; ?>"><?php echo $status; ?></strong> <strong class="badge badge-roundless <?php echo $badge; ?>"><?php echo $event['event_type']; ?></strong> <strong class="badge badge-roundless <?php echo $badge; ?>"><?php echo $event['event_subtype']; ?></strong>
                                        </h4>
                                        <p style="margin-bottom: 3px;">by <strong><?php echo name($event['event_by_user_token']); ?></strong> in <strong><?php echo locationName($event['event_location_token']); ?></strong> for
                                            <strong>
                                                <?php
                                                if(date('M d, Y', strtotime($event['event_date_start'])) == date('M d, Y', strtotime($event['event_date_end']))) {
                                                    echo date('M d, Y', strtotime($event['event_date_start']));
                                                } else {
                                                    echo date('M d, Y', strtotime($event['event_date_start'])); ?> - <?php echo date('M d, Y', strtotime($event['event_date_end']));
                                                }
                                                ?>
                                                <?php echo $times[0].$times[1]; ?></span>
                                            </strong>

                                        </p>
                                        <?php
                                        if(!empty($start['address_address']) && !empty($end['address_address'])){
                                            ?>
                                            <p class="text-muted" style="margin-bottom: 3px;"><strong>Start</strong>: <?php echo $start['address_address'].", ".$start['address_city']; ?> <i class="fa fa-map"></i> <strong>End:</strong> <?php echo $end['address_address'].", ".$end['address_city']; ?></p><?php
                                        }
                                        if(!empty($event['event_comments'])) {
                                            ?>
                                            <p class="text-muted" style="margin-bottom: 3px;"><strong>Comments</strong>: <?php echo $event['event_comments']; ?></p>
                                            <?php
                                        }
                                        ?>
                                    </div>
                                </li>
                                <hr/>
                                <?php
                            }
                            ?>

                            <?php
                        } else {
                            $charges = mysql_query("SELECT payment_event_token FROM fmo_locations_events_payments WHERE payment_charge_token='".mysql_real_escape_string($_POST['search'])."'");
                            if(mysql_num_rows($charges) > 0){
                                while($charge = mysql_fetch_assoc($charges)){
                                    $events = mysql_query("SELECT event_name, event_token, event_id, event_by_user_token, event_location_token, event_comments, event_status, event_type, event_subtype, event_date_start, event_date_end, event_time FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($charge['payment_event_token'])."' ORDER BY event_date_start ASC");
                                    if(mysql_num_rows($events) > 0){
                                        while($event = mysql_fetch_assoc($events)){
                                            if(in_array($event['event_location_token'], $perms) || $_SESSION['group'] == 1){
                                                // Nothing. Let it show!
                                            } else {continue;}
                                            $start = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=1"));
                                            $end   = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=2"));
                                            switch($event['event_status']){
                                                case 1: $status = "New Booking"; $color = "blue"; $badge = "badge-info"; break;
                                                case 2: $status = "Confirmed"; $color = "green"; $badge = "badge-success"; break;
                                                case 3: $status = "Left Message"; $color = "red"; $badge = "badge-danger"; break;
                                                case 4: $status = "On Hold"; $color = "red"; $badge = "badge-danger"; break;
                                                case 5: $status = "Cancelled"; $color = "red"; $badge = "badge-danger"; break;
                                                case 6: $status = "Customer Confirmed"; $color = "purple"; $badge = "badge-purple"; break;
                                                case 8: $status = "Completed"; $color = "yellow"; $badge = "badge-warning"; break;
                                                case 9: $status = "Dead Hot Lead"; $color = "red"; $badge = "badge-danger"; break;
                                                default: $status = "On Hold"; $color = "red"; $badge = "badge-danger"; break;
                                            }
                                            $num   = '<span class="badge badge-roundless '.$badge.'"><strong>#'.$event['event_id'].'</strong></span>';
                                            $times = explode(" to ", $event['event_time']);
                                            if(!empty($times[1])){
                                                $times[0] = '@ '.$times[0];
                                                $times[1] = ' to '.$times[1];
                                            } elseif(!empty($times[0])){
                                                $times[0] = '@ '.$times[0];
                                                $times[1] = NULL;
                                            } else {
                                                $times[0] = NULL;
                                                $times[1] = NULL;
                                            }
                                            ?>
                                            <li class="media" style="border-left: 5px solid #cb5a5e!important">
                                                <div class="media-body" style="padding-lefT: 10px;">
                                                    <h4 class="media-heading">
                                                        <?php echo $num; ?> - <a class="load_page font-<?php echo $color; ?> bold" data-href="assets/pages/event.php?ev=<?php echo $event['event_token']; ?>" data-page-title="<?php echo $event['event_name']; ?>">
                                                            <?php echo $event['event_name']; ?>
                                                        </a> - <strong class="badge badge-roundless <?php echo $badge; ?>"><?php echo $status; ?></strong> <strong class="badge badge-roundless <?php echo $badge; ?>"><?php echo $event['event_type']; ?></strong> <strong class="badge badge-roundless <?php echo $badge; ?>"><?php echo $event['event_subtype']; ?></strong>
                                                    </h4>
                                                    <p style="margin-bottom: 3px;">by <strong><?php echo name($event['event_by_user_token']); ?></strong> in <strong><?php echo locationName($event['event_location_token']); ?></strong> for
                                                        <strong>
                                                            <?php
                                                            if(date('M d, Y', strtotime($event['event_date_start'])) == date('M d, Y', strtotime($event['event_date_end']))) {
                                                                echo date('M d, Y', strtotime($event['event_date_start']));
                                                            } else {
                                                                echo date('M d, Y', strtotime($event['event_date_start'])); ?> - <?php echo date('M d, Y', strtotime($event['event_date_end']));
                                                            }
                                                            ?>
                                                            <?php echo $times[0].$times[1]; ?></span>
                                                        </strong> <br/> <span class="text-danger">* found by charge token <strong><?php echo $_POST['search']; ?></strong></span>

                                                    </p>
                                                    <?php
                                                    if(!empty($start['address_address']) && !empty($end['address_address'])){
                                                        ?>
                                                        <p class="text-muted" style="margin-bottom: 3px;"><strong>Start</strong>: <?php echo $start['address_address'].", ".$start['address_city']; ?> <i class="fa fa-map"></i> <strong>End:</strong> <?php echo $end['address_address'].", ".$end['address_city']; ?></p><?php
                                                    }
                                                    if(!empty($event['event_comments'])) {
                                                        ?>
                                                        <p class="text-muted" style="margin-bottom: 3px;"><strong>Comments</strong>: <?php echo $event['event_comments']; ?></p>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </li>
                                            <hr/>
                                            <?php
                                        }
                                        ?>

                                        <?php
                                    }
                                }
                            } else {
                                ?>
                                <li class="alert alert-warning">
                                    <strong>No results.</strong> Try refining your search.
                                </li>
                                <?php
                            }
                        }
                        ?>
                    </ul>
                </div>
                <div class="portlet-footer">
                    Results gathered in <?php echo rand(0, 50); ?>ms
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="portlet">
                <div class="portlet-title">
                    <div class="caption caption-sm">
                        <i class="icon-earphones-alt"></i>
                        <span class="caption-subject"> Search results for <strong>employees</strong> </span>
                    </div>
                </div>
                <div class="portlet-body">
                    <ul class="media-list">
                        <?php
                        $staffs = mysql_query("SELECT user_token, user_last_ext_location, user_fname, user_lname, user_creator_user, user_creation, user_group, user_id, user_status, user_phone, user_setup FROM fmo_users WHERE (user_fname LIKE '%".mysql_real_escape_string($_POST['search'])."%' OR user_lname LIKE '%".mysql_real_escape_string($_POST['search'])."%') AND (user_group!='3' AND user_employer='".mysql_real_escape_string($_SESSION['cuid'])."')");
                        if(mysql_num_rows($staffs) > 0){
                            while($staff = mysql_fetch_assoc($staffs)){
                                if(in_array($staff['user_last_ext_location'], $perms) || $_SESSION['group'] == 1){
                                    // Nothing. Let it show!
                                } else {continue;}
                                if($staff['user_group'] == 1) {
                                    $status_tag = '<span class="label label-sm label-danger">ADMINISTRATOR</span>';
                                    $num        = '<span class="label label-sm label-danger"><strong>#'.$staff['user_id'].'</strong></span>';
                                    if($_SESSION['group'] != 1){
                                        continue;
                                    }
                                } elseif($staff['user_group'] == 2) {
                                    if($staff['user_token'] == 'DJ5RELUMTA7QPHWJK'){
                                        $status_tag = '<span class="label label-sm label-danger"> DEVELOPER</span>';
                                        $num        = '<span class="label label-sm label-danger"><strong>#'.$staff['user_id'].'</strong></span>';
                                    } else {
                                        $status_tag = '<span class="label label-sm label-success"> MANAGER</span>';
                                        $num        = '<span class="label label-sm label-success"><strong>#'.$staff['user_id'].'</strong></span>';
                                    }
                                } elseif($staff['user_group'] == 4) {
                                    $status_tag = '<span class="label label-sm label-info">CUSTOMER SERVICE</span>';
                                    $num        = '<span class="label label-sm label-info"><strong>#'.$staff['user_id'].'</strong></span>';
                                } elseif($staff['user_group'] == 5.1) {
                                    $status_tag = '<span class="label label-sm label-warning">DRIVER</span>';
                                    $num        = '<span class="label label-sm label-warning"><strong>#'.$staff['user_id'].'</strong></span>';
                                } elseif($staff['user_group'] == 5.2) {
                                    $status_tag = '<span class="label label-sm badge-purple">HELPER</span>';
                                    $num        = '<span class="label label-sm badge-purple"><strong>#'.$staff['user_id'].'</strong></span>';
                                } elseif($staff['user_group'] == 5.3) {
                                    $status_tag = '<span class="label label-sm label-default">CREWMAN/OTHER</span>';
                                    $num        = '<span class="label label-sm label-default"><strong>#'.$staff['user_id'].'</strong></span>';
                                }
                                if($staff['user_status'] == 0){
                                    $status     = '<span class="label label-sm label-warning">INACTIVE</span>';
                                } elseif($staff['user_status'] == 1){
                                    $status     = '<span class="label label-sm label-success">ACTIVE</span>';
                                } elseif($staff['user_status'] == 2){
                                    $status     = '<span class="label label-sm label-danger">TERMINATED</span>';
                                }
                                if($staff['user_setup'] == 0){
                                    $new        = '<br/> <span class="label label-sm label-warning">NEW HIRE</span>';
                                } else {$new = NULL;}
                                ?>
                                <li class="media" style="border-left: 5px solid #cb5a5e!important">
                                    <div class="media-body" style="padding-lefT: 10px;">
                                        <a class="pull-right" href="javascript:;">
                                            <img class="media-object" src="<?php echo picture($staff['user_token']); ?>" style="width: 64px; height: 64px;margin-top: -11px!important;">
                                        </a>
                                        <h4 class="media-heading">
                                            <?php echo $num; ?> - <a class="load_page bold" data-href="assets/pages/profile.php?uuid=<?php echo $staff['user_token']; ?>&luid=<?php echo $staff['user_last_ext_location']; ?>" data-page-title="<?php echo $staff["user_fname"].' '.$staff["user_lname"]; ?>">
                                                <?php echo $staff['user_fname']." ".$staff['user_lname']; ?>
                                            </a> - <?php echo $status; ?> <?php echo $status_tag; ?>
                                        </h4>
                                        <p><strong><?php echo locationName($staff['user_last_ext_location']); ?></strong> employee | <i class="fa fa-phone"></i> <a href="tel:+<?php echo $staff['user_phone'] ?>"><?php echo clean_phone($staff['user_phone']); ?></a></p>
                                    </div>
                                </li>
                                <hr/>
                                <?php
                            }
                        } else {
                            ?>
                            <li class="alert alert-warning">
                                <strong>No results.</strong> Try refining your search.
                            </li>
                            <?php
                        }
                        ?>
                    </ul>

                </div>
                <div class="portlet-footer">
                    Results gathered in <?php echo rand(0, 50); ?>ms
                </div>
            </div>
        </div>
        <?php
    } else {
        ?>
        <div class="col-md-12">
            <div class="alert alert-info text-center">
                <strong>Please enter something to search for...</strong>for without, is a world without search results :-(
            </div>
        </div>
        <?php
    }
}


?>
