<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/8/2017
 * Time: 11:21 PM
 */
session_start();
include '../init.php';

if(isset($_GET) && $_GET['type'] == 'comments'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findComments = mysql_query("SELECT comment_id, comment_comment, comment_by_user_token, comment_timestamp FROM fmo_locations_events_comments WHERE comment_event_token='".mysql_real_escape_string($_GET['ev'])."' ORDER BY comment_timestamp DESC");
    $iTotalRecords = mysql_num_rows($findComments);

    $records = array();
    $records["data"] = array();

    while($comt = mysql_fetch_assoc($findComments)) {
        $records["data"][] = array(
            ''.$comt['comment_timestamp'].'',
            ''.$comt['comment_comment'].'',
            ''.name($comt['comment_by_user_token']).'',
        );
    }


    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET) && $_GET['type'] == 'documents'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findDocuments = mysql_query("SELECT document_id, document_link, document_desc, document_by_user_token, document_timestamp FROM fmo_locations_events_documents WHERE document_event_token='".mysql_real_escape_string($_GET['ev'])."' ORDER BY document_id DESC");
    $iTotalRecords = mysql_num_rows($findDocuments);

    $records = array();
    $records["data"] = array();

    while($doc = mysql_fetch_assoc($findDocuments)) {
        $records["data"][] = array(
            '<embed height="200" width="100%" src="'.$doc['document_link'].'"/><br/><center>'.$doc['document_type'].'</center>',
            'File Type: <strong>Event Document</strong><br/> File Description: <strong>'.$doc['document_desc'].'</strong> <br/> <a target="_blank" href="'.$doc['document_link'].'"><strong>Click here to view document</strong></a> <br/> File uploaded by: <strong>'.name($doc['document_by_user_token']).'</strong> <br/> File uploaded on: '.date('m/d/Y G:s A', strtotime($doc['document_timestamp'])),
            ''.name($doc['document_by_user_token']).'',
        );
    }


    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET) && $_GET['type'] == 'reviews'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findReviews = mysql_query("SELECT review_rating, review_comments, review_name, review_timestamp FROM fmo_locations_events_reviews WHERE review_event_token='".mysql_real_escape_string($_GET['ev'])."'");
    $iTotalRecords = mysql_num_rows($findReviews);

    $records = array();
    $records["data"] = array();

    while($rv = mysql_fetch_assoc($findReviews)) {
        $records["data"][] = array(
            '<div class="rateYo" data-rateyo-rating="'.number_format($rv['review_rating'], 1).'"></div>',
            ''.$rv['review_comments'].'',
            ''.$rv['review_name'].'',
        );
    }


    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET) && $_GET['type'] == 'claims'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findClaims = mysql_query("SELECT claim_timestamp, claim_item, claim_padded, claim_weight, claim_comments FROM fmo_locations_events_claims WHERE claim_event_token='".mysql_real_escape_string($_GET['ev'])."'");
    $iTotalRecords = mysql_num_rows($findClaims);

    $records = array();
    $records["data"] = array();

    while($cl = mysql_fetch_assoc($findClaims)) {
        if($cl['claim_padded'] == 'No'){
            $label = "label-danger";
        } else {
            $label = "label-success";
        }
        $records["data"][] = array(
            ''.$cl['claim_timestamp'].'',
            ''.$cl['claim_item'].'',
            '<span class="label '.$label.'">'.$cl['claim_padded'].'</span>',
            ''.$cl['claim_weight'].'',
            ''.$cl['claim_comments'].'',
            '<a class="btn default btn-xs red-stripe"><i class="fa fa-times"></i> Delete</a>'
        );
    }


    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET) && $_GET['type'] == 'timeline'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findTimelines = mysql_query("SELECT timeline_id, timeline_by_user_token, timeline_type, timeline_reasoning, timeline_timestamp  FROM fmo_locations_events_timelines WHERE timeline_event_token='".mysql_real_escape_string($_GET['ev'])."' ORDER BY timeline_timestamp DESC");
    $iTotalRecords = mysql_num_rows($findTimelines);

    $records = array();
    $records["data"] = array();

    while($time = mysql_fetch_assoc($findTimelines)) {
        $records["data"][] = array(
            ''.date('m-d-Y G:i:s A', strtotime($time['timeline_timestamp'])).'',
            ''.$time['timeline_type'].'',
            ''.$time['timeline_reasoning'].'',
            ''.name($time['timeline_by_user_token']).''
        );
    }


    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET) && $_GET['type'] == 'labor'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findLabor = mysql_query("SELECT laborer_id, laborer_user_token, laborer_hours_worked, laborer_role, laborer_timestamp, laborer_by_user_token, laborer_tip, laborer_rate, laborer_commission FROM fmo_locations_events_laborers WHERE laborer_event_token='".mysql_real_escape_string($_GET['ev'])."'");
    $iTotalRecords = mysql_num_rows($findLabor);

    $records = array();
    $records["data"] = array();

    while($lb = mysql_fetch_assoc($findLabor)) {
        $records["data"][] = array(
            '<span class="label label-sm label-success text-center"><a class="lb_'.$lb['laborer_id'].'" style="color:white" data-name="laborer_role" data-pk="'.$lb['laborer_id'].'" data-type="select" data-source="[{value: \'CREW LEADER\', text: \'Crew Leader\'}, {value: \'CREWMAN\', text: \'Crewman\'}]" data-placement="right" data-title="Select new role.." data-url="assets/app/update_settings.php?setting=event_laborers">'.$lb['laborer_role'].'</a></span>',
            ''.name($lb['laborer_user_token']).' - Commission: <strong>%'.number_format($lb['laborer_commission'] * 100, 2).'</strong>',
            '$'.number_format($lb['laborer_rate'], 2).'/hr',
            '<a class="lb_'.$lb['laborer_id'].'" style="color:#333333" data-inputclass="form-control" data-name="laborer_hours_worked" data-pk="'.$lb['laborer_id'].'" data-type="number" data-placement="right" data-title="Enter new paid hours.." data-url="assets/app/update_settings.php?setting=event_laborers">'.number_format($lb['laborer_hours_worked'], 2).'</a>',
            '$<a class="lb_'.$lb['laborer_id'].'" style="color:#333333" data-inputclass="form-control" data-name="laborer_tip" data-pk="'.$lb['laborer_id'].'" data-type="number" data-placement="right" data-title="Enter new tip/other pay.." data-url="assets/app/update_settings.php?setting=event_laborers">'.number_format($lb['laborer_tip'], 2).'</a>',
            ''.name($lb['laborer_by_user_token']).'',
            '<a class="btn default btn-xs red-stripe edit" data-edit="lb_'.$lb['laborer_id'].'" data-reload=""><i class="fa fa-edit"></i> Edit</a> <a class="btn default btn-xs red delete_labor" data-delete="lb_'.$lb['laborer_id'].'"><i class="fa fa-times"></i></a>',
        );
    }


    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET) && $_GET['type'] == 'assets'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findAsset = mysql_query("SELECT asset_id, asset_name, asset_by_user_token FROM fmo_locations_events_assets WHERE asset_event_token='".mysql_real_escape_string($_GET['ev'])."'");
    $iTotalRecords = mysql_num_rows($findAsset);

    $records = array();
    $records["data"] = array();

    while($lb = mysql_fetch_assoc($findAsset)) {
        $records["data"][] = array(
            ''.$lb['asset_name'].'',
            ''.name($lb['asset_by_user_token']).'',
            '<a class="btn default btn-xs red del_asset" data-id="'.$lb['asset_id'].'"><i class="fa fa-times"></i></a>',
        );
    }


    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET['type']) && $_GET['type'] == 'rates'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findRates = mysql_query("SELECT services_id, services_item, services_item_desc, services_taxable, services_commissionable, services_type FROM fmo_services WHERE services_location_token='".mysql_real_escape_string($_GET['luid'])."'");
    $iTotalRecords = mysql_num_rows($findRates);

    $records = array();
    $records["data"] = array();

    while($services = mysql_fetch_assoc($findRates)) {
        if($services['services_taxable'] == 1) {
            $icon = '<i class="fa fa-check text-danger"></i>';
        } else {$icon = NULL;}
        if($services['services_commissionable'] == 1) {
            $icon2 = '<i class="fa fa-check text-success"></i>';
        } else {$icon2 = NULL;}
        if($services['services_type'] == 'Discount'){
            $records["data"][] = array(
                '<a>'.$services['services_item'].'</a> '.$icon.$icon2,
                '<button type="button" data-id="'.$services['services_id'].'" data-ev="'.$_GET['ev'].'" data-qty="1" class="btn default btn-xs blue-stripe add_item"><i class="fa fa-plus"></i> Add to invoice</a>',
            );
        } else {
            $records["data"][] = array(
                ''.$services['services_item'].' '.$icon.$icon2,
                '<button type="button" data-id="'.$services['services_id'].'" data-ev="'.$_GET['ev'].'" data-qty="0" class="btn default btn-xs blue-stripe add_item"><i class="fa fa-plus"></i> Add to invoice</a>',
            );
        }

    }

    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET['type']) && $_GET['type'] == 'sales'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findItems = mysql_query("SELECT item_id, item_item, item_desc, item_qty, item_cost, item_total, item_redeemable FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($_GET['ev'])."'");
    $iTotalRecords = mysql_num_rows($findItems);

    $records = array();
    $records["data"] = array();

    while($items = mysql_fetch_assoc($findItems)) {
        if(!isset($_GET['VmP'])){
            if($items['item_redeemable'] == 2){
                $options = '<a class="btn default btn-xs red-stripe edit pull-right no_print" data-edit="item_'.$items['item_id'].'" data-reload="eve" data-update="sales" data-selec="autoselect" data-event="'.$_GET['ev'].'" data-luid="'.$_GET['luid'].'"><i class="fa fa-edit"></i> Edit</a> ';
                $records["data"][] = array(
                    '<span class="text-success">'.$items['item_item'].'</span> '.$options.'',
                    '<span class="text-success">'.$items['item_desc'].'</span>',
                    '<a class="item_'.$items['item_id'].' text-success" style="color:#333333" data-inputclass="form-control" data-name="item_qty" data-pk="'.$items['item_id'].'" data-type="number" data-placement="right" data-title="Enter new quantity.." data-url="assets/app/update_settings.php?setting=event_items">'.$items['item_qty'].'</a>',
                    '<a class="item_'.$items['item_id'].' text-success" style="color:#333333" data-inputclass="form-control" data-name="item_cost" data-pk="'.$items['item_id'].'" data-type="number" data-placement="right" data-title="Enter new cost.." data-url="assets/app/update_settings.php?setting=event_items">'.$items['item_cost'].'</a>',
                    '<strong class="text-danger pull-right">'.$items['item_total'].'</strong>'
                );
            } else {
                if($items['item_item'] == 'Booking Fee'){
                    $options = NULL;
                } else {
                    $options = '<a class="btn default btn-xs red-stripe edit pull-right no_print" data-edit="item_'.$items['item_id'].'" data-reload="eve" data-update="sales" data-selec="autoselect" data-event="'.$_GET['ev'].'" data-luid="'.$_GET['luid'].'"><i class="fa fa-edit"></i> Edit</a> <a class="btn default btn-xs red delete_item pull-right no_print" data-delete="item_'.$items['item_id'].'" data-event="'.$_GET['ev'].'"><i class="fa fa-times"></i></a>';
                }
                $records["data"][] = array(
                    ''.$items['item_item'].' '.$options.'',
                    '<a class="item_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_desc" data-pk="'.$items['item_id'].'" data-type="text" data-placement="right" data-title="Enter new description.." data-url="assets/app/update_settings.php?setting=event_items">'.$items['item_desc'].'</a>',
                    '<a class="item_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_qty" data-pk="'.$items['item_id'].'" data-type="number" data-placement="right" data-title="Enter new quantity.." data-url="assets/app/update_settings.php?setting=event_items">'.$items['item_qty'].'</a>',
                    '<a class="item_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_cost" data-pk="'.$items['item_id'].'" data-type="number" data-placement="right" data-title="Enter new cost.." data-url="assets/app/update_settings.php?setting=event_items">'.$items['item_cost'].'</a>',
                    '<strong class="text-danger pull-right">'.$items['item_total'].'</strong>'
                );
            }
        } else {
            if($items['item_redeemable'] == 2){
               $records["data"][] = array(
                    '<span class="text-success">'.$items['item_item'].'</span>',
                    '<span class="pull-right">'.$items['item_qty'].'</span>',
                    '<span class="pull-right">'.$items['item_cost'].'</span>',
                    '<strong class="text-success pull-right">'.$items['item_total'].'</strong>'
                );
            } else {
                $records["data"][] = array(
                    ''.$items['item_item'].'',
                    '<span class="pull-right">'.$items['item_qty'].'</span>',
                    '<span class="pull-right">'.$items['item_cost'].'</span>',
                    '<strong class="text-danger pull-right">'.$items['item_total'].'</strong>'
                );
            }
        }
    }

    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}
