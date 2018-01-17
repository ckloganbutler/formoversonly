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
                                            $timeclock = mysql_query("SELECT timeclock_id, timeclock_clockin, timeclock_clockout, timeclock_hours, timeclock_timestamp FROM fmo_users_employee_timeclock WHERE timeclock_user='".mysql_real_escape_string($employee['user_token'])."' AND (DATE(timeclock_clockout)>='".mysql_real_escape_string($range[0])."' AND DATE(timeclock_clockout)<='".mysql_real_escape_string($range[1])."') ORDER BY timeclock_timestamp DESC");
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
                                            $loans = mysql_query("SELECT advance_requested, advance_timestamp, advance_reason FROM fmo_users_employee_advances WHERE (DATE(advance_timestamp)>='".mysql_real_escape_string($range[0])."' AND DATE(advance_timestamp)<='".mysql_real_escape_string($range[1])."') AND advance_user_token='".mysql_real_escape_string($employee['user_token'])."'");
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
                                            $timeclock = mysql_query("SELECT timeclock_id, timeclock_clockin, timeclock_clockout, timeclock_hours, timeclock_timestamp FROM fmo_users_employee_timeclock WHERE timeclock_user='".mysql_real_escape_string($employee['user_token'])."' AND (DATE(timeclock_clockout)>='".mysql_real_escape_string($range[0])."' AND DATE(timeclock_clockout)<='".mysql_real_escape_string($range[1])."') ORDER BY timeclock_timestamp DESC");
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
                                            $loans = mysql_query("SELECT advance_requested, advance_timestamp, advance_reason FROM fmo_users_employee_advances WHERE (DATE(advance_timestamp)>='".mysql_real_escape_string($range[0])."' AND DATE(advance_timestamp)<='".mysql_real_escape_string($range[1])."') AND advance_user_token='".mysql_real_escape_string($employee['user_token'])."'");
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
                        <div class="caption caption-md">
                            <i class="fa fa-bar-chart-o theme-font bold"></i>
                            <span class="caption-subject font-red bold uppercase">SALES</span> <span class="font-red">|</span> <button class="btn btn-xs red-stripe print" data-print="#sales_summary"><i class="fa fa-print"></i> Print</button>
                        </div>
                        <ul class="nav nav-tabs">
                            <?php
                            if($_SESSION['uuid'] == 'DH8I8KKVVXLZAJA5G' || $_SESSION['uuid'] == 'DJ5RELUMTA7QPHWJK'){
                                ?>
                                <li class="">
                                    <a href="#bookingfees" data-toggle="tab" aria-expanded="false" style="color: black;" class="tab_print" data-print="#bookingfees">
                                        Booking Fees</a>
                                </li>
                                <?php
                            }
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_accr") !== false){
                                ?>
                                <li>
                                    <a href="#acc_r" data-toggle="tab" aria-expanded="true" style="color: black;" class="tab_print" data-print="#acc_r">
                                        Accounts Receivable</a>
                                </li>
                                <?php
                            }
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_redemption") !== false){
                                ?>
                                <li>
                                    <a href="#redemption" data-toggle="tab" aria-expanded="true" style="color: black;" class="tab_print" data-print="#redemption">
                                        Redemptions</a>
                                </li>
                                <?php
                            }
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_serviceitems") !== false){
                                ?>
                                <li>
                                    <a href="#services" data-toggle="tab" aria-expanded="true" style="color: black;" class="tab_print" data-print="#services">
                                        Service Items</a>
                                </li>
                                <?php
                            }
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_cjr") !== false){
                                ?>
                                <li>
                                    <a href="#cjr" data-toggle="tab" aria-expanded="true" style="color: black;" class="tab_print" data-print="#cjr">
                                        Completed Jobs Report</a>
                                </li>
                                <?php
                            }
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_expdpt") !== false){
                                ?>
                                <li class="<?php if(isset($_GET['show']) && $_GET['show'] == 'exp_dp'){echo "active";} ?>">
                                    <a href="#exp_dpt" data-toggle="tab" aria-expanded="true" style="color: black;" class="tab_print" data-print="#exp_dpt">
                                        Expenses & Deposits</a>
                                </li>
                                <?php
                            }
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_summary") !== false){
                                ?>
                                <li class="<?php if(!isset($_GET['show'])){echo "active";} ?>">
                                    <a href="#sales_summary" data-toggle="tab" aria-expanded="true" style="color: black;" class="tab_print" data-print="#sales_summary">
                                        Sales Summary</a>
                                </li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content">
                            <?php
                            /*
                             *  SALES SUMMARY FOR INDIANAPOLIS. INCLUDES ALL PAYMENTS FROM EVENTS.
                             */
                            $undeposited = array();
                            $deposits    = array();
                            $sales       = array();
                            $cjr_payment = array();
                            $cjr_items   = array();
                            $cjr_men     = array();
                            $services    = array();
                            $totals      = array();
                            $events      = mysql_query("SELECT event_id, event_token, event_user_token, event_name, event_date_start, event_location_token, event_cjr, event_status FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<='".mysql_real_escape_string($range[1])."') AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_date_start ASC");
                            if(mysql_num_rows($events) > 0){
                                while($event = mysql_fetch_assoc($events)){
                                    $payments = mysql_query("SELECT payment_user_token, payment_transaction_id, payment_type, payment_amount, payment_payout_reason, payment_payout_amount, payment_detail, payment_deposit_token, payment_timestamp, payment_by_user_token FROM fmo_locations_events_payments WHERE payment_event_token='".mysql_real_escape_string($event['event_token'])."'");
                                    $total          = 0;
                                    if(mysql_num_rows($payments) > 0){
                                        while($payment = mysql_fetch_assoc($payments)){
                                            $void = explode(" - ", $payment['payment_type']);
                                            if($void[1] != "VOIDED" && $void[1] != 'Booking Fee'){
                                                $cjr_payment[$event['event_token']]['payments'][] = array(
                                                    ''.date('m-d-Y', strtotime($payment['payment_timestamp'])).'',
                                                    ''.$payment['payment_type'].'',
                                                    ''.name($payment['payment_by_user_token']).'',
                                                    '$'.number_format($payment['payment_amount'], 2).'',
                                                );
                                                if(empty($payment['payment_deposit_token'])) {
                                                    if($void[0] == 'Cash' || $void[0] == 'Check'){
                                                        $deposit_warn = '<strong class="font-red"><i class="fa fa-warning"></i> UNDEPOSITED FUNDS</strong>';
                                                        $undeposited['payment'][] = array(
                                                            ''.date('m-d-Y', strtotime($payment['payment_timestamp'])).'',
                                                            '<a class="load_page" data-href="assets/pages/event.php?ev='.$event['event_token'].'" data-page-title="'.$event['event_name'].'"><strong>'.$event['event_name'].'</strong> - <strong>ID #</strong>: '.$event['event_id'].'</a>',
                                                            ''.$payment['payment_type'].'',
                                                            '$'.number_format($payment['payment_amount'], 2).'',
                                                            '<input type="number" class="form-control input-sm deposit-amount pull-right" data-id="'.$payment['payment_transaction_id'].'" placeholder="$0.00" value="'.$payment['payment_payout_amount'].'" style="width: 100px;">',
                                                            '<input type="text" class="form-control input-sm deposit-reason pull-right" data-id="'.$payment['payment_transaction_id'].'" placeholder="Reason" value="'.$payment['payment_payout_reason'].'">',
                                                            '<button class="btn btn-xs default red-stripe select-dpt" id="'.$payment['payment_transaction_id'].'" data-id="'.$payment['payment_transaction_id'].'" value="'.number_format($payment['payment_amount'] - $payment['payment_payout_amount'], 2, '.', '').'"">Select $<span class="amt">'.number_format($payment['payment_amount'] - $payment['payment_payout_amount'], 2).'</span> for deposit</button>'
                                                        );
                                                    } else {
                                                        $deposit_warn = NULL;
                                                    }
                                                } else {
                                                    $deposit_warn = NULL;
                                                }
                                                $total += $payment['payment_amount'];
                                            } else {
                                                $deposit_warn = NULL;
                                            }
                                        }
                                    } else {
                                        $deposit_warn = NULL;
                                    }

                                    if($event['event_cjr'] != 1 && $event['event_status'] != 9){
                                        if(empty($deposit_warn)){
                                            $deposit_done = true;
                                        } else {
                                            $deposit_done = false;
                                        }
                                        $findLabor = mysql_query("SELECT laborer_user_token, laborer_hours_worked, laborer_role, laborer_rate FROM fmo_locations_events_laborers WHERE laborer_event_token='".mysql_real_escape_string($event['event_token'])."'");
                                        while($lb = mysql_fetch_assoc($findLabor)) {
                                            $cjr_men[$event['event_token']]['men'][] = array(
                                                '<span class="badge badge-info">'.$lb['laborer_role'].'</span> - '.name($lb['laborer_user_token']).'',
                                                '$'.number_format($lb['laborer_rate'], 2).'/hr',
                                                ''.$lb['laborer_hours_worked'].'hrs'
                                            );
                                        }
                                        $cjr['records'][] = array(
                                            ''.$event['event_name'].'',
                                            ''.date('m-d-Y', strtotime($event['event_date_start'])).'',
                                            ''.$event['event_status'].'',
                                            ''.$deposit_done.'',
                                            ''.$event['event_token'].'',
                                        );
                                    }

                                    $location = mysql_fetch_array(mysql_query("SELECT location_sales_tax FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));

                                    if(!empty($location['location_sales_tax'])){
                                        $tax = $location['location_sales_tax'];
                                    } else {$tax = 0;}

                                    $findItems = mysql_query("SELECT item_item, item_desc, item_qty, item_cost, item_total, item_taxable, item_commission, item_redeemable, item_prepay FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."'");
                                    $iTotalRecords = mysql_num_rows($findItems);

                                    $findPaid = mysql_query("SELECT payment_amount, payment_type FROM fmo_locations_events_payments WHERE payment_event_token='".mysql_real_escape_string($event['event_token'])."'");
                                    $bTotalRecords = mysql_num_rows($findPaid);

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
                                            } if($item['item_commission'] == 1){
                                                $totals['coms']  += number_format($item['item_total'], 2, '.', '');
                                            } else {
                                                $totals['coms'] += 0.00;
                                            }
                                            if(in_array($item['item_item'], $services['types'], true)){
                                                $count = $services['types'][$item['item_item']]['count'];
                                                $services['types'][$item['item_item']]['count'] += $count++;
                                                $services['types'][$item['item_item']]['total_qty'] += $item['item_qty'];
                                                if($item['item_taxable'] == 1){
                                                    $services['types'][$item['item_item']]['sales'] += ($item['item_qty'] * $item['item_cost']) + ($item['item_qty'] * $item['item_cost']) * $tax;
                                                } else {
                                                    $services['types'][$item['item_item']]['sales'] += ($item['item_qty'] * $item['item_cost']);
                                                } if($item['item_redeemable'] == 1){
                                                    $services['types'][$item['item_item']]['prepay']   += $item['item_prepay'];
                                                    $services['types'][$item['item_item']]['discount'] += $item['item_total'];
                                                }
                                                $services['types'][$item['item_item']]['sales']     += $item['item_qty'];
                                            } else {
                                                if(!empty($item['item_item'])){
                                                    $services['types'][$item['item_item']]['count']    += 1;
                                                    $services['types'][$item['item_item']]['name']      = $item['item_item'];
                                                    $services['types'][$item['item_item']]['total_qty'] += $item['item_qty'];
                                                    if($item['item_taxable'] == 1){
                                                        $services['types'][$item['item_item']]['sales'] += ($item['item_qty'] * $item['item_cost']) + ($item['item_qty'] * $item['item_cost']) * $tax;
                                                    } else {
                                                        $services['types'][$item['item_item']]['sales'] += ($item['item_qty'] * $item['item_cost']);
                                                    } if($item['item_redeemable'] == 1){
                                                        $services['types'][$item['item_item']]['prepay']   += $item['item_prepay'];
                                                        $services['types'][$item['item_item']]['discount'] += $item['item_total'];
                                                    }
                                                }
                                            }
                                            $cjr_items[$event['event_token']]['items'][] = array(
                                                ''.$item['item_item'].'',
                                                ''.$item['item_qty'].'',
                                                ''.$item['item_cost'].'',
                                                ''.$item['item_total'].'',
                                            );

                                        }
                                        $total2['total'] = $total2['sub_total'] + $total2['tax'];
                                    } else {
                                        $total2['total']     = 0;
                                        $total2['sub_total'] = 0;
                                    }

                                    if($bTotalRecords > 0){
                                        while($paid = mysql_fetch_assoc($findPaid)){
                                            $void = explode(" - ", $paid['payment_type']);
                                            if($void[1] != 'VOIDED'){
                                                $total2['paid'] += $paid['payment_amount'];
                                            }
                                        }
                                        $total2['unpaid'] = $total2['total'] - $total2['paid'];
                                    } else {
                                        $total2['unpaid'] = $total2['total'];
                                        $total2['paid']   = 0;
                                    }

                                    $totals['gross']     += $total2['total'];
                                    $totals['grossTax']  += $total2['tax'];
                                    $totals['taxable']   += $total2['taxable'];

                                    if($total2['unpaid'] > 0 && $total2['unpaid'] !== false){
                                        $acc_r['acc'][] = array(
                                            ''.date('m-d-Y', strtotime($event['event_date_start'])).'',
                                            '<strong>EVENT</strong>: '.$event['event_name'].' <strong>CUSTOMER</strong>: '.name($event['event_user_token']).'',
                                            ''.number_format($total2['paid'], 2).'',
                                            ''.number_format($total2['unpaid'], 2).'',
                                        );
                                    }
                                    if($event['event_status'] == 5){
                                        $extra = "<span class='badge badge-danger badge-roundless'>Cancelled</span>";
                                    } else {$extra = NULL;}
                                    $sales['records'][] = array(
                                        ''.date('m-d-Y', strtotime($event['event_date_start'])).'',
                                        '<strong>'.$event['event_name'].'</strong> - <strong>ID #</strong>: '.$event['event_id'].' '.$extra,
                                        ''.$deposit_warn.'',
                                        ''.$totals['code'].'',
                                        '$'.number_format($total2['total'], 2).'',
                                    );
                                }
                            }

                            /*
                             * BOOKING FEES. ONLY FOR US. :-)
                             */
                            if($_SESSION['uuid'] == 'DH8I8KKVVXLZAJA5G' || $_SESSION['uuid'] == 'DJ5RELUMTA7QPHWJK'){
                                $bf = array();
                                $bookingfees = mysql_query("SELECT payment_event_token, payment_user_token, payment_company_token, payment_type, payment_charge_token, payment_amount, payment_era, payment_timestamp, payment_by_user_token FROM fmo_locations_events_payments WHERE payment_type REGEXP '[[:<:]]Booking Fee[[:>:]]' ORDER BY payment_timestamp ASC");
                                if(mysql_num_rows($bookingfees) > 0){
                                    while($booking = mysql_fetch_assoc($bookingfees)){
                                        $bf['records'][] = array(
                                            '<strong>'.date('m-d-Y', strtotime($booking['payment_timestamp'])).'</strong>',
                                            '<strong>'.companyName($booking['payment_company_token']).'</strong> - '.eventName($booking['payment_event_token']),
                                            ''.$booking['payment_era'].'',
                                            ''.$booking['payment_charge_token'].'',
                                            '<strong>'.eventLocationName($booking['payment_event_token']).'</strong>',
                                            '<strong>'.name($booking['payment_by_user_token']).'</strong>',
                                        );
                                    }
                                }
                            }
                            /*
                            *  EXPENSES SUMMARY FOR INDIANAPOLIS. INCLUDES ALL PAYMENTS FROM EVENTS.
                            */
                            $ex = array();
                            $expenses = mysql_query("SELECT expense_desc, expense_name, expense_date, expense_type, expense_reason, expense_amount, expense_by FROM fmo_locations_expenses WHERE expense_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (expense_date>='".mysql_real_escape_string($range[0])."' AND expense_date<='".mysql_real_escape_string($range[1])."') ORDER BY expense_date ASC");
                            if(mysql_num_rows($expenses) > 0){
                                while($expense = mysql_fetch_assoc($expenses)){
                                    $ex['expenses'][] = array(
                                        ''.date('m-d-Y', strtotime($expense['expense_date'])).'',
                                        '<strong>'.$expense['expense_reason'].'</strong> - '.sentence_case($expense['expense_desc']).'',
                                        ''.name($expense['expense_by']).'',
                                        ''.$expense['expense_type'].'',
                                        '$'.number_format($expense['expense_amount'], 2).'',
                                    );
                                }
                            }

                            $de = array();
                            $deposit = mysql_query("SELECT deposit_id, deposit_token, deposit_teller, deposit_comments, deposit_amount, deposit_by_user_token, deposit_timestamp FROM fmo_locations_deposits WHERE deposit_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (deposit_timestamp>='".mysql_real_escape_string($range[0])."' AND deposit_timestamp<='".mysql_real_escape_string($range[1])."')");
                            if(mysql_num_rows($deposit) > 0){
                                while($dpts = mysql_fetch_assoc($deposit)){
                                    $payments    = mysql_query("SELECT payment_type, payment_payout_amount, payment_amount, payment_event_token, payment_deposit_token FROM fmo_locations_events_payments WHERE payment_deposit_token='".mysql_real_escape_string($dpts['deposit_token'])."'");
                                    if(mysql_num_rows($payments) > 0){
                                        $totalPayout = 0;
                                        while($payment = mysql_fetch_assoc($payments)){
                                            $events  = mysql_query("SELECT event_name, event_id FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($payment['payment_event_token'])."'");
                                            $totalPayout+=$payment['payment_payout_amount'];
                                            if(mysql_num_rows($events) > 0){
                                                while($event = mysql_fetch_assoc($events)){
                                                    $deposits[$payment['payment_deposit_token']]['payments'][] = array(
                                                        '<strong>'.$event['event_name'].'</strong> - <strong>ID #</strong>: '.$event['event_id'],
                                                        ''.$payment['payment_type'].'',
                                                        '$'.number_format($payment['payment_payout_amount'], 2).'',
                                                        '$'.number_format($payment['payment_amount'] - $payment['payment_payout_amount'], 2).''
                                                    );
                                                }
                                            }
                                        }
                                    }

                                    $de['deposits'][] = array(
                                        ''.date('m-d-Y', strtotime($dpts['deposit_timestamp'])).'',
                                        '<strong>'.$dpts['deposit_id'].'</strong>',
                                        ''.name($dpts['deposit_by_user_token']).'',
                                        ''.$dpts['deposit_token'].'',
                                        '$'.number_format($dpts['deposit_amount'], 2).'',
                                        ''.$dpts['deposit_by_user_token'].'',
                                        ''.$dpts['deposit_teller'].'',
                                        ''.$dpts['deposit_comments'].'',
                                        '$'.number_format($totalPayout, 2).'',
                                    );

                                }
                            }
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_accr") !== false){
                                ?>
                                <div class="tab-pane <?php if(isset($_GET['show']) && $_GET['show'] == 'acc_r'){echo "active";} ?>" id="acc_r">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <center>
                                                <h3>
                                                    <i class="fa fa-dollar"></i> Accounts Receivable | <strong><?php echo date('m-d-Y', strtotime($range[0])); ?></strong> - <strong><?php echo date('m-d-Y', strtotime($range[1])); ?></strong>
                                                </h3><br/>
                                            </center>
                                        </div>
                                    </div>
                                    <div class="portlet">
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover dats" data-table="acc_rece" data-order="0" data-asdc="asc" id="acc_rece">
                                                <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Event & Customer</th>
                                                    <th class="text-right">Amount <em>Paid</em></th>
                                                    <th class="text-right">Amount <em>Still Due</em></th>
                                                    <th class="text-right">Actions</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach($acc_r['acc'] as $items){
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $items[0]; ?></td>
                                                        <td><?php echo $items[1]; ?></td>
                                                        <td class="text-right"><strong class="text-success">$<?php echo $items[2]; ?></strong></td>
                                                        <td class="text-right"><strong class="text-danger">$<?php echo $items[3]; ?></strong></td>
                                                        <td class="text-right">
                                                            <button class="btn btn-xs red "><i class="fa fa-dollar"></i> Receive payment</button>
                                                            <button class="btn btn-xs purple "><i class="fa fa-user-secret"></i> Mark for Attorney</button>
                                                        </td>
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
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_redemption") !== false){
                                ?>
                                <div class="tab-pane <?php if(isset($_GET['show']) && $_GET['show'] == 'redemption'){echo "active";} ?>" id="redemption">
                                    <?php
                                    /*
                                     *  ITEMS THAT NEED TO BE REDEEMED
                                     */
                                    $redeem = array();
                                    $events = mysql_query("SELECT event_token, event_user_token, event_name FROM fmo_locations_events WHERE event_company_token='".mysql_real_escape_string($_SESSION['cuid'])."'");
                                    if(mysql_num_rows($events) > 0){
                                        while($event = mysql_fetch_assoc($events)){
                                            $items = mysql_query("SELECT item_id, item_item, item_desc, item_added FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."' AND item_redeemable=1");
                                            if(mysql_num_rows($items) > 0){
                                                while($item = mysql_fetch_assoc($items)){
                                                    $redeem['items'][] = array(
                                                        ''.$event['event_name'].'',
                                                        ''.$item['item_item'].'',
                                                        ''.$item['item_desc'].'',
                                                        ''.$item['item_added'].'',
                                                        ''.$item['item_id'].''
                                                    );
                                                }
                                            }
                                        }
                                    }

                                    ?>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <center>
                                                <h3>
                                                    <i class="fa fa-dollar"></i> Redemption Report | <strong><?php echo date('m-d-Y', strtotime($range[0])); ?></strong> - <strong><?php echo date('m-d-Y', strtotime($range[1])); ?></strong>
                                                </h3><br/>
                                            </center>
                                        </div>
                                    </div>
                                    <div class="portlet">
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover dats" data-table="redempt" data-order="0" data-asdc="desc" id="redempt">
                                                <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Item</th>
                                                    <th>Description/Codes</th>
                                                    <th>Event Name</th>
                                                    <th>Actions</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach($redeem['items'] as $items){
                                                    ?>
                                                    <tr>
                                                        <td><?php echo date('m-d-Y', strtotime($items[3])); ?></td>
                                                        <td><?php echo $items[1]; ?></td>
                                                        <td><?php echo $items[2]; ?></td>
                                                        <td><?php echo $items[0]; ?></td>
                                                        <td>
                                                            <?php
                                                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_redemption_redeem") !== false){
                                                                ?>
                                                                <button class="btn btn-xs red redeem btn-block" data-redeem="<?php echo $items[4]; ?>"><i class="fa fa-check"></i> Mark as redeemed (currently not)</button>
                                                                <?php
                                                            }
                                                            ?>
                                                        </td>
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
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_serviceitems") !== false){
                                ?>
                                <div class="tab-pane <?php if(isset($_GET['show']) && $_GET['show'] == 'services'){echo "active";} ?>" id="services">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <center>
                                                <h3>
                                                    <i class="fa fa-dollar"></i> Service Items Report | <strong><?php echo date('m-d-Y', strtotime($range[0])); ?></strong> - <strong><?php echo date('m-d-Y', strtotime($range[1])); ?></strong>
                                                </h3><br/>
                                            </center>
                                        </div>
                                    </div>
                                    <div class="portlet">
                                        <div class="portlet-title" style="margin-bottom: 0px;">
                                            <div class="caption">
                                                <small><strong>Service Items Report</strong>- <?php echo locationName2($_GET['luid']); ?></small>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover" id="service_items">
                                                <thead>
                                                <tr>
                                                    <th>Service Item</th>
                                                    <th>Total # of uses</th>
                                                    <th>Total Quantity</th>
                                                    <th>Average</th>
                                                    <th>Total Income</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach($services['types'] as $srv){
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $srv['name'];  ?></td>
                                                        <td><?php echo $srv['count']; ?></td>
                                                        <td><?php echo $srv['total_qty']; ?></td>
                                                        <td><?php echo number_format($srv['sales'] / $srv['total_qty'], 2); ?> avg</td>
                                                        <td class="text-success bold">$<?php echo number_format($srv['sales'], 2); ?></td>
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
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_cjr") !== false){
                                ?>
                                <div class="tab-pane <?php if(isset($_GET['show']) && $_GET['show'] == 'cjr'){echo "active";} ?>" id="cjr">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <center>
                                                <h3>
                                                    <i class="fa fa-dollar"></i> Completed Jobs Report | <strong><?php echo date('m-d-Y', strtotime($range[0])); ?></strong> - <strong><?php echo date('m-d-Y', strtotime($range[1])); ?></strong>
                                                </h3><br/>
                                            </center>
                                        </div>
                                    </div>
                                    <div class="portlet">
                                        <div class="portlet-title" style="margin-bottom: 0px;">
                                            <div class="caption">
                                                <small><strong>Completed Jobs Report</strong> - <?php echo locationName($_GET['luid']); ?></small>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <?php
                                            $pk = 0;
                                            if(!empty($cjr['records'])){
                                                foreach($cjr['records'] as $exs){
                                                    $pk++;
                                                    switch($exs[2]){
                                                        case 1: $status = "New Booking"; $s_btn = "red-stripe"; $s_color = "font-red"; break;
                                                        case 2: $status = "Confirmed";  $s_btn = "red-stripe"; $s_color = "font-red";  break;
                                                        case 3: $status = "Left Message";  $s_btn = "red-stripe"; $s_color = "font-red"; break;
                                                        case 4: $status = "On Hold";  $s_btn = "red-stripe"; $s_color = "font-red";  break;
                                                        case 5: $status = "Cancelled";  $s_btn = "green-stripe"; $s_color = "font-green";  break;
                                                        case 9: $status = "Dead Hot Lead";  $s_btn = "red-stripe"; $s_color = "font-red"; break;
                                                        case 8: $status = "Completed"; $s_btn = "green-stripe"; $s_color = "font-green"; break;
                                                        default: $status = "On Hold";  $s_btn = "red-stripe"; $s_color = "font-red"; break;
                                                    }
                                                    if($exs[3] == true){
                                                        $color = "font-green";
                                                        $btns  = "green-stripe";
                                                        $btn   = "green";
                                                        $stat  = "Completed";
                                                        $dis   = NULL;
                                                    } else {
                                                        $color = "font-red";
                                                        $btns  = "red-stripe";
                                                        $btn   = "red";
                                                        $stat  = "Incomplete";
                                                        $dis   = "disabled";
                                                    }
                                                    if($exs[2] != 8 && $exs[2] != 5){
                                                        $dis     = "disabled";
                                                        $btn     = "red";
                                                        $s_color = "font-red";
                                                    }

                                                    ?>
                                                    <div id="cjr_h_<?php echo $pk; ?>" class="panel-group" <?php if($pk == 1){echo "style='margin-top: 10px;'";} ?>>
                                                        <div class="panel panel-default">
                                                            <div class="panel-heading">
                                                                <div class="actions pull-right" style="margin-top: -6px; margin-right: -9px">
                                                                    <a href="javascript:;" class="btn btn-default <?php echo $s_btn; ?> btn-sm">
                                                                        <i class="fa fa-tag"></i> <span class="hidden-sm hidden-xs">Status:</span> <strong class="<?php echo $s_color; ?>"><?php echo $status; ?></strong></a>
                                                                    <a href="javascript:;" class="btn btn-default <?php echo $btns; ?> btn-sm">
                                                                        <i class="fa fa-bank"></i> <span class="hidden-sm hidden-xs">Deposit: </span> <strong class="<?php echo $color; ?>"><?php echo $stat; ?></strong></a>
                                                                    <a href="javascript:;" class="btn btn-default btn-sm popout" data-pop="event.php?ev=<?php echo $exs[4]; ?>" data-page-title="Reviewing Event">
                                                                        <i class="fa fa-pencil-square"></i> <span class="hidden-sm hidden-xs">Review</span></a>
                                                                    <a href="javascript:;" class="btn <?php echo $btn; ?> btn-sm <?php echo $dis; ?> cjr_apr" data-ev="<?php echo $exs[4]; ?>">
                                                                        <i class="fa fa-external-link-square"></i> <span class="hidden-sm hidden-xs">Approve</span></a>
                                                                </div>
                                                                <div class="caption">
                                                                    <h4 class="panel-title">
                                                                        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#cjr_h_<?php echo $pk; ?>" href="#cjr_<?php echo $pk; ?>" aria-expanded="false"><strong><?php echo $exs[1]; ?></strong> - <strong class="<?php echo $color; ?>"><?php echo $exs[0]; ?></strong></a>
                                                                    </h4>
                                                                </div>
                                                            </div>
                                                            <div id="cjr_<?php echo $pk; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                                                <div class="panel-body">
                                                                    <div class="scroller">
                                                                        <div class="row">
                                                                            <div class="col-md-4">
                                                                                <div class="portlet">
                                                                                    <div class="portlet-title" style="margin-bottom: 0px;">
                                                                                        <div class="caption">
                                                                                            <small><strong>Crewmen</strong>- </small>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="portlet-body">
                                                                                        <table class="table table-striped table-hover datatable">
                                                                                            <thead>
                                                                                            <tr>
                                                                                                <th>Laborer</th>
                                                                                                <th>Rate</th>
                                                                                                <th class="text-right" style="padding-right: 5px">Hours</th>
                                                                                            </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                            <?php
                                                                                            foreach($cjr_men[$exs[4]]['men'] as $py){
                                                                                                ?>
                                                                                                <tr>
                                                                                                    <td><?php echo $py[0]; ?></td>
                                                                                                    <td><?php echo $py[1]; ?></td>
                                                                                                    <td class="text-right"><?php echo $py[2] ?></td>
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
                                                                            <div class="col-md-4">
                                                                                <div class="portlet">
                                                                                    <div class="portlet-title" style="margin-bottom: 0px;">
                                                                                        <div class="caption">
                                                                                            <small><strong>Service Items</strong>- </small>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="portlet-body">
                                                                                        <table class="table table-striped table-hover datatable">
                                                                                            <thead>
                                                                                            <tr>
                                                                                                <th>Item</th>
                                                                                                <th>Quantity</th>
                                                                                                <th>Cost</th>
                                                                                                <th class="text-right" style="padding-right: 5px">Total</th>
                                                                                            </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                            <?php
                                                                                            foreach($cjr_items[$exs[4]]['items'] as $py){
                                                                                                ?>
                                                                                                <tr>
                                                                                                    <td><?php echo $py[0]; ?></td>
                                                                                                    <td><?php echo $py[1]; ?></td>
                                                                                                    <td>$<?php echo $py[2] ?></td>
                                                                                                    <td class="text-right">$<?php echo $py[3] ?></td>
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
                                                                            <div class="col-md-4">
                                                                                <div class="portlet">
                                                                                    <div class="portlet-title" style="margin-bottom: 0px;">
                                                                                        <div class="caption">
                                                                                            <small><strong>Payments</strong>- </small>
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="portlet-body">
                                                                                        <table class="table table-striped table-hover datatable">
                                                                                            <thead>
                                                                                            <tr>
                                                                                                <th>Payment Date</th>
                                                                                                <th>Type</th>
                                                                                                <th>By</th>
                                                                                                <th class="text-right" style="padding-right: 5px">Amount</th>
                                                                                            </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                            <?php
                                                                                            foreach($cjr_payment[$exs[4]]['payments'] as $py){
                                                                                                ?>
                                                                                                <tr>
                                                                                                    <td><?php echo $py[0]; ?></td>
                                                                                                    <td><?php echo $py[1]; ?></td>
                                                                                                    <td><?php echo $py[2]; ?></td>
                                                                                                    <td class="text-right"><?php echo $py[3] ?></td>
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
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                }
                                            } else {
                                                ?>
                                                <div class="alert alert-warning alert-dismissable">
                                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                                    <strong>No estimates available to view!</strong> Add new estimates to see them appear here.
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_expdpt") !== false){
                                ?>
                                <div class="tab-pane <?php if(isset($_GET['show']) && $_GET['show'] == 'exp_dp'){echo "active";} ?>" id="exp_dpt">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <center>
                                                <h3>
                                                    <i class="fa fa-dollar"></i> Expenses & Deposits Report | <strong><?php echo date('m-d-Y', strtotime($range[0])); ?></strong> - <strong><?php echo date('m-d-Y', strtotime($range[1])); ?></strong>
                                                </h3><br/>
                                            </center>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="portlet">
                                                <div class="portlet-title" style="margin-bottom: 0px;">
                                                    <div class="caption font-red">
                                                        <small><strong><i class="fa fa-exclamation-triangle"></i> UNDEPOSITED FUNDS</strong> - <?php echo locationName($_GET['luid']); ?></small>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <table class="table table-striped table-hover datatable">
                                                        <thead>
                                                        <tr>
                                                            <th>Event Date</th>
                                                            <th>Event Name & ID</th>
                                                            <th class="text-right" style="padding-right: 5px">Payment Type</th>
                                                            <th class="text-right" style="padding-right: 5px">Total</th>
                                                            <th class="text-right" style="padding-right: 5px">Payout Amount</th>
                                                            <th class="text-right" style="padding-right: 5px">Payout Reason</th>
                                                            <th class="text-right" style="padding-right: 5px">Select for Deposit</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        foreach($undeposited['payment'] as $dpt){
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $dpt[0]; ?></td>
                                                                <td><?php echo $dpt[1]; ?></td>
                                                                <td class="text-right"><?php echo $dpt[2]; ?></td>
                                                                <td class="text-right font-green"><strong><?php echo $dpt[3]; ?></strong></td>
                                                                <td class="text-right"><?php echo $dpt[4]; ?></td>
                                                                <td class="text-right"><?php echo $dpt[5]; ?></td>
                                                                <td class="text-right font-red"><?php echo $dpt[6]; ?></td>
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
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="portlet red box">
                                                <div class="portlet-title">
                                                    <div class="caption">
                                                        <small>Make deposit(s):</small>
                                                    </div>
                                                </div>
                                                <div class="portlet-body" id="start_b_dp">
                                                    <center><h4>Please select an amount to be deposited for tools to appear here.</h4></center>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                                            <div class="portlet">
                                                <div class="portlet-title" style="margin-bottom: 0px;">
                                                    <div class="caption">
                                                        <small><strong>Expenses</strong> - <?php echo locationName($_GET['luid']); ?></small>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <table class="table table-striped table-hover datatable">
                                                        <thead>
                                                        <tr>
                                                            <th>Expense Date</th>
                                                            <th>Expense Reasoning</th>
                                                            <th class="text-right" style="padding-right: 5px">By</th>
                                                            <th class="text-right" style="padding-right: 5px">Type</th>
                                                            <th class="text-right" style="padding-right: 5px">Amount</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        foreach($ex['expenses'] as $exs){
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $exs[0]; ?></td>
                                                                <td><?php echo substr($exs[1], 0, 55)." ..."; ?></td>
                                                                <td class="text-right"><?php $name = explode(" ", $exs[2]); echo $name[0]." ".substr($name[1], 0, 1)."."; ?></td>
                                                                <td class="text-right"><?php echo $exs[3]; ?></td>
                                                                <td class="text-right font-red"><strong><?php echo $exs[4]; ?></strong></td>
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
                                        <div class="col-md-6 col-lg-6 col-sm-12 col-xs-12">
                                            <div class="portlet">
                                                <div class="portlet-title" style="margin-bottom: 0px;">
                                                    <div class="caption">
                                                        <small><strong>Deposits</strong> - <?php echo locationName($_GET['luid']); ?></small>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <?php
                                                    $pk = 0;
                                                    if(!empty($de['deposits'])){
                                                        foreach($de['deposits'] as $exs){
                                                            $pk++;
                                                            ?>
                                                            <div id="deposit_h_<?php echo $pk; ?>" class="panel-group" <?php if($pk == 1){echo "style='margin-top: 10px;'";} ?>>
                                                                <div class="panel panel-default">
                                                                    <div class="panel-heading">
                                                                        <div class="actions pull-right hidden" style="margin-top: -6px; margin-right: -9px">
                                                                            <a href="javascript:;" class="btn btn-default btn-sm edit" data-edit="cs_<?php echo $exs[1]; ?>">
                                                                                <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md hidden-xs">Edit</span> </a>
                                                                        </div>
                                                                        <div class="caption">
                                                                            <h4 class="panel-title">
                                                                                <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#estimate_h_<?php echo $pk; ?>" href="#estimate_<?php echo $pk; ?>" aria-expanded="false"><strong>Deposit Number #<?php echo $exs[1]; ?></strong></a>
                                                                            </h4>
                                                                        </div>
                                                                    </div>
                                                                    <div id="estimate_<?php echo $pk; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">
                                                                        <div class="panel-body">
                                                                            <div class="scroller">
                                                                                <div class="row">
                                                                                    <div class="col-md-12 col-sm-12 col-xs-12">
                                                                                        <h4> (#<?php echo $exs[1]; ?>) <?php echo $exs[0]; ?> - <strong><?php $name = explode(" ", $exs[2]); echo $name[0]." ".substr($name[1], 0, 1)."."; ?></strong> deposited <strong class="text-success"><?php echo $exs[4]; ?></strong></h4>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row">
                                                                                    <div class="col-md-4 col-sm-12 col-xs-12">
                                                                                        <div class="well" style="margin-top: 11px; margin-bottom: 8px;">
                                                                                            <?php
                                                                                            if(!empty($exs[6])){
                                                                                                ?>
                                                                                                <img src="<?php echo $exs[6]; ?>" class="img-thumbnail"/>
                                                                                                <?php
                                                                                            } else {
                                                                                                ?>
                                                                                                <address>

                                                                                                    <h5 class="text-center" style="margin: 0;">Teller ticket will appear here once it has been uploaded.</h5>
                                                                                                </address>
                                                                                                <?php
                                                                                            }
                                                                                            ?>
                                                                                        </div>
                                                                                        <?php
                                                                                        if(empty($exs[6])){
                                                                                            ?>
                                                                                            <button class="btn btn-block default red-stripe fire" data-fire="teller" data-uuid="<?php echo $exs[5]; ?>" data-id="<?php echo $exs[3]; ?>">Request Teller Ticket</button>
                                                                                            <?php
                                                                                        }
                                                                                        ?>
                                                                                    </div>
                                                                                    <div class="col-md-8 col-sm-12 col-xs-12">
                                                                                        <table class="table table-striped table-hover datatable" style="margin: 0 !important;">
                                                                                            <thead>
                                                                                            <tr role="row" class="heading">
                                                                                                <th>
                                                                                                    Event
                                                                                                </th>
                                                                                                <th class="text-right" style="padding-right: 5px">
                                                                                                    Type
                                                                                                </th>
                                                                                                <th class="text-right" style="padding-right: 5px">
                                                                                                    Payout
                                                                                                </th>
                                                                                                <th class="text-right" style="padding-right: 5px">
                                                                                                    Amount
                                                                                                </th>
                                                                                            </tr>
                                                                                            </thead>
                                                                                            <tbody>
                                                                                            <?php
                                                                                            foreach($deposits[$exs[3]]['payments'] as $dp){
                                                                                                ?>
                                                                                                <tr>
                                                                                                    <td><?php echo $dp[0]; ?></td>
                                                                                                    <td class="text-right text-success"><?php echo $dp[1]; ?></td>
                                                                                                    <td class="text-right text-danger"><strong><?php echo $dp[2]; ?></strong></td>
                                                                                                    <td class="text-right text-success"><strong><?php echo $dp[3]; ?></strong></td>
                                                                                                </tr>
                                                                                                <?php
                                                                                            }
                                                                                            ?>
                                                                                            </tbody>
                                                                                        </table>
                                                                                        <?php
                                                                                        if(!empty($exs[7])){
                                                                                            ?>
                                                                                            <p><strong>Comments:</strong><br/><?php echo $exs[7]; ?></p>
                                                                                            <?php
                                                                                        }
                                                                                        ?>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <?php
                                                        }
                                                    } else {
                                                        ?>
                                                        <div class="alert alert-warning alert-dismissable">
                                                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                                                            <strong>No estimates available to view!</strong> Add new estimates to see them appear here.
                                                        </div>
                                                        <?php
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                            if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_reports_sales_summary") !== false){
                                ?>
                                <div class="tab-pane <?php if(!isset($_GET['show'])){echo "active";} ?>" id="sales_summary">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <center>
                                                <h3>
                                                    <i class="fa fa-dollar"></i> Sales Summary | <strong><?php echo date('m-d-Y', strtotime($range[0])); ?></strong> - <strong><?php echo date('m-d-Y', strtotime($range[1])); ?></strong>
                                                </h3><br/>
                                            </center>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="portlet">
                                                <div class="portlet-title" style="margin-bottom: 0px;">
                                                    <div class="caption">
                                                        <small><strong>Sales Report</strong>- <?php echo locationName($_GET['luid']); ?></small>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <table class="table table-striped table-hover datatable">
                                                        <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Event Name & ID</th>
                                                            <th></th>
                                                            <th class="text-right" style="padding-right: 5px">Promotion Code</th>
                                                            <th class="text-right" style="padding-right: 5px">Transaction Amount</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        foreach($sales['records'] as $items){
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $items[0]; ?></td>
                                                                <td><?php echo $items[1]; ?></td>
                                                                <td class="text-right"><?php echo $items[2]; ?></td>
                                                                <td class="text-right"><?php echo $items[3]; ?></td>
                                                                <td class="text-right font-green"><strong><?php echo $items[4]; ?></strong></td>
                                                            </tr>
                                                            <?php
                                                        }
                                                        ?>
                                                        </tbody>
                                                    </table>

                                                    <div class="invoice">
                                                        <div class="row">
                                                            <div class="col-xs-12 invoice-block">
                                                                <ul class="list-unstyled amounts">
                                                                    <li>
                                                                        <small class="bold">(<strong>$<?php echo $totals['coms']; ?></strong> commissionable)</small>  Sub Total: <h3 style="display: inline;" class="text-success bold">$<?php echo number_format($totals['gross'], 2); ?></h3>
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
                                                                        NET: <h3 style="display: inline" class="text-success bold">$<?php echo number_format(($totals['gross'] + $totals['discounts']) + $totals['prepaid'], 2); ?></h3>
                                                                    </li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>


                                            <div class="portlet">
                                                <div class="portlet-title" style="margin-bottom: 0px;">
                                                    <div class="caption">
                                                        <small><strong>Expense Report</strong>- <?php echo locationName($_GET['luid']); ?></small>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <table class="table table-striped table-hover datatable">
                                                        <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Expense Reasoning</th>
                                                            <th class="text-right" style="padding-right: 5px">Expense By</th>
                                                            <th class="text-right" style="padding-right: 5px">Expense Type</th>
                                                            <th class="text-right" style="padding-right: 5px">Expense Amount</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        foreach($ex['expenses'] as $exs){
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $exs[0]; ?></td>
                                                                <td><?php echo $exs[1]; ?></td>
                                                                <td class="text-right"><?php echo $exs[2]; ?></td>
                                                                <td class="text-right"><?php echo $exs[3]; ?></td>
                                                                <td class="text-right font-red"><strong><?php echo $exs[4]; ?></strong></td>
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


                                            <div class="portlet">
                                                <div class="portlet-title" style="margin-bottom: 0px;">
                                                    <div class="caption">
                                                        <small><strong>Deposit Report</strong>- <?php echo locationName($_GET['luid']); ?></small>
                                                    </div>
                                                </div>
                                                <div class="portlet-body">
                                                    <table class="table table-striped table-hover datatable">
                                                        <thead>
                                                        <tr>
                                                            <th>Date</th>
                                                            <th>Deposit ID by Depositor</th>
                                                            <th class="text-right" style="padding-right: 5px">Deposit Payout</th>
                                                            <th class="text-right" style="padding-right: 5px">Deposit Amount</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                        foreach($de['deposits'] as $exs){
                                                            ?>
                                                            <tr>
                                                                <td><?php echo $exs[0]; ?></td>
                                                                <td>#<strong><?php echo $exs[1]; ?></strong> by <?php echo $exs[2]; ?></td>
                                                                <td class="text-right"><?php echo $exs[8] ?></td>
                                                                <td class="text-right"><?php echo $exs[4]; ?></td>
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
                                    </div>
                                </div>
                                <?php
                            }
                            if($_SESSION['uuid'] == 'DH8I8KKVVXLZAJA5G' || $_SESSION['uuid'] == 'DJ5RELUMTA7QPHWJK'){
                                ?>
                                <div class="tab-pane <?php if(isset($_GET['show']) && $_GET['show'] == 'bookingfees'){echo "active";} ?>" id="bookingfees">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <center>
                                                <h3>
                                                    <i class="fa fa-dollar"></i> Booking Fees | <strong><?php echo date('m-d-Y', strtotime($range[0])); ?></strong> - <strong><?php echo date('m-d-Y', strtotime($range[1])); ?></strong>
                                                </h3><br/>
                                            </center>
                                        </div>
                                    </div>
                                    <div class="portlet">
                                        <div class="portlet-title" style="margin-bottom: 0px;">
                                            <div class="caption">
                                                <small><strong>Booking Fee Report</strong>- <?php echo locationName2($_GET['luid']); ?></small>
                                            </div>
                                        </div>
                                        <div class="portlet-body">
                                            <table class="table table-striped table-hover datatable">
                                                <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Company Name & Event</th>
                                                    <th>Taken Where</th>
                                                    <th>Transaction Token</th>
                                                    <th>Location</th>
                                                    <th>Taken By</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach($bf['records'] as $bfs){
                                                    ?>
                                                    <tr>
                                                        <td><?php echo $bfs[0]; ?></td>
                                                        <td><?php echo $bfs[1]; ?></td>
                                                        <td><?php echo $bfs[2]; ?></td>
                                                        <td><?php echo $bfs[3]; ?></td>
                                                        <td><?php echo $bfs[4]; ?></td>
                                                        <td><?php echo $bfs[5]; ?></td>
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
                    $('.dats').each( function(){
                        var id    = $(this).data('table');
                        var order = $(this).data('order');
                        var asdc  = $(this).data('asdc');
                        $('#'+id).dataTable({
                            "order": [[ order, "" + asdc + "" ]],
                            "bFilter" : false,
                            "bLengthChange": false,
                            "bPaginate": false,
                            "info": false,
                            "saveState": true
                        });
                    });
                    $('.datatable').dataTable({
                        "order": [[ 0, "desc" ]],
                        "bFilter" : false,
                        "bLengthChange": false,
                        "bPaginate": false,
                        "info": false,
                        "saveState": true
                    });
                    $('#service_items').DataTable( {
                        "order": [[ 4, "desc" ]],
                        "bFilter" : false,
                        "bLengthChange": false,
                        "bPaginate": false,
                        "info": false,
                        "saveState": true
                    } );
                    $('.redeem').click(function() {
                        var item = $(this).data('redeem');
                        $(this).closest('tr').remove();
                        $.ajax({
                            url: 'assets/app/update_settings.php?setting=redeem',
                            type: 'POST',
                            data: {
                                item: item
                            },
                            success: function(s){

                                toastr.info("<strong>Logan says:</strong><br/>Item ("+item+") has been redeemed.");
                            },
                            error: function(e){

                            }
                        });
                    });
                    $('.fire').click(function(f){
                        var fire = $(this).attr('data-fire');
                        var dpt  = $(this).attr('data-id');
                        var uuid = $(this).attr('data-uuid');
                        $.ajax({
                            url: 'assets/app/texting.php?txt='+fire,
                            type: 'POST',
                            data: {
                                dpt: dpt,
                                uuid: uuid
                            },
                            success: function(f){
                                toastr.success("<strong>Logan says:</strong><br/>Request was sent to the person who made the deposit. ");
                            },
                            error: function(f){
                                toastr.error("<strong>Logan says:</strong><br/>Oops.. that didnt work correctly.");
                            }
                        })
                    });
                    $('.deposit-amount').on('change', function() {
                        var id     = $(this).attr('data-id');
                        var amount = $(this).val();
                        $.ajax({
                            url: 'assets/app/update_settings.php?update=deposit_amount',
                            type: 'POST',
                            data: {
                                id: id,
                                amount: amount
                            },
                            success: function(s){
                                $('#'+id).html("Select $"+s+" for deposit");
                                $('#'+id).val(s.replace(/,/g, ''));
                                toastr.info("<strong>Logan says:</strong><br/>I updated that for you.");
                            },
                            error: function(e){

                            }
                        });
                    });
                    $('.deposit-reason').on('change', function() {
                        var id     = $(this).attr('data-id');
                        var reason = $(this).val();
                        $.ajax({
                            url: 'assets/app/update_settings.php?update=deposit_reason',
                            type: 'POST',
                            data: {
                                id: id,
                                reason: reason
                            },
                            success: function(s){

                                toastr.info("<strong>Logan says:</strong><br/>I updated that for you.");
                            },
                            error: function(e){

                            }
                        });
                    });
                    $('.cjr_apr').unbind('click').on('click', function() {
                        var ev = $(this).attr('data-ev');
                        $(this).closest('.panel-group').remove();
                        $.ajax({
                            url: 'assets/app/update_settings.php?update=event_fly',
                            type: 'POST',
                            data: {
                                name: 'event_cjr',
                                value: 1,
                                pk: ev
                            },
                            success: function(s){
                                toastr.success("<strong>Logan says:</strong><br/>Event has been approved & record of you doing so has been recorded on event's timeline.");
                            },
                            error: function(s){
                                toastr.error("<strong>Logan says:</strong><br/>Something didn't work properly. Try again? (I know you want too).");
                            }
                        });
                    });
                    var real_amt = 0;
                    var deposits = 0;
                    var payments = [];
                    $('.select-dpt').click(function() {
                        var thingy =   $(this).attr('data-id');
                        var id     =   $(this).attr('data-id');
                        var amt    =   $(this).val();
                        real_amt   += +$(this).val();
                        deposits++;
                        payments.push(id);

                        console.log(real_amt);

                        $(this).closest('td').append("<button class='btn btn-xs default red-stripe remove-dpt' data-id='"+id+"' value='"+amt+"'><i class='fa fa-times'></i> Unselect</button>");
                        $('.remove-dpt').unbind('click');
                        $('.remove-dpt').click(function(){
                            var pid = $(this).attr('data-id');
                            payments.splice(pid, 1);
                            real_amt -= $(this).val();
                            deposits--;
                            $(this).remove();
                            $('#'+pid).addClass('red-stripe');
                            $('#'+pid).removeClass('green-stripe');
                            $('#'+pid).removeAttr('disabled');
                            $('#'+pid).html('Select '+$(this).val()+' for deposit.');

                            $('.bulk-deposit-amount').html(real_amt.toFixed(2));
                            $('.bulk-deposit-count').html(deposits);
                            $('.bulk-deposit-submit').val(payments);

                            console.log(real_amt);

                            if(real_amt <= 0){
                                $('#start_b_dp').html('<h4 class="text-center">Please select an amount to be deposited for tools to appear here.</h4>');
                            }

                        });

                        $(this).html("<i class='fa fa-check'></i> Selected $"+$(this).val());
                        $(this).removeClass("red-stripe");
                        $(this).addClass("green-stripe");
                        $(this).prop('disabled', true);
                        if($('#bulk-deposit').length){
                            $('.bulk-deposit-amount').html(real_amt.toFixed(2));
                            $('.bulk-deposit-count').html(deposits);
                            $('.bulk-deposit-submit').val(payments);
                        } else {
                            $('#start_b_dp').html("");
                            $('#start_b_dp').append("<div id='bulk-deposit'> </div>");
                            $('#bulk-deposit').append("<h3 class='text-center'>Total amount selected: $<strong class='bulk-deposit-amount text-success'>"+real_amt.toFixed(2)+"</strong></h3>");
                            $('#bulk-deposit').append("<br/><button class='btn red btn-block btn-xl bulk-deposit-submit' value='"+payments+"'>Deposit $<strong class='bulk-deposit-amount'>"+real_amt.toFixed(2)+"</strong> total from <strong class='bulk-deposit-count'>"+deposits+"</strong> payments</button>")

                            $('.bulk-deposit-submit').click(function() {
                                $(this).prop('disabled', true);
                                var ids = $(this).val();
                                $.ajax({
                                    url: 'assets/app/add_setting.php?setting=deposit&luid=<?php echo $_GET['luid']; ?>&cuid=<?php echo $_SESSION['cuid']; ?>',
                                    type: 'POST',
                                    data: {
                                        d: '<?php echo struuid(true); ?>',
                                        ids: ids
                                    },
                                    success: function(s){
                                        $.ajax({
                                            url: 'assets/pages/sub/reports_master.php?luid=<?php echo $_GET['luid']; ?>&cuid=<?php echo $_SESSION['cuid']; ?>&show=exp_dp',
                                            type: 'POST',
                                            data: {
                                                type: 'sales',
                                                ext: "<?php echo date('Y-m-d', strtotime($range[0])); ?> - <?php echo date('Y-m-d', strtotime($range[1])); ?>"
                                            },
                                            success: function(data) {
                                                $(document).find('#reports-content').html(data);
                                            },
                                            error: function(e){

                                            }
                                        });
                                        toastr.info("<strong>Logan says:</strong><br/>I have added those payments to this locations deposits.");
                                    },
                                    error: function(e){

                                    }
                                });
                            });
                        }
                    });

                    $('.scroller').slimScroll({
                        height: 250,
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
                                $score     = mysql_query("SELECT event_id, event_by_user_token, event_booking, event_status, event_subtype FROM fmo_locations_events WHERE event_company_token='".mysql_real_escape_string($_SESSION['cuid'])."' AND (event_creation>='".mysql_real_escape_string($range[0])."' AND event_creation<='".mysql_real_escape_string($range[1])."')  AND NOT event_status=0 AND NOT event_status=9 ORDER BY event_date_start ASC");
                                if(mysql_num_rows($score) > 0){
                                    while($scr = mysql_fetch_assoc($score)){
                                        if($scr['event_subtype'] == 'Move'){
                                            $scorecard['people'][$scr['event_by_user_token']]['name']                       =  name($scr['event_by_user_token']);
                                            $scorecard['people'][$scr['event_by_user_token']]['token']                      =  $scr['event_by_user_token'];
                                            $scorecard['people'][$scr['event_by_user_token']]['events']                     += 1;
                                            $scorecard['people'][$scr['event_by_user_token']]['bookings']                   += $scr['event_booking'];
                                            $scorecard['people'][$scr['event_by_user_token']]['statuses'][$status]['count'] += 1;
                                        }
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
                                                    <th class="text-right">Event Count</th>
                                                    <th class="text-right">Booking Fee Count</th>
                                                    <th class="text-right">Booking Fee %</th>
                                                    <th class="text-right">Hours Worked</th>
                                                    <th class="text-right">Productivity</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <?php
                                                foreach($scorecard['people'] as $score){
                                                    $hours = 0;
                                                    $timeclock = mysql_query("SELECT timeclock_id, timeclock_hours FROM fmo_users_employee_timeclock WHERE timeclock_user='".mysql_real_escape_string($score['token'])."' AND (timeclock_clockout>='".mysql_real_escape_string($range[0])."' AND timeclock_clockout<='".mysql_real_escape_string($range[1])."') ORDER BY timeclock_timestamp DESC");
                                                    if(mysql_num_rows($timeclock) > 0){
                                                        while($tc = mysql_fetch_assoc($timeclock)){
                                                            $hours += $tc['timeclock_hours'];
                                                        }
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td class="bold"><?php echo $score['name']; ?></td>
                                                        <td class="text-right">
                                                            <strong class="text-success"><?php echo $score['events']; ?></strong>
                                                        </td>
                                                        <td class="bold text-right"><?php echo $score['bookings']; ?></td>
                                                        <td class="text-success bold text-right"><?php echo number_format(($score['bookings'] / $score['events']) * 100, 2); ?>%</td>
                                                        <td class="text-danger bold text-right"><?php echo number_format($hours, 2); ?>hrs</td>
                                                        <td class="text-success bold text-right"><?php echo number_format($score['bookings'] / $hours, 4); ?>%</td>
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
                        "order": [[ 5, "desc" ]],
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
    }
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>

