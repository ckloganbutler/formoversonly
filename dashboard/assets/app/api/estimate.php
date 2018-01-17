<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 7/29/2017
 * Time: 4:49 AM
 */

include '../init.php';

if(isset($_GET['type']) && $_GET['type'] == 'math'){
    $math  = array();
    $a = $_POST['a'];
    $b = $_POST['b'];
    $c = $_POST['c'];
    $d = $_POST['d'];
    $e = $_POST['e'];
    $f = $_POST['f'];
    $estimate = mysql_fetch_array(mysql_query("SELECT estimate_truckrate_rate, estimate_truckfee_rate, estimate_laborrate_rate, estimate_weekend_upcharge_rate FROM fmo_locations_events_estimates WHERE estimate_token='".mysql_real_escape_string($_GET['est'])."'"));
    $math['truck_fee']        = $estimate['estimate_truckfee_rate'] * $a;
    $math['labor_rate']       = $estimate['estimate_laborrate_rate'];
    $math['truck_rate']       = $estimate['estimate_truckrate_rate'];
    $math['upcharge']         = $estimate['estimate_weekend_upcharge_rate'];
    if($estimate['estimate_weekend_upcharge_rate'] > 0){
        $math['total_labor_rate'] = ($estimate['estimate_laborrate_rate'] * $b) + ($estimate['estimate_truckrate_rate'] * $a) + $estimate['estimate_weekend_upcharge_rate'];
    } else {
        $math['total_labor_rate'] = ($estimate['estimate_laborrate_rate'] * $b) + ($estimate['estimate_truckrate_rate'] * $a);
    }
    $math['packing']          = $math['total_labor_rate'] * $d;
    $math['transport']        = $math['total_labor_rate'] * $e;
    $math['unload']           = $math['total_labor_rate'] * $f;
    $math['county_fee']       = ($math['total_labor_rate'] * .5) * $c;
    $math['total']            = number_format(($math['truck_fee'] + $math['total_labor_rate']) + ($math['packing'] + $math['transport'] + $math['unload'] + $math['county_fee']), 2);
    $math['valuation']        = number_format($math['total'] * .30, 2);
    echo json_encode($math);
}

