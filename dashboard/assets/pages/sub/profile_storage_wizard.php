<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/3/2017
 * Time: 3:52 AM
 */
session_start();
include '../../app/init.php';

if(isset($_SESSION['logged'])){
$profile  = mysql_fetch_array(mysql_query("SELECT user_fname, user_lname, user_email, user_phone, user_token, user_employer_dln, user_address, user_city, user_state, user_zip FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'"));
$storage  = mysql_fetch_array(mysql_query("SELECT storage_unit_name, storage_location_token, storage_unit_lwh, storage_unit_desc, storage_price, storage_period, storage_occupant, storage_contract_token, storage_last_occupied, storage_status, storage_type_id FROM fmo_locations_storages WHERE storage_token='".mysql_real_escape_string($_GET['su'])."'"));
$types    = mysql_fetch_array(mysql_query("SELECT type_floor, type_desc, type_climate FROM fmo_locations_storages_types WHERE type_id='".mysql_real_escape_string($storage['storage_type_id'])."'"));
$location = mysql_fetch_array(mysql_query("SELECT location_storage_stripe_public, location_storage_days_late, location_storage_days_auction, location_storage_tax, location_storage_deposit, location_creditcard_fee, location_nickname, location_storage_late_fee, location_storage_auction_fee FROM fmo_locations WHERE location_token='".mysql_real_escape_string($storage['storage_location_token'])."'"));
$uuidperm = mysql_fetch_array(mysql_query("SELECT user_esc_permissions FROM fmo_users WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'"));

$n       = struuid(true);
?>
<div class="row">
    <div class="col-md-12">
        <div class="portlet light" id="form_wizard_1">
            <div class="portlet-body form">
                <?php
                if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_storage_create_contracts") !== false){
                    ?>
                    <form action="#" id="submit_form" method="POST" editable-form name="textBtnForm">
                        <div class="form-body">
                            <ul class="nav nav-pills nav-justified steps hidden">
                                <li>
                                    <a href="#tab1" data-toggle="tab" class="step">
                                        <span class="number"> 1 </span>
                                        <span class="desc"><i class="fa fa-check"></i> Customer Details </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#tab3" data-toggle="tab" class="step">
                                        <span class="number">2 </span>
                                        <span class="desc"><i class="fa fa-check"></i> Contract Details </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#tab4" data-toggle="tab" class="step">
                                        <span class="number">3 </span>
                                        <span class="desc"><i class="fa fa-check"></i> Contract Auth. </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#tab5" data-toggle="tab" class="step">
                                        <span class="number">4 </span>
                                        <span class="desc"><i class="fa fa-check"></i> Payment </span>
                                    </a>
                                </li>
                            </ul>
                            <div id="bar" class="progress progress-striped" role="progressbar">
                                <div class="progress-bar progress-bar-success">
                                </div>
                            </div>
                            <div class="tab-content">
                                <div class="alert alert-danger display-none">
                                    <button class="close" data-dismiss="alert"></button>
                                    You have some form errors. Please check below.
                                </div>

                                <div class="tab-pane" id="tab1">

                                    <div class="row">
                                        <div class="col-md-12">
                                            <h3 style="margin-top: 0;"><strong class="font-red"><?php echo $storage['storage_unit_lwh']; ?> - Unit #: <?php echo $storage['storage_unit_name']; ?></strong> (Floor <?php echo $types['type_floor'].", ".$types['type_desc']." [Climate: ".$types['type_climate']."]"; ?>) <strong>move-in</strong> for <strong class="font-blue"><?php echo sentence_case($profile['user_fname']); ?></strong></h3>
                                            <h2><strong class="text-success">$<span class="fake-rent"><?php echo $storage['storage_price']."</span></strong>/<span class='prorate'>".$storage['storage_period']; ?></span> <small>+ deposit of <strong class="text-success bold fake-deposit">$<?php echo number_format($location['location_storage_deposit'], 2); ?></strong> today.</small></h2>
                                            <small><strong>Based on our records:</strong>
                                                <br/> The customer is: <strong><?php echo name($_GET['uuid']) ?></strong>
                                                <br/> Their email and phone is: <strong><?php if(!empty($profile['user_email'])){ echo $profile['user_email']; } else { echo "N/A"; }  ?></strong> | <strong><?php if(!empty($profile['user_phone'])){ echo clean_phone($profile['user_phone']); } else { echo "N/A";} ?></strong>
                                                <br/> They're requesting: <strong><?php echo $storage['storage_unit_lwh']; ?> - Unit #: <?php echo $storage['storage_unit_name']; ?> (Floor <?php echo $types['type_floor'].", ".$types['type_desc']." [Climate: ".$types['type_climate']."]"; ?>) </strong>
                                                <br/>
                                                <br/> <br/> Below, we need the initial information required to begin a contract with storage. Please input all necessary information to the best of your knowledge. All fields marked with a <strong>red star</strong> ( <span class="text-danger">*</span> ) are <strong>required</strong>!</small>
                                            <h4>Please enter personal customer details: </h4>
                                            <hr/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-4">
                                            <label class="control-label">Customer Phone Number <span class="font-red">*</span></label>
                                            <div class="input-icon">
                                                <i class="fa fa-phone"></i>
                                                <input class="form-control placeholder-no-fix" id="phone" type="text" autocomplete="off" placeholder="Phone Number" name="phone" value="<?php if(!empty($profile['user_phone'])){echo clean_phone($profile['user_phone']);} ?>"/>
                                                <span class="help-block">This should be the customer's phone number.</span>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="control-label">Customer Email Address</label>
                                            <div class="input-icon">
                                                <i class="fa fa-envelope"></i>
                                                <input class="form-control placeholder-no-fix" type="email" autocomplete="off" placeholder="Email Address" name="email" value="<?php if(!empty($profile['user_email'])){echo $profile['user_email'];} ?>"/>
                                                <span class="help-block">This should be the customer's email address.</span>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-4">
                                            <label class="control-label">State ID/Drivers License #</label>
                                            <div class="input-icon">
                                                <i class="fa fa-picture-o"></i>
                                                <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="State ID/Drivers License #" name="dln" value="<?php if(!empty($profile['user_employer_dln'])){echo $profile['user_employer_dln'];} ?>"/>
                                                <span class="help-block"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-5">
                                            <label class="control-label">Customer's street address <span class="font-red">*</span></label>
                                            <div class="input-icon">
                                                <i class="fa fa-building"></i>
                                                <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Street address.." name="address" value="<?php echo $profile['user_address']; ?>"/>
                                                <span class="help-block">This should be the customers <strong>permanent address</strong>.</span>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label class="control-label">Zip code <span class="font-red">*</span></label>
                                            <div class="">
                                                <input class="form-control placeholder-no-fix" type="number" autocomplete="off" placeholder="Zip code.." name="zip" value="<?php echo $profile['user_zip']; ?>"/>
                                                <span class="help-block">i.e 46219</span>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="control-label">City <span class="font-red">*</span></label>
                                            <div class="">
                                                <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="City.." name="city" value="<?php echo $profile['user_city']; ?>"/>
                                                <span class="help-block">i.e Indianapolis</span>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-2">
                                            <label class="control-label">State <span class="font-red">*</span></label>
                                            <div class="">
                                                <select class="form-control state" name="state" id="state">
                                                    <option disabled selected value="">State</option>
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
                                                <span class="help-block">i.e. IN</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-3">
                                            <label class="control-label">Alternate contact</label>
                                            <div class="input-icon">
                                                <i class="fa fa-users"></i>
                                                <input class="form-control placeholder-no-fix alt-contact" type="text" autocomplete="off" placeholder="Full contact name.." />
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="control-label">Address <strong>/</strong> Relationship</label>
                                            <div class="input-icon">
                                                <i class="fa fa-home"></i>
                                                <input class="form-control placeholder-no-fix alt-contact-notes" type="text" autocomplete="off" placeholder="Contact address.."/>
                                            </div>
                                        </div>
                                        <div class="form-group col-md-3">
                                            <label class="control-label">& their phone number</label>
                                            <div class="input-icon">
                                                <i class="fa fa-phone"></i>
                                                <input class="form-control placeholder-no-fix alt-contact-phone" id="phone2" type="text" autocomplete="off" placeholder="Contact phone.."/>
                                            </div>
                                        </div>
                                        <div class="col-md-3"><a class="add-alt"><i style="font-size: 30px; margin-top: 35px;" class="fa fa-plus text-danger"></i></a></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="alert alert-danger alt-error" style="display: none;">You need both a name & number.</div>
                                            <div class="alert alert-danger alt-error2" style="display: none;">You can't have more than 3 alternate contacts.</div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Authorized Contacts:</h5>
                                            <div id="alts" class="has-none">

                                            </div>
                                            <span class="current" data-row="1"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab3">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h3 style="margin-top: 0;"><strong class="font-red"><?php echo $storage['storage_unit_lwh']; ?> - Unit #: <?php echo $storage['storage_unit_name']; ?></strong> (Floor <?php echo $types['type_floor'].", ".$types['type_desc']." [Climate: ".$types['type_climate']."]"; ?>) <strong>move-in</strong> for <strong class="font-blue"><?php echo sentence_case($profile['user_fname']); ?></strong></h3>
                                            <h2><strong class="text-success">$<span class="fake-rent"><?php echo $storage['storage_price']."</span></strong>/<span class='prorate'>".$storage['storage_period']; ?></span><small>+ deposit of <strong class="text-success bold fake-deposit">$<?php echo number_format($location['location_storage_deposit'], 2); ?></strong> today.</small></h2>

                                            <h5>Great. Now we need to make sure the details of the storage contract are correct. Please verify below a few bits of information before proceeding.</h5>

                                            <h4>Please confirm & adjust contract details as needed: </h4>
                                            <hr/>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-4">
                                            <h5><strong>Move-in</strong> Details</h5>

                                            <div class="form-group">
                                                <label class="control-label">Move-in/Next due dates <span class="font-red">*</span></label>
                                                <div class="input-icon">
                                                    <div class="input-group input-md datepicker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy" style="margin-top: -4px; width: 100% !important;">
                                                        <input type="text" class="form-control dates d1" name="startdate" value="<?php echo date("m/d/Y"); ?>">
                                                        <span class="input-group-addon"> <i class="fa fa-arrow-circle-right"></i> </span>
                                                        <input type="text" class="form-control dates d2" name="nextDue" value="<?php if($storage['storage_period'] == 'Weekly'){$d = "+ 1 week";} else {$d = "+ 1 month";} echo date("m/d/Y", strtotime('today '.$d)); ?>">
                                                    </div>
                                                    <strong class="help-block"><i class="fa fa-arrow-up faa-vertical animated"></i> <span class="font-xs">MOVE-IN DATE</span> <span class="pull-right font-xs">NEXT DUE DATE <i class="fa fa-arrow-up faa-vertical animated"></i></span></strong>
                                                </div>
                                            </div>
                                        </div>
                                        <?php
                                        if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_storage_create_contracts_adj") !== false){
                                             ?>
                                            <div class="col-md-2">
                                                <h5><strong>Ongoing</strong> Fees/Adjustments</h5>
                                                <div class="form-group">
                                                    <label class="control-label">Rent adjustment amount <span class="font-red">*</span></label>
                                                    <div class="input-icon">
                                                        <i class="fa fa-area-chart"></i>
                                                        <input class="form-control placeholder-no-fix rent-adj" type="number" step="any" autocomplete="off" placeholder="Rent adjustment.." name="rate_adj" value="0.00"/>
                                                        <span class="help-block"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_storage_create_contracts_dpt") !== false){
                                            ?>
                                            <div class="col-md-2">
                                                <h5><strong>One-time</strong> Fees/Discounts</h5>
                                                <div class="form-group">
                                                    <label class="control-label">Deposit amount</label>
                                                    <div class="input-icon">
                                                        <i class="fa fa-usd"></i>
                                                        <input class="form-control placeholder-no-fix deposit" type="number" step="any" autocomplete="off" placeholder="Rate adjustment.." name="deposit" value="<?php echo number_format($location['location_storage_deposit'], 2); ?>"/>
                                                        <span class="help-block"></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php
                                        }
                                        ?>

                                        <div class="col-md-4">
                                            <h5><strong>Access</strong> options</h5>
                                            <div class="form-group">
                                                <label class="control-label">Gate code</label>
                                                <div class="input-icon">
                                                    <i class="fa fa-power-off"></i>
                                                    <input class="form-control placeholder-no-fix" type="number" step="any" autocomplete="off" placeholder="Gate code.." name="gate" value="<?php echo rand(1000, 9999); ?>"/>
                                                    <span class="help-block"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">

                                        <div class="col-md-12">
                                            <h5><strong>Automatic payment</strong> tools</h5>

                                            <div class="form-group">
                                                <label class="control-label"><strong>mPay</strong> mobile <span class="font-blue">*</span></label>
                                                <div class="input-icon">
                                                    <div class="input-group input-md datepicker input-daterange" data-date="10/11/2012" data-date-format="mm/dd/yyyy" style="margin-top: -4px; width: 100% !important;">
                                                        <label>
                                                            <input type="checkbox" class="icheck" value="1" name="auto"> Sign up for <strong>mPay&trade;</strong>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12">
                                            <h5><strong><span class="text-danger">*</span> Pro-rating:</strong> Changing the date will pro-rate the rent. </h5>
                                            <h5><strong><span class="font-blue">*</span> mPay:</strong> This will automatically charge the customer if they choose to use a credit card.</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab4">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h3 style="margin-top: 0;"><strong class="font-red"><?php echo $storage['storage_unit_lwh']; ?> - Unit #: <?php echo $storage['storage_unit_name']; ?></strong> (Floor <?php echo $types['type_floor'].", ".$types['type_desc']." [Climate: ".$types['type_climate']."]"; ?>) <strong>move-in</strong> for <strong class="font-blue"><?php echo sentence_case($profile['user_fname']); ?></strong></h3>
                                            <h2><strong class="text-success">$<span class="fake-rent"><?php echo $storage['storage_price']."</span></strong>/<span class='prorate'>".$storage['storage_period']; ?></span> <small>+ deposit of <strong class="text-success bold fake-deposit">$<?php echo number_format($location['location_storage_deposit'], 2); ?></strong> today.</small></h2>

                                            <h5>Nice, we've made it to the final steps. Now we just need to print the contract--get it signed, and uploaded back into the system. After you re-upload the contract, you'll be able to take a payment. Once that is complete, a record of the storage unit contract will be availabble <strong>in the users profile.</strong></h5>

                                            <h4 class="bold">Please print the following contract, then scan it back in to continue. <button type="button" class="btn default red-stripe pull-right print" data-print="#contrract"><i class="fa fa-print"></i> Print</button></h4>
                                            <hr/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12" id="contrract">
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
                                                            <span id="contract_name"></span> <br/>
                                                            <span id="contract_address"></span> <br/>
                                                            <span id="contract_csz"></span> <br/>
                                                        </td>
                                                        <td colspan="2">
                                                            <i class="fa fa-phone"></i> &nbsp; <strong><?php echo clean_phone($profile['user_phone']); ?></strong> <br/>
                                                            <i class="fa fa-envelope"></i> &nbsp; <strong id="contract_email"><?php echo $profile['user_email']; ?></strong> <br/>
                                                            <i class="fa fa-user"></i> &nbsp; Driver's License Number: <strong id="contract_dln"></strong> <br/>
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
                                                            If i provide my credit card information, I have authorized <strong><?php echo $location['location_nickname']; ?></strong> to automatically debit my bank account or charge my credit card as applicable and requested every month for all charges associated with my storage room. (Cardholder agrees to notify <strong><?php echo $location['location_nickname']; ?></strong> of any changes to the banking or credit card information (account number and expiration date). <br/><span class="font-xs text-muted">If you use a debit/credit card a <?php echo number_format($location['location_creditcard_fee'] * 100, 0); ?>% card fee will be applied to your total transaction.</span>
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
                                                            <h5>Today: <strong class="text-success">$<span class="fake-rent"><?php echo $storage['storage_price']."</span></strong>/<span class='prorate'>".$storage['storage_period']; ?></span><small>+ deposit of <strong class="text-success bold fake-deposit">$<?php echo number_format($location['location_storage_deposit'], 2); ?></strong></small></h5>
                                                            <h6><span class="text-success bold">$<span class="fake-cnn"></span></span>/<?php echo $storage['storage_period']; ?> on-going every <span id="contract_next_due"></span> of each month.</h6>
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
                                        </div>
                                        <div class="col-md-12" id="scann" style="display: none;">
                                            <br/><br/><br/>
                                            <div class="alert alert-warning scanner-alert hidden" style="width: 65%; margin: auto!important; margin-bottom: 80px; ">
                                                <strong>File will be uploaded upon submission on next page.</strong>
                                            </div>
                                            <h3 class='text-center'>Please upload the scanned contract with required signatures. <br/> <small class="bold text-muted"><span id="lol">Awe snap, did you miss it?</span> <a class="show-contract">Click here to bring it back.</a></small></h3>
                                            <br/><br/><br/>
                                            <div class='form-control' style='width: 65%; margin: auto!important;'>
                                                <input type='file' name='scanned_contract' class='scanner'>
                                            </div>
                                            <br/><br/><br/>

                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="tab5">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <h3 style="margin-top: 0;"><strong class="font-red"><?php echo $storage['storage_unit_lwh']; ?> - Unit #: <?php echo $storage['storage_unit_name']; ?></strong> (Floor <?php echo $types['type_floor'].", ".$types['type_desc']." [Climate: ".$types['type_climate']."]"; ?>) <strong>move-in</strong> for <strong class="font-blue"><?php echo sentence_case($profile['user_fname']); ?></strong></h3>
                                            <h2><strong class="text-success">$<span id="fake-rent" class="fake-rent"><?php echo $storage['storage_price']."</span></strong>/<span class='prorate'>".$storage['storage_period']; ?></span> <small>+ deposit of <strong class="text-success bold fake-deposit">$<?php echo number_format($location['location_storage_deposit'], 2); ?></strong> today.</small></h2>

                                            <h5>Nice, we've made it to the final steps. Now we just need to print the contract--get it signed, and uploaded back into the system. After you re-upload the contract, you'll be able to take a payment. Once that is complete, a record of the storage unit contract will be availabble <strong>in the users profile.</strong></h5>

                                            <h4>Payment Details <small class="hidden-xs hidden-sm"><span class="text-danger">| </span>collecting amount due.</small> <span class="pull-right">Amount due: <strong><span class="text-success">$<span id="owe_rent">0.00</span></span></strong></span></h4>
                                            <hr/>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="scroller2" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
                                                <div class="portlet">
                                                    <div class="portlet-body">
                                                        <div class="table-container">
                                                            <form role="form" id="add_service_rate">
                                                                <table class="table table-striped table-hover datatable" data-src="assets/app/api/storage.php?type=rates&luid=<?php echo $storage['storage_location_token']; ?>&ct=<?php echo $n; ?>">
                                                                    <thead>
                                                                    <tr role="row" class="heading">
                                                                        <th>
                                                                            Service Name
                                                                        </th>
                                                                        <th width="12%" class="text-center">
                                                                            Invoice item <i class="fa fa-arrow-right"></i>
                                                                        </th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>

                                                                    </tbody>
                                                                </table>
                                                            </form>
                                                        </div>
                                                        <small class="bold">(<i class="fa fa-check text-danger light"></i> = Taxable | <i class="fa fa-check text-success light"></i> = Commissionable | <span class="text-danger bold">Discount</span>)</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-8">
                                            <div class="scroller2" style="height: 290px;" data-always-visible="1" data-rail-visible1="1">
                                                <div class="portlet">
                                                    <div class="portlet-body" id="invoice">
                                                        <div class="invoice">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <h5><strong>Rent & mandatory</strong> charges</h5>
                                                                    <div class="table-container">
                                                                        <table class="table table-striped table-hover tablez">
                                                                            <thead>
                                                                                <tr role="row" class="heading">
                                                                                    <th>
                                                                                        Item
                                                                                    </th>
                                                                                    <th>
                                                                                        Description
                                                                                    </th>
                                                                                    <th>
                                                                                        Quantity
                                                                                    </th>
                                                                                    <th>
                                                                                        Unit Cost
                                                                                    </th>
                                                                                    <th>
                                                                                        <span class="pull-right">Total</span>
                                                                                    </th>
                                                                                </tr>
                                                                            </thead>
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td class="bold">Rent <input type="hidden" id="charge_rent" name="rent"></td>
                                                                                    <td>Standard rent (<span class='prorate'><?php echo $storage['storage_period']; ?></span>)</td>
                                                                                    <td>1</td>
                                                                                    <td class="bold">$<span class="tr_rent"></span></td>
                                                                                    <td class="bold text-right text-danger">$<span class="tr_rent_total"></span></td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td class="bold">Deposit <input type="hidden" id="charge_deposit" name="deposit"></td>
                                                                                    <td>Secuirty deposit</td>
                                                                                    <td>1</td>
                                                                                    <td class="bold">$<span class="tr_deposit"></span></td>
                                                                                    <td class="bold text-right text-danger">$<span class="tr_deposit_total"></span> </td>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <h5><strong>Other items</strong> charged</h5>
                                                                    <div class="table-container">
                                                                        <form role="form" id="add_service_rate">
                                                                            <table class="table table-striped table-hover datatable sales" data-src="assets/app/api/storage.php?type=sales&ct=<?php echo $n; ?>&luid=<?php echo $storage['storage_location_token']; ?>">
                                                                                <thead>
                                                                                <tr role="row" class="heading">
                                                                                    <th>
                                                                                        Item
                                                                                        <span class="pull-right no_print">
                                                                                            Options
                                                                                        </span>
                                                                                    </th>
                                                                                    <th>
                                                                                        Description
                                                                                    </th>
                                                                                    <th>
                                                                                        Quantity
                                                                                    </th>
                                                                                    <th>
                                                                                        Unit Cost
                                                                                    </th>
                                                                                    <th>
                                                                                        <span class="pull-right">Total</span>
                                                                                    </th>
                                                                                </tr>
                                                                                </thead>
                                                                                <tbody>

                                                                                </tbody>
                                                                            </table>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col-xs-6">
                                                                </div>
                                                                <div class="col-xs-6 invoice-block">
                                                                    <ul class="list-unstyled amounts" style="margin-bottom: 0;">
                                                                        <li>
                                                                            Sub Total: <h3 style="display: inline" class="text-danger bold">$<span id="owe_sub_total"></span></h3>
                                                                        </li>
                                                                        <li>
                                                                            <small class="bold" id="taxable_fees"></small> Taxes Due:  <h3 style="display: inline;" class="text-danger bold">$<span id="owe_tax"></span></h3>
                                                                        </li>
                                                                        <li id="cc_fees">
                                                                            Credit Card Fees: <h3 style="display: inline;" class="text-danger bold">$<span id="owe_cc_fees"></span></h3>
                                                                        </li>
                                                                        <li>
                                                                            Grand Total: <h3 style="display: inline;" class="text-danger bold">$<span id="owe_total"></span></h3>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                            <!--
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <div class="form-group form-md-line-input">
                                                                        <select class="form-control type" name="type" data-target=".tender-inputs">
                                                                            <option disabled selected value="">Select one..</option>
                                                                            <option value="Cash" data-show=".cash" data-input="cash">Cash</option>
                                                                            <option value="Check" data-show=".chec" data-input="chec">Check</option>
                                                                            <option value="Invoice" data-show=".invoice" data-input="invoice">Invoice</option>
                                                                            <option value="Credit/Debt" data-show=".cc" data-input="cc">Credit/Debt Card (ckPay&trade;)</option>
                                                                            <option value="Other" data-show=".other" data-input="other">Credit/Debt Card (Other Payment Processor)</option>
                                                                        </select>
                                                                        <label for="form_control_1">Tender Type</label>
                                                                        <span class="help-block"></span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row" style="margin-top: 10px; padding: 2px;">
                                                                <div class="col-md-12">
                                                                    <div class="tender-inputs">
                                                                        <div class="form-group form-md-line-input cash hidden">
                                                                            <input type="number" step="any" class="form-control input-sm amt" name="amount" id="cash" value="<?php echo number_format($total['unpaid'], 2); ?>">
                                                                            <label for="form_control_1">Cash Amount</label>
                                                                            <span class="help-block"></span>
                                                                        </div>
                                                                        <div class="form-group form-md-line-input cash hidden">
                                                                            <input type="text" step="any" class="form-control input-sm" name="notes" id="cash_notes" placeholder="...">
                                                                            <label for="form_control_1">Cash Notes</label>
                                                                            <span class="help-block"></span>
                                                                        </div>
                                                                        <div class="form-group form-md-line-input chec hidden">
                                                                            <input type="number" step="any" class="form-control input-sm amt" name="amount" id="chec" value="<?php echo number_format($total['unpaid'], 2); ?>">
                                                                            <label for="form_control_1">Check Amount</label>
                                                                            <span class="help-block"></span>
                                                                        </div>
                                                                        <div class="form-group form-md-line-input chec hidden">
                                                                            <input type="number" step="any" class="form-control input-sm" name="notes" id="chec_notes" placeholder="...">
                                                                            <label for="form_control_1">Check Number</label>
                                                                            <span class="help-block"></span>
                                                                        </div>
                                                                        <div class="form-group form-md-line-input invoice hidden">
                                                                            <input type="number" step="any" class="form-control input-sm amt" name="amount" id="invoice" value="<?php echo number_format($total['unpaid'], 2); ?>">
                                                                            <label for="form_control_1">Invoice Amount</label>
                                                                            <span class="help-block"></span>
                                                                        </div>
                                                                        <div class="form-group form-md-line-input invoice hidden">
                                                                            <input type="text" class="form-control input-sm" name="notes" id="invoice_notes" placeholder="...">
                                                                            <label for="form_control_1">Invoice Notes</label>
                                                                            <span class="help-block"></span>
                                                                        </div>
                                                                        <div class="input-group margin-top-10 cc hidden margin-bottom-25">
                                                                            <input onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)" type="number" step='any' class="form-control amt" name="amt_b4" id="amt_pay" value="">
                                                                            <span class="input-group-addon" id="surcharge">
                                                                                + <?php echo number_format($total['unpaid'] * .03, 2); ?> (3%)
                                                                            </span>
                                                                            <input onchange="(function(el){el.value=parseFloat(el.value).toFixed(2);})(this)" type="number" step='any' class="form-control" name="amount" id="cc" value=""  readonly>
                                                                        </div>
                                                                        <div class="form-inline cc hidden margin-bottom-25 text-center">
                                                                            <div class="form-group form-md-line-input">
                                                                                <div class="input-icon">
                                                                                    <input type="text" size="20" data-stripe="name" class="form-control input-sm" value="<?php echo $user['user_fname']." ".$user['user_lname']; ?>">
                                                                                    <div class="form-control-focus">
                                                                                    </div>
                                                                                    <span class="help-block">Name on Card</span>
                                                                                    <i class="fa fa-user"></i>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group form-md-line-input">
                                                                                <div class="input-icon">
                                                                                    <input type="text" size="20" data-stripe="number" class="form-control input-sm card">
                                                                                    <div class="form-control-focus">
                                                                                    </div>
                                                                                    <span class="help-block">Card number</span>
                                                                                    <i class="fa fa-credit-card"></i>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group form-md-line-input">
                                                                                <div class="input-icon">
                                                                                    <input type="text" size="2" data-stripe="exp" class="form-control input-sm exp" style="width: 90px!important;">
                                                                                    <div class="form-control-focus">
                                                                                    </div>
                                                                                    <span class="help-block">Expiration</span>
                                                                                    <i class="fa fa-calendar"></i>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group form-md-line-input">
                                                                                <div class="input-icon">
                                                                                    <input type="text" size="4" data-stripe="cvc" class="form-control input-sm cvc">
                                                                                    <div class="form-control-focus">
                                                                                    </div>
                                                                                    <span class="help-block">CVC</span>
                                                                                    <i class="fa fa-sort-numeric-asc"></i>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group cc hidden">
                                                                            <input type="text" name="notes" id="cc_notes" class="hidden"/>
                                                                            <input type="text" name="charge" id="charge" class="hidden"/>
                                                                            <button id="checkout" class="btn btn-block red "><span class="error-handler">Pay now!</span> <i class="fa fa-credit-card"></i></button>
                                                                        </div>

                                                                        <div class="form-group form-md-line-input other hidden">
                                                                            <input type="number" step="any" class="form-control input-sm" name="amount" id="other" value="<?php echo number_format($total['unpaid'], 2); ?>">
                                                                            <label for="form_control_1">Credit/Debt Charge Amount</label>
                                                                            <span class="help-block"></span>
                                                                        </div>
                                                                        <div class="form-group form-md-line-input other hidden" style="margin-bottom: 48px">
                                                                            <input type="text" step="any" class="form-control input-sm" name="notes" placeholder="...">
                                                                            <label for="form_control_1">Credit/Debt Approval Number</label>
                                                                            <span class="help-block"></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            -->
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="form-actions">
                            <div class="row">
                                <div class="col-md-12">
                                    <button href="javascript:;" class="btn default button-previous pull-left">
                                        <i class="m-icon-swapleft"></i> Back </button>
                                    <button href="javascript:;" class="btn blue button-next pull-right">
                                        Continue <i class="m-icon-swapright m-icon-white"></i>
                                    </button>
                                    <button href="javascript:;" class="btn green button-submit pull-right" id="real_submit" type="submit" name="status" value="1">
                                        Submit <i class="m-icon-swapright m-icon-white"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php
                } else {
                    ?>
                    <div class="alert alert-danger" style="margin-top: 20px;">
                        <strong>You do not have permission to create storage contracts.</strong>
                    </div>
                    <?php
                }
                ?>

            </div>
        </div>
    </div>
