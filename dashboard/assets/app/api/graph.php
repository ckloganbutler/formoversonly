<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 8/25/2017
 * Time: 3:44 AM
 */
session_start();
include '../init.php';

if(isset($_GET['ty']) && $_GET['ty'] == 'lt'){
    $present     = mysql_query("SELECT event_token, event_date_start, event_location_token FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND YEAR(event_date_start)='".mysql_real_escape_string(date('Y'))."' AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_date_start ASC");
    $previous    = mysql_query("SELECT event_token, event_date_start, event_location_token FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND YEAR(event_date_start)='".mysql_real_escape_string(date('Y', strtotime('-1 year')))."' AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_date_start ASC") or die(mysql_error());
    $totals['gross'][date('Y')]                              = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    $totals['gross'][date('Y', strtotime('-1 year'))]        = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    if(mysql_num_rows($present) > 0){
        while($event = mysql_fetch_assoc($present)){
            $location = mysql_fetch_array(mysql_query("SELECT location_sales_tax FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));
            if(!empty($location['location_sales_tax'])){
                $tax = $location['location_sales_tax'];
            } else {$tax = 0;}
            $findItems = mysql_query("SELECT item_item, item_total, item_taxable, item_redeemable, item_prepay, item_desc, item_commission FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."'");
            $iTotalRecords = mysql_num_rows($findItems);
            $findPaid = mysql_query("SELECT payment_amount, payment_type FROM fmo_locations_events_payments WHERE payment_event_token='".mysql_real_escape_string($event['event_token'])."'");
            $bTotalRecords = mysql_num_rows($findPaid);
            $total = array();
            $total['sub_total'] = 0.00;
            $total['tax']       = 0.00;
            $total['cc_fees']   = 0.00;
            $total['total']     = 0.00;
            $total['dis']       = 0.00;
            $total['ppay']      = 0.00;
            $total['paid']      = 0.00;
            $total['unpaid']    = 0.00;
            $totals['code']      = NULL;
            if($iTotalRecords > 0){
                while($item = mysql_fetch_assoc($findItems)){
                    if($item['item_item'] != 'Booking Fee'){

                        if($item['item_total'] > 0){
                            $total['sub_total'] += $item['item_total'];
                        } else {
                            $total['fake_sub_total'] += $item['item_total'];
                        }

                        if($item['item_taxable'] == 1){
                            $total['tax']      += $item['item_total'] * $tax;
                            $totals['taxable']  += $item['item_total'];
                        } else {
                            $total['tax']   += 0.00;
                        }

                        if($item['item_redeemable'] > 0){
                            $totals['discounts'] += $item['item_total'];
                            $totals['prepaid']   += $item['item_prepay'];
                            $totals['code']      =  $item['item_desc'];
                            $total['dis']        += $item['item_total'];
                            $total['ppay']       += $item['item_prepay'];
                        }

                        if($item['item_commission'] > 0){
                            $totals['coms'] += $item['item_total'];
                        } else {
                            $totals['coms'] += 0.00;
                        }
                    } else {
                        $totals['bfs'] += $item['item_total'];
                    }
                }
                $total['total'] = number_format($total['sub_total'], 2, '.', '');
            } else {
                $total['total']     = 0.00;
                $total['sub_total'] = 0.00;
            }
            if($bTotalRecords > 0){
                while($paid = mysql_fetch_assoc($findPaid)){
                    $void = explode(" - ", $paid['payment_type']);
                    if($void[1] != 'VOIDED' && $void[0] != 'Invoice'){
                        $total['paid'] += $paid['payment_amount'];
                        if($void[0] == 'Credit/Debt' && $void[1] != 'Booking Fee'){
                            $total['cc_fees'] += ($paid['payment_amount'] / 1.03) * .03;
                        }
                    }
                }
            }
            $totals['gross'][date('Y')][date('n', strtotime($event['event_date_start']))] += $total['total'] + $total['tax'] + $total['fake_sub_total'] + $total['cc_fees'] + $total['ppay'];
        }
    }

    if(mysql_num_rows($previous) > 0){
        while($event = mysql_fetch_assoc($previous)){
            $location = mysql_fetch_array(mysql_query("SELECT location_sales_tax FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));
            if(!empty($location['location_sales_tax'])){
                $tax = $location['location_sales_tax'];
            } else {$tax = 0;}
            $findItems = mysql_query("SELECT item_item, item_total, item_taxable, item_redeemable, item_prepay, item_desc, item_commission FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."'");
            $iTotalRecords = mysql_num_rows($findItems);
            $findPaid = mysql_query("SELECT payment_amount, payment_type FROM fmo_locations_events_payments WHERE payment_event_token='".mysql_real_escape_string($event['event_token'])."'");
            $bTotalRecords = mysql_num_rows($findPaid);
            $total = array();
            $total['sub_total'] = 0.00;
            $total['tax']       = 0.00;
            $total['cc_fees']   = 0.00;
            $total['total']     = 0.00;
            $total['dis']       = 0.00;
            $total['ppay']      = 0.00;
            $total['paid']      = 0.00;
            $total['unpaid']    = 0.00;
            $totals['code']      = NULL;
            if($iTotalRecords > 0){
                while($item = mysql_fetch_assoc($findItems)){
                    if($item['item_item'] != 'Booking Fee'){

                        if($item['item_total'] > 0){
                            $total['sub_total'] += $item['item_total'];
                        } else {
                            $total['fake_sub_total'] += $item['item_total'];
                        }

                        if($item['item_taxable'] == 1){
                            $total['tax']      += $item['item_total'] * $tax;
                            $totals['taxable']  += $item['item_total'];
                        } else {
                            $total['tax']   += 0.00;
                        }

                        if($item['item_redeemable'] > 0){
                            $totals['discounts'] += $item['item_total'];
                            $totals['prepaid']   += $item['item_prepay'];
                            $totals['code']      =  $item['item_desc'];
                            $total['dis']        += $item['item_total'];
                            $total['ppay']       += $item['item_prepay'];
                        }

                        if($item['item_commission'] > 0){
                            $totals['coms'] += $item['item_total'];
                        } else {
                            $totals['coms'] += 0.00;
                        }
                    } else {
                        $totals['bfs'] += $item['item_total'];
                    }
                }
                $total['total'] = number_format($total['sub_total'], 2, '.', '');
            } else {
                $total['total']     = 0.00;
                $total['sub_total'] = 0.00;
            }
            if($bTotalRecords > 0){
                while($paid = mysql_fetch_assoc($findPaid)){
                    $void = explode(" - ", $paid['payment_type']);
                    if($void[1] != 'VOIDED' && $void[0] != 'Invoice'){
                        $total['paid'] += $paid['payment_amount'];
                        if($void[0] == 'Credit/Debt' && $void[1] != 'Booking Fee'){
                            $total['cc_fees'] += ($paid['payment_amount'] / 1.03) * .03;
                        }
                    }
                }
            }
            $totals['gross'][date('Y', strtotime('-1 year'))][date('n', strtotime($event['event_date_start']))] += $total['total'] + $total['tax'] + $total['fake_sub_total'] + $total['cc_fees'] + $total['ppay'];
        }
    }

    echo json_encode($totals['gross']);
}