<?php
/**
 * Created by PhpStorm.
 * User: gameroom
 * Date: 6/20/2017
 * Time: 3:13 PM
 */
session_start();
include '../../app/init.php';

if(isset($_SESSION['logged'])){
    $uuidperm = mysql_fetch_array(mysql_query("SELECT user_esc_permissions FROM fmo_users WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'"));
    if($_POST['type'] == 'pryl'){
        $range = explode(" - ", $_POST['ext']);
        if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_payroll") !== false){
            ?>
            <div class="col-md-12">
                <div class="portlet">
                    <div class="portlet-title tabbable-line">
                        <div class="caption caption-md">
                            <i class="fa fa-bar-chart-o theme-font bold"></i>
                            <span class="caption-subject font-red bold uppercase">PAYROLL</span> <span class="font-red">|</span> <button class="btn btn-xs red-stripe print" data-print="<?php if($_SESSION['group'] == 1){echo "#payrollsummary_admin";}else{echo"#payrollsummary";} ?>"><i class="fa fa-print"></i> Print</button>
                        </div>
                        <ul class="nav nav-tabs">
                            <?php
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_payroll_location_summary") !== false){
                                ?>
                                <li class='active'>
                                    <a href="#payrollsummary" data-toggle="tab" aria-expanded="true" style="color: black;" class="tab_print" data-print="#payrollsummary">
                                        Location Payroll Summary</a>
                                </li>
                                <?php
                            }
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_payroll_company_summary") !== false){
                                ?>
                                <li >
                                    <a href="#payrollsummary_admin" data-toggle="tab" aria-expanded="true" style="color: black;" class="tab_print" data-print="#payrollsummary_admin">
                                        Company Payroll Summary</a>
                                </li>
                                <?php
                            }
                            ?>

                        </ul>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content">
                            <?php
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_payroll_location_summary") !== false){
                                ?>
                                <div class="tab-pane active" id="payrollsummary">
                                    <center>
                                        <h3>
                                            <i class="fa fa-bar-chart-o"></i> Location Payroll Summary | <strong><?php echo date('m-d-Y', strtotime($range[0])); ?></strong> - <strong><?php echo date('m-d-Y', strtotime($range[1])); ?></strong>
                                        </h3><br/>
                                    </center>
                                    <?php
                                    /*
                                     *
                                     *  SUMMARY REPORT
                                     *
                                     */
                                    $totals = array();
                                    $eventsDone = "";
                                    $os = array(); $key = 0;
                                    $employees = mysql_query("SELECT user_token, user_group, user_id, user_employer_rate, user_employer_commission FROM fmo_users WHERE user_employer='".mysql_real_escape_string($_GET['cuid'])."' AND user_employer_location='".mysql_real_escape_string($_GET['luid'])."' ORDER BY user_lname ASC");
                                    if(mysql_num_rows($employees) > 0){
                                        while($employee = mysql_fetch_assoc($employees)){
                                            $gross = 0;
                                            $hours = 0;
                                            $advs  = 0;
                                            $other = 0;
                                            if($employee['user_group'] == 1) {
                                                $status_tag = '<span class="label label-sm label-danger">ADMINISTRATOR</span>';
                                            } elseif($employee['user_group'] == 2) {
                                                if($employee['user_token'] == 'DJ5RELUMTA7QPHWJK'){
                                                    $status_tag = '<span class="label label-sm label-danger"> DEVELOPER</span>';
                                                } else {
                                                    $status_tag = '<span class="label label-sm label-success"> MANAGER</span>';
                                                }
                                            } elseif($employee['user_group'] == 4) {
                                                $status_tag = '<span class="label label-sm label-info">CUSTOMER SERVICE</span>';
                                            } elseif($employee['user_group'] == 5.1) {
                                                $status_tag = '<span class="label label-sm label-warning">DRIVER</span>';
                                            } elseif($employee['user_group'] == 5.2) {
                                                $status_tag = '<span class="label label-sm badge-purple">HELPER</span>';
                                            } elseif($employee['user_group'] == 5.3) {
                                                $status_tag = '<span class="label label-sm label-default">CREWMAN/OTHER</span>';
                                            }
                                            $os['users'][$employee['user_token']]['info'] = array(
                                                ''.nameByLast($employee['user_token']).' '.$status_tag,
                                                ''.locationName2($_GET['luid']).'',
                                                ''.$employee['user_id'].'',
                                                ''.$employee['user_token'].'',
                                                '%'.number_format($employee['user_employer_commission'] * 100, 0).'',
                                            );
                                            $tr = 0;
                                            $laborers  = mysql_query("SELECT laborer_user_token, laborer_event_token, laborer_rate, laborer_hours_worked, laborer_tip, laborer_desc, laborer_timestamp FROM fmo_locations_events_laborers WHERE laborer_user_token='".mysql_real_escape_string($employee['user_token'])."'");
                                            if(mysql_num_rows($laborers) > 0){
                                                while($labor = mysql_fetch_assoc($laborers)){
                                                    $events = mysql_query("SELECT event_date_start, event_name, event_id, event_token, event_location_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($labor['laborer_event_token'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<='".mysql_real_escape_string($range[1])."')");
                                                    if(mysql_num_rows($events) > 0){
                                                        while($event = mysql_fetch_assoc($events)) {
                                                            $pay   =  $labor['laborer_rate'] * $labor['laborer_hours_worked'];
                                                            $gross += $pay;
                                                            $hours += $labor['laborer_hours_worked'];
                                                            $other += $labor['laborer_tip'];
                                                            $os['users'][$employee['user_token']]['events'][] = array(
                                                                ''.date('m-d-Y', strtotime($event['event_date_start'])).'',
                                                                '<strong>EVENT: </strong>'.$event['event_name'].' - <strong>BOL #:</strong> '.$event['event_id'].'',
                                                                ''.$labor['laborer_hours_worked'].'hrs',
                                                                '$'.$labor['laborer_rate'].'/hr',
                                                                '$'.number_format($pay, 2).'',
                                                                '$'.number_format($labor['laborer_tip'], 2).''
                                                            ); $tr++;

                                                            if(strpos($eventsDone, $event['event_token']) !== false){
                                                                // burh!
                                                            } else {
                                                                $location = mysql_fetch_array(mysql_query("SELECT location_sales_tax FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));

                                                                if(!empty($location['location_sales_tax'])){
                                                                    $tax = $location['location_sales_tax'];
                                                                } else {$tax = 0;}

                                                                $findItems = mysql_query("SELECT item_item, item_desc, item_qty, item_cost, item_total, item_taxable, item_redeemable, item_prepay FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."'");
                                                                $iTotalRecords = mysql_num_rows($findItems);

                                                                $total2 = array();
                                                                $totals['code']      = NULL;
                                                                if($iTotalRecords > 0){
                                                                    while($item = mysql_fetch_assoc($findItems)){
                                                                        $total2['sub_total'] += $item['item_total'];
                                                                        if($item['item_taxable'] == 1){
                                                                            $total2['tax']     += $item['item_total'] * $tax;
                                                                            $total2['taxable'] += number_format($item['item_total'], 2, '.', '');
                                                                        } else {
                                                                            $total2['tax']   += 0;
                                                                        } if($item['item_redeemable'] == 1){
                                                                            $totals['discounts'] += $item['item_total'];
                                                                            $totals['prepaid']   += $item['item_prepay'];
                                                                            $totals['code']      =  $item['item_desc'];
                                                                        }

                                                                    }
                                                                    $total2['total'] = $total2['sub_total'] + $total2['tax'];
                                                                } else {
                                                                    $total2['total']     = 0;
                                                                    $total2['sub_total'] = 0;
                                                                }
                                                                $totals['gross']     += $total2['total'];
                                                                $totals['grossTax']  += $total2['tax'];
                                                                $totals['taxable']   += $total2['taxable'];
                                                                $eventsDone          .= "".$event['event_token']."|";
                                                            }

                                                        }
                                                    } elseif($labor['laborer_user_token'] == $labor['laborer_event_token']) {
                                                        if(date('Y-m-d', strtotime($labor['laborer_timestamp'])) >= $range[0] && date('Y-m-d', strtotime($labor['laborer_timestamp'])) <= $range[1]){
                                                            $pay   =  $labor['laborer_rate'] * $labor['laborer_hours_worked'];
                                                            $gross += $pay;
                                                            $hours += $labor['laborer_hours_worked'];
                                                            $other += $labor['laborer_tip'];
                                                            $os['users'][$employee['user_token']]['misc'][] = array(
                                                                ''.date('m-d-Y', strtotime($labor['laborer_timestamp'])).'',
                                                                '<strong>MISC: </strong>'.$labor['laborer_desc'].'',
                                                                ''.$labor['laborer_hours_worked'].'hrs',
                                                                '$'.$labor['laborer_rate'].'/hr',
                                                                '$'.number_format($pay, 2).'',
                                                                '$'.number_format($labor['laborer_tip'], 2).''
                                                            ); $tr++;
                                                        }
                                                    } else {
                                                        continue;
                                                    }
                                                }
                                            }
                                            $timeclock = mysql_query("SELECT timeclock_id, timeclock_clockin, timeclock_clockout, timeclock_hours, timeclock_timestamp FROM fmo_users_employee_timeclock WHERE timeclock_user='".mysql_real_escape_string($employee['user_token'])."' AND (timeclock_clockout>='".mysql_real_escape_string($range[0])."' AND timeclock_clockout<='".mysql_real_escape_string($range[1])."') ORDER BY timeclock_timestamp DESC");
                                            if(mysql_num_rows($timeclock) > 0){
                                                while($tc = mysql_fetch_assoc($timeclock)){
                                                    $gross += $tc['timeclock_hours'] * $employee['user_employer_rate'];
                                                    $hours += $tc['timeclock_hours'];
                                                    $os['users'][$employee['user_token']]['clock'][] = array(
                                                        '<strong>TIMECLOCK: </strong>'.date('g:i:s A', strtotime($tc['timeclock_clockin']))." - ".date('g:i:s A', strtotime($tc['timeclock_clockout'])),
                                                        ''.date('m-d-Y', strtotime($tc['timeclock_timestamp'])).'',
                                                        ''.$tc['timeclock_hours'].'hrs',
                                                        '$'.$employee['user_employer_rate'].'/hr',
                                                        '$'.number_format($tc['timeclock_hours'] * $employee['user_employer_rate'], 2).'',
                                                    ); $tr++;
                                                }
                                            }
                                            $loans = mysql_query("SELECT advance_requested, advance_timestamp, advance_reason FROM fmo_users_employee_advances WHERE (advance_timestamp>='".mysql_real_escape_string($range[0])."' AND advance_timestamp<='".mysql_real_escape_string($range[1])."') AND advance_user_token='".mysql_real_escape_string($employee['user_token'])."'");
                                            if(mysql_num_rows($loans) > 0){
                                                while($loan = mysql_fetch_assoc($loans)){
                                                    $advs += $loan['advance_requested'] + $loan['advance_requested'] * .10;
                                                    $os['users'][$employee['user_token']]['loans'][] = array(
                                                        '$'.number_format($loan['advance_requested'] + $loan['advance_requested'] * .10, 2).'',
                                                        ''.date('m-d-Y', strtotime($loan['advance_timestamp'])).'',
                                                        '<strong>LOAN: </strong>'.$loan['advance_reason'].'',
                                                    ); $tr++;
                                                }
                                            }
                                            $os['users'][$employee['user_token']]['totals'] = array(
                                                '$'.number_format($gross, 2).'',
                                                ''.number_format($hours, 2).'hrs',
                                                '$'.number_format($advs, 2).'',
                                                '$'.number_format($other, 2).'',
                                            );
                                            $totals['labor_cost'] += $gross + $other;
                                            $totals['labor_hrs']  += $hours;
                                            if($tr <= 0){
                                                unset($os['users'][$employee['user_token']]);
                                                continue;
                                            }
                                        }
                                    }
                                    foreach($os['users'] as $user){
                                        ?>
                                        <style type="text/css">
                                            .table-scrollable {
                                                margin: 0 0 !important;
                                            }
                                        </style>
                                        <div class="portlet" style="margin-bottom: 0!important;">
                                            <div class="portlet-title" style="margin-bottom: 0px;">
                                                <div class="caption" >
                                                    <small><strong><?php echo $user['info'][0]; ?></strong> | Employee #: <strong><?php echo $user['info'][2]; ?></strong> | Commission: <strong><?php echo $user['info'][4]; ?></strong> | <?php echo $user['info'][1]; ?></small>
                                                </div>
                                            </div>
                                            <div class="portlet-body" style="padding-top: 0px;">
                                                <table class="table table-striped table-hover datatable">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 140px;">
                                                            Date
                                                        </th>
                                                        <th style="padding-right: 8px;">
                                                            <?php
                                                            if(!empty($user['loans'])){
                                                                ?>
                                                                <strong class="text-danger pull-right">Loans</strong>
                                                                <?php
                                                            }
                                                            ?>
                                                        </th>
                                                        <th style="padding-right: 8px; width: 100px;"  class="text-right">
                                                            Hours
                                                        </th>
                                                        <th style="padding-right: 8px; width: 100px;" width="8%" class="text-right">
                                                            Rate
                                                        </th>
                                                        <th style="padding-right: 8px;" width="8%" class="text-right">
                                                            <strong>Pay</strong>
                                                        </th>
                                                        <th style="padding-right: 8px;" width="8%" class="text-right">
                                                            Other Pay
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    foreach($user['events'] as $event){
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $event[0]; ?></td>
                                                            <td><?php echo $event[1]; ?></td>
                                                            <td class="text-right"><?php echo $event[2]; ?></td>
                                                            <td class="text-right"><?php echo $event[3]; ?></td>
                                                            <td class="text-right"><?php echo $event[4]; ?></td>
                                                            <td class="text-right"><?php echo $event[5]; ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    foreach($user['misc'] as $misc){
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $misc[0]; ?></td>
                                                            <td><?php echo $misc[1]; ?></td>
                                                            <td class="text-right"><?php echo $misc[2]; ?></td>
                                                            <td class="text-right"><?php echo $misc[3]; ?></td>
                                                            <td class="text-right"><?php echo $misc[4]; ?></td>
                                                            <td class="text-right"><?php echo $misc[5]; ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    foreach($user['clock'] as $clock){
                                                        ?>
                                                        <tr style="background-color: lightyellow">
                                                            <td><?php echo $clock[1]; ?></td>
                                                            <td><?php echo $clock[0]; ?></td>
                                                            <td class="text-right"><?php echo $clock[2]; ?></td>
                                                            <td class="text-right"><?php echo $clock[3]; ?></td>
                                                            <td class="text-right"><?php echo $clock[4]; ?></td>
                                                            <td></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    foreach($user['loans'] as $loan){
                                                        ?>
                                                        <tr class="text-danger">
                                                            <td><?php echo $loan[1]; ?></td>
                                                            <td><?php echo $loan[2]; ?> <span class="pull-right"><?php echo $loan[0]; ?></span></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td class="text-right"></td>
                                                            <td class="text-right"></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td><strong>TOTALS <i class="fa fa-arrow-right"></i></strong></td>
                                                        <td class="text-right"><?php if(!empty($user['loans'])){ ?><strong class="text-danger"><?php echo $user['totals'][2]; ?></strong><?php } ?></td>
                                                        <td class="text-right"><strong><?php echo $user['totals'][1]; ?></strong></td>
                                                        <td class="text-right"></td>
                                                        <td class="text-right"><strong><?php echo $user['totals'][0]; ?></strong></td>
                                                        <td class="text-right"><strong><?php echo $user['totals'][3]; ?></strong></td>
                                                    </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                        <?
                                    }

                                    $NET = ($totals['gross'] + $totals['discounts']) + $totals['prepaid'];
                                    ?>
                                    <div class="invoice">
                                        <div class="row">
                                            <div class="col-xs-12 invoice-block">
                                                <ul class="list-unstyled amounts">
                                                    <li>
                                                        Total Income: <h3 style="display: inline;" class="text-success bold">$<?php echo number_format($totals['gross'], 2); ?></h3>
                                                    </li>
                                                    <li>
                                                        <small class="bold">(<strong>$<?php echo $totals['taxable']; ?></strong> taxable)</small> Taxes: <h3 style="display: inline;" class="text-success bold">$<?php echo number_format($totals['grossTax'], 2); ?></h3>
                                                    </li>
                                                    <li>
                                                        Discounts: <h3 style="display: inline" class="text-danger bold">$<?php echo number_format($totals['discounts'], 2); ?></h3>
                                                    </li>
                                                    <li>
                                                        Prepaid: <h3 style="display: inline" class="text-success bold">$<?php echo number_format($totals['prepaid'], 2); ?></h3>
                                                    </li>
                                                    <li>
                                                        Total NET Income: <h3 style="display: inline" class="text-success bold">$<?php echo number_format(($totals['gross'] + $totals['discounts']) + $totals['prepaid'], 2); ?></h3>
                                                    </li>
                                                    <li>
                                                        Total Labor Hours: <h3 style="display: inline" class="text-danger bold"><?php echo number_format($totals['labor_hrs'], 2); ?> hrs</h3>
                                                    </li>
                                                    <li>
                                                        Total Labor Costs: <h3 style="display: inline" class="text-danger bold">$<?php echo number_format($totals['labor_cost'], 2); ?></h3>
                                                    </li>
                                                    <li>
                                                        Total Labor Percentage: <h3 style="display: inline" class="text-success bold"><?php echo number_format(($totals['labor_cost'] / $NET) * 100, 2); ?>%</h3>
                                                    </li>
                                                    <?php echo $eventsDone; ?>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_payroll_company_summary") !== false){
                                ?>
                                <div class="tab-pane" id="payrollsummary_admin">
                                    <center>
                                        <h3>
                                            <i class="fa fa-bar-chart-o"></i> Company Payroll Summary | <strong><?php echo date('m-d-Y', strtotime($range[0])); ?></strong> - <strong><?php echo date('m-d-Y', strtotime($range[1])); ?></strong>
                                        </h3> <br/>
                                    </center>
                                    <?php
                                    /*
                                     *
                                     *  OWNER SUMMARY REPORT
                                     *
                                     */

                                    $eventsDone  = "";
                                    $totals      = array();
                                    $os = array(); $key = 0;
                                    $employees = mysql_query("SELECT user_token, user_id, user_group, user_last_ext_location, user_employer_rate, user_employer_commission FROM fmo_users WHERE user_employer='".mysql_real_escape_string($_GET['cuid'])."' ORDER BY user_lname ASC");
                                    if(mysql_num_rows($employees) > 0){
                                        while($employee = mysql_fetch_assoc($employees)){
                                            $gross = 0;
                                            $hours = 0;
                                            $advs  = 0;
                                            $other = 0;
                                            if($employee['user_group'] == 1) {
                                                $status_tag = '<span class="label label-sm label-danger">ADMINISTRATOR</span>';
                                            } elseif($employee['user_group'] == 2) {
                                                if($employee['user_token'] == 'DJ5RELUMTA7QPHWJK'){
                                                    $status_tag = '<span class="label label-sm label-danger"> DEVELOPER</span>';
                                                } else {
                                                    $status_tag = '<span class="label label-sm label-success"> MANAGER</span>';
                                                }
                                            } elseif($employee['user_group'] == 4) {
                                                $status_tag = '<span class="label label-sm label-info">CUSTOMER SERVICE</span>';
                                            } elseif($employee['user_group'] == 5.1) {
                                                $status_tag = '<span class="label label-sm label-warning">DRIVER</span>';
                                            } elseif($employee['user_group'] == 5.2) {
                                                $status_tag = '<span class="label label-sm badge-purple">HELPER</span>';
                                            } elseif($employee['user_group'] == 5.3) {
                                                $status_tag = '<span class="label label-sm label-default">CREWMAN/OTHER</span>';
                                            }
                                            $os['users'][$employee['user_token']]['info'] = array(
                                                ''.nameByLast($employee['user_token']).' '.$status_tag.'',
                                                ''.locationName2($employee['user_last_ext_location']).'',
                                                ''.$employee['user_id'].'',
                                                ''.$employee['user_token'].'',
                                                '%'.number_format($employee['user_employer_commission'] * 100, 0).'',
                                            );
                                            $tr = 0;
                                            $laborers  = mysql_query("SELECT laborer_user_token, laborer_event_token, laborer_rate, laborer_hours_worked, laborer_tip, laborer_desc, laborer_timestamp FROM fmo_locations_events_laborers WHERE laborer_user_token='".mysql_real_escape_string($employee['user_token'])."'");
                                            if(mysql_num_rows($laborers) > 0){
                                                while($labor = mysql_fetch_assoc($laborers)){
                                                    $events = mysql_query("SELECT event_date_start, event_name, event_id, event_token, event_location_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($labor['laborer_event_token'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<='".mysql_real_escape_string($range[1])."')");
                                                    if(mysql_num_rows($events) > 0){
                                                        while($event = mysql_fetch_assoc($events)){
                                                            $pay   =  $labor['laborer_rate'] * $labor['laborer_hours_worked'];
                                                            $gross += $pay;
                                                            $hours += $labor['laborer_hours_worked'];
                                                            $other += $labor['laborer_tip'];
                                                            $os['users'][$employee['user_token']]['events'][] = array(
                                                                ''.date('m-d-Y', strtotime($event['event_date_start'])).'',
                                                                '<strong>EVENT: </strong>'.$event['event_name'].' - <strong>BOL #:</strong> '.$event['event_id'].'',
                                                                ''.$labor['laborer_hours_worked'].'hrs',
                                                                '$'.$labor['laborer_rate'].'/hr',
                                                                '$'.number_format($pay, 2).'',
                                                                '$'.number_format($labor['laborer_tip'], 2).''
                                                            ); $tr++;


                                                            if(strpos($eventsDone, $event['event_token']) !== false){
                                                                // burh!
                                                            } else {
                                                                $location = mysql_fetch_array(mysql_query("SELECT location_sales_tax FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));

                                                                if(!empty($location['location_sales_tax'])){
                                                                    $tax = $location['location_sales_tax'];
                                                                } else {$tax = 0;}

                                                                $findItems = mysql_query("SELECT item_item, item_desc, item_qty, item_cost, item_total, item_taxable, item_redeemable, item_prepay FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."'");
                                                                $iTotalRecords = mysql_num_rows($findItems);

                                                                $total2 = array();
                                                                $totals['code']      = NULL;
                                                                if($iTotalRecords > 0){
                                                                    while($item = mysql_fetch_assoc($findItems)){
                                                                        $total2['sub_total'] += $item['item_total'];
                                                                        if($item['item_taxable'] == 1){
                                                                            $total2['tax']     += $item['item_total'] * $tax;
                                                                            $total2['taxable'] += number_format($item['item_total'], 2, '.', '');
                                                                        } else {
                                                                            $total2['tax']   += 0;
                                                                        } if($item['item_redeemable'] == 1){
                                                                            $totals['discounts'] += $item['item_total'];
                                                                            $totals['prepaid']   += $item['item_prepay'];
                                                                            $totals['code']      =  $item['item_desc'];
                                                                        }

                                                                    }
                                                                    $total2['total'] = $total2['sub_total'] + $total2['tax'];
                                                                } else {
                                                                    $total2['total']     = 0;
                                                                    $total2['sub_total'] = 0;
                                                                }
                                                                $totals['gross']     += $total2['total'];
                                                                $totals['grossTax']  += $total2['tax'];
                                                                $totals['taxable']   += $total2['taxable'];
                                                                $eventsDone          .= "".$event['event_token']."|";
                                                            }

                                                        }
                                                    } elseif($labor['laborer_user_token'] == $labor['laborer_event_token']) {
                                                        if(date('Y-m-d', strtotime($labor['laborer_timestamp'])) >= $range[0] && date('Y-m-d', strtotime($labor['laborer_timestamp'])) <= $range[1]){
                                                            $pay   =  $labor['laborer_rate'] * $labor['laborer_hours_worked'];
                                                            $gross += $pay;
                                                            $hours += $labor['laborer_hours_worked'];
                                                            $other += $labor['laborer_tip'];
                                                            $os['users'][$employee['user_token']]['misc'][] = array(
                                                                ''.date('m-d-Y', strtotime($labor['laborer_timestamp'])).'',
                                                                '<strong>MISC: </strong>'.$labor['laborer_desc'].'',
                                                                ''.$labor['laborer_hours_worked'].'hrs',
                                                                '$'.$labor['laborer_rate'].'/hr',
                                                                '$'.number_format($pay, 2).'',
                                                                '$'.number_format($labor['laborer_tip'], 2).''
                                                            ); $tr++;
                                                        }
                                                    } else {
                                                        continue;
                                                    }
                                                }
                                            }
                                            $timeclock = mysql_query("SELECT timeclock_id, timeclock_clockin, timeclock_clockout, timeclock_hours, timeclock_timestamp FROM fmo_users_employee_timeclock WHERE timeclock_user='".mysql_real_escape_string($employee['user_token'])."' AND (timeclock_clockout>='".mysql_real_escape_string($range[0])."' AND timeclock_clockout<='".mysql_real_escape_string($range[1])."') ORDER BY timeclock_timestamp DESC");
                                            if(mysql_num_rows($timeclock) > 0){
                                                while($tc = mysql_fetch_assoc($timeclock)){
                                                    $gross += $tc['timeclock_hours'] * $employee['user_employer_rate'];
                                                    $hours += $tc['timeclock_hours'];
                                                    $os['users'][$employee['user_token']]['clock'][] = array(
                                                        '<strong>TIMECLOCK: </strong>'.date('g:i:s A', strtotime($tc['timeclock_clockin']))." - ".date('g:i:s A', strtotime($tc['timeclock_clockout'])),
                                                        ''.date('m-d-Y', strtotime($tc['timeclock_timestamp'])).'',
                                                        ''.$tc['timeclock_hours'].'hrs',
                                                        '$'.$employee['user_employer_rate'].'/hr',
                                                        '$'.number_format($tc['timeclock_hours'] * $employee['user_employer_rate'], 2).'',
                                                    ); $tr++;
                                                }
                                            }
                                            $loans = mysql_query("SELECT advance_requested, advance_timestamp, advance_reason FROM fmo_users_employee_advances WHERE (advance_timestamp>='".mysql_real_escape_string($range[0])."' AND advance_timestamp<='".mysql_real_escape_string($range[1])."') AND advance_user_token='".mysql_real_escape_string($employee['user_token'])."'");
                                            if(mysql_num_rows($loans) > 0){
                                                while($loan = mysql_fetch_assoc($loans)){
                                                    $advs += $loan['advance_requested'] + $loan['advance_requested'] * .10;
                                                    $os['users'][$employee['user_token']]['loans'][] = array(
                                                        '$'.number_format($loan['advance_requested'] + $loan['advance_requested'] * .10, 2).'',
                                                        ''.date('m-d-Y', strtotime($loan['advance_timestamp'])).'',
                                                        '<strong>LOAN: </strong>'.$loan['advance_reason'].'',
                                                    ); $tr++;
                                                }
                                            }
                                            $os['users'][$employee['user_token']]['totals'] = array(
                                                '$'.number_format($gross, 2).'',
                                                ''.number_format($hours, 2).'hrs',
                                                '$'.number_format($advs, 2).'',
                                                '$'.number_format($other, 2).'',
                                            );
                                            $totals['labor_cost'] += $gross + $other;
                                            $totals['labor_hrs']  += $hours;
                                            if($tr <= 0){
                                                unset($os['users'][$employee['user_token']]);
                                                continue;
                                            }
                                        }
                                    }
                                    foreach($os['users'] as $user){
                                        ?>
                                        <style type="text/css">
                                            .table-scrollable {
                                                margin: 0 0 !important;
                                            }
                                        </style>
                                        <div class="portlet" style="margin-bottom: 0!important;">
                                            <div class="portlet-title" style="margin-bottom: 0px;">
                                                <div class="caption">
                                                    <small><strong><?php echo $user['info'][0]; ?></strong> | Employee #: <strong><?php echo $user['info'][2]; ?></strong> | Commission: <strong><?php echo $user['info'][4]; ?></strong> | <?php echo $user['info'][1]; ?></small>
                                                </div>
                                            </div>
                                            <div class="portlet-body" style="padding-top: 0px;">
                                                <table class="table table-striped table-hover datatable">
                                                    <thead>
                                                    <tr>
                                                        <th style="width: 140px;">
                                                            Date
                                                        </th>
                                                        <th style="padding-right: 8px;">
                                                            <?php
                                                            if(!empty($user['loans'])){
                                                                ?>
                                                                <strong class="text-danger pull-right">Loans</strong>
                                                                <?php
                                                            }
                                                            ?>
                                                        </th>
                                                        <th style="padding-right: 8px; width: 100px;"  class="text-right">
                                                            Hours
                                                        </th>
                                                        <th style="padding-right: 8px; width: 100px;" width="8%" class="text-right">
                                                            Rate
                                                        </th>
                                                        <th style="padding-right: 8px;" width="8%" class="text-right">
                                                            <strong>Pay</strong>
                                                        </th>
                                                        <th style="padding-right: 8px;" width="8%" class="text-right">
                                                            Other Pay
                                                        </th>
                                                    </tr>
                                                    </thead>
                                                    <tbody>
                                                    <?php
                                                    foreach($user['events'] as $event){
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $event[0]; ?></td>
                                                            <td><?php echo $event[1]; ?></td>
                                                            <td class="text-right"><?php echo $event[2]; ?></td>
                                                            <td class="text-right"><?php echo $event[3]; ?></td>
                                                            <td class="text-right"><?php echo $event[4]; ?></td>
                                                            <td class="text-right"><?php echo $event[5]; ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    foreach($user['misc'] as $misc){
                                                        ?>
                                                        <tr>
                                                            <td><?php echo $misc[0]; ?></td>
                                                            <td><?php echo $misc[1]; ?></td>
                                                            <td class="text-right"><?php echo $misc[2]; ?></td>
                                                            <td class="text-right"><?php echo $misc[3]; ?></td>
                                                            <td class="text-right"><?php echo $misc[4]; ?></td>
                                                            <td class="text-right"><?php echo $misc[5]; ?></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    foreach($user['clock'] as $clock){
                                                        ?>
                                                        <tr style="background-color: lightyellow">
                                                            <td><?php echo $clock[1]; ?></td>
                                                            <td><?php echo $clock[0]; ?></td>
                                                            <td class="text-right"><?php echo $clock[2]; ?></td>
                                                            <td class="text-right"><?php echo $clock[3]; ?></td>
                                                            <td class="text-right"><?php echo $clock[4]; ?></td>
                                                            <td></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    foreach($user['loans'] as $loan){
                                                        ?>
                                                        <tr class="text-danger">
                                                            <td><?php echo $loan[1]; ?></td>
                                                            <td><?php echo $loan[2]; ?> <span class="pull-right"><?php echo $loan[0]; ?></span></td>
                                                            <td></td>
                                                            <td></td>
                                                            <td class="text-right"></td>
                                                            <td class="text-right"></td>
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                    </tbody>
                                                    <tfoot>
                                                    <tr>
                                                        <td><strong>TOTALS <i class="fa fa-arrow-right"></i></strong></td>
                                                        <td class="text-right"><?php if(!empty($user['loans'])){ ?><strong class="text-danger"><?php echo $user['totals'][2]; ?></strong><?php } ?></td>
                                                        <td class="text-right"><strong><?php echo $user['totals'][1]; ?></strong></td>
                                                        <td class="text-right"></td>
                                                        <td class="text-right"><strong><?php echo $user['totals'][0]; ?></strong></td>
                                                        <td class="text-right"><strong><?php echo $user['totals'][3]; ?></strong></td>
                                                    </tr>
                                                    </tfoot>
                                                </table>
                                            </div>
                                        </div>
                                        <?
                                    }

                                    $NET = ($totals['gross'] + $totals['discounts']) + $totals['prepaid'];
                                    ?>
                                    <div class="invoice">
                                        <div class="row">
                                            <div class="col-xs-12 invoice-block">
                                                <ul class="list-unstyled amounts">
                                                    <li>
                                                        Total Income: <h3 style="display: inline;" class="text-success bold">$<?php echo number_format($totals['gross'], 2); ?></h3>
                                                    </li>
                                                    <li>
                                                        <small class="bold">(<strong>$<?php echo $totals['taxable']; ?></strong> taxable)</small> Taxes: <h3 style="display: inline;" class="text-success bold">$<?php echo number_format($totals['grossTax'], 2); ?></h3>
                                                    </li>
                                                    <li>
                                                        Discounts: <h3 style="display: inline" class="text-danger bold">$<?php echo number_format($totals['discounts'], 2); ?></h3>
                                                    </li>
                                                    <li>
                                                        Prepaid: <h3 style="display: inline" class="text-success bold">$<?php echo number_format($totals['prepaid'], 2); ?></h3>
                                                    </li>
                                                    <li>
                                                        Total NET Income: <h3 style="display: inline" class="text-success bold">$<?php echo number_format(($totals['gross'] + $totals['discounts']) + $totals['prepaid'], 2); ?></h3>
                                                    </li>
                                                    <li>
                                                        Total Labor Hours: <h3 style="display: inline" class="text-danger bold"><?php echo number_format($totals['labor_hrs'], 2); ?> hrs</h3>
                                                    </li>
                                                    <li>
                                                        Total Labor Costs: <h3 style="display: inline" class="text-danger bold">$<?php echo number_format($totals['labor_cost'], 2); ?></h3>
                                                    </li>
                                                    <li>
                                                        Total Labor Percentage: <h3 style="display: inline" class="text-success bold"><?php echo number_format(($totals['labor_cost'] / $NET) * 100, 2); ?>%</h3>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function(e){
                    $('.datatable').dataTable({
                        "order": [[ 0, "asc" ]],
                        "bFilter" : false,
                        "bLengthChange": false,
                        "bPaginate": false,
                        "info": false
                    });
                });
            </script>
            <?php
        } else {
            ?>
            <div class="col-md-12">
                <div class="alert alert-danger">
                    <h5 class="text-center" style="margin-top: 10px"><strong>You do not have permission to view this report.</strong></h5>
                </div>
            </div>
            <?php
        }
    } elseif ($_POST['type'] == 'sales'){
        $range = explode(" - ", $_POST['ext']);
        if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales") !== false){
            ?>
            <div class="col-md-12">
                <div class="portlet">
                    <div class="portlet-title tabbable-line">
                        <ul class="nav nav-tabs nav-justified"> <!--  <button class="btn btn-xs red-stripe print" data-print="#sales_summary"><i class="fa fa-print"></i> Print</button> -->
                            <?php
                            if($_SESSION['uuid'] == 'DH8I8KKVVXLZAJA5G' || $_SESSION['uuid'] == 'DJ5RELUMTA7QPHWJK'){
                                ?>
                                <li class="">
                                    <a class="sub_pl tab_print" data-href="assets/pages/sub/sub/reports_master_sub.php?ty=ftk&luid=<?php echo $_GET['luid']; ?>" data-page-title="Booking Fees" data-toggle="tab" data-print="#bookingfees" style="color: #888" data-ext="<?php echo $range[0]; ?> - <?php echo $range[1]; ?>">
                                        Booking Fees</a>
                                </li>
                                <?php
                            }
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_accr") !== false){
                                ?>
                                <li>
                                    <a class="sub_pl tab_print" data-href="assets/pages/sub/sub/reports_master_sub.php?ty=akr&luid=<?php echo $_GET['luid']; ?>" data-page-title="Accounts Receivable" data-toggle="tab" data-print="#acc_r" style="color: #888" data-ext="<?php echo $range[0]; ?> - <?php echo $range[1]; ?>">
                                        Accounts Receivable</a>
                                </li>
                                <?php
                            }
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_redemption") !== false){
                                ?>
                                <li>
                                    <a class="sub_pl tab_print" data-href="assets/pages/sub/sub/reports_master_sub.php?ty=rdt&luid=<?php echo $_GET['luid']; ?>" data-page-title="Redemptions" data-toggle="tab" data-print="#redemption" style="color: #888" data-ext="<?php echo $range[0]; ?> - <?php echo $range[1]; ?>" >
                                        Redemptions</a>
                                </li>
                                <?php
                            }
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_serviceitems") !== false){
                                ?>
                                <li>
                                    <a class="sub_pl tab_print" data-href="assets/pages/sub/sub/reports_master_sub.php?ty=srv&luid=<?php echo $_GET['luid']; ?>" data-page-title="Service Items" data-toggle="tab" data-print="#services" style="color: #888" data-ext="<?php echo $range[0]; ?> - <?php echo $range[1]; ?>" >
                                        Service Items</a>
                                </li>
                                <?php
                            }
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_cjr") !== false){
                                ?>
                                <li>
                                    <a class="sub_pl tab_print" data-href="assets/pages/sub/sub/reports_master_sub.php?ty=cjr&luid=<?php echo $_GET['luid']; ?>" data-page-title="Completed Jobs Report" data-toggle="tab" data-print="#cjr" style="color: #888" data-ext="<?php echo $range[0]; ?> - <?php echo $range[1]; ?>" >
                                        Completed Jobs Report</a>
                                </li>
                                <?php
                            }
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_expdpt") !== false){
                                ?>
                                <li class="<?php if(isset($_GET['show']) && $_GET['show'] == 'exp_dp'){echo "active";} ?>">
                                    <a class="sub_pl tab_print" data-href="assets/pages/sub/sub/reports_master_sub.php?ty=ext&luid=<?php echo $_GET['luid']; ?>" data-page-title="Expenses & Deposits" data-toggle="tab" data-print="#exp_dpt" style="color: #888" data-ext="<?php echo $range[0]; ?> - <?php echo $range[1]; ?>" >
                                        Expenses & Deposits</a>
                                </li>
                                <?php
                            }
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_summary") !== false){
                                ?>
                                <li class="<?php if(!isset($_GET['show'])){echo "active";} ?>">
                                    <a class="sub_pl tab_print sales" data-href="assets/pages/sub/sub/reports_master_sub.php?ty=svk&luid=<?php echo $_GET['luid']; ?>" data-page-title="Sales Summary" data-toggle="tab" data-print="#sales_summary" style="color: #888" data-ext="<?php echo $range[0]; ?> - <?php echo $range[1]; ?>" >
                                        Sales Summary</a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content" id="sub_content">

                        </div>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function(e){
                    $('.sales').click();
                });
            </script>
            <?php
        } else {
            ?>
            <div class="col-md-12">
                <div class="alert alert-danger">
                    <h5 class="text-center" style="margin-top: 10px"><strong>You do not have permission to view this report.</strong></h5>
                </div>
            </div>
            <?php
        }
    } elseif($_POST['type'] == 'personal'){
        $range = explode(" - ", $_POST['ext']);
        ?>
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title tabbable-line">
                    <div class="caption caption-md">
                        <i class="fa fa-bar-chart-o theme-font bold"></i>
                        <span class="caption-subject font-red bold uppercase">My Hours</span> <span class="font-red">|</span> <button class="btn btn-xs red-stripe print" data-print="#my_hours"><i class="fa fa-print"></i> Print</button>
                    </div>
                </div>
                <div class="portlet-body" id="my_hours">
                    <?php
                    if($_SESSION['logged'] == true){
                        ?>
                        <center>
                            <h3>
                                <i class="fa fa-bar-chart-o"></i>My Hours | <strong><?php echo date('m-d-Y', strtotime($range[0])); ?></strong> - <strong><?php echo date('m-d-Y', strtotime($range[1])); ?></strong>
                            </h3><br/>
                        </center>
                        <?php
                        /*
                         *
                         *  SUMMARY REPORT
                         *
                         */

                        $employee = mysql_fetch_array(mysql_query("SELECT user_token, user_id, user_employer_rate, user_employer_commission FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'"));

                        $os = array(); $key = 0;
                        $gross = 0;
                        $hours = 0;
                        $advs  = 0;
                        $other = 0;
                        $os['users'][$employee['user_token']]['info'] = array(
                            ''.nameByLast($employee['user_token']).'',
                            ''.locationName2($_GET['luid']).'',
                            ''.$employee['user_id'].'',
                            ''.$employee['user_token'].'',
                            '%'.number_format($employee['user_employer_commission'] * 100, 0).'',
                        );
                        $laborers  = mysql_query("SELECT laborer_user_token, laborer_event_token, laborer_rate, laborer_hours_worked, laborer_tip, laborer_desc, laborer_timestamp FROM fmo_locations_events_laborers WHERE laborer_user_token='".mysql_real_escape_string($_GET['uuid'])."' AND (laborer_timestamp>='".mysql_real_escape_string($range[0])."' AND laborer_timestamp<='".mysql_real_escape_string($range[1])."')");
                        if(mysql_num_rows($laborers) > 0){
                            while($labor = mysql_fetch_assoc($laborers)){
                                $events = mysql_query("SELECT event_date_start, event_name, event_id FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($labor['laborer_event_token'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<='".mysql_real_escape_string($range[1])."')");
                                if(mysql_num_rows($events) > 0){
                                    while($event = mysql_fetch_assoc($events)){
                                        $pay   =  $labor['laborer_rate'] * $labor['laborer_hours_worked'];
                                        $gross += $pay;
                                        $hours += $labor['laborer_hours_worked'];
                                        $other += $labor['laborer_tip'];
                                        $os['users'][$employee['user_token']]['events'][] = array(
                                            ''.date('m-d-Y', strtotime($event['event_date_start'])).'',
                                            '<strong>EVENT: </strong>'.$event['event_name'].' - <strong>BOL #:</strong> '.$event['event_id'].'',
                                            ''.$labor['laborer_hours_worked'].'hrs',
                                            '$'.$labor['laborer_rate'].'/hr',
                                            '$'.number_format($pay, 2).'',
                                            '$'.number_format($labor['laborer_tip'], 2).''
                                        );
                                    }
                                } elseif($labor['laborer_user_token'] == $labor['laborer_event_token']) {
                                    $pay   =  $labor['laborer_rate'] * $labor['laborer_hours_worked'];
                                    $gross += $pay;
                                    $hours += $labor['laborer_hours_worked'];
                                    $other += $labor['laborer_tip'];
                                    $os['users'][$employee['user_token']]['misc'][] = array(
                                        ''.date('m-d-Y', strtotime($labor['laborer_timestamp'])).'',
                                        '<strong>MISC: </strong>'.$labor['laborer_desc'].'',
                                        ''.$labor['laborer_hours_worked'].'hrs',
                                        '$'.$labor['laborer_rate'].'/hr',
                                        '$'.number_format($pay, 2).'',
                                        '$'.number_format($labor['laborer_tip'], 2).''
                                    );
                                } else {
                                    continue;
                                }
                            }
                        }
                        $timeclock = mysql_query("SELECT timeclock_id, timeclock_clockin, timeclock_clockout, timeclock_hours, timeclock_timestamp FROM fmo_users_employee_timeclock WHERE timeclock_user='".mysql_real_escape_string($_GET['uuid'])."' AND (timeclock_clockout>='".mysql_real_escape_string($range[0])."' AND timeclock_clockout<='".mysql_real_escape_string($range[1])."') ORDER BY timeclock_timestamp DESC");
                        if(mysql_num_rows($timeclock) > 0){
                            while($tc = mysql_fetch_assoc($timeclock)){
                                $gross += $tc['timeclock_hours'] * $employee['user_employer_rate'];
                                $hours += $tc['timeclock_hours'];
                                $os['users'][$employee['user_token']]['clock'][] = array(
                                    '<strong>TIMECLOCK: </strong>'.date('g:i:s A', strtotime($tc['timeclock_clockin']))." - ".date('g:i:s A', strtotime($tc['timeclock_clockout'])),
                                    ''.date('m-d-Y', strtotime($tc['timeclock_timestamp'])).'',
                                    ''.$tc['timeclock_hours'].'hrs',
                                    '$'.$employee['user_employer_rate'].'/hr',
                                    '$'.number_format($tc['timeclock_hours'] * $employee['user_employer_rate'], 2).'',
                                );
                            }
                        }
                        $loans = mysql_query("SELECT advance_requested, advance_timestamp, advance_reason FROM fmo_users_employee_advances WHERE (advance_timestamp>='".mysql_real_escape_string($range[0])."' AND advance_timestamp<='".mysql_real_escape_string($range[1])."') AND advance_user_token='".mysql_real_escape_string($_GET['uuid'])."'");
                        if(mysql_num_rows($loans) > 0){
                            while($loan = mysql_fetch_assoc($loans)){
                                $advs += $loan['advance_requested'] + $loan['advance_requested'] * .10;
                                $os['users'][$employee['user_token']]['loans'][] = array(
                                    '$'.number_format($loan['advance_requested'] + $loan['advance_requested'] * .10, 2).'',
                                    ''.date('m-d-Y', strtotime($loan['advance_timestamp'])).'',
                                    '<strong>LOAN: </strong>'.$loan['advance_reason'].'',
                                );
                            }
                        }
                        $os['users'][$employee['user_token']]['totals'] = array(
                            '$'.number_format($gross, 2).'',
                            ''.number_format($hours, 2).'hrs',
                            '$'.number_format($advs, 2).'',
                            '$'.number_format($other, 2).'',
                        );
                        foreach($os['users'] as $user){
                            ?>
                            <div class="portlet">
                                <div class="portlet-title" style="margin-bottom: 0px;">
                                    <div class="caption">
                                        <small><strong><?php echo $user['info'][0]; ?></strong> | Employee #: <strong><?php echo $user['info'][2]; ?></strong> | Commission: <strong><?php echo $user['info'][4]; ?></strong> | <?php echo $user['info'][1]; ?></small>
                                    </div>
                                </div>
                                <div class="portlet-body" style="padding-top: 0px;">
                                    <table class="table table-striped table-hover datatable">
                                        <thead>
                                        <tr>
                                            <th style="width: 140px;">
                                                Date
                                            </th>
                                            <th style="padding-right: 8px;">
                                                <?php
                                                if(!empty($user['loans'])){
                                                    ?>
                                                    <strong class="text-danger pull-right">Loans</strong>
                                                    <?php
                                                }
                                                ?>
                                            </th>
                                            <th style="padding-right: 8px; width: 100px;"  class="text-right">
                                                Hours
                                            </th>
                                            <th style="padding-right: 8px; width: 100px;" width="8%" class="text-right">
                                                Rate
                                            </th>
                                            <th style="padding-right: 8px;" width="8%" class="text-right">
                                                <strong>Pay</strong>
                                            </th>
                                            <th style="padding-right: 8px;" width="8%" class="text-right">
                                                Other Pay
                                            </th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        foreach($user['events'] as $event){
                                            ?>
                                            <tr>
                                                <td><?php echo $event[0]; ?></td>
                                                <td><?php echo $event[1]; ?></td>
                                                <td class="text-right"><?php echo $event[2]; ?></td>
                                                <td class="text-right"><?php echo $event[3]; ?></td>
                                                <td class="text-right"><?php echo $event[4]; ?></td>
                                                <td class="text-right"><?php echo $event[5]; ?></td>
                                            </tr>
                                            <?php
                                        }
                                        foreach($user['misc'] as $misc){
                                            ?>
                                            <tr>
                                                <td><?php echo $misc[0]; ?></td>
                                                <td><?php echo $misc[1]; ?></td>
                                                <td class="text-right"><?php echo $misc[2]; ?></td>
                                                <td class="text-right"><?php echo $misc[3]; ?></td>
                                                <td class="text-right"><?php echo $misc[4]; ?></td>
                                                <td class="text-right"><?php echo $misc[5]; ?></td>
                                            </tr>
                                            <?php
                                        }
                                        foreach($user['clock'] as $clock){
                                            ?>
                                            <tr style="background-color: lightyellow">
                                                <td><?php echo $clock[1]; ?></td>
                                                <td><?php echo $clock[0]; ?></td>
                                                <td class="text-right"><?php echo $clock[2]; ?></td>
                                                <td class="text-right"><?php echo $clock[3]; ?></td>
                                                <td class="text-right"><?php echo $clock[4]; ?></td>
                                                <td></td>
                                            </tr>
                                            <?php
                                        }
                                        foreach($user['loans'] as $loan){
                                            ?>
                                            <tr class="text-danger">
                                                <td><?php echo $loan[1]; ?></td>
                                                <td><?php echo $loan[2]; ?> <span class="pull-right"><?php echo $loan[0]; ?></span></td>
                                                <td></td>
                                                <td></td>
                                                <td class="text-right"></td>
                                                <td class="text-right"></td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td><strong>TOTALS <i class="fa fa-arrow-right"></i></strong></td>
                                            <td class="text-right"><?php if(!empty($user['loans'])){ ?><strong class="text-danger"><?php echo $user['totals'][2]; ?></strong><?php } ?></td>
                                            <td class="text-right"><strong><?php echo $user['totals'][1]; ?></strong></td>
                                            <td class="text-right"></td>
                                            <td class="text-right"><strong><?php echo $user['totals'][0]; ?></strong></td>
                                            <td class="text-right"><strong><?php echo $user['totals'][3]; ?></strong></td>
                                        </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <?
                        }
                        ?>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function(e){
                $('.datatable').dataTable({
                    "order": [[ 0, "asc" ]],
                    "bFilter" : false,
                    "bLengthChange": false,
                    "bPaginate": false,
                    "info": false
                });
            });
        </script>
        <?php
    } elseif($_POST['type'] == 'other'){
        $range = explode(" - ", $_POST['ext']);
        if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_other") !== false){
            ?>
            <div class="col-md-12">
                <div class="portlet">
                    <div class="portlet-title tabbable-line">
                        <div class="caption caption-md">
                            <i class="fa fa-bar-chart-o theme-font bold"></i>
                            <span class="caption-subject font-red bold uppercase">SALES</span> <span class="font-red">|</span> <button class="btn btn-xs red-stripe print" data-print="#sales_summary"><i class="fa fa-print"></i> Print</button>
                        </div>
                        <ul class="nav nav-tabs">
                            <?php
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_other_scorecard") !== false){
                                ?>
                                <li class="active">
                                    <a href="#score" data-toggle="tab" aria-expanded="true" style="color: black;" class="tab_print" data-print="#score">
                                        Score Card</a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                    <?php

                    ?>
                    <div class="portlet-body" id="other">
                        <div class="tab-content">
                            <?php
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_other_scorecard") !== false){
                                $scorecard = array();
                                $score     = mysql_query("SELECT event_id, event_by_user_token, event_booking, event_status FROM fmo_locations_events WHERE (event_creation>='".mysql_real_escape_string($range[0])."' AND event_creation<='".mysql_real_escape_string($range[1])."')  AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_date_start ASC");
                                if(mysql_num_rows($score) > 0){
                                    while($scr = mysql_fetch_assoc($score)){
                                        switch($scr['event_status']){
                                            case 1: $status = "New Booking";break;
                                            case 2: $status = "Confirmed"; break;
                                            case 3: $status = "Left Message"; break;
                                            case 4: $status = "On Hold"; break;
                                            case 5: $status = "Canceled"; break;
                                            case 6: $status = "Customer Confirmed"; break;
                                            case 8: $status = "Completed"; break;
                                            case 9: $status = "Dead Hot Lead"; break;
                                            default: $status = "On Hold"; break;
                                        }
                                        $scorecard['people'][$scr['event_by_user_token']]['name']                       =  name($scr['event_by_user_token']);
                                        $scorecard['people'][$scr['event_by_user_token']]['events']                     += 1;
                                        $scorecard['people'][$scr['event_by_user_token']]['bookings']                   += $scr['event_booking'];
                                        $scorecard['people'][$scr['event_by_user_token']]['statuses'][$status]['name']  =  $status;
                                        $scorecard['people'][$scr['event_by_user_token']]['statuses'][$status]['count'] += 1;
                                    }
                                }
                                ?>
                                <div class="tab-pane active" id="score">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <center>
                                                <h3>
                                                    <i class="fa fa-dollar"></i> Score Card | <strong><?php echo date('m-d-Y', strtotime($range[0])); ?></strong> - <strong><?php echo date('m-d-Y', strtotime($range[1])); ?></strong>
                                                </h3><br/>
                                            </center>
                                        </div>
                                    </div>
                                    <div class="portlet">
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover datatable">
                                                <thead>
                                                <tr>
                                                    <th>Employee</th>
                                                    <th></th>
                                                    <th class="text-right">Event Count</th>
                                                    <th class="text-right">Booking Fee Count</th>
                                                    <th class="text-right">Percentage</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach($scorecard['people'] as $score){
                                                    ?>
                                                    <tr>
                                                        <td class="bold"><?php echo $score['name']; ?></td>
                                                        <td>
                                                            <span class="text-muted">
                                                                (<?php
                                                                $i = 0;
                                                                foreach($score['statuses'] as $stats){
                                                                    echo " ".$stats['name'].": <strong>".$stats['count']."</strong> ";
                                                                }
                                                                ?>)
                                                            </span>
                                                        </td>
                                                        <td class="text-right">
                                                            <strong class="text-success"><?php echo $score['events']; ?></strong>
                                                        </td>
                                                        <td class="bold text-right"><?php echo $score['bookings']; ?></td>
                                                        <td class="text-success bold text-right"><?php echo number_format(($score['bookings'] / $score['events']) * 100, 2); ?>%</td>
                                                    </tr>
                                                    <?php
                                                }
                                                ?>

                                                </tbody>
                                            </table>
                                            <!--
                                            <div class="task-footer">
                                                <div class="btn-arrow-link pull-right">
                                                    <a href="javascript:;">See All Records</a>
                                                    <i class="icon-arrow-right"></i>
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function(e){
                    $('.datatable').dataTable({
                        "order": [[ 2, "desc" ]],
                        "bFilter" : false,
                        "bLengthChange": false,
                        "bPaginate": false,
                        "info": false
                    });
                });
            </script>
            <?php
        } else {
            ?>
            <div class="col-md-12">
                <div class="alert alert-danger">
                    <h5 class="text-center" style="margin-top: 10px"><strong>You do not have permission to view this report.</strong></h5>
                </div>
            </div>
            <?php
        }
    } elseif($_POST['type'] == 'marketing'){
        $range = explode(" - ", $_POST['ext']);
        if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_marketing") !== false){
            ?>
            <div class="col-md-12">
                <div class="portlet">
                    <div class="portlet-title tabbable-line">
                        <ul class="nav nav-tabs nav-justified"> <!--  <button class="btn btn-xs red-stripe print" data-print="#sales_summary"><i class="fa fa-print"></i> Print</button> -->
                            <?php
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_marketing") !== false){
                                ?>
                                <li class="">
                                    <a class="sub_pl tab_print marketing" data-href="assets/pages/sub/sub/reports_master_sub.php?ty=mkt&luid=<?php echo $_GET['luid']; ?>" data-page-title="Booking Fees" data-toggle="tab" data-print="#bookingfees" style="color: #888" data-ext="<?php echo $range[0]; ?> - <?php echo $range[1]; ?>">
                                        Marketing Report</a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content" id="sub_content">

                        </div>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function(e){
                    $('.marketing').click();
                });
            </script>
            <?php
        } else {
            ?>
            <div class="col-md-12">
                <div class="alert alert-danger">
                    <h5 class="text-center" style="margin-top: 10px"><strong>You do not have permission to view this report.</strong></h5>
                </div>
            </div>
            <?php
        }
    } elseif($_POST['type'] == 'storage'){
        $range = explode(" - ", $_POST['ext']);
        if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_storage") !== false){
            ?>
            <div class="col-md-12">
                <div class="portlet">
                    <div class="portlet-title tabbable-line">
                        <ul class="nav nav-tabs nav-justified"> <!--  <button class="btn btn-xs red-stripe print" data-print="#sales_summary"><i class="fa fa-print"></i> Print</button> -->
                            <?php
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_lockactions") !== false){
                                ?>
                                <li>
                                    <a class="sub_pl tab_print opening" data-href="assets/pages/sub/sub/reports_master_sub.php?ty=str_o&luid=<?php echo $_GET['luid']; ?>" data-page-title="Sales Summary" data-toggle="tab" data-print="#sales_summary" style="color: #888" data-ext="<?php echo $range[0]; ?> - <?php echo $range[1]; ?>" >
                                        Opening</a>
                                </li>
                                <?php
                            }
                            ?>
                            <?php
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_storage_rentroll") !== false){
                                ?>
                                <li>
                                    <a class="sub_pl tab_print" data-href="assets/pages/sub/sub/reports_master_sub.php?ty=str_r&luid=<?php echo $_GET['luid']; ?>" data-page-title="Sales Summary" data-toggle="tab" data-print="#sales_summary" style="color: #888" data-ext="<?php echo $range[0]; ?> - <?php echo $range[1]; ?>" >
                                        Rent Roll</a>
                                </li>
                                <?php
                            }
                            ?>
                            <?php
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_sales") !== false){
                                ?>
                                <li>
                                    <a class="sub_pl tab_print" data-href="assets/pages/sub/sub/reports_master_sub.php?ty=str_s&luid=<?php echo $_GET['luid']; ?>" data-page-title="Sales Summary" data-toggle="tab" data-print="#sales_summary" style="color: #888" data-ext="<?php echo $range[0]; ?> - <?php echo $range[1]; ?>" >
                                        Sales</a>
                                </li>
                                <?php
                            }
                            ?>
                            <?php
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_closings") !== false){
                                ?>
                                <li class="<?php if(!isset($_GET['show'])){echo "active";} ?>">
                                    <a class="sub_pl tab_print closing" data-href="assets/pages/sub/sub/reports_master_sub.php?ty=str&luid=<?php echo $_GET['luid']; ?>" data-page-title="Sales Summary" data-toggle="tab" data-print="#sales_summary" style="color: #888" data-ext="<?php echo $range[0]; ?> - <?php echo $range[1]; ?>" >
                                        Closing</a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content" id="sub_content">

                        </div>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function(e){
                    $('.closing').click();
                });
            </script>
            <?php
        } else {
            ?>
            <div class="col-md-12">
                <div class="alert alert-danger">
                    <h5 class="text-center" style="margin-top: 10px"><strong>You do not have permission to view this report.</strong></h5>
                </div>
            </div>
            <?php
        }
    }
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>