</div>
<?php
if($_SESSION['group'] == 1 || strpos($uuidperm['user_esc_permissions'], "view_storage_create_contracts") !== false){


    ?>
    <span class="hidden fake-news"><?php echo $storage['storage_price']; ?></span>
    <script>
        $(document).ready(function(){

            function updateIn(){
                var adj = $('input[name="rate_adj"]').val();
                var dpt = $('input[name="deposit"]').val();
                var real = $('.fake-news').text();
                var number0 = parseFloat(real).toFixed(2);
                var number2 = parseFloat(adj).toFixed(2);
                $('.fake-cnn').html(parseFloat(+number0 + +number2).toFixed(2));
                $.ajax({
                    url: 'assets/app/api/storage.php?type=inv&luid=<?php echo $_GET['luid']; ?>&su=<?php echo $_GET['su']; ?>',
                    type: 'POST',
                    data: {
                        contract: '<?php echo $n; ?>',
                        uuid: '<?php echo $profile['user_token']; ?>',
                        mr: parseFloat(+number0 + +number2).toFixed(2),
                        date1: $('.d1').val(),
                        date2: $('.d2').val(),
                        deposit: dpt
                    },
                    success: function(m){
                        var owe = JSON.parse(m);
                        $(document).find('#owe_sub_total').html(parseFloat(owe.sub_total).toFixed(2));
                        $(document).find('#owe_tax').html(parseFloat(owe.tax).toFixed(2));
                        $(document).find('#owe_total').html(parseFloat(owe.total).toFixed(2));
                        $(document).find('#PLPAP_SUBTOTAL').html(parseFloat(owe.sub_total).toFixed(2));
                        $(document).find('#PLPAP_TAXES').html(parseFloat(owe.tax).toFixed(2));
                        $(document).find('#PLPAP_TOTAL').html(parseFloat(owe.total).toFixed(2));
                        $(document).find('#owe_total_unpaid').html(parseFloat(owe.unpaid).toFixed(2));
                        $(document).find('#owe_paid').html(parseFloat(owe.paid).toFixed(2));
                        $(document).find('#owe_rent').html(parseFloat(owe.total).toFixed(2));
                        $(document).find('.amt').val(parseFloat(owe.total).toFixed(2));
                        $(document).find('#amt_pay').trigger('change');
                        if(parseFloat(owe.cc_fees).toFixed(2) > 0){
                            $(document).find("#cc_fees").show();
                            $(document).find("#owe_cc_fees").html(parseFloat(owe.cc_fees).toFixed(2));
                            $(document).find(".load_payments").removeClass("margin-top-15");
                        } else {
                            $(document).find("#cc_fees").hide();
                            $(document).find("#owe_cc_fees").html("");
                            $(document).find(".load_payments").addClass("margin-top-15");
                        }
                        if(parseFloat(owe.taxable).toFixed(2) > 0){
                            $(document).find("#taxable_fees").show();
                            $(document).find("#taxable_fees").html("($"+ parseFloat(owe.taxable).toFixed(2) +" taxable)");
                        } else {
                            $(document).find("#taxable_fees").hide();
                        }
                    },
                    error: function(e){

                    }
                });
            }

            updateIn();

            $(function() {
                // IMPORTANT: Fill in your client key
                var clientKey = "js-InlLzUGLaGPQYhaSPQrQGnDmZH0HPvLyT6ks10ebG31Ekcxa3Y0KmE6ml73bDOJw";

                var cache = {};
                var container = $("#form_wizard_1");

                /** Handle successful response */
                function handleResp(data) {
                    // Check for error
                    if (data.error_msg) toastr.error("<strong>Ckai says:</strong><br/>"+data.error_msg);
                    else if ("city" in data) {
                        // Set city and state
                        container.find("input[name='city']").val(data.city);
                        container.find('.state option[value="'+data.state+'"]').attr("selected", "selected");
                        console.log(data.state);
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

            $('.scanner-alert').hide();
            $('.add-alt').unbind().on('click', function() {
                var row     = $('.current').attr('data-row');
                var contact = $('.alt-contact').val();
                var phone   = $('.alt-contact-phone').val();
                var notes   = $('.alt-contact-notes').val();
                if(row <= 3){
                    if(contact.length > 0 && phone.length > 0){
                        if($('#alts').hasClass('has-none')){
                            $('#alts').removeClass('has-none').html("");
                        }
                        $('.alt-error').hide();
                        $('#alts').append("<div class='alert alert-success'><strong>"+ contact +" @ "+ notes +" ["+ phone +"]</strong> was added to alternate contacts.<input type='hidden' name='alt[]' value='"+contact+"|"+ notes +"|"+phone+"'></div>");
                        $('.alt-contact').val("");
                        $('.alt-contact-phone').val("");
                        $('.alt-contact-notes').val("");
                        $('#a'+row).html('<td>'+ contact +'</td><td>'+ notes +'</td><td>'+ phone +'</td>');
                        row = +row + +1;
                        $('.current').attr("data-row", row);
                    } else {
                        $('.alt-error').show();
                    }
                } else {
                    $('.alt-error2').show();
                }
            });

            $('.datatable').each(function(){
                var url = $(this).attr('data-src');
                $(this).DataTable({
                    "processing": true,
                    "serverSide": true,
                    //"dom": "<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'<'table-group-actions pull-right'>>r>t<'row'<'col-md-8 col-sm-12'pli><'col-md-4 col-sm-12'>>",
                    "bStateSave": true, // save datatable state(pagination, sort, etc) in cookie.
                    "bPaginate": false,
                    "bFilter": false,
                    "info": false,
                    "ajax": {
                        "url": url, // ajax source
                    },
                    "lengthMenu": [
                        [10, 20, 50, 100, 150, -1],
                        [10, 20, 50, 100, 150, "All"] // change per page values here
                    ],
                    "pageLength": 10, // default record count per page
                    "order": [
                        [1, "asc"]
                    ]// set first column as a default sort by asc
                });
            });

            $('.tablez').dataTable({
                "order": [[ 4, "asc" ]],
                "bFilter" : false,
                "bLengthChange": false,
                "bPaginate":false,
                "info": false
            });

            $("#phone").inputmask("mask", {
                "mask": "(999) 999-9999"
            });

            $("#phone2").inputmask("mask", {
                "mask": "(999) 999-9999"
            });

            var date = $('.datepicker').datepicker({
                rtl: Metronic.isRTL(),
                orientation: "left",
                autoclose: true,
                onSelect: function(dateText) {
                    console.log("Selected date: " + dateText + "; input's current value: " + this.value);
                }
            });

            $('.scroller2').slimScroll({
                height: 670
            });


            if (!jQuery().bootstrapWizard) {
                return;
            }

            var form = $('#submit_form');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            form.validate({
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    phone: {
                        required: true
                    },
                    address: {
                        required: true
                    },
                    zip: {
                        required: true
                    },
                    city: {
                        required: true
                    },
                    state: {
                        required: true
                    },
                    deposit: {
                        required: true
                    },
                    startdate: {
                        required: true
                    },
                    nextDue: {
                        required: true
                    },
                    dln: {
                        required: true
                    }
                },


                invalidHandler: function (event, validator) { //display error alert on form submit
                    success.hide();
                    error.show();
                    Metronic.scrollTo(error, -200);
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
                    success.show();
                    error.hide();
                    //add here some ajax code to submit your form or just call form.submit() if you want to submit the form without ajax
                }

            });


            var handleTitle = function(tab, navigation, index) {
                var total = navigation.find('li').length;
                var current = index + 1;
                // set wizard title
                $('.step-title', $('#form_wizard_1')).text('Step ' + (index + 1) + ' of ' + total);
                // set done steps
                jQuery('li', $('#form_wizard_1')).removeClass("done");
                var li_list = navigation.find('li');
                for (var i = 0; i < index; i++) {
                    jQuery(li_list[i]).addClass("done");
                }

                if (current == 1) {
                    $('#form_wizard_1').find('.button-previous').hide();
                } else {
                    $('#form_wizard_1').find('.button-previous').show();
                }

                if(current == 2){

                }

                function nth(d) {
                    if(d>3 && d<21) return 'th'; // thanks kennebec
                    switch (d % 10) {
                        case 1:  return "st";
                        case 2:  return "nd";
                        case 3:  return "rd";
                        default: return "th";
                    }
                }

                if (current == 3){
                    $('#contract_name').html("<?php echo name($_GET['uuid']); ?>");
                    $('#contract_address').html($('input[name="address"]').val());
                    $('#contract_csz').html($('input[name="city"]').val() + ", " + $('select[name="state"]').val() + ", " + $('input[name="zip"]').val());
                    $('#contract_email').html($('input[name="email"]').val());
                    $('#contract_dln').html($('input[name="dln"]').val());

                    var date = new Date($('input[name="nextDue"]').val());
                    $('#contract_next_due').html("<strong>" + date.getDate()+nth(date.getDate()) + "</strong>");
                    window.onfocus=function(){
                         $('#scann').show();
                         $('#contrract').hide();
                    };
                    $('.scanner').on('change', function(){
                        $('.scanner-alert').removeClass("hidden");
                    });
                    $('.show-contract').on('click', function(){
                        $('#scann').hide();
                        $('#contrract').show();
                        $('#lol').html("You're lucky I'm here for you, my friend..");
                    });
                }

                if (current >= total) {
                    $('#form_wizard_1').find('.button-next').hide();
                    updateIn();
                    var line = $('#fake-rent').text();
                    var dpt = $('input[name="deposit"]').val();
                    console.log(line);
                    $('.tr_rent, .tr_rent_total').html(parseFloat(line.replace(/,/g, '')).toFixed(2));
                    $('.tr_deposit, .tr_deposit_total').html(parseFloat(dpt.replace(/,/g, '')).toFixed(2));
                    $('#charge_rent').val(parseFloat(line.replace(/,/g, '')).toFixed(2));
                    $('#charge_deposit').val(parseFloat(dpt.replace(/,/g, '')).toFixed(2));
                    $('#form_wizard_1').find('.button-submit').show();
                } else {
                    $('#form_wizard_1').find('.button-next').show();
                    $('#form_wizard_1').find('.button-submit').hide();
                }
                Metronic.scrollTo($('.page-title'));
            }

            $('.icheck').iCheck({
                checkboxClass: 'icheckbox_minimal',
                radioClass: 'iradio_minimal'
            });

            // default form wizard
            $('#form_wizard_1').bootstrapWizard({
                'nextSelector': '.button-next',
                'previousSelector': '.button-previous',
                onTabClick: function (tab, navigation, index, clickedIndex) {
                    return false;
                    /*
                     success.hide();
                     error.hide();
                     if (form.valid() == false) {
                     return false;
                     }
                     handleTitle(tab, navigation, clickedIndex);
                     */
                },
                onNext: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    if (form.valid() == false) {
                        return false;
                    }

                    handleTitle(tab, navigation, index);
                },
                onPrevious: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    handleTitle(tab, navigation, index);
                },
                onTabShow: function (tab, navigation, index) {
                    var total = navigation.find('li').length;
                    var current = index + 1;
                    var $percent = (current / total) * 100;
                    $('#form_wizard_1').find('.progress-bar').css({
                        width: $percent + '%'
                    });
                }
            });

            $('#form_wizard_1').find('.button-previous').hide();
            $('#form_wizard_1 .button-submit').click(function () {
                Pace.track(function(){
                    $.ajax({
                        url: 'assets/app/add_setting.php?setting=su&su=<?php echo $_GET['su']; ?>&uuid=<?php echo $profile['user_token']; ?>&c=<?php echo $n; ?>&luid=<?php echo $_GET['luid']; ?>',
                        type: 'POST',
                        data: $('#submit_form').serialize(),
                        success: function(d) {
                            $.ajax({
                                url: 'assets/pages/profile.php?uuid=<?php echo $profile['user_token']; ?>&s=true',
                                success: function(vat) {
                                    $('#page_content').html(vat);
                                    toastr.success("<strong>Ckai says</strong><br/>That storage unit has been added to the system, and messages have been sent to the customer.");
                                },
                                error: function() {
                                    toastr.error("<strong>CkAI says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                }
                            });
                        },
                        error: function() {
                            toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                        }
                    });
                });
            }).hide();

            Stripe.setPublishableKey('<?php echo $location['location_storage_stripe_public']; ?>');

            function stripeResponseHandler(status, response) {
                // Grab the form:
                var $form = $('#submit_form');

                if (response.error) { // Problem!

                    // Show the errors on the form:
                    toastr.error("<strong>Logan says:</strong><br/>" + response.error.message);
                    $form.find('#checkout').prop('disabled', false); // Re-enable submission
                    $form.find('#checkout').html("Check your fields. Try again? <i class='fa fa-credit-card'></i>");

                } else { // Token was created!

                    // Get the token ID:
                    var token = response.id;

                    // Insert the token ID into the form so it gets submitted to the server:
                    //$form.append($('<input type="hidden" name="auth">').val(token));

                    $.ajax({
                        url: 'assets/app/checkout.php?e=LOL&cuid=<?php echo $_SESSION['cuid']; ?>&c=<?php echo $n; ?>&uuid=<?php echo $profile['user_token']; ?>',
                        type: 'post',
                        data: {
                            token: token,
                            amount: $('#cc').val().replace('.', ''),
                            email: $('input[name="email"]').val()
                        },
                        success: function(data) {
                            if (data.length > 8) {
                                toastr.info("<strong>Logan says:</strong><br/>Card was charged successfully, here's the confirmation token: " + data);
                                $('.error-handler').html("");
                                $('#cc_notes').removeAttr('disabled');
                                $('#cc_notes').attr('value', "Approval: "+data);
                                $('#charge').removeAttr('disabled');
                                $('#charge').attr('value', data);
                                $('#real_submit').click();
                            }
                            if (data == 'error-4'){
                                $form.find('#checkout').html("Card declined. Try again? <i class='fa fa-credit-card'></i>");
                                toastr.error("<strong>Logan says:</strong><br/>Card was declined. Please try again using a different card, or review your card information for mistakes.");
                                $form.find('#checkout').prop('disabled', false); // Re-enable submission
                            }

                            if (data == 'error-2'){
                                $form.find('#checkout').html("Error. Try again? <i class='fa fa-credit-card'></i>")
                                toastr.error("<strong>Logan says:</strong><br/>A programatic error has occured, and the card has not been charged.");
                                $form.find('#checkout').prop('disabled', false);
                            }
                        },
                        error: function(data) {
                            console.log("Ajax Error!");
                            console.log(data);
                        }
                    });
                }
            };

            $('#checkout').on('click', function() {
                $(function(event) {
                    var $form  = $('#submit_form');
                    // Disable the submit button to prevent repeated clicks:
                    $('#checkout').prop('disabled', true);
                    $('#checkout').html("<i class='fa fa-spinner fa-spin'></i>");

                    // Request a token from Stripe:
                    Stripe.card.createToken($form, stripeResponseHandler);

                    // Prevent the form from being submitted:
                    return false;
                });
            });

            $('.card').inputmask("mask", {
                "mask": "9999 9999 9999 9999",
                "removeMaskOnSubmit": false,
                "placeholder": ""
            });
            $('.exp').inputmask("mask", {
                "mask": "99/99",
                "removeMaskOnSubmit": false,
                "placeholder": ""
            });
            $('.cvc').inputmask("mask", {
                "mask": "9999",
                "placeholder": ""
            });
            $('#amt_pay').on('change', function() {
                var value           = $(this).val();
                var surcharge       = parseFloat(Math.round((+value * +<?php echo number_format($location['location_creditcard_fee'], 2); ?>) * 100) / 100).toFixed(2);
                var after           = parseFloat(Math.round((+value + +value * +<?php echo number_format($location['location_creditcard_fee'], 2); ?>) * 100) / 100).toFixed(2);
                $("#surcharge").html("+ " + parseFloat(surcharge).toFixed(2).replace (/,/g, "") + " (3%) =");
                $("#cc").val(parseFloat(after).toFixed(2).replace (/,/g, ""));
            });

            $('.state option[value="<?php echo $profile['user_state']; ?>"]').attr("selected", "selected");

            $('.rent-adj, .deposit, .dates').on('change', function() {
                        var adj = $('input[name="rate_adj"]').val();
                        var dpt = $('input[name="deposit"]').val();
                        var real = $('.fake-news').text();
                        var number0  = parseFloat(real).toFixed(2);
                        var number2 = parseFloat(adj).toFixed(2);
                        $('.fake-rent').html(parseFloat(+number0 + +number2).toFixed(2));
                        $('.fake-cnn').html(parseFloat(+number0 + +number2).toFixed(2));
                        $('.fake-deposit').html("$" + parseFloat(+dpt).toFixed(2));
                        $('.prorate').html("Pro-rated");
                        $.ajax({
                            url: 'assets/app/api/math.php?e=EmP&su=<?php echo $_GET['su']; ?>',
                            type: 'POST',
                            data: {
                                mr: parseFloat(+number0 + +number2).toFixed(2),
                                date1: $('.d1').val(),
                                date2: $('.d2').val()
                            },
                            success: function(data){
                                $('.fake-rent').html(data);
                            },
                            error: function() {
                                toastr.error("<strong>Logan says:</strong><br/> Something went wrong.");
                            }
                        })
                    });

                    $('.type').on('change', function() {
                        var type    = $(this).val();
                        var target  = $(this).data('target');
                        var show   =  $("option:selected", this).data('show');
                        $(target).children().addClass('hidden');
                        $(show).removeClass('hidden');
                        $(".tender-inputs input:hidden").attr('disabled', 'disabled');
                        $(".tender-inputs input:visible").removeAttr('disabled');
                        $('.tender-inputs input:visible[name="amount"]').focus();

                        if(type == 'Credit/Debt'){
                            $('.button-submit').hide();
                        } else {
                            $('.button-submit').show();
                        }
                    });
                });
            </script>
            <?php
        }
        ?>

<?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
