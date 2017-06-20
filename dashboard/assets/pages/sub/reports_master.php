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
                        <span class="caption-subject font-red bold uppercase">PAYROLL</span> <span class="font-red">|</span> <button class="btn btn-xs red-stripe"><i class="fa fa-print"></i> Print current tab</button>
                    </div>
                    <ul class="nav nav-tabs">
                        <li class="">
                            <a href="#timesheets" data-toggle="tab" aria-expanded="false" style="color: black;">
                                Time Sheets</a>
                        </li>
                        <li class="">
                            <a href="#deductions" data-toggle="tab" aria-expanded="false" style="color: black;">
                                Deductions</a>
                        </li>
                        <?php
                        if($_SESSION['group'] == 1){
                            ?>
                            <li class="active">
                                <a href="#ownersummary" data-toggle="tab" aria-expanded="true" style="color: black;">
                                    Owner Summary</a>
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
                        if($_SESSION['group'] == 1){
                            ?>
                            <div class="tab-pane active" id="ownersummary">
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
                                        $os['users'][$employee['user_token']]['info'] = array(
                                            ''.nameByLast($employee['user_token']).'',
                                            ''.locationName2($employee['user_last_ext_location']).'',
                                            ''.$employee['user_id'].'',
                                            ''.$employee['user_token'].'',
                                        );
                                        $laborers  = mysql_query("SELECT laborer_user_token, laborer_event_token, laborer_rate, laborer_hours_worked, laborer_tip FROM fmo_locations_events_laborers WHERE laborer_user_token='".mysql_real_escape_string($employee['user_token'])."'");
                                        if(mysql_num_rows($laborers) > 0){
                                            $pay = 0; $gross = 0;
                                            while($labor = mysql_fetch_assoc($laborers)){
                                                $events = mysql_query("SELECT event_date_start, event_name, event_id FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($labor['laborer_event_token'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<'".mysql_real_escape_string($range[1])."')");
                                                if(mysql_num_rows($events) > 0){
                                                    while($event = mysql_fetch_assoc($events)){
                                                        $pay   =  ($labor['laborer_rate'] * $labor['laborer_hours_worked']) + $labor['laborer_tip'];
                                                        $gross += $pay;
                                                        $os['users'][$employee['user_token']]['events'][] = array(
                                                            ''.date('m-d-Y', strtotime($event['event_date_start'])).'',
                                                            ''.$event['event_name'].' - <strong>BOL #:</strong> '.$event['event_id'].'',
                                                            ''.$labor['laborer_hours_worked'].'hrs',
                                                            '$'.$labor['laborer_rate'].'/hr',
                                                            '$'.number_format($pay - $labor['laborer_tip']).'',
                                                            '$'.number_format($labor['laborer_tip']).''
                                                        );
                                                    }
                                                } else {
                                                    continue;
                                                }
                                            }
                                        }
                                        $hrs = 0;
                                        $timeclock = mysql_query("SELECT timeclock_id, timeclock_clockin, timeclock_clockout, timeclock_hours, timeclock_timestamp FROM fmo_users_employee_timeclock WHERE timeclock_user='".mysql_real_escape_string($employee['user_token'])."' AND (timeclock_clockout>='".mysql_real_escape_string($range[0])."' AND timeclock_clockout<'".mysql_real_escape_string($range[1])."') ORDER BY timeclock_timestamp DESC");
                                        if(mysql_num_rows($timeclock) > 0){
                                            while($tc = mysql_fetch_assoc($timeclock)){
                                                $os['users'][$employee['user_token']]['clock'][] = array(
                                                    'Timeclock/Office',
                                                    ''.date('m-d-Y', strtotime($tc['timeclock_timestamp'])).'',
                                                    ''.$tc['timeclock_hours'].'hrs',
                                                    ''.$employee['user_employer_rate'].'',
                                                    '$'.number_format($tc['timeclock_hours'] * $employee['user_employer_rate'], 2).'',
                                                );
                                            }
                                        }
                                        $os['users'][$employee['user_token']]['gross'] = array(
                                            ''.number_format($gross, 2).''
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
                                            <table class="table table-striped table-hover">
                                                <thead>
                                                <tr>
                                                    <th width="8%">
                                                        Date
                                                    </th>
                                                    <th>
                                                        Worked On/In
                                                    </th>
                                                    <th width="8%" class="text-right">
                                                        Hours
                                                    </th>
                                                    <th width="8%" class="text-right">
                                                        Rate
                                                    </th>
                                                    <th width="8%" class="text-right">
                                                        <strong>Pay</strong>
                                                    </th>
                                                    <th width="8%" class="text-right">
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
                                                        </tr>
                                                        <?php
                                                    }
                                                    ?>
                                                </tbody>
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
        <?php
    }
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>