if(isset($_GET['type']) && $_GET['type'] == 'payments'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findItems = mysql_query("SELECT payment_id, payment_type, payment_amount, payment_detail, payment_by_user_token, payment_charge_token FROM fmo_locations_events_payments WHERE payment_event_token='".mysql_real_escape_string($_GET['ev'])."'");
    $iTotalRecords = mysql_num_rows($findItems);

    $records = array();
    $records["data"] = array();

    while($items = mysql_fetch_assoc($findItems)) {
        $void = explode(" - ", $items['payment_type']);
        if($void[1] != 'VOIDED' && $void[0] != 'Booking Fee' && $void[0] != 'Credit/Debt'){
            $color = 'text-success';
            $button = '<a class="btn default btn-xs red delete_payment pull-right no_print" data-void="'.$items['payment_id'].'" data-event="'.$_GET['ev'].'" data-luid="'.$_GET['luid'].'"><i class="fa fa-times"></i> Void</a>';
        } elseif($void[0] == 'Booking Fee') {
            $button = NULL;
            $color = 'text-success';
        } elseif($void[0] == 'Credit/Debt' && $_SESSION['group'] == 1 && $void[1] != 'VOIDED') {
            $button = '<a class="btn default btn-xs red refund_payment pull-right no_print" data-refund="'.$items['payment_charge_token'].'" data-event="'.$_GET['ev'].'" data-luid="'.$_GET['luid'].'"><i class="fa fa-times"></i> Refund via Stripe</a>';
            $color = 'text-success';
        } else {
            $button = NULL;
            $color = 'text-danger';
        }
        if($void[0] == 'Credit/Debt'){
            $detail = substr($items['payment_charge_token'], -6);
        } else {
            $detail = $items['payment_detail'];
        }
        $records["data"][] = array(
            ''.$items['payment_type'].' '.$button.'',
            ''.$detail.'',
            ''.name($items['payment_by_user_token']).'',
            '<strong class="'.$color.' pull-right">'.$items['payment_amount'].'</strong>'

        );
    }

    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET['type']) && $_GET['type'] == 'inv'){
    $location = mysql_fetch_array(mysql_query("SELECT location_sales_tax, location_creditcard_fee FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));

    if(!empty($location['location_sales_tax'])){
        $tax = $location['location_sales_tax'];
    } else {$tax = 0;}

    $findItems = mysql_query("SELECT item_total, item_taxable, item_commission FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($_POST['event'])."'");
    $iTotalRecords = mysql_num_rows($findItems);

    $findPaid = mysql_query("SELECT payment_amount, payment_type FROM fmo_locations_events_payments WHERE payment_event_token='".mysql_real_escape_string($_POST['event'])."'");
    $bTotalRecords = mysql_num_rows($findPaid);


    $total = array();
    $total['sub_total'] = 0.00;
    $total['tax']       = 0.00;
    $total['taxable']   = 0.00;
    $total['cc_fees']   = 0.00;
    $total['total']     = 0.00;
    $total['paid']      = 0.00;
    $total['unpaid']    = 0.00;
    if($iTotalRecords > 0){
        while($item = mysql_fetch_assoc($findItems)){
            $total['sub_total'] += $item['item_total'];
            if($item['item_taxable'] == 1){
                $total['tax']     += number_format($item['item_total'] * $tax, 2, '.', '');
                $total['taxable'] += number_format($item['item_total'], 2, '.', '');
            } else {
                $total['tax']   += 0.00;
            }
            if($item['item_commission'] == 1){
                $total['coms']  += number_format($item['item_total'], 2, '.', '');
            } else {
                $total['coms'] += 0.00;
            }
        }
        $total['total'] = number_format($total['sub_total'] + $total['tax'], 2, '.', '');
    } else {
        $total['total']     = 0.00;
        $total['sub_total'] = 0.00;
    }

    if(isset($_GET['mpay']) && $_GET['mpay'] == 'true'){
        $total['cc_fees'] += $total['total'] * $location['location_creditcard_fee'];
        $total['total']   += $total['cc_fees'];
    }

    if($bTotalRecords > 0){
        while($paid = mysql_fetch_assoc($findPaid)){
            $void = explode(" - ", $paid['payment_type']);
            if($void[1] != 'VOIDED' && $void[0] != 'Invoice'){
                $total['paid'] += $paid['payment_amount'];
                if($void[0] == 'Credit/Debt' && $void[1] != 'Booking Fee'){
                    $total['total']   += ($paid['payment_amount'] / 1.03) * .03;
                    $total['cc_fees'] += ($paid['payment_amount'] / 1.03) * .03;
                }
            }

        }
        $total['unpaid'] = number_format($total['total'] - $total['paid'], 2, '.', '');
        $total['amount'] = intval(floatval(str_replace("$", "", $total['unpaid']))*100);
    } else {
        $total['unpaid'] = number_format($total['total'], 2, '.', '');
        $total['amount'] = intval(floatval(str_replace("$", "", $total['unpaid']))*100);
        $total['paid']   = 0.00;
    }



    echo json_encode($total);
}
if(isset($_GET['type']) && $_GET['type'] == 'math'){
    $math  = array();
    $a = $_POST['a'];
    $b = $_POST['b'];
    $c = $_POST['c'];
    $event = mysql_fetch_array(mysql_query("SELECT event_truckrate_rate, event_truckfee_rate, event_laborrate_rate, event_weekend_upcharge_rate, event_adjustment FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));
    $math['truck_fee']        = $event['event_truckfee_rate'] * $a;
    $math['labor_rate']       = $event['event_laborrate_rate'];
    $math['truck_rate']       = $event['event_truckrate_rate'];
    $math['upcharge']         = $event['event_weekend_upcharge_rate'];
    if($event['event_weekend_upcharge_rate'] > 0){
        $math['total_labor_rate'] = ($event['event_laborrate_rate'] * $b) + ($event['event_truckrate_rate'] * $a) + $event['event_weekend_upcharge_rate'] + $event['event_adjustment'];
    } else {
        $math['total_labor_rate'] = ($event['event_laborrate_rate'] * $b) + ($event['event_truckrate_rate'] * $a) + $event['event_adjustment'];
    }
    $math['county_fee']       = ($math['total_labor_rate'] * .5) * $c;
    echo json_encode($math);
}

if(isset($_GET['type']) && $_GET['type'] == 'book_now'){
    $event    = mysql_fetch_array(mysql_query("SELECT event_name, event_user_token, event_time, event_date_start, event_date_end, event_type, event_subtype, event_truckfee, event_laborrate, event_countyfee, event_comments, event_additions, event_email, event_phone, event_token, event_location_token, event_company_token, event_user_token, event_zip FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));
    $location = mysql_fetch_array(mysql_query("SELECT location_booking_fee_disclaimer, location_disclaimers, location_max_trucks, location_max_men, location_max_counties FROM fmo_locations WHERE location_token='".$event['event_location_token']."'"));

    ?>
    <form id="book_it" method="POST">
        <div class="form-group">
            <label class="control-label">Event Name <span class="required">*</span></label>
            <div class="input-icon">
                <i class="fa fa-user"></i>
                <input type="text" class="form-control placeholder-no-fix" name="name" value="<?php echo $event['event_name']; ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label">Start date <span class="required">*</span> (bookings must be 2 days in the future) </label>
            <div class="input-icon">
                <i class="fa fa-calendar"></i>
                <input type="text" class="form-control date-picker" data-date="10/11/2012" data-date-format="mm/dd/yyyy" id="date" style="width: 100% !important;" name="startdate" value="<?php echo date("m/d/Y", strtotime($event['event_date_start'])); ?>">
                <input type="hidden" name="enddate" value="<?php echo date("Y-m-d", strtotime($event['event_date_start'])); ?>">
                <input type="hidden" name="date" value="<?php echo date("Y-m-d", strtotime($event['event_date_start'])); ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="control-label">Requested start time <span class="required">*</span></label>
            <select class="form-control" name="time" id="time_select" style="border-left: 2px solid #c23f44 !important">
                <option disabled selected value="">Select a start time..</option>
                <?php
                $timeOptions = mysql_query("SELECT time_start, time_end FROM fmo_locations_times WHERE time_location_token='".mysql_real_escape_string($event['event_location_token'])."'");
                if(mysql_num_rows($timeOptions) > 0){
                    while($t = mysql_fetch_assoc($timeOptions)){
                        if(empty($t['time_end'])){
                            continue;
                        }
                        ?>
                        <option <?php if($t['time_start']." to ".$t['time_end'] == $event['event_time']){echo "selected";} ?> value="<?php echo $t['time_start']; ?> to <?php echo $t['time_end']; ?>"><?php echo $t['time_start']; ?> - <?php echo $t['time_end']; ?></option>
                        <?php
                    }
                }
                ?>
            </select>
        </div>
        <div class="form-group hidden">
            <label class="control-label">Type <span class="required">*</span></label>
            <div class="input-icon">
                <select class="form-control placeholder-no-fix" name="type">
                    <option selected value="Local">Local</option>
                </select>
            </div>
        </div>
        <div class="form-group hidden">
            <label class="control-label">Sub-Type <span class="required">*</span></label>
            <div class="input-icon">
                <select class="form-control placeholder-no-fix" name="subtype">
                    <option selected value="Move">Move</option>
                    <option value="Estimate">Estimate</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label">Contact Email</label>
            <div class="input-icon">
                <i class="fa fa-envelope"></i>
                <input type="text" class="form-control placeholder-no-fix" name="email" value="<?php echo $event['event_email']; ?>"/>
            </div>
        </div>
        <div class="form-group">
            <label class="control-label">Contact Phone <span class="required">*</span></label>
            <div class="input-icon">
                <i class="fa fa-phone"></i>
                <input type="text" class="form-control placeholder-no-fix" id="mask_phone" name="phone" value="<?php echo clean_phone($event['event_phone']); ?>"/>
            </div>
        </div>
        <hr/>
        <h4><strong>Requested crew size</strong> and their rates</h4>
        <div class="row">
            <div class="btn-group col-xs-12">
                <a class="btn btn-block default red-stripe dropdown-toggle" href="javascript:;" data-toggle="dropdown">
                    <i class="fa fa-truck"></i> Trucks: <strong id="truckfee" class="event_truckfee_out edits" data-a="#truckfee" data-b="#laborrate" data-c="#countyfee"><?php echo $event['event_truckfee']; ?></strong> for $<span id="TF"></span> <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu pull-right">
                    <?php
                    for($i = 0; $i <= $location['location_max_trucks']; $i++){
                        ?>
                        <li>
                            <a class="rate_changer" data-value="<?php echo $i; ?>" data-name="event_truckfee"><i class="fa fa-truck font-red"></i> <?php echo $i; ?></a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div> <br/>
        <div class="row">
            <div class="btn-group col-xs-12">
                <a class="btn btn-block default red-stripe dropdown-toggle" href="javascript:;" data-toggle="dropdown">
                    <i class="fa fa-users"></i> Crewmen: <strong id="laborrate" class="event_laborrate_out edits" data-a="#truckfee" data-b="#laborrate" data-c="#countyfee"><?php echo $event['event_laborrate']; ?></strong> for $<span id="LR"></span> <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu pull-right">
                    <?php
                    for($i = 0; $i <= $location['location_max_men']; $i++){
                        ?>
                        <li>
                            <a class="rate_changer" data-value="<?php echo $i; ?>" data-name="event_laborrate"><i class="fa fa-users font-red"></i> <?php echo $i; ?></a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
        <hr/>
        <h4><strong>Comments</strong> and extra stuff</h4>
        <div class="form-group">
            <textarea placeholder="BOL comments (psst! the comment you're about to type will automatically save when you're done typing." class="form-control bol_comments" style="height: 180px;"><?php echo $event['event_comments']; ?></textarea>
            <span style="margin-top: -23px; margin-right: 10px;" class="bol_countdown pull-right"></span>
        </div>
        <hr/>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            <strong>Pick up</strong> location(s)
                        </div>
                        <div class="actions">
                            <a class="btn default red-stripe" data-toggle="modal" href="#draggable" data-location-type="1">
                                <i class="fa fa-plus"></i>
                                <span class="hidden-480">Add pickup</span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <?php
                        $pickups = mysql_query("SELECT address_id, address_address, address_suite, address_city, address_state, address_zip, address_closest_intersection, address_county, address_stairs, address_distance, address_comments, address_bedrooms, address_garage, address_special, address_square_footage FROM fmo_locations_events_addresses WHERE address_event_token='".mysql_real_escape_string($event['event_token'])."' AND address_type=1");
                        if(mysql_num_rows($pickups) > 0){
                            $pk = 0;
                            while($pickup = mysql_fetch_assoc($pickups)){
                                $pk++
                                ?>
                                <div id="pickup_h_<?php echo $pk; ?>" class="panel-group">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <div class="actions pull-right" style="margin-top: -6px; margin-right: -9px">
                                                <a href="javascript:;" class="btn btn-default btn-sm edit" data-edit="pu_<?php echo $pickup['address_id']; ?>">
                                                    <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md hidden-xs">Edit</span> </a>
                                            </div>
                                            <div class="caption">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#pickup_h_<?php echo $pk; ?>" href="#pickup_<?php echo $pk; ?>" aria-expanded="false"><strong><?php echo $pickup['address_address']; ?>, <?php echo $pickup['address_city']; ?>, <?php echo $pickup['address_state']; ?> <?php echo $pickup['address_zip']; ?></strong></a>
                                                </h4>
                                            </div>
                                        </div>
                                        <div id="pickup_<?php echo $pk; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                            <div class="panel-body">
                                                <address>
                                                    <strong>Physical Address</strong><br>
                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_address" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new street address.." data-url="assets/app/update_settings.php?update=event_addy">
                                                        <?php echo $pickup['address_address']; ?>
                                                    </a><br/>
                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_city" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new city.." data-url="assets/app/update_settings.php?update=event_addy">
                                                        <?php echo $pickup['address_city']; ?>
                                                    </a>,
                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_state" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Select new state.." data-url="assets/app/update_settings.php?update=event_addy">
                                                        <?php echo $pickup['address_state']; ?>
                                                    </a>
                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_zip" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new zipcode.." data-url="assets/app/update_settings.php?update=event_addy">
                                                        <?php echo $pickup['address_zip']; ?>
                                                    </a><br/>
                                                    <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_county" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new county.." data-url="assets/app/update_settings.php?update=event_addy">
                                                        <?php echo $pickup['address_county']; ?>
                                                    </a>
                                                </address>
                                                <address>
                                                    Closest intersection:
                                                    <strong>
                                                        <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_closest_intersection" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                            <?php echo $pickup['address_closest_intersection']; ?>
                                                        </a><br/>
                                                    </strong>

                                                    Stairs:
                                                    <strong>
                                                        <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_stairs" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                            <?php echo $pickup['address_stairs']; ?>
                                                        </a><br/>
                                                    </strong>

                                                    Parking Distance:
                                                    <strong>
                                                        <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_distance" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="assets/app/update_settings.php?update=event_addy">
                                                            <?php echo $pickup['address_distance']; ?>
                                                        </a><br/>
                                                    </strong>

                                                    Bedrooms:
                                                    <strong>
                                                        <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_bedrooms" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="assets/app/update_settings.php?update=event_addy">
                                                            <?php echo $pickup['address_bedrooms']; ?>
                                                        </a><br/>
                                                    </strong>

                                                    Garage:
                                                    <strong>
                                                        <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_garage" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="assets/app/update_settings.php?update=event_addy">
                                                            <?php echo $pickup['address_garage']; ?>
                                                        </a><br/>
                                                    </strong>

                                                    Special Item(s):
                                                    <strong>
                                                        <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_special" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="assets/app/update_settings.php?update=event_addy">
                                                            <?php echo $pickup['address_special']; ?>
                                                        </a><br/>
                                                    </strong>

                                                    Square Footage:
                                                    <strong>
                                                        <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_square_footage" data-pk="<?php echo $pickup['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="assets/app/update_settings.php?update=event_addy">
                                                            <?php echo $pickup['address_square_footage']; ?>
                                                        </a><br/>
                                                    </strong>
                                                </address>
                                                <address>
                                                    Comments: <br/>
                                                    <strong>
                                                        <a class="pu_<?php echo $pickup['address_id']; ?>" style="color:#333333" data-name="address_comments" data-pk="<?php echo $pickup['address_id']; ?>" data-type="textarea" data-placement="right" data-title="Enter new comments.." data-url="assets/app/update_settings.php?update=event_addy">
                                                            <?php echo $pickup['address_comments']; ?>
                                                        </a>
                                                    </strong>
                                                </address>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $pk_strt  = $pickup['address_address'];
                                $pk_state = $pickup['address_state'];
                                $pk_city  = $pickup['address_city'];
                                $pk_zip   = $pickup['address_zip'];
                            }
                        } else {
                            ?>
                            <div class="alert alert-warning alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                <strong>No pickup locations!</strong> Add a new location to see them appear here.
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="portlet">
                    <div class="portlet-title">
                        <div class="caption">
                            <strong>Destination</strong> location(s)
                        </div>
                        <div class="actions">
                            <a class="btn default red-stripe" data-toggle="modal" href="#draggable" data-location-type="2">
                                <i class="fa fa-plus"></i>
                                <span class="hidden-480">Add destination</span>
                            </a>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <?php
                        $dests = mysql_query("SELECT address_address, address_suite, address_city, address_state, address_zip, address_closest_intersection, address_county, address_stairs, address_distance, address_comments FROM fmo_locations_events_addresses WHERE address_event_token='".mysql_real_escape_string($event['event_token'])."' AND address_type=2");
                        if(mysql_num_rows($dests) > 0){
                            $pk = 0;
                            while($dest = mysql_fetch_assoc($dests)){
                                $pk++
                                ?>
                                <div id="dest_h_<?php echo $pk; ?>" class="panel-group">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <div class="actions pull-right" style="margin-top: -6px; margin-right: -9px">
                                                <a href="javascript:;" class="btn btn-default btn-sm edit" data-edit="ds_<?php echo $dest['address_id']; ?>">
                                                    <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md hidden-xs">Edit</span> </a>
                                            </div>
                                            <div class="caption">
                                                <h4 class="panel-title">
                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#dest_h_<?php echo $pk; ?>" href="#dest_<?php echo $pk; ?>" aria-expanded="false"><strong><?php echo $dest['address_address']; ?>, <?php echo $dest['address_city']; ?>, <?php echo $dest['address_state']; ?> <?php echo $dest['address_zip']; ?></strong></a>
                                                </h4>
                                            </div>
                                        </div>
                                        <div id="dest_<?php echo $pk; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                            <div class="panel-body">
                                                <address>
                                                    <strong>Physical Address</strong><br>
                                                    <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_address" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new street address.." data-url="assets/app/update_settings.php?update=event_addy">
                                                        <?php echo $dest['address_address']; ?>
                                                    </a><br/>
                                                    <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_city" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new city.." data-url="assets/app/update_settings.php?update=event_addy">
                                                        <?php echo $dest['address_city']; ?>
                                                    </a>,
                                                    <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_state" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Select new state.." data-url="assets/app/update_settings.php?update=event_addy">
                                                        <?php echo $dest['address_state']; ?>
                                                    </a>
                                                    <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_zip" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new zipcode.." data-url="assets/app/update_settings.php?update=event_addy">
                                                        <?php echo $dest['address_zip']; ?>
                                                    </a><br/>
                                                    <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_county" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new county.." data-url="assets/app/update_settings.php?update=event_addy">
                                                        <?php echo $dest['address_county']; ?>
                                                    </a>
                                                </address>
                                                <address>
                                                    Closest intersection:
                                                    <strong>
                                                        <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_closest_intersection" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                            <?php echo $dest['address_closest_intersection']; ?>
                                                        </a><br/>
                                                    </strong>

                                                    Stairs:
                                                    <strong>
                                                        <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_stairs" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new closest intersection.." data-url="assets/app/update_settings.php?update=event_addy">
                                                            <?php echo $dest['address_stairs']; ?>
                                                        </a><br/>
                                                    </strong>

                                                    Parking Distance:
                                                    <strong>
                                                        <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_distance" data-pk="<?php echo $dest['address_id']; ?>" data-type="text" data-placement="right" data-title="Enter new distance.." data-url="assets/app/update_settings.php?update=event_addy">
                                                            <?php echo $dest['address_distance']; ?>
                                                        </a><br/>
                                                    </strong>
                                                </address>
                                                <address>
                                                    Comments: <br/>
                                                    <strong>
                                                        <a class="ds_<?php echo $dest['address_id']; ?>" style="color:#333333" data-name="address_comments" data-pk="<?php echo $dest['address_id']; ?>" data-type="textarea" data-placement="right" data-title="Enter new comments.." data-url="assets/app/update_settings.php?update=event_addy">
                                                            <?php echo $dest['address_comments']; ?>
                                                        </a>
                                                    </strong>
                                                </address>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                $ds_strt  = $dest['address_address'];
                                $ds_state = $dest['address_state'];
                                $ds_city  = $dest['address_city'];
                                $ds_zip   = $dest['address_zip'];
                            }
                        } else {
                            ?>
                            <div class="alert alert-warning alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                <strong>No destination locations!</strong> Add a new location to see them appear here.
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-12 text-center">
                <div id="gmap_basic" class="gmaps" style="height: 450px;">
                </div>
            </div>
        </div>
        <hr/>
        <button type="button" class="btn red button-submit">
            Proceed to final step <i class="m-icon-swapright m-icon-white"></i>
        </button>
    </form>
    <form class="cc-form" id="booking_fee_form" style="display: none !important;">
        <strong><span class="text-danger">*</span> <span class="text-danger">BOOKING FEE</span></strong>: For our peace of mind that you're <strong>serious about business</strong>, we require all online bookings to pay a one-time, non-refundable payment of <strong>$10.00</strong>.<br/>
        <br/>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">Carderholder Name <span class="required">*</span></label>
            <div class="input-icon">
                <i class="fa fa-user"></i>
                <input type="text" data-stripe="name" class="form-control input-sm card_name" placeholder="Cardholder Name">
            </div>
        </div>
        <div class="form-inline">
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Credit/Debt Card Number <span class="required">*</span></label>
                <div class="input-icon">
                    <i class="fa fa-credit-card"></i>
                    <input type="text" data-stripe="number" class="form-control input-sm card_num" placeholder="Card Number" style="width:155px;">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Exp <span class="required">*</span></label>
                <div class="input-icon">
                    <i class="fa fa-calendar"></i>
                    <input type="text" data-stripe="exp" class="form-control input-sm exp_date" placeholder="Exp" style="width:80px;">
                </div>
            </div>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">CVC <span class="required">*</span></label>
                <div class="input-icon">
                    <i class="fa fa-sort-numeric-asc"></i>
                    <input type="text" data-stripe="cvc" class="form-control input-sm cvc_num" placeholder="CVC" style="width:81px;">
                </div>
            </div>
        </div>
        <input type="text" name="notes" id="booking_notes" class="hidden"/>
        <button id="booking_fee" class="btn btn-block red" style="margin-top: 10px;" type="button"><span class="error-handler">Pay $10.00 booking fee</span> <i class="fa fa-credit-card"></i></button>
    </form>
    <br/><br/>
    <strong><span class="text-danger">*</span> <span class="text-warning">WARNING</span>: You should keep this page confidential. It allows access to private information.</strong> <br/>
    <strong><span class="text-danger">*</span> <span class="text-info">NOTICE</span>: By booking your move online with <?php echo companyName($event['event_company_token']) ?> & For Movers Only, you agree to our terms of service & privacy policy.</strong>
    <br/><br/>
    <form method="POST" action="" role="form" id="new_location">
        <div class="modal fade bs-modal-lg" id="draggable" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content box red">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h3 class="modal-title font-bold">Add event location</h3>
                    </div>
                    <div class="modal-body">
                        <div class="row hidden">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Location Type</label>
                                    <select name="type" class="form-control" id="type">
                                        <option value="1">Pick up</option>
                                        <option value="2">Destination</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Street Address</label>
                                    <input type="text" class="form-control" name="address" placeholder="Street Address">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Zip Code</label>
                                    <input type="number" class="form-control" name="zip" id="zip_auto">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>City</label>
                                    <input type="text" class="form-control" name="city" placeholder="City">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>State</label>
                                    <select name="state" class="form-control state">
                                        <option value="" selected disabled>Select one..</option>
                                        <option value="AL">Alabama</option>
                                        <option value="AK">Alaska</option>
                                        <option value="AZ">Arizona</option>
                                        <option value="AR">Arkansas</option>
                                        <option value="CA">California</option>
                                        <option value="CO">Colorado</option>
                                        <option value="CT">Connecticut</option>
                                        <option value="DE">Delaware</option>
                                        <option value="DC">District Of Columbia</option>
                                        <option value="FL">Florida</option>
                                        <option value="GA">Georgia</option>
                                        <option value="HI">Hawaii</option>
                                        <option value="ID">Idaho</option>
                                        <option value="IL">Illinois</option>
                                        <option value="IN">Indiana</option>
                                        <option value="IA">Iowa</option>
                                        <option value="KS">Kansas</option>
                                        <option value="KY">Kentucky</option>
                                        <option value="LA">Louisiana</option>
                                        <option value="ME">Maine</option>
                                        <option value="MD">Maryland</option>
                                        <option value="MA">Massachusetts</option>
                                        <option value="MI">Michigan</option>
                                        <option value="MN">Minnesota</option>
                                        <option value="MS">Mississippi</option>
                                        <option value="MO">Missouri</option>
                                        <option value="MT">Montana</option>
                                        <option value="NE">Nebraska</option>
                                        <option value="NV">Nevada</option>
                                        <option value="NH">New Hampshire</option>
                                        <option value="NJ">New Jersey</option>
                                        <option value="NM">New Mexico</option>
                                        <option value="NY">New York</option>
                                        <option value="NC">North Carolina</option>
                                        <option value="ND">North Dakota</option>
                                        <option value="OH">Ohio</option>
                                        <option value="OK">Oklahoma</option>
                                        <option value="OR">Oregon</option>
                                        <option value="PA">Pennsylvania</option>
                                        <option value="RI">Rhode Island</option>
                                        <option value="SC">South Carolina</option>
                                        <option value="SD">South Dakota</option>
                                        <option value="TN">Tennessee</option>
                                        <option value="TX">Texas</option>
                                        <option value="UT">Utah</option>
                                        <option value="VT">Vermont</option>
                                        <option value="VA">Virginia</option>
                                        <option value="WA">Washington</option>
                                        <option value="WV">West Virginia</option>
                                        <option value="WI">Wisconsin</option>
                                        <option value="WY">Wyoming</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Street Address 2 (Optional)</label>
                                    <input type="text" class="form-control" name="address2" placeholder="Complex Name / Second Address">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Suite/Apt</label>
                                    <input type="text" class="form-control" name="suite" placeholder="Apt/Suite #">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Closest Intersection</label>
                                    <input type="text" class="form-control" name="closest_intersection" placeholder="Intersection">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>County</label>
                                    <input type="text" class="form-control" name="county" placeholder="County name">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Stairs</label>
                                    <select name="stairs" class="form-control">
                                        <option disabled selected value="">Select one..</option>
                                        <option value="No stairs">No stairs</option>
                                        <option value="1 flight">1 flight</option>
                                        <option value="2 flights">2 flights</option>
                                        <option value="Elevator">Elevator</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Parking Distance</label>
                                    <select name="distance" class="form-control">
                                        <option disabled selected value="">Select one..</option>
                                        <option value="Less than 50">Less than 50</option>
                                        <option value="More than 50">More than 50</option>
                                        <option value="More than 100">More than 100</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row extra-forms">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Bedrooms</label>
                                    <select class="form-control" name="bedrooms">
                                        <option disabled selected value="">Select one..</option>
                                        <option value="Miscellaneous Items">Miscellaneous Items</option>
                                        <option value="1 Bedroom">1 Bedroom</option>
                                        <option value="2 Bedrooms">2 Bedrooms</option>
                                        <option value="3 Bedrooms">3 Bedrooms</option>
                                        <option value="4 Bedrooms">4 Bedrooms</option>
                                        <option value="5 Bedrooms">5 Bedrooms</option>
                                        <option value="6+ Bedroom">6+ Bedrooms</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Garage</label>
                                    <select class="form-control" name="garage">
                                        <option disabled selected value="">Select one..</option>
                                        <option value="No garage">No garage</option>
                                        <option value="1 Car">1 Car</option>
                                        <option value="2 Cars">2 Cars</option>
                                        <option value="3 Cars">3 Cars</option>
                                        <option value="4+ Cars">4+ Cars</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Square Footage</label>
                                    <select name="sqft" class="form-control">
                                        <option disabled selected value="">Select one..</option>
                                        <option value="Less than 1000sqft">Less than 1000sqft</option>
                                        <option value="Less than 1500sqft">Less than 1500sqft</option>
                                        <option value="Less than 2000sqft">Less than 2000sqft</option>
                                        <option value="Less than 2500sqft">Less than 2500sqft</option>
                                        <option value="Less than 3000sqft">Less than 3000sqft</option>
                                        <option value="Less than 3500sqft">Less than 3500sqft</option>
                                        <option value="Less than 4000sqft">Less than 4000sqft</option>
                                        <option value="Less than 4500sqft">Less than 4500sqft</option>
                                        <option value="More than 4500sqft+">More than 4500sqft+</option>
                                        <option value="More than 5000sqft+">More than 5000sqft+</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Special Item(s)</label>
                                    <input type="text" class="form-control" name="special" placeholder="Special Item(s)">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Comments</label>
                                    <input type="text" class="form-control" name="comments">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn red">Save location</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <script>
        $(document).ready(function(){

            Stripe.setPublishableKey('pk_live_ftqBPIkJ6eBemXHToHiU8Eqa');

            $(function() {
                // IMPORTANT: Fill in your client key
                var clientKey = "js-InlLzUGLaGPQYhaSPQrQGnDmZH0HPvLyT6ks10ebG31Ekcxa3Y0KmE6ml73bDOJw";

                var cache = {};
                var container = $("#new_location");

                /** Handle successful response */
                function handleResp(data) {
                    // Check for error
                    if (data.error_msg) toastr.error("<strong>Ckai says:</strong><br/>"+data.error_msg);
                    else if ("city" in data) {
                        // Set city and state
                        container.find("input[name='city']").val(data.city);
                        container.find('.state option[value="'+data.state+'"]').attr("selected", "selected");
                    }
                }
                // Set up event handlers
                container.find("input[name='zip']").on("keyup change", function() {
                    // Get zip code
                    var zipcode = $(this).val().substring(0, 5);
                    if (zipcode.length == 5 && /^[0-9]+$/.test(zipcode)) {
                        // Check cache
                        if (zipcode in cache) {
                            handleResp(cache[zipcode]);
                        } else {
                            // Build url
                            var url = "https://www.zipcodeapi.com/rest/"+clientKey+"/info.json/" + zipcode + "/radians";
                            // Make AJAX request
                            $.ajax({
                                "url": url,
                                "dataType": "json"
                            }).done(function(data) {
                                handleResp(data);

                                // Store in cache
                                cache[zipcode] = data;
                            }).fail(function(data) {
                                if (data.responseText && (json = $.parseJSON(data.responseText))) {
                                    // Store in cache
                                    cache[zipcode] = json;

                                    // Check for error
                                    if (json.error_msg) toastr.error("<strong>Ckai says:</strong><br/>"+json.error_msg);
                                } else toastr.error("<strong>Ckai says:</strong><br/>Unknown error. You really f**ked up!");
                            });
                        }
                    }
                });
            });

            function initMap() {
                var directionsService = new google.maps.DirectionsService;
                var directionsDisplay = new google.maps.DirectionsRenderer;
                var map = new google.maps.Map(document.getElementById('gmap_basic'), {
                    zoom: 5,
                    center: {lat: 40.2672, lng: 86.1349}
                });
                directionsDisplay.setMap(map);
                calculateAndDisplayRoute(directionsService, directionsDisplay);
            }

            function calculateAndDisplayRoute(directionsService, directionsDisplay) {
                var waypts = [];
                waypts.push({
                    location: "<?php echo $pk_strt; ?>, <?php echo $pk_city; ?>, <?php echo $pk_state; ?>, <?php echo $pk_zip; ?>",
                    stopover: true
                });

                directionsService.route({
                    origin: "<?php echo locationAddress($event['event_location_token']); ?>",
                    destination: "<?php echo $ds_strt; ?>, <?php echo $ds_city; ?>, <?php echo $ds_state; ?>, <?php echo $ds_zip; ?>",
                    waypoints: waypts,
                    optimizeWaypoints: true,
                    travelMode: 'DRIVING'
                }, function(response, status) {
                    if (status === 'OK') {
                        directionsDisplay.setDirections(response);
                        var route = response.routes[0];
                        var total = 0;
                        var summaryPanel = document.getElementById('results-map-panel');
                        summaryPanel.innerHTML = '';
                        // For each route, display summary information.
                        for (var i = 0; i < route.legs.length; i++) {
                            var routeSegment = i + 1;
                            var mi           = route.legs[i].distance.text.replace(/[^\d.-]/g, '');
                            if(routeSegment == 1){
                                summaryPanel.innerHTML += 'From <strong>dispatch</strong> to <strong>first location</strong> (' + route.legs[i].distance.text + '): ' + routeSegment +
                                    '<br>';
                            } else {
                                summaryPanel.innerHTML += 'From <strong>pickup</strong> to <strong>destination</strong> (' + route.legs[i].distance.text + '): ' + routeSegment +
                                    '<br>';
                            }
                            total += +mi;
                            summaryPanel.innerHTML += route.legs[i].start_address + ' <strong>to</strong>  ';
                            summaryPanel.innerHTML += route.legs[i].end_address + ' <br/><br/>';
                        }
                        summaryPanel.innerHTML += '<strong>Total milage</strong>: ' + total + ' mi';
                    } else {
                        toastr.error("<strong>Logan says:</strong><br/>There is not enough information to route the trip. Please add at least 1 pickup and destination location.")
                    }
                });
            }

            initMap();


            function stripeResponseHandler(status, response) {
                // Grab the form:
                var $form = $('#booking_fee_form');

                if (response.error) { // Problem!

                    // Show the errors on the form:
                    toastr.error("<strong>Logan says:</strong><br/>" + response.error.message);
                    $form.find('#booking_fee').prop('disabled', false); // Re-enable submission
                    $('#booking_fee').html("Check your fields. Try again? <i class='fa fa-credit-card'></i>");

                } else { // Token was created!

                    // Get the token ID:
                    var token = response.id;

                    // Insert the token ID into the form so it gets submitted to the server:
                    //$form.append($('<input type="hidden" name="auth">').val(token));

                    $.ajax({
                        url: '../app/charge.php?ev=<?php echo $event['event_token']; ?>',
                        type: 'post',
                        data: {
                            token: token,
                            amount: 1000,
                            email: "<?php echo $event['event_email']; ?>"
                        },
                        success: function(data) {
                            if (data.length > 9) {
                                toastr.info("<strong>Logan says:</strong><br/>Card was charged successfully, here's the confirmation token: " + data);
                                $('#booking_notes').removeAttr('disabled');
                                $('#booking_notes').attr('value', data);
                                $.ajax({
                                    url: '../app/update_settings.php?update=event_fly&tok='+data+'&uuid=<?php echo $event['event_user_token']; ?>&cuid=<?php echo $event['event_company_token']; ?>&era=self',
                                    type: 'POST',
                                    data: {
                                        name: 'event_booking',
                                        value: 1,
                                        pk: '<?php echo $event['event_token']; ?>'
                                    },
                                    success: function(s){
                                        toastr.success("<strong>Logan says:</strong><br/>Booking fee paid, 10$ has been charged to the card you provided.");
                                        $.ajax({
                                            url: '../app/update_settings.php?update=event&e=<?php echo $event['event_token']; ?>&s=1&callback=conf&cuid=<?php echo $event['event_company_token']; ?>&luid=<?php echo $event['event_location_token']; ?>',
                                            type: 'POST',
                                            data: $('#book_it').serialize(),
                                            success: function(d) {
                                                window.location = "https://www.formoversonly.com/dashboard/assets/public/conf.php?ty=confirm&ev=<?php echo $event['event_token']; ?>"
                                            },
                                            error: function() {
                                                toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                            }
                                        });
                                    },
                                    error: function(s){

                                    }
                                });
                            }
                            if (data == 'error-4'){
                                $('#booking_fee').html("Card declined. Try again? <i class='fa fa-credit-card'></i>");
                                toastr.error("<strong>Logan says:</strong><br/>Card was declined. Please try again using a different card, or review your card information for mistakes.");
                                $form.find('#booking_fee').prop('disabled', false); // Re-enable submission
                            }

                            if (data == 'error-2'){
                                $('#booking_fee').html("Error. Try again? <i class='fa fa-credit-card'></i>")
                                toastr.error("<strong>Logan says:</strong><br/>A programatic error has occured, and the card has not been charged.");
                                $form.find('#booking_fee').prop('disabled', false);
                            }
                        },
                        error: function(data) {
                            console.log("Ajax Error!");
                            console.log(data);
                        }
                    });
                }
            };


            $('#booking_fee').unbind().click(function(ee) {
                var $form  = $('#booking_fee_form');
                // Disable the submit button to prevent repeated clicks:
                $('#booking_fee').attr("disabled","disabled");
                $('#booking_fee').html("<i class='fa fa-spinner fa-spin'></i>");

                // Request a token from Stripe:
                Stripe.card.createToken($form, stripeResponseHandler);

                // Prevent the form from being submitted:
                return false;
            });

            $('.card_num').inputmask("mask", {
                "mask": "9999 9999 9999 9999",
                "removeMaskOnSubmit": false,
                "placeholder": ""
            });
            $('.exp_date').inputmask("mask", {
                "mask": "99/99",
                "removeMaskOnSubmit": false,
                "placeholder": ""
            });
            $('.cvc_num').inputmask("mask", {
                "mask": "9999",
                "placeholder": ""
            });

            var form = $('#book_it');

            form.validate({
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    name: {
                        required: true
                    },
                    type: {
                        required: true
                    },
                    subtype: {
                        required: true
                    },
                    phone: {
                        required: true
                    },
                    email: {
                        required: true
                    },
                    startdate: {
                        required: true
                    },
                    time: {
                        required: true
                    },
                    event_truckfee: {
                        required: true
                    },
                    event_laborrate: {
                        required: true
                    },
                    event_countyfee: {
                        required: true
                    }
                },


                invalidHandler: function (event, validator) { //display error alert on form submit

                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').removeClass('has-success').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.form-group').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    if (label.attr("for") == "gender" || label.attr("for") == "payment[]") { // for checkboxes and radio buttons, no need to show OK icon
                        label
                            .closest('.form-group').removeClass('has-error').addClass('has-success');
                        label.remove(); // remove error label here
                    } else { // display success icon for other inputs
                        label
                            .addClass('valid') // mark the current input as valid and display OK icon
                            .closest('.form-group').removeClass('has-error').addClass('has-success'); // set success class to the control group
                    }
                },

                submitHandler: function (form) {

                }

            });

            $('#new_location').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                    type: {
                        required: true
                    },
                    address: {
                        required: true
                    },
                    city: {
                        required: true
                    },
                    state: {
                        required: true
                    },
                    zip: {
                        required: true,
                        number: true,
                        minlength: 5,
                        maxlength: 5
                    },
                    comments: {
                        maxlength: 100
                    }
                },

                invalidHandler: function(event, validator) { //display error alert on form submit

                },

                highlight: function(element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').addClass('has-error'); // set error class to the control group
                },

                success: function(label) {
                    label.closest('.form-group').removeClass('has-error');
                    label.remove();
                },


                submitHandler: function(form) {
                    $.ajax({
                        url: '../app/add_event.php?ev=pmk&e=<?php echo $event['event_token']; ?>',
                        type: "POST",
                        data: $('#new_location').serialize(),
                        success: function(data) {
                            $('#draggable').modal('hide');
                            $('#new_location')[0].reset();
                            toastr.success("<strong>Logan says</strong>:<br/>That location has been added to this events record. Let me refresh the event for you, so you can see the changes.");
                            $.ajax({
                                url: '../app/update_settings.php?update=event&e=<?php echo $event['event_token']; ?>&s=0&cuid=<?php echo $event['event_company_token']; ?>&no_text=true',
                                type: 'POST',
                                data: $('#book_it').serialize(),
                                success: function(d) {
                                    $.ajax({
                                        url: '../app/api/event.php?type=book_now&ev=<?php echo $event['event_token']; ?>',
                                        type: 'POST',
                                        success: function(d){
                                            $(document).find('.rates-form').html(d);
                                        },
                                        error: function(e){

                                        }
                                    });
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
                }
            });

            $('#book_it .button-submit').click(function () {
                $(this).hide();
               $('.cc-form').show();
            });
            <?php $today = date('Y-m-d', strtotime("today midnight")); ?>
            $('#date').daterangepicker({
                    opens: (Metronic.isRTL() ? 'right' : 'left'),
                    startDate: "<?php echo date('Y-m-d', strtotime($today." + 2 days")); ?>",
                    endDate: "<?php echo date('Y-m-d', strtotime($today." + 2 days")); ?>",
                    minDate: "<?php echo date('Y-m-d', strtotime($today." + 2 days")); ?>",
                    showDropdowns: false,
                    showWeekNumbers: false,
                    singleDatePicker: true,
                    timePicker: false,
                    timePickerIncrement: 1,
                    timePicker12Hour: true,
                    buttonClasses: ['btn btn-sm'],
                    applyClass: ' blue',
                    cancelClass: 'default',
                    format: 'YYYY-MM-DD',
                    separator: ' to ',
                    locale: {
                        applyLabel: 'Apply',
                        fromLabel: 'From',
                        toLabel: 'To',
                        customRangeLabel: 'Custom Range',
                        daysOfWeek: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
                        monthNames: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                        firstDay: 0
                    }
                },
                function (start, end) {
                    $('input[name="enddate"]').val(start.format('YYYY-MM-DD'));
                    $('input[name="date"]').val(start.format('YYYY-MM-DD'));
                }
            );

            $("#book_it").validate().element("#time_select");

            $('.rate_changer').on('click', function(){
                var name   = $(this).attr('data-name');
                var value  = $(this).attr('data-value');
                $.ajax({
                    url: '../app/update_settings.php?update=event_fly',
                    type: 'POST',
                    data: {
                        name: name,
                        value: value,
                        pk: "<?php echo $event['event_token']; ?>"
                    },
                    success: function(d){
                        $('.'+name+'_out').html(value);
                        var a = $('#truckfee').attr('data-a');
                        var b = $('#laborrate').attr('data-b');
                        $.ajax({
                            url: '../app/api/event.php?type=math&ev=<?php echo $event['event_token']; ?>',
                            type: 'POST',
                            data: {
                                a: $(a).text(),
                                b: $(b).text()
                            },
                            success: function(d){
                                var e = JSON.parse(d);
                                $("#TF").html(e.truck_fee);
                                $("#LR").html(e.total_labor_rate);
                            },
                            error: function(e){

                            }
                        })
                    },
                    error: function(e){
                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                    }
                });
            });

            $('.bol_comments').on('change', function(){
                var comment = $(this).val();
                $.ajax({
                    url: '../app/update_settings.php?update=ev_bol_comments',
                    type: 'POST',
                    data: {
                        comment: comment,
                        ev: '<?php echo $event['event_token']; ?>'
                    },
                    success: function(bol_cmts){
                        toastr.success("<strong>Logan says:</strong><br/> BOL comments saved (see? told you). ");
                    },
                    error: function(){

                    }
                }) ;
            });


            var a = $('#truckfee').attr('data-a');
            var b = $('#laborrate').attr('data-b');
            $.ajax({
                url: '../app/api/event.php?type=math&ev=<?php echo $event['event_token']; ?>',
                type: 'POST',
                data: {
                    a: $(a).text(),
                    b: $(b).text()
                },
                success: function(d){
                    var e = JSON.parse(d);
                    $("#TF").html(e.truck_fee);
                    $("#LR").html(e.total_labor_rate);
                },
                error: function(e){

                }
            });

            function updateCountdown2() {
                var remaining = 500 - $('.bol_comments').val().length;
                $('.bol_countdown').html('('+ remaining + ' characters remaining)');
            }

            updateCountdown2();

            $('.bol_comments').change(updateCountdown2);
            $('.bol_comments').keyup(updateCountdown2);
            $('.bol_comments').on('change', function(){
                var comment = $(this).val();
                updateCountdown2();
                $.ajax({
                    url: '../app/update_settings.php?update=ev_bol_comments',
                    type: 'POST',
                    data: {
                        comment: comment,
                        ev: '<?php echo $event['event_token']; ?>'
                    },
                    success: function(bol_cmts){
                        toastr.success("<strong>Logan says:</strong><br/> BOL comments saved (see? told you). ");
                    },
                    error: function(){

                    }
                }) ;
            });

            $("#mask_phone").inputmask("mask", {
                "mask": "(999) 999-9999"
            });

            $('#draggable').on('show.bs.modal', function(e) {

                //get data-id attribute of the clicked element
                var type = $(e.relatedTarget).data('location-type');
                var zip  = "<?php echo $event['event_zip']; ?>";

                if(type == 1){
                    $('.extra-forms').show();
                } else {
                    $('.extra-forms').hide();
                }

                //populate the textbox
                $('#zip_auto').val(zip).trigger("change");
                $('#type option[value="'+type+'"]').attr("selected", "selected");
            });
        });
    </script>
    <?php
}