if(isset($_GET['type']) && $_GET['type'] == 'rates'){
    $iDisplayLength = intval($_REQUEST['length']);
    $iDisplayStart = intval($_REQUEST['start']);
    $sEcho = intval($_REQUEST['draw']);
    $findRates = mysql_query("SELECT services_id, services_item, services_item_desc, services_taxable, services_type FROM fmo_services WHERE services_location_token='".mysql_real_escape_string($_GET['luid'])."'");
    $iTotalRecords = mysql_num_rows($findRates);

    $records = array();
    $records["data"] = array();

    while($services = mysql_fetch_assoc($findRates)) {
        if($services['services_taxable'] == 0) {
            $taxable_tag = '<span class="label label-sm label-danger">NO</span>';
        } else {
            $taxable_tag = '<span class="label label-sm label-success">YES</span>';
        }
        if($services['services_type'] == 'Discount'){
            $records["data"][] = array(
                '<a>'.$services['services_item'].'</a>',
                '<button type="button" data-id="'.$services['services_id'].'" data-est="'.$_GET['est'].'" data-luid="'.$_GET['luid'].'" class="btn default btn-xs blue-stripe add_item"><i class="fa fa-plus"></i> Add to estimate</a>',
            );
        } else {
            $records["data"][] = array(
                ''.$services['services_item'].'',
                '<button type="button" data-id="'.$services['services_id'].'" data-est="'.$_GET['est'].'" data-luid="'.$_GET['luid'].'" class="btn default btn-xs blue-stripe add_item"><i class="fa fa-plus"></i> Add to estimate</a>',
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
    $findItems = mysql_query("SELECT item_id, item_item, item_desc, item_qty, item_cost, item_total, item_redeemable FROM fmo_locations_events_estimates_items WHERE item_estimate_token='".mysql_real_escape_string($_GET['est'])."'");
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
            if($items['item_item'] == 'Booking Fee' || isset($_GET['n_edt'])){
                $options = NULL;
            } else {
                if(!isset($_GET['e']) && $_GET['e'] != 'f'){
                    $options = '<a class="btn default btn-xs red-stripe edit pull-right no_print" data-edit="item_'.$items['item_id'].'" data-reload="est" data-selec="autoselect" data-estimate="'.$_GET['est'].'" data-luid="'.$_GET['luid'].'"><i class="fa fa-edit"></i> Edit</a> <a class="btn default btn-xs red delete_item pull-right no_print" data-delete="item_'.$items['item_id'].'" data-estimate="'.$_GET['est'].'"><i class="fa fa-times"></i></a>';
                    $records["data"][] = array(
                        ''.$items['item_item'].' '.$options.'',
                        '<a class="item_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_desc" data-pk="'.$items['item_id'].'" data-type="text" data-placement="right" data-title="Enter new description.." data-url="../app/update_settings.php?setting=estimate_items">'.$items['item_desc'].'</a>',
                        '<a class="item_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_qty" data-pk="'.$items['item_id'].'" data-type="number" data-placement="right" data-title="Enter new quantity.." data-url="../app/update_settings.php?setting=estimate_items">'.$items['item_qty'].'</a>',
                        '<a class="item_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_cost" data-pk="'.$items['item_id'].'" data-type="number" data-placement="right" data-title="Enter new cost.." data-url="../app/update_settings.php?setting=estimate_items">'.$items['item_cost'].'</a>',
                        '<strong class="text-danger pull-right">'.$items['item_total'].'</strong>'
                    );
                } else {
                    $records["data"][] = array(
                        ''.$items['item_item'].'',
                        '<a class="item_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_qty" data-pk="'.$items['item_id'].'" data-type="number" data-placement="right" data-title="Enter new quantity.." data-url="../app/update_settings.php?setting=estimate_items">'.$items['item_qty'].'</a>',
                        '<a class="item_'.$items['item_id'].'" style="color:#333333" data-inputclass="form-control" data-name="item_cost" data-pk="'.$items['item_id'].'" data-type="number" data-placement="right" data-title="Enter new cost.." data-url="../app/update_settings.php?setting=estimate_items">'.$items['item_cost'].'</a>',
                        '<strong class="text-danger pull-right">'.$items['item_total'].'</strong>'
                    );
                }

            }

        }

    }

    $records["draw"] = $sEcho;
    $records["recordsTotal"] = $iTotalRecords;
    $records["recordsFiltered"] = $iTotalRecords;

    echo json_encode($records);
}

if(isset($_GET['type']) && $_GET['type'] == 'inv'){
    $location = mysql_fetch_array(mysql_query("SELECT location_sales_tax FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));

    if(!empty($location['location_sales_tax'])){
        $tax = $location['location_sales_tax'];
    } else {$tax = 0;}

    $findItems = mysql_query("SELECT item_total, item_taxable FROM fmo_locations_events_estimates_items WHERE item_estimate_token='".mysql_real_escape_string($_POST['estimate'])."'");
    $iTotalRecords = mysql_num_rows($findItems);


    $total = array();
    $total['sub_total'] = 0.00;
    $total['tax']       = 0.00;
    $total['total']     = 0.00;
    $total['unpaid']    = 0.00;
    if($iTotalRecords > 0){
        while($item = mysql_fetch_assoc($findItems)){
            $total['sub_total'] += $item['item_total'];
            if($item['item_taxable'] == 1){
                $total['tax']   += number_format($item['item_total'] * $tax, 2, '.', '');
            } else {
                $total['tax']   += 0.00;
            }
        }
        $total['total'] = number_format($total['sub_total'] + $total['tax'], 2, '.', '');
    } else {
        $total['total']     = 0.00;
        $total['sub_total'] = 0.00;
    }

    $total['unpaid'] = number_format($total['total'], 2, '.', '');
    $total['paid']   = 0.00;

    echo json_encode($total);
}

