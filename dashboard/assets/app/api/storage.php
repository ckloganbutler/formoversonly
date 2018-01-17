<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 11/26/2017
 * Time: 3:28 PM
 */
session_start();
include '../init.php';


if(isset($_GET['type']) && $_GET['type'] == 'rates'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findRates = mysql_query("SELECT services_id, services_item, services_item_desc, services_taxable, services_commissionable, services_type FROM fmo_services WHERE services_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (services_type LIKE '%Supplies%' OR services_type LIKE '%Discount%' OR services_type LIKE '%Storage%')");
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
        if(isset($_GET['rt'])){
            $tty = 1;
        } else {$tty = 0;}
        if($services['services_type'] == 'Discount'){
            $records["data"][] = array(
                '<a class="bold">'.$services['services_item'].'</a> '.$icon.$icon2,
                '<button type="button" data-id="'.$services['services_id'].'" data-uuid="'.$_GET['uuid'].'" data-ct="'.$_GET['ct'].'" data-tty="'.$tty.'" data-luid="'.$_GET['luid'].'" data-qty="1" class="btn default btn-xs blue-stripe add_contract_item"><i class="fa fa-plus"></i> Add to invoice</a>',
            );
        } else {
            $records["data"][] = array(
                ''.$services['services_item'].' '.$icon.$icon2,
                '<button type="button" data-id="'.$services['services_id'].'" data-uuid="'.$_GET['uuid'].'" data-ct="'.$_GET['ct'].'" data-tty="'.$tty.'" data-luid="'.$_GET['luid'].'" data-qty="1" class="btn default btn-xs blue-stripe add_contract_item"><i class="fa fa-plus"></i> Add to invoice</a>',
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
    $findItems = mysql_query("SELECT item_id, item_item, item_desc, item_qty, item_cost, item_total, item_redeemable, item_discount, item_percent FROM fmo_locations_storages_contracts_items WHERE item_contract_token='".mysql_real_escape_string($_GET['ct'])."' ");
    $iTotalRecords = mysql_num_rows($findItems);

    $records = array();
    $records["data"] = array();

    if(isset($_GET['rt'])){
        $reload = "rt";
    } else {$reload = "ct";}
    while($items = mysql_fetch_assoc($findItems)) {
        $symbol = ($items['item_percent'] == 1) ? "" : "$";
        if($items['item_discount'] == 1){
            $options = '<a class="btn default btn-xs red-stripe edit pull-right no_print" data-edit="items_'.$items['item_id'].'" data-reload="'.$reload.'" data-update="sales" data-selec="autoselect" data-event="'.$_GET['ct'].'" data-luid="'.$_GET['luid'].'"><i class="fa fa-edit"></i> Edit</a> <a class="btn default btn-xs red delete_item_str pull-right no_print" data-delete="items_'.$items['item_id'].'" data-event="'.$_GE['ct'].'"><i class="fa fa-times"></i></a>';
            $records["data"][] = array(
                '<strong>DISCOUNT</strong>: '.$items['item_item'].' '.$options.'',
                '<a class="items_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_desc" data-pk="'.$items['item_id'].'" data-type="text" data-placement="right" data-title="Enter new description.." data-url="assets/app/update_settings.php?setting=c_items">'.$items['item_desc'].'</a>',
                '<a class="items_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_qty" data-pk="'.$items['item_id'].'" data-type="number" data-placement="right" data-title="Enter new quantity.." data-url="assets/app/update_settings.php?setting=c_items">'.$items['item_qty'].'</a>',
                ''.$symbol.'<a class="items_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_cost" data-pk="'.$items['item_id'].'" data-type="number" data-placement="right" data-title="Enter new cost.." data-url="assets/app/update_settings.php?setting=c_items">'.$items['item_cost'].'</a>',
                '<strong class="text-danger pull-right">'.$symbol.$items['item_total'].'</strong>'
            );
        } else {
            $options = '<a class="btn default btn-xs red-stripe edit pull-right no_print" data-edit="items_'.$items['item_id'].'" data-reload="'.$reload.'" data-update="sales" data-selec="autoselect" data-event="'.$_GET['ct'].'" data-luid="'.$_GET['luid'].'"><i class="fa fa-edit"></i> Edit</a> <a class="btn default btn-xs red delete_item_str pull-right no_print" data-delete="items_'.$items['item_id'].'" data-event="'.$_GET['ct'].'"><i class="fa fa-times"></i></a>';
            $records["data"][] = array(
                ''.$items['item_item'].' '.$options.'',
                '<a class="items_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_desc" data-pk="'.$items['item_id'].'" data-type="text" data-placement="right" data-title="Enter new description.." data-url="assets/app/update_settings.php?setting=c_items">'.$items['item_desc'].'</a>',
                '<a class="items_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_qty" data-pk="'.$items['item_id'].'" data-type="number" data-placement="right" data-title="Enter new quantity.." data-url="assets/app/update_settings.php?setting=c_items">'.$items['item_qty'].'</a>',
                ''.$symbol.'<a class="items_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_cost" data-pk="'.$items['item_id'].'" data-type="number" data-placement="right" data-title="Enter new cost.." data-url="assets/app/update_settings.php?setting=c_items">'.$items['item_cost'].'</a>',
                '<strong class="text-danger pull-right">'.$symbol.$items['item_total'].'</strong>'
            );
        }


    }

    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET['type']) && $_GET['type'] == 'rent'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findItems = mysql_query("SELECT item_id, item_item, item_desc, item_qty, item_cost, item_total, item_redeemable, item_discount, item_percent FROM fmo_locations_storages_contracts_items WHERE item_user_token='".mysql_real_escape_string($_GET['uuid'])."' AND NOT item_paid=1");
    $iTotalRecords = mysql_num_rows($findItems);

    $records = array();
    $records["data"] = array();

    if(isset($_GET['rt'])){
        $reload = "rt";
    } else {$reload = "ct";}
    while($items = mysql_fetch_assoc($findItems)) {
        $symbol = ($items['item_percent'] == 1) ? "" : "$";
        if($items['item_discount'] == 1){
            $options = '<a class="btn default btn-xs red-stripe edit pull-right no_print" data-edit="items_'.$items['item_id'].'" data-reload="'.$reload.'" data-update="sales" data-selec="autoselect" data-event="'.$_GET['ct'].'" data-luid="'.$_GET['luid'].'"><i class="fa fa-edit"></i> Edit</a> <a class="btn default btn-xs red delete_item_str pull-right no_print" data-delete="items_'.$items['item_id'].'" data-event="'.$_GE['ct'].'"><i class="fa fa-times"></i></a>';
            $records["data"][] = array(
                '<strong>DISCOUNT</strong>: '.$items['item_item'].' '.$options.'',
                '<a class="items_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_desc" data-pk="'.$items['item_id'].'" data-type="text" data-placement="right" data-title="Enter new description.." data-url="assets/app/update_settings.php?setting=c_items">'.$items['item_desc'].'</a>',
                '<a class="items_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_qty" data-pk="'.$items['item_id'].'" data-type="number" data-placement="right" data-title="Enter new quantity.." data-url="assets/app/update_settings.php?setting=c_items">'.$items['item_qty'].'</a>',
                ''.$symbol.'<a class="items_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_cost" data-pk="'.$items['item_id'].'" data-type="number" data-placement="right" data-title="Enter new cost.." data-url="assets/app/update_settings.php?setting=c_items">'.$items['item_cost'].'</a>',
                '<strong class="text-danger pull-right">'.$symbol.$items['item_total'].'</strong>'
            );
        } else {
            $options = '<a class="btn default btn-xs red-stripe edit pull-right no_print" data-edit="items_'.$items['item_id'].'" data-reload="'.$reload.'" data-update="sales" data-selec="autoselect" data-event="'.$_GET['ct'].'" data-luid="'.$_GET['luid'].'"><i class="fa fa-edit"></i> Edit</a> <a class="btn default btn-xs red delete_item_str pull-right no_print" data-delete="items_'.$items['item_id'].'" data-event="'.$_GET['ct'].'"><i class="fa fa-times"></i></a>';
            $records["data"][] = array(
                ''.$items['item_item'].' '.$options.'',
                '<a class="items_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_desc" data-pk="'.$items['item_id'].'" data-type="text" data-placement="right" data-title="Enter new description.." data-url="assets/app/update_settings.php?setting=c_items">'.$items['item_desc'].'</a>',
                '<a class="items_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_qty" data-pk="'.$items['item_id'].'" data-type="number" data-placement="right" data-title="Enter new quantity.." data-url="assets/app/update_settings.php?setting=c_items">'.$items['item_qty'].'</a>',
                ''.$symbol.'<a class="items_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_cost" data-pk="'.$items['item_id'].'" data-type="number" data-placement="right" data-title="Enter new cost.." data-url="assets/app/update_settings.php?setting=c_items">'.$items['item_cost'].'</a>',
                '<strong class="text-danger pull-right">'.$symbol.$items['item_total'].'</strong>'
            );
        }


    }

    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET['type']) && $_GET['type'] == 'inv'){
    $ct      = (empty($_POST['contract'])) ? $_GET['ct'] : $_POST['contract'];
    $storage = mysql_fetch_array(mysql_query("SELECT storage_period FROM fmo_locations_storages WHERE storage_token='".mysql_real_escape_string($_GET['su'])."'"));
    $location = mysql_fetch_array(mysql_query("SELECT location_storage_creditcard_fee FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    $findItems = mysql_query("SELECT item_total, item_taxable, item_taxable_amount, item_commission FROM fmo_locations_storages_contracts_items WHERE item_contract_token='".mysql_real_escape_string($ct)."'");
    $iTotalRecords = mysql_num_rows($findItems);

    $findPaid = mysql_query("SELECT payment_amount, payment_type FROM fmo_locations_storages_contracts_payments WHERE payment_contract_token='".mysql_real_escape_string($ct)."'");
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
                $tax = $item['item_taxable_amount'];
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

    }

    if($bTotalRecords > 0){
        while($paid = mysql_fetch_assoc($findPaid)){
            $void = explode(" - ", $paid['payment_type']);
            if($void[1] != 'VOIDED' && $void[0] != 'Invoice'){
                $total['paid'] += $paid['payment_amount'];
                if($void[0] == 'Credit/Debt' && $void[1] != 'Booking Fee'){
                    $total['total']   += ($paid['payment_amount'] / (1 + $location['location_storage_creditcard_fee'])) * $location['location_storage_creditcard_fee'];
                    $total['cc_fees'] += ($paid['payment_amount'] / (1 + $location['location_storage_creditcard_fee'])) * $location['location_storage_creditcard_fee'];
                }
            }

        }
        $total['unpaid'] = number_format($total['total'] - $total['paid'], 2, '.', '');
        $total['amount'] = intval(floatval(str_replace("$", "", $total['unpaid']))*100);
    } else {
        $total['unpaid'] = number_format($total['total'], 2, '.', '');
        $total['amount'] = intval(floatval(str_replace("$", "", $total['unpaid']))*100);
    }

    if(!isset($_GET['no_calc'])){
        $mr    = $_POST['mr'];
        $date1 = new DateTime(date('Y-m-d', strtotime($_POST['date1'])));
        $date2 = new DateTime(date('Y-m-d', strtotime($_POST['date2'])));

        if($storage['storage_period'] == 'Weekly'){
            $dpm = 7;
        } else { $dpm = cal_days_in_month(CAL_GREGORIAN, date('m', strtotime($date1)), date('Y', strtotime($date1))); }


        $interval        = $date1->diff($date2);
        $days            = $interval->days;
        $total['rent']         = number_format(($mr / $dpm) * $days, 2, '.', '');
        $total['deposit']      = $_POST['deposit'];

        $total['total']     += $total['rent'] + $total['deposit'];
        $total['sub_total'] += $total['rent'] + $total['deposit'];
    }
    echo json_encode($total);
}

if(isset($_GET['type']) && $_GET['type'] == 'inv_c'){
    $uuid     = (empty($_POST['uuid'])) ? $_GET['uuid'] : $_POST['uuid'];
    $location = mysql_fetch_array(mysql_query("SELECT location_storage_creditcard_fee FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    $total = array();
    $total['sub_total'] = 0.00;
    $total['tax']       = 0.00;
    $total['taxable']   = 0.00;
    $total['cc_fees']   = 0.00;
    $total['total']     = 0.00;
    $total['paid']      = 0.00;
    $total['unpaid']    = 0.00;
    $findItems = mysql_query("SELECT item_total, item_taxable, item_taxable_amount, item_commission FROM fmo_locations_storages_contracts_items WHERE item_user_token='".mysql_real_escape_string($uuid)."'");
    $iTotalRecords = mysql_num_rows($findItems);

    $findPaid = mysql_query("SELECT payment_amount, payment_type FROM fmo_locations_storages_contracts_payments WHERE payment_user_token='".mysql_real_escape_string($uuid)."'");
    $bTotalRecords = mysql_num_rows($findPaid);

    if($iTotalRecords > 0){
        while($item = mysql_fetch_assoc($findItems)){
            $total['sub_total'] += $item['item_total'];
            if($item['item_taxable'] == 1){
                $tax = $item['item_taxable_amount'];
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
    }

    if($bTotalRecords > 0){
        while($paid = mysql_fetch_assoc($findPaid)){
            $void = explode(" - ", $paid['payment_type']);
            if($void[1] != 'VOIDED' && $void[0] != 'Invoice'){
                $total['paid'] += $paid['payment_amount'];
                if($void[0] == 'Credit/Debt' && $void[1] != 'Booking Fee'){
                    $total['total']   += ($paid['payment_amount'] / (1 + $location['location_storage_creditcard_fee'])) * $location['location_storage_creditcard_fee'];
                    $total['cc_fees'] += ($paid['payment_amount'] / (1 + $location['location_storage_creditcard_fee'])) * $location['location_storage_creditcard_fee'];
                }
            }

        }
        $total['unpaid'] = number_format($total['total'] - $total['paid'], 2, '.', '');
        $total['amount'] = intval(floatval(str_replace("$", "", $total['unpaid']))*100);
    } else {
        $total['unpaid'] = number_format($total['total'], 2, '.', '');
        $total['amount'] = intval(floatval(str_replace("$", "", $total['unpaid']))*100);
    }
    echo json_encode($total);
}

if(isset($_GET['type']) && $_GET['type'] == 'contract'){
    $profile  = mysql_fetch_array(mysql_query("SELECT user_fname, user_lname, user_email, user_phone, user_token, user_employer_dln FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'"));
    $storage  = mysql_fetch_array(mysql_query("SELECT storage_unit_name, storage_location_token, storage_unit_lwh, storage_unit_desc, storage_price, storage_period, storage_occupant, storage_contract_token, storage_last_occupied, storage_status, storage_type_id FROM fmo_locations_storages WHERE storage_token='".mysql_real_escape_string($_GET['su'])."'"));
    $contract = mysql_fetch_array(mysql_query("SELECT contract_start, contract_address, contract_city, contract_state, contract_zip, contract_rate_adj, contract_deposit, contract_email, contract_phone, contract_by FROM fmo_locations_storages_contracts WHERE contract_token='".$storage['storage_contract_token']."'"));
    $types    = mysql_fetch_array(mysql_query("SELECT type_floor, type_desc, type_climate FROM fmo_locations_storages_types WHERE type_id='".mysql_real_escape_string($storage['storage_type_id'])."'"));
    $location = mysql_fetch_array(mysql_query("SELECT location_storage_stripe_public, location_storage_days_late, location_storage_days_auction, location_storage_tax, location_storage_deposit, location_storage_creditcard_fee, location_nickname, location_storage_late_fee, location_storage_auction_fee FROM fmo_locations WHERE location_token='".mysql_real_escape_string($storage['storage_location_token'])."'"));

    ?>
    <table class="table table-full-width table-bordered">
        <thead>
        <tr>
            <th colspan="3">
                <h3 class="text-center"><?php echo $location['location_nickname']; ?> - RENTAL AGREEMENT <br/> <small>6800 E. 30th Street, Indianapolis, Indiana, 46219</small></h3>
            </th>
        </tr>
        <tr>
            <th colspan="3" style="border-right: none; border-top: none;">
                <h5>Customer Information</h5>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td colspan="1">
                <?php echo name($profile['user_token']); ?> <br/>
                <?php echo $contract['contract_address']; ?> <br/>
                <?php echo $contract['contract_city']; ?>, <?php echo $contract['contract_state']; ?>, <?php echo $contract['contract_zip']; ?> <br/>
            </td>
            <td colspan="2">
                <i class="fa fa-phone"></i> &nbsp; <strong><?php echo clean_phone($contract['contract_phone']); ?></strong> <br/>
                <i class="fa fa-envelope"></i> &nbsp; <strong id="contract_email"><?php echo $contract['contract_email']; ?></strong> <br/>
                <i class="fa fa-user"></i> &nbsp; Driver's License Number: <strong id="contract_dln"><?php echo $contract['contract_dln']; ?></strong> <br/>
            </td>
        </tr>
        <tr>
            <td colspan="3" style="font-size: 10px;">
                <strong><?php echo $location['location_nickname']; ?></strong> acknowledges that your e-mail address and mobile phone number is highly confidential and they will be treated with the utmost respect.  We do not provide, supply, sell or otherwise distribute your personal information, including email address, to any third party.   However, I hereby authorize <strong><?php echo $location['location_nickname']; ?></strong> to contact me in any way possible including email and text messages. <br/>
                <br/>
                Emergency Alternate Contact: Designate a person residing at a permanent address other than your own.
            </td>
        </tr>
        <tr>
            <th>
                Alternate Contact Name
            </th>
            <th>
                Address / Relationship
            </th>
            <th>
                Phone
            </th>
        </tr>
        <?php
        $a = 0; $alts = mysql_query("SELECT alt_id, alt_name, alt_address, alt_phone FROM fmo_locations_storages_alts WHERE alt_user_token='".mysql_real_escape_string($profile['user_token'])."'");
        while($alt = mysql_fetch_assoc($alts)){
            $a++;
            ?>
            <tr id="a<?php echo $a; ?>">
                <td><?php echo $alt['alt_name']; ?></td>
                <td><?php echo $alt['alt_address']; ?></td>
                <td><?php echo clean_phone($alt['alt_phone']); ?></td>
            </tr>
            <?php
        } if($a < 3){
            for($i = $a; $i < 3; $i++){
                ?>
                <tr id="a<?php echo $i; ?>">
                    <td>&nbsp;</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <?php
            }
        }
        ?>
        <tr>
            <td colspan="3" style="font-size: 10px">
                ONLY THE CUSTOMER AND AUTHORIZED ACCESS PERSONS WILL BE ALLOWED INTO TO THE ROOM UNDER THIS AGREEMENT. <?php echo $location['location_nickname']; ?> MUST BE NOTIFIED IN WRITING ANY CHANGES OF AUTHORIZED PERSONS, ADDRESS, TELEPHONE, OR LOST OR STOLEN CARDS.

                <br/>
                <h6 class="text-center bold">ATTENTION</h6>
                <p class="text-muted">
                    This is a month-to-month lease. The term of this tenancy shall commence on the rental agreement date written, and shall continue thereafter on a monthly basis. Rent is payable in advance of the rental agreement date specified. <strong><?php echo $location['location_nickname']; ?></strong> is not a bailee of customer`s property. <strong><?php echo $location['location_nickname']; ?></strong> does not accept control, custody or responsibility for the care of property. <strong><?php echo $location['location_nickname']; ?></strong> does not provide insurance in any way for items being stored or vehicles used in the transportation in and out of the facility. <strong><?php echo $location['location_nickname']; ?></strong> is in no way liable for indoor or outdoor storage item without an insurance policy naming such item.  All outdoor storage is at the customer's sole risk.  Customer shall notify <strong><?php echo $location['location_nickname']; ?></strong> immediately, in writing, of address or telephone changes. Customers must provide their own diskus style lock (only one customer lock per room). <strong><?php echo $location['location_nickname']; ?></strong> may, but is not required to, lock the space if it is found open. Rent paid in advance is considered prepaid rent and will be refunded upon vacating. There is no refund for unused days if you vacate after the rent due date of the current month. <strong><?php echo $location['location_nickname']; ?></strong> reserves the right to change storage room rates with 30 days prior written notice to customer. It is your responsibility to pay on or before the due date. Free self-addressed payment envelopes may be provided for mailing rental payments. <strong><?php echo $location['location_nickname']; ?></strong> has the right to establish or change hours of operation or to proclaim rules and amendments, or additional rules and regulations for the safety, care and cleanliness of the premises or the preservation of good order at the facility. Customer agrees to follow all of the <strong><?php echo $location['location_nickname']; ?></strong> rules currently in effect, or that may be put into effect from time to time. Customer`s access to the premises may be conditioned in any manner deemed reasonably necessary by <strong><?php echo $location['location_nickname']; ?></strong> to maintain order on the premises. Such measures may include, but are not limited to, requiring verification of customer`s identity, limiting hours of operation and requiring customer to sign in and sign out upon entering and leaving the premises. Customer Understands all sizes are approximate.
                    <br/><br/>
                </p>
                <h6 class="text-center bold">CAUTION</h6>
                <p class="text-muted">
                    If rent is not paid on or before the due date, a $<?php echo number_format($location['location_storage_late_fee'], 2); ?> late charge is due.  A $<?php echo number_format($location['location_storage_auction_fee'], 2); ?> lien processing fee plus all expenses associated with the sale will also be charged when the rent is <?php echo $location['location_storage_days_auction']; ?> days late. The customer shall bear all risks of loss or damage to any and all property stored in the rental space, including, but not limited to, loss or damage resulting from the negligence of <strong><?php echo $location['location_nickname']; ?></strong>. <strong><?php echo $location['location_nickname']; ?></strong> is hereby given a contractual landlord's lien upon all property stored by the customer to secure payment of all monies due under this agreement, including any fees and costs. The lien exists and will be enforceable from the date rent or other charges are due and unpaid. The property shall be deemed to be attached from the first day of this agreement. The property stored in the leased space may be sold to satisfy the lien if customer remains in default for 30 days or more.Written notice will be sent to the customer during the default period. Proceeds from the sale will be distributed first to satisfy all liens. The remainder, if any, will be held for the customer for six months, then the funds will be transferred to the appropriate state authority. This lien and all rights granted are in addition to any lien or rights granted by the statutes of the state. In addition to the rents and charges agreed upon and provided for in this rental agreement, customer shall be liable for all costs, fees and expenses, including attorney's fees, reasonably incurred, incident to default, present or future, for the preservation, storage, inventory, advertisement and sale of the property stored in the rental space, or other disposition, and to enforce the rights provided for under this rental agreement. <strong><?php echo $location['location_nickname']; ?></strong> shall be entitled to attorney fees and costs incurred in enforcing its rights under this agreement. Upon default of any obligation under this rental agreement, customer and all authorized individuals shall be denied access to the property contained in the rental space until such time that the default has been remedied and the total balance owed has been paid in full. Customer shall be permitted to have access to the rental space for the purpose of viewing and verifying the contents of the rental space during the default period. A minimum $10 cleaning fee will be assessed if the space is dirty or in need of repair at contract termination. Customer can use dumpster only after paying appropriate Dumpster fee. Customers are never to use dumpsters for disposal of hazardous or toxic materials, or wastes (e.g., paints, chemicals, flammables, etc.), off-site refuse or items such as couches, mattresses, etc.
                </p>
                <h6 class="text-center bold">WARNING</h6>
                <p class="text-muted">
                    Customer shall have access to the rental space only for the purpose of storing and removing property stored in that rental space. The rental space shall not be used for residential purposes or operation of a business. Customer agrees not to store any hazardous materials, hazardous substance, hazardous waste, solid waste, toxic chemicals, illegal goods, explosives, highly flammable materials, perishable foods or any other goods which may cause danger or damage to the rental space. Customer agrees not to store any living creature or organism, or any dead animal or other carcass. Customer agrees that personal property and rental space shall not be used for any unlawful purpose. Customer agrees not to store property with a total value in excess of $5,000. Customer agrees not to leave waste, not to alter or affix signs on the rental space and agrees to keep the rental space in good condition during the term of the rental agreement. <strong><?php echo $location['location_nickname']; ?></strong> property, such as furniture pads or storage carts, shall not be placed or locked in the rental space. Customer agrees not to store collectibles, heirlooms, jewelry, works of art or any other item of sentimental value.
                </p>
                <h6 class="text-center bold">LOW COST INSURANCE COVERAGE TERMS AND CONDITIONS</h6>
                <p class="text-muted">
                    Insurance coverage is only effective for customers who have elected Insurance protection on the reverse side of this form, and paid the appropriate Insurance fee. Valuation of Loss: Loss is adjusted at actual cash value. There is a $100 deductible for each loss occurrence and property is covered only while within the <strong><?php echo $location['location_nickname']; ?></strong> storage room. If a customer rents more than one room, Insurance must be purchased separately for each room the customer wishes to protect. Exclusions: There is no protection for: (1) loss or damage to bills, currency, securities, notes, deeds, furs, antiques, jewelry, artwork, precious metals or stones, vehicles or contraband (2) loss resulting from theft, except burglary* evidenced by visible signs of forced entry (3) loss resulting from mysterious disappearance, intentional or criminal acts (4) damage resulting from flood, tidal waters, groundwater or any subsurface water including sewers and drains (5) damage resulting from nuclear explosion or contamination, war or civil insurrection, natural deterioration, vermin, insect infestation, wear and tear or atmospheric change.*Burglary coverage is limited to 50% of the coverage amount unless replacement cost insurance option was selected. Protection Period: Insurance fees must be paid in advance for the same number of months for which you make storage-rent payments. Nonpayment or breach of rental agreement automatically terminates this protection.
                </p>
            </td>
        </tr>
        <tr>
            <td colspan="1" style="font-size: 12px; width: 49%;" class="bold text-center">
                Credit Card / RECURRING ACH Payment Plan:<br/>
                If i provide my credit card information, I have authorized <strong><?php echo $location['location_nickname']; ?></strong> to automatically debit my bank account or charge my credit card as applicable and requested every month for all charges associated with my storage room. (Cardholder agrees to notify <strong><?php echo $location['location_nickname']; ?></strong> of any changes to the banking or credit card information (account number and expiration date). <br/><span class="font-xs text-muted">If you use a debit/credit card a <?php echo number_format($location['location_storage_creditcard_fee'] * 100, 0); ?>% card fee will be applied to your total transaction.</span>
            </td>
            <td colspan="2" class="bold text-center">
                Failure to pay within <?php echo $location['location_storage_days_late']; ?> day(s) after your due date will result in:<br/>
                $<?php echo number_format($location['location_storage_late_fee'], 2); ?> late-fee charged. Denied access to your room.<br/>
                $<?php echo number_format($location['location_storage_auction_fee'], 2); ?> lien processing fee. Assessment of a lien and sales of stored goods at public auction.<br/>
                Cash payments that are over the amount due will be applied as credit - <span class="text-muted">no change given</span>
                <br/><br/>
                Agreed unit and rate<br/>
                <hr/>
                <h6 style="margin-top: 0;"><strong class="font-red"><?php echo $storage['storage_unit_lwh']; ?> - Unit #: <?php echo $storage['storage_unit_name']; ?></strong> (Floor <?php echo $types['type_floor'].", ".$types['type_desc']." [Climate: ".$types['type_climate']."]"; ?>) </h6>
                <h6><span class="text-success bold">$<?php echo number_format($storage['storage_price'] + $contract['contract_rate_adj'], 2); ?></span>/<?php echo $storage['storage_period']; ?> on-going every <strong><?php echo date('dS', strtotime($contract['contract_start'])); ?></strong> of each month.</h6>
            </td>
        </tr>
        <tr>
            <td colspan="1" class="text-center" style="border-top: none;">
                <br/><br/>
                X Authorized Signature____________________________________________
            </td>
            <td colspan="2"class="text-center" style="border-top: none;">
                <br/><br/>
                X Customer Signature:____________________________________________Date:_____________.
            </td>
        </tr>
        </tbody>
    </table>
    <?php
}