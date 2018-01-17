<?php
/**
 * Created by PhpStorm.
 * User: gameroom
 * Date: 6/10/2017
 * Time: 9:08 AM
 */
include '../app/init.php';

if(isset($_GET['t']) && $_GET['t'] == 'auth_tok'){
    $id   = $_GET['i'];
    $loan = mysql_query("SELECT advance_id, advance_user_token, advance_requested, advance_reason, advance_by_user_token FROM fmo_users_employee_advances WHERE advance_id='".mysql_real_escape_string($id)."'");
    if(mysql_num_rows($loan) > 0){
        $l = mysql_fetch_array($loan);
        $refStart                = new DateTime('2017-01-02');
        $periodLength            = 14;
        $now                     = new DateTime();
        $cur                     = date('Y-m-d');
        $daysIntoCurrentPeriod   = (int)$now->diff($refStart)->format('%a') % $periodLength;
        $currentPeriodStart      = clone $now;
        $currentPeriodStart->sub(new DateInterval('P'.$daysIntoCurrentPeriod.'D'));
        $start                   = date("Y-m-d", strtotime($cur." -".$daysIntoCurrentPeriod." days"));
        $end                     = date('Y-m-d', strtotime($start." +14 days"));
        ?>
        <body onload="window.print()">
        <center>
            <h3 style="text-decoration: underline">AUTHORIZATION FOR VOLUNTARY PAYROLL DEDUCTION</h3>
        </center> <br/><br/>
        I, <strong><?php echo name($l['advance_user_token']); ?></strong>, hereby authorize to deduct from my wages for the sum of <strong>$<?php echo number_format($l['advance_requested'], 2); ?></strong>, from the pay period starting: <strong><?php echo $start; ?></strong> until the full amount plus 10% loan fee of <strong>$<?php echo number_format($l['advance_requested'] * .10, 2); ?></strong> has been deducted. In the event that my employment ends for any reason before the final deduction is made, the entire remaining balance <strong>MAY (X)</strong> or <strong>MAY NOT (X)</strong> be deducted from my final wages. I understand that my authorization may be revoked at any time.
        <br/><br/>
        Notes: <strong><?php echo $l['advance_reason']; ?></strong>
        <br/><br/><br/>
        I understand that this pay advance is subject to the following terms:
        <br/><br/>
        &nbsp; &nbsp; &nbsp; &nbsp;  1.) <strong>The total amount will be deducated from my paycheck for the pay period we are currently in, to the extent permitted by applicable law.</strong> <br/>
        &nbsp; &nbsp; &nbsp; &nbsp;  2.) <strong>The amount requested here will be available 1 day after signing this request form.</strong> <br/>
        <br/><br/><br/>

        X _________________________________ Date: ______________ Loan Ticket Number: <strong><?php echo $l['advance_id']; ?></strong><br/>
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ( <?php echo name($l['advance_user_token']); ?> ) <br/>
        <br/><br/>
        X _________________________________ Date: ______________ Check Number Issued: <br/>
        &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; ( <?php echo name($l['advance_by_user_token']); ?> ) <br/>
        </body>
        <?php
    }
}