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
    if($_POST['type'] == 'pryl'){
        $range = explode(" - ", $_POST['ext']);
        ?>
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title tabbable-line">
                    <div class="caption caption-md">
                        <i class="fa fa-bar-chart-o theme-font bold"></i>
                        <span class="caption-subject font-red bold uppercase">PAYROLL</span> <span class="font-red">|</span> <button class="btn btn-xs red-stripe print" data-print="<?php if($_SESSION['group'] == 1){echo "#payrollsummary_admin";}else{echo"#payrollsummary";} ?>"><i class="fa fa-print"></i> Print</button>
                    </div>
                    <ul class="nav nav-tabs">
                        <li class="">
                            <a href="#timesheets" data-toggle="tab" aria-expanded="false" style="color: black;" class="tab_print" data-print="#timesheets">
                                Time Sheets</a>
                        </li>
                        <li class="">
                            <a href="#deductions" data-toggle="tab" aria-expanded="false" style="color: black;" class="tab_print" data-print="#deductions">
                                Deductions</a>
                        </li>
                        <?php
                        if($_SESSION['group'] <= 2){
                            ?>
                            <li <?php if($_SESSION['group'] != 1){echo "class='active'";} ?>>
                                <a href="#payrollsummary" data-toggle="tab" aria-expanded="true" style="color: black;" class="tab_print" data-print="#payrollsummary">
                                    Payroll Summary</a>
                            </li>
                            <?php
                        }
                        if($_SESSION['group'] == 1){
                            ?>
                            <li class="active">
                                <a href="#payrollsummary_admin" data-toggle="tab" aria-expanded="true" style="color: black;" class="tab_print" data-print="#payrollsummary_admin">
                                    Payroll Summary (Admin)</a>
                            </li>
                            <?php
                        }
                        ?>

                    </ul>
                </div>
                <div class="portlet-body">
                    <div class="tab-content">
                        <div class="tab-pane" id="timesheets">

                        </div>
                        <div class="tab-pane" id="deductions">

                        </div>
                        <?php
                        if($_SESSION['group'] <= 2){
                            ?>
                            <div class="tab-pane <?php if($_SESSION['group'] != 1){echo "active";} ?>" id="payrollsummary">
                                <center>
                                    <h3>
                                        <i class="fa fa-bar-chart-o"></i> Payroll Summary | <strong><?php echo date('m-d-Y', strtotime($range[0])); ?></strong> - <strong><?php echo date('m-d-Y', strtotime($range[1])); ?></strong>
                                    </h3><br/>
                                </center>
                                <?php
                                /*
                                 *
                                 *  SUMMARY REPORT
                                 *
                                 */

                                $os = array(); $key = 0;
                                $employees = mysql_query("SELECT user_token, user_id, user_employer_rate FROM fmo_users WHERE user_employer='".mysql_real_escape_string($_GET['cuid'])."' AND user_last_ext_location='".mysql_real_escape_string($_GET['luid'])."' ORDER BY user_lname ASC");
                                if(mysql_num_rows($employees) > 0){
                                    while($employee = mysql_fetch_assoc($employees)){
                                        $gross = 0;
                                        $hours = 0;
                                        $advs  = 0;
                                        $other = 0;
                                        $os['users'][$employee['user_token']]['info'] = array(
                                            ''.nameByLast($employee['user_token']).'',
                                            ''.locationName2($_GET['luid']).'',
                                            ''.$employee['user_id'].'',
                                            ''.$employee['user_token'].'',
                                        );
                                        $laborers  = mysql_query("SELECT laborer_user_token, laborer_event_token, laborer_rate, laborer_hours_worked, laborer_tip FROM fmo_locations_events_laborers WHERE laborer_user_token='".mysql_real_escape_string($employee['user_token'])."'");
                                        if(mysql_num_rows($laborers) > 0){
                                            while($labor = mysql_fetch_assoc($laborers)){
                                                $events = mysql_query("SELECT event_date_start, event_name, event_id FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($labor['laborer_event_token'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<'".mysql_real_escape_string($range[1])."')");
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
                                                } else {
                                                    continue;
                                                }
                                            }
                                        }
                                        $timeclock = mysql_query("SELECT timeclock_id, timeclock_clockin, timeclock_clockout, timeclock_hours, timeclock_timestamp FROM fmo_users_employee_timeclock WHERE timeclock_user='".mysql_real_escape_string($employee['user_token'])."' AND (timeclock_clockout>='".mysql_real_escape_string($range[0])."' AND timeclock_clockout<'".mysql_real_escape_string($range[1])."') ORDER BY timeclock_timestamp DESC");
                                        if(mysql_num_rows($timeclock) > 0){
                                            while($tc = mysql_fetch_assoc($timeclock)){
                                                $gross += $tc['timeclock_hours'] * $employee['user_employer_rate'];
                                                $hours += $tc['timeclock_hours'];
                                                $os['users'][$employee['user_token']]['clock'][] = array(
                                                    '<strong>TIMECLOCK: </strong>Used clock in/out to track hours',
                                                    ''.date('m-d-Y', strtotime($tc['timeclock_timestamp'])).'',
                                                    ''.$tc['timeclock_hours'].'hrs',
                                                    '$'.$employee['user_employer_rate'].'/hr',
                                                    '$'.number_format($tc['timeclock_hours'] * $employee['user_employer_rate'], 2).'',
                                                );
                                            }
                                        }
                                        $loans = mysql_query("SELECT advance_requested, advance_timestamp, advance_reason FROM fmo_users_employee_advances WHERE (advance_timestamp>='".mysql_real_escape_string($range[0])."' AND advance_timestamp<'".mysql_real_escape_string($range[1])."') AND advance_user_token='".mysql_real_escape_string($employee['user_token'])."'");
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
                                        if(mysql_num_rows($laborers) <= 0 && mysql_num_rows($timeclock) <= 0){
                                            unset($os['users'][$employee['user_token']]);
                                            continue;
                                        }
                                    }
                                }
                                foreach($os['users'] as $user){
                                    ?>
                                    <div class="portlet">
                                        <div class="portlet-title" style="margin-bottom: 0px;">
                                            <div class="caption">
                                                <small><strong><?php echo $user['info'][0]; ?></strong> | Employee #: <strong><?php echo $user['info'][2]; ?></strong> | <?php echo $user['info'][1]; ?></small>
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
                            </div>
                            <?php
                        }
                        if($_SESSION['group'] == 1){
                            ?>
                            <div class="tab-pane active" id="payrollsummary_admin">
                                <center>
                                    <h3>
                                        <i class="fa fa-bar-chart-o"></i> Payroll Summary (Admin) | <strong><?php echo date('m-d-Y', strtotime($range[0])); ?></strong> - <strong><?php echo date('m-d-Y', strtotime($range[1])); ?></strong>
                                    </h3> <br/>
                                </center>
                                <?php
                                /*
                                 *
                                 *  OWNER SUMMARY REPORT
                                 *
                                 */

                                $os = array(); $key = 0;
                                $employees = mysql_query("SELECT user_token, user_id, user_last_ext_location, user_employer_rate FROM fmo_users WHERE user_employer='".mysql_real_escape_string($_GET['cuid'])."' ORDER BY user_lname ASC");
                                if(mysql_num_rows($employees) > 0){
                                    while($employee = mysql_fetch_assoc($employees)){
                                        $gross = 0;
                                        $hours = 0;
                                        $advs  = 0;
                                        $other = 0;
                                        $os['users'][$employee['user_token']]['info'] = array(
                                            ''.nameByLast($employee['user_token']).'',
                                            ''.locationName2($employee['user_last_ext_location']).'',
                                            ''.$employee['user_id'].'',
                                            ''.$employee['user_token'].'',
                                        );
                                        $laborers  = mysql_query("SELECT laborer_user_token, laborer_event_token, laborer_rate, laborer_hours_worked, laborer_tip FROM fmo_locations_events_laborers WHERE laborer_user_token='".mysql_real_escape_string($employee['user_token'])."'");
                                        if(mysql_num_rows($laborers) > 0){
                                            while($labor = mysql_fetch_assoc($laborers)){
                                                $events = mysql_query("SELECT event_date_start, event_name, event_id FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($labor['laborer_event_token'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<'".mysql_real_escape_string($range[1])."')");
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
                                                } else {
                                                    continue;
                                                }
                                            }
                                        }
                                        $timeclock = mysql_query("SELECT timeclock_id, timeclock_clockin, timeclock_clockout, timeclock_hours, timeclock_timestamp FROM fmo_users_employee_timeclock WHERE timeclock_user='".mysql_real_escape_string($employee['user_token'])."' AND (timeclock_clockout>='".mysql_real_escape_string($range[0])."' AND timeclock_clockout<'".mysql_real_escape_string($range[1])."') ORDER BY timeclock_timestamp DESC");
                                        if(mysql_num_rows($timeclock) > 0){
                                            while($tc = mysql_fetch_assoc($timeclock)){
                                                $gross += $tc['timeclock_hours'] * $employee['user_employer_rate'];
                                                $hours += $tc['timeclock_hours'];
                                                $os['users'][$employee['user_token']]['clock'][] = array(
                                                    '<strong>TIMECLOCK: </strong>Used clock in/out to track hours',
                                                    ''.date('m-d-Y', strtotime($tc['timeclock_timestamp'])).'',
                                                    ''.$tc['timeclock_hours'].'hrs',
                                                    '$'.$employee['user_employer_rate'].'/hr',
                                                    '$'.number_format($tc['timeclock_hours'] * $employee['user_employer_rate'], 2).'',
                                                );
                                            }
                                        }
                                        $loans = mysql_query("SELECT advance_requested, advance_timestamp, advance_reason FROM fmo_users_employee_advances WHERE (advance_timestamp>='".mysql_real_escape_string($range[0])."' AND advance_timestamp<'".mysql_real_escape_string($range[1])."') AND advance_user_token='".mysql_real_escape_string($employee['user_token'])."'");
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
                                        if(mysql_num_rows($laborers) <= 0 && mysql_num_rows($timeclock) <= 0){
                                            unset($os['users'][$employee['user_token']]);
                                            continue;
                                        }
                                    }
                                }
                                foreach($os['users'] as $user){
                                    ?>
                                    <div class="portlet">
                                        <div class="portlet-title" style="margin-bottom: 0px;">
                                            <div class="caption">
                                                <small><strong><?php echo $user['info'][0]; ?></strong> | Employee #: <strong><?php echo $user['info'][2]; ?></strong> | <?php echo $user['info'][1]; ?></small>
                                            </div>
                                        </div>
                                        <div class="portlet-body" style="padding-top: 0px;">
                                            <table class="table table-striped table-hover datatable">
                                                <thead>
                                                <tr>
                                                    <th width="8%">
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
                                                    <th style="padding-right: 8px;" width="8%" class="text-right">
                                                        Hours
                                                    </th>
                                                    <th style="padding-right: 8px;" width="8%" class="text-right">
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
    } elseif ($_POST['type'] == 'sales'){
        $range = explode(" - ", $_POST['ext']);
        ?>
        <div class="col-md-12">
            <div class="portlet">
                <div class="portlet-title tabbable-line">
                    <div class="caption caption-md">
                        <i class="fa fa-bar-chart-o theme-font bold"></i>
                        <span class="caption-subject font-red bold uppercase">SALES</span> <span class="font-red">|</span> <button class="btn btn-xs red-stripe print" data-print="#redemption"><i class="fa fa-print"></i> Print</button>
                    </div>
                    <ul class="nav nav-tabs">
                        <li class="">
                            <a href="#bookingfees" data-toggle="tab" aria-expanded="false" style="color: black;" class="tab_print" data-print="#bookingfees">
                                Booking Fees</a>
                        </li>
                        <li class="">
                            <a href="#sales" data-toggle="tab" aria-expanded="false" style="color: black;" class="tab_print" data-print="#sales">
                                Sales</a>
                        </li>
                        <li>
                            <a href="#taxes" data-toggle="tab" aria-expanded="true" style="color: black;" class="tab_print" data-print="#taxes">
                                Taxes</a>
                        </li>
                        <li class="active">
                            <a href="#redemption" data-toggle="tab" aria-expanded="true" style="color: black;" class="tab_print" data-print="#redemption">
                                Redemptions</a>
                        </li>
                    </ul>
                </div>
                <div class="portlet-body">
                    <div class="tab-content">
                        <div class="tab-pane" id="bookingfees">

                        </div>
                        <div class="tab-pane" id="sales">

                        </div>
                        <div class="tab-pane" id="taxes">

                        </div>
                        <div class="tab-pane active" id="redemption">
                            <?php
                            /*
                             *  ITEMS THAT NEED TO BE REDEEMED
                             */
                            $redeem = array();
                            $events = mysql_query("SELECT event_token, event_user_token FROM fmo_locations_events WHERE event_company_token='".mysql_real_escape_string($_SESSION['cuid'])."'");
                            if(mysql_num_rows($events) > 0){
                                while($event = mysql_fetch_assoc($events)){
                                    $items = mysql_query("SELECT item_item, item_desc FROM fmo_locations_events_items WHERE item_event_token='".mysql_real_escape_string($event['event_token'])."' AND item_redeemable=1");
                                    if(mysql_num_rows($items) > 0){
                                        while($item = mysql_fetch_assoc($items)){
                                            $redeem['event'][$event['event_token']]['items'][] = array(
                                                ''.nameFromEvent($event['event_user_token']).'',
                                                ''.$item['item_item'].'',
                                                ''.$item['item_desc'].''
                                            );
                                        }
                                    }
                                }
                            }

                            ?>
                            <div class="portlet">
                                <div class="portlet-body">
                                    <div class="task-content">
                                        <!-- START TASK LIST -->
                                        <ul class="task-list">
                                            <?php
                                            foreach($redeem['items'] as $event){
                                                ?>
                                                <li>
                                                    <div class="task-checkbox">
                                                        <input type="checkbox" class="liChild" value=""/>
                                                    </div>
                                                    <div class="task-title">
                                                    <span class="task-title-sp">
                                                        <?php echo $event[1]; ?> </span>
                                                        <span class="label label-sm label-success">
                                                        <?php echo $event[0]; ?></span>
                                                        <span class="task-bell">
                                                        <i class="fa fa-bell-o"></i></span>
                                                    </div>
                                                    <div class="task-config">
                                                        <div class="task-config-btn btn-group">
                                                            <a class="btn btn-xs default" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                                                <i class="fa fa-cog"></i><i class="fa fa-angle-down"></i>
                                                            </a>
                                                            <ul class="dropdown-menu pull-right">
                                                                <li>
                                                                    <a href="javascript:;">
                                                                        <i class="fa fa-check"></i> Complete </a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;">
                                                                        <i class="fa fa-pencil"></i> Edit </a>
                                                                </li>
                                                                <li>
                                                                    <a href="javascript:;">
                                                                        <i class="fa fa-trash-o"></i> Cancel </a>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </li>
                                                <?php
                                            }
                                            ?>
                                        </ul>
                                    </div>
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
        <script>
            $(document).ready(function(e){
                $('.datatable').dataTable({
                    "order": [[ 0, "asc" ]],
                    "bFilter" : false,
                    "bLengthChange": false,
                    "bPaginate": false,
                    "info": false
                });
                $('.task-list input[type="checkbox"]').change(function() {
                    if ($(this).is(':checked')) {
                        $(this).parents('li').addClass("task-done");
                    } else {
                        $(this).parents('li').removeClass("task-done");
                    }
                });
            });
        </script>
        <?php
    }
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>

