<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/3/2017
 * Time: 3:52 AM
 */
session_start();
include '../app/init.php';

if(isset($_SESSION['logged'])){
    mysql_query("UPDATE fmo_users SET user_last_location='".mysql_real_escape_string(basename(__FILE__, '.php')).".php?".$_SERVER['QUERY_STRING']."' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'");
    $location   = mysql_fetch_array(mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    $user       = mysql_fetch_array(mysql_query("SELECT user_fname, user_lname FROM fmo_users WHERE user_token='".mysql_real_escape_string($_GET['uuid'])."'"));
    ?>
    <div class="page-content">
        <h3 class="page-title">
            Create Event <small>for <?php echo $user['user_fname']." ".$user['user_lname']; ?></small>
        </h3>
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a><?php echo $location['location_name']; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>">Dashboard</a>
                    <i class="fa fa-angle-right"></i>
                </li>

                <li>
                    <a class="load_page" data-href="assets/pages/create_event.php?luid=<?php echo $_GET['luid']; ?>&uuid=<?php echo $_GET['uuid']; ?>" data-page-title="Create Event">Create Event</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="icon-notebook bold"></i>
                            <span class="caption-subject bold font-red uppercase"> Create Event </span>
                            <span class="caption-helper">creating a new booking for <?php echo $user['user_fname']." ".$user['user_lname']; ?></span>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="portlet" id="form_wizard_1">
                                    <div class="portlet-body form">
                                        <form action="#" class="form-horizontal" id="submit_form" method="POST">
                                            <div class="form-wizard">
                                                <div class="form-body">
                                                    <ul class="nav nav-pills nav-justified steps">
                                                        <li>
                                                            <a href="#tab1" data-toggle="tab" class="step">
                                                                <span class="number"> 1 </span>
                                                                <span class="desc"><i class="fa fa-check"></i> Basic Information </span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#tab2" data-toggle="tab" class="step">
                                                                <span class="number">2 </span>
                                                                <span class="desc"><i class="fa fa-check"></i> Pickup Location </span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#tab3" data-toggle="tab" class="step active">
												                <span class="number">3 </span>
                                                                <span class="desc"><i class="fa fa-check"></i> Destination Location </span>
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="#tab4" data-toggle="tab" class="step">
												                <span class="number">4 </span>
                                                                <span class="desc"><i class="fa fa-check"></i> Billing Setup </span>
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
                                                        <div class="alert alert-success display-none">
                                                            <button class="close" data-dismiss="alert"></button>
                                                            Your form validation is successful!
                                                        </div>
                                                        <div class="tab-pane active" id="tab1">
                                                            <h3 class="block">Provide your account details</h3>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Username <span class="required">* </span></label>
                                                                <div class="col-md-4">
                                                                    <input type="text" class="form-control" name="username"/>
                                                                    <span class="help-block">Provide your username </span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Password <span class="required">* </span></label>
                                                                <div class="col-md-4">
                                                                    <input type="password" class="form-control" name="password" id="submit_form_password"/>
                                                                    <span class="help-block">Provide your password. </span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Confirm Password <span class="required">* </span</label>
                                                                <div class="col-md-4">
                                                                    <input type="password" class="form-control" name="rpassword"/>
                                                                    <span class="help-block">Confirm your password </span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Email <span class="required">* </span></label>
                                                                <div class="col-md-4">
                                                                    <input type="text" class="form-control" name="email"/>
                                                                    <span class="help-block">Provide your email address </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane" id="tab2">
                                                            <h3 class="block">Provide your profile details</h3>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Fullname <span class="required">* </span></label>
                                                                <div class="col-md-4">
                                                                    <input type="text" class="form-control" name="fullname"/>
                                                                    <span class="help-block">Provide your fullname </span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Phone Number <span class="required">* </span>
                                                                </label>
                                                                <div class="col-md-4">
                                                                    <input type="text" class="form-control" name="phone"/>
                                                                    <span class="help-block">Provide your phone number </span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Gender <span class="required">* </span></label>
                                                                <div class="col-md-4">
                                                                    <div class="radio-list">
                                                                        <label>
                                                                            <input type="radio" name="gender" value="M" data-title="Male"/>
                                                                            Male </label>
                                                                        <label>
                                                                            <input type="radio" name="gender" value="F" data-title="Female"/>
                                                                            Female </label>
                                                                    </div>
                                                                    <div id="form_gender_error">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Address <span class="required">* </span></label>
                                                                <div class="col-md-4">
                                                                    <input type="text" class="form-control" name="address"/>
                                                                    <span class="help-block">Provide your street address </span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">City/Town <span class="required">* </span></label>
                                                                <div class="col-md-4">
                                                                    <input type="text" class="form-control" name="city"/>
                                                                    <span class="help-block">Provide your city or town </span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Country</label>
                                                                <div class="col-md-4">
                                                                    <select name="country" id="country_list" class="form-control">
                                                                        <option value=""></option>
                                                                        <option value="AF">Afghanistan</option>
                                                                        <option value="AL">Albania</option>
                                                                        <option value="DZ">Algeria</option>
                                                                        <option value="AS">American Samoa</option>
                                                                        <option value="AD">Andorra</option>
                                                                        <option value="AO">Angola</option>
                                                                        <option value="AI">Anguilla</option>
                                                                        <option value="AR">Argentina</option>
                                                                        <option value="AM">Armenia</option>
                                                                        <option value="AW">Aruba</option>
                                                                        <option value="AU">Australia</option>
                                                                        <option value="AT">Austria</option>
                                                                        <option value="AZ">Azerbaijan</option>
                                                                        <option value="BS">Bahamas</option>
                                                                        <option value="BH">Bahrain</option>
                                                                        <option value="BD">Bangladesh</option>
                                                                        <option value="BB">Barbados</option>
                                                                        <option value="BY">Belarus</option>
                                                                        <option value="BE">Belgium</option>
                                                                        <option value="BZ">Belize</option>
                                                                        <option value="BJ">Benin</option>
                                                                        <option value="BM">Bermuda</option>
                                                                        <option value="BT">Bhutan</option>
                                                                        <option value="BO">Bolivia</option>
                                                                        <option value="BA">Bosnia and Herzegowina</option>
                                                                        <option value="BW">Botswana</option>
                                                                        <option value="BV">Bouvet Island</option>
                                                                        <option value="BR">Brazil</option>
                                                                        <option value="IO">British Indian Ocean Territory</option>
                                                                        <option value="BN">Brunei Darussalam</option>
                                                                        <option value="BG">Bulgaria</option>
                                                                        <option value="BF">Burkina Faso</option>
                                                                        <option value="BI">Burundi</option>
                                                                        <option value="KH">Cambodia</option>
                                                                        <option value="CM">Cameroon</option>
                                                                        <option value="CA">Canada</option>
                                                                        <option value="CV">Cape Verde</option>
                                                                        <option value="KY">Cayman Islands</option>
                                                                        <option value="CF">Central African Republic</option>
                                                                        <option value="TD">Chad</option>
                                                                        <option value="CL">Chile</option>
                                                                        <option value="CN">China</option>
                                                                        <option value="CX">Christmas Island</option>
                                                                        <option value="CC">Cocos (Keeling) Islands</option>
                                                                        <option value="CO">Colombia</option>
                                                                        <option value="KM">Comoros</option>
                                                                        <option value="CG">Congo</option>
                                                                        <option value="CD">Congo, the Democratic Republic of the</option>
                                                                        <option value="CK">Cook Islands</option>
                                                                        <option value="CR">Costa Rica</option>
                                                                        <option value="CI">Cote d'Ivoire</option>
                                                                        <option value="HR">Croatia (Hrvatska)</option>
                                                                        <option value="CU">Cuba</option>
                                                                        <option value="CY">Cyprus</option>
                                                                        <option value="CZ">Czech Republic</option>
                                                                        <option value="DK">Denmark</option>
                                                                        <option value="DJ">Djibouti</option>
                                                                        <option value="DM">Dominica</option>
                                                                        <option value="DO">Dominican Republic</option>
                                                                        <option value="EC">Ecuador</option>
                                                                        <option value="EG">Egypt</option>
                                                                        <option value="SV">El Salvador</option>
                                                                        <option value="GQ">Equatorial Guinea</option>
                                                                        <option value="ER">Eritrea</option>
                                                                        <option value="EE">Estonia</option>
                                                                        <option value="ET">Ethiopia</option>
                                                                        <option value="FK">Falkland Islands (Malvinas)</option>
                                                                        <option value="FO">Faroe Islands</option>
                                                                        <option value="FJ">Fiji</option>
                                                                        <option value="FI">Finland</option>
                                                                        <option value="FR">France</option>
                                                                        <option value="GF">French Guiana</option>
                                                                        <option value="PF">French Polynesia</option>
                                                                        <option value="TF">French Southern Territories</option>
                                                                        <option value="GA">Gabon</option>
                                                                        <option value="GM">Gambia</option>
                                                                        <option value="GE">Georgia</option>
                                                                        <option value="DE">Germany</option>
                                                                        <option value="GH">Ghana</option>
                                                                        <option value="GI">Gibraltar</option>
                                                                        <option value="GR">Greece</option>
                                                                        <option value="GL">Greenland</option>
                                                                        <option value="GD">Grenada</option>
                                                                        <option value="GP">Guadeloupe</option>
                                                                        <option value="GU">Guam</option>
                                                                        <option value="GT">Guatemala</option>
                                                                        <option value="GN">Guinea</option>
                                                                        <option value="GW">Guinea-Bissau</option>
                                                                        <option value="GY">Guyana</option>
                                                                        <option value="HT">Haiti</option>
                                                                        <option value="HM">Heard and Mc Donald Islands</option>
                                                                        <option value="VA">Holy See (Vatican City State)</option>
                                                                        <option value="HN">Honduras</option>
                                                                        <option value="HK">Hong Kong</option>
                                                                        <option value="HU">Hungary</option>
                                                                        <option value="IS">Iceland</option>
                                                                        <option value="IN">India</option>
                                                                        <option value="ID">Indonesia</option>
                                                                        <option value="IR">Iran (Islamic Republic of)</option>
                                                                        <option value="IQ">Iraq</option>
                                                                        <option value="IE">Ireland</option>
                                                                        <option value="IL">Israel</option>
                                                                        <option value="IT">Italy</option>
                                                                        <option value="JM">Jamaica</option>
                                                                        <option value="JP">Japan</option>
                                                                        <option value="JO">Jordan</option>
                                                                        <option value="KZ">Kazakhstan</option>
                                                                        <option value="KE">Kenya</option>
                                                                        <option value="KI">Kiribati</option>
                                                                        <option value="KP">Korea, Democratic People's Republic of</option>
                                                                        <option value="KR">Korea, Republic of</option>
                                                                        <option value="KW">Kuwait</option>
                                                                        <option value="KG">Kyrgyzstan</option>
                                                                        <option value="LA">Lao People's Democratic Republic</option>
                                                                        <option value="LV">Latvia</option>
                                                                        <option value="LB">Lebanon</option>
                                                                        <option value="LS">Lesotho</option>
                                                                        <option value="LR">Liberia</option>
                                                                        <option value="LY">Libyan Arab Jamahiriya</option>
                                                                        <option value="LI">Liechtenstein</option>
                                                                        <option value="LT">Lithuania</option>
                                                                        <option value="LU">Luxembourg</option>
                                                                        <option value="MO">Macau</option>
                                                                        <option value="MK">Macedonia, The Former Yugoslav Republic of</option>
                                                                        <option value="MG">Madagascar</option>
                                                                        <option value="MW">Malawi</option>
                                                                        <option value="MY">Malaysia</option>
                                                                        <option value="MV">Maldives</option>
                                                                        <option value="ML">Mali</option>
                                                                        <option value="MT">Malta</option>
                                                                        <option value="MH">Marshall Islands</option>
                                                                        <option value="MQ">Martinique</option>
                                                                        <option value="MR">Mauritania</option>
                                                                        <option value="MU">Mauritius</option>
                                                                        <option value="YT">Mayotte</option>
                                                                        <option value="MX">Mexico</option>
                                                                        <option value="FM">Micronesia, Federated States of</option>
                                                                        <option value="MD">Moldova, Republic of</option>
                                                                        <option value="MC">Monaco</option>
                                                                        <option value="MN">Mongolia</option>
                                                                        <option value="MS">Montserrat</option>
                                                                        <option value="MA">Morocco</option>
                                                                        <option value="MZ">Mozambique</option>
                                                                        <option value="MM">Myanmar</option>
                                                                        <option value="NA">Namibia</option>
                                                                        <option value="NR">Nauru</option>
                                                                        <option value="NP">Nepal</option>
                                                                        <option value="NL">Netherlands</option>
                                                                        <option value="AN">Netherlands Antilles</option>
                                                                        <option value="NC">New Caledonia</option>
                                                                        <option value="NZ">New Zealand</option>
                                                                        <option value="NI">Nicaragua</option>
                                                                        <option value="NE">Niger</option>
                                                                        <option value="NG">Nigeria</option>
                                                                        <option value="NU">Niue</option>
                                                                        <option value="NF">Norfolk Island</option>
                                                                        <option value="MP">Northern Mariana Islands</option>
                                                                        <option value="NO">Norway</option>
                                                                        <option value="OM">Oman</option>
                                                                        <option value="PK">Pakistan</option>
                                                                        <option value="PW">Palau</option>
                                                                        <option value="PA">Panama</option>
                                                                        <option value="PG">Papua New Guinea</option>
                                                                        <option value="PY">Paraguay</option>
                                                                        <option value="PE">Peru</option>
                                                                        <option value="PH">Philippines</option>
                                                                        <option value="PN">Pitcairn</option>
                                                                        <option value="PL">Poland</option>
                                                                        <option value="PT">Portugal</option>
                                                                        <option value="PR">Puerto Rico</option>
                                                                        <option value="QA">Qatar</option>
                                                                        <option value="RE">Reunion</option>
                                                                        <option value="RO">Romania</option>
                                                                        <option value="RU">Russian Federation</option>
                                                                        <option value="RW">Rwanda</option>
                                                                        <option value="KN">Saint Kitts and Nevis</option>
                                                                        <option value="LC">Saint LUCIA</option>
                                                                        <option value="VC">Saint Vincent and the Grenadines</option>
                                                                        <option value="WS">Samoa</option>
                                                                        <option value="SM">San Marino</option>
                                                                        <option value="ST">Sao Tome and Principe</option>
                                                                        <option value="SA">Saudi Arabia</option>
                                                                        <option value="SN">Senegal</option>
                                                                        <option value="SC">Seychelles</option>
                                                                        <option value="SL">Sierra Leone</option>
                                                                        <option value="SG">Singapore</option>
                                                                        <option value="SK">Slovakia (Slovak Republic)</option>
                                                                        <option value="SI">Slovenia</option>
                                                                        <option value="SB">Solomon Islands</option>
                                                                        <option value="SO">Somalia</option>
                                                                        <option value="ZA">South Africa</option>
                                                                        <option value="GS">South Georgia and the South Sandwich Islands</option>
                                                                        <option value="ES">Spain</option>
                                                                        <option value="LK">Sri Lanka</option>
                                                                        <option value="SH">St. Helena</option>
                                                                        <option value="PM">St. Pierre and Miquelon</option>
                                                                        <option value="SD">Sudan</option>
                                                                        <option value="SR">Suriname</option>
                                                                        <option value="SJ">Svalbard and Jan Mayen Islands</option>
                                                                        <option value="SZ">Swaziland</option>
                                                                        <option value="SE">Sweden</option>
                                                                        <option value="CH">Switzerland</option>
                                                                        <option value="SY">Syrian Arab Republic</option>
                                                                        <option value="TW">Taiwan, Province of China</option>
                                                                        <option value="TJ">Tajikistan</option>
                                                                        <option value="TZ">Tanzania, United Republic of</option>
                                                                        <option value="TH">Thailand</option>
                                                                        <option value="TG">Togo</option>
                                                                        <option value="TK">Tokelau</option>
                                                                        <option value="TO">Tonga</option>
                                                                        <option value="TT">Trinidad and Tobago</option>
                                                                        <option value="TN">Tunisia</option>
                                                                        <option value="TR">Turkey</option>
                                                                        <option value="TM">Turkmenistan</option>
                                                                        <option value="TC">Turks and Caicos Islands</option>
                                                                        <option value="TV">Tuvalu</option>
                                                                        <option value="UG">Uganda</option>
                                                                        <option value="UA">Ukraine</option>
                                                                        <option value="AE">United Arab Emirates</option>
                                                                        <option value="GB">United Kingdom</option>
                                                                        <option value="US">United States</option>
                                                                        <option value="UM">United States Minor Outlying Islands</option>
                                                                        <option value="UY">Uruguay</option>
                                                                        <option value="UZ">Uzbekistan</option>
                                                                        <option value="VU">Vanuatu</option>
                                                                        <option value="VE">Venezuela</option>
                                                                        <option value="VN">Viet Nam</option>
                                                                        <option value="VG">Virgin Islands (British)</option>
                                                                        <option value="VI">Virgin Islands (U.S.)</option>
                                                                        <option value="WF">Wallis and Futuna Islands</option>
                                                                        <option value="EH">Western Sahara</option>
                                                                        <option value="YE">Yemen</option>
                                                                        <option value="ZM">Zambia</option>
                                                                        <option value="ZW">Zimbabwe</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Remarks</label>
                                                                <div class="col-md-4">
                                                                    <textarea class="form-control" rows="3" name="remarks"></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane" id="tab3">
                                                            <h3 class="block">Provide your billing and credit card details</h3>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Card Holder Name <span class="required">
													* </span>
                                                                </label>
                                                                <div class="col-md-4">
                                                                    <input type="text" class="form-control" name="card_name"/>
                                                                    <span class="help-block">
														</span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Card Number <span class="required">
													* </span>
                                                                </label>
                                                                <div class="col-md-4">
                                                                    <input type="text" class="form-control" name="card_number"/>
                                                                    <span class="help-block">
														</span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">CVC <span class="required">
													* </span>
                                                                </label>
                                                                <div class="col-md-4">
                                                                    <input type="text" placeholder="" class="form-control" name="card_cvc"/>
                                                                    <span class="help-block">
														</span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Expiration(MM/YYYY) <span class="required">
													* </span>
                                                                </label>
                                                                <div class="col-md-4">
                                                                    <input type="text" placeholder="MM/YYYY" maxlength="7" class="form-control" name="card_expiry_date"/>
                                                                    <span class="help-block">
														e.g 11/2020 </span>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Payment Options <span class="required">
													* </span>
                                                                </label>
                                                                <div class="col-md-4">
                                                                    <div class="checkbox-list">
                                                                        <label>
                                                                            <input type="checkbox" name="payment[]" value="1" data-title="Auto-Pay with this Credit Card."/> Auto-Pay with this Credit Card </label>
                                                                        <label>
                                                                            <input type="checkbox" name="payment[]" value="2" data-title="Email me monthly billing."/> Email me monthly billing </label>
                                                                    </div>
                                                                    <div id="form_payment_error">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="tab-pane" id="tab4">
                                                            <h3 class="block">Confirm your account</h3>
                                                            <h4 class="form-section">Account</h4>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Username:</label>
                                                                <div class="col-md-4">
                                                                    <p class="form-control-static" data-display="username">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Email:</label>
                                                                <div class="col-md-4">
                                                                    <p class="form-control-static" data-display="email">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <h4 class="form-section">Profile</h4>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Fullname:</label>
                                                                <div class="col-md-4">
                                                                    <p class="form-control-static" data-display="fullname">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Gender:</label>
                                                                <div class="col-md-4">
                                                                    <p class="form-control-static" data-display="gender">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Phone:</label>
                                                                <div class="col-md-4">
                                                                    <p class="form-control-static" data-display="phone">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Address:</label>
                                                                <div class="col-md-4">
                                                                    <p class="form-control-static" data-display="address">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">City/Town:</label>
                                                                <div class="col-md-4">
                                                                    <p class="form-control-static" data-display="city">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Country:</label>
                                                                <div class="col-md-4">
                                                                    <p class="form-control-static" data-display="country">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Remarks:</label>
                                                                <div class="col-md-4">
                                                                    <p class="form-control-static" data-display="remarks">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <h4 class="form-section">Billing</h4>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Card Holder Name:</label>
                                                                <div class="col-md-4">
                                                                    <p class="form-control-static" data-display="card_name">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Card Number:</label>
                                                                <div class="col-md-4">
                                                                    <p class="form-control-static" data-display="card_number">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">CVC:</label>
                                                                <div class="col-md-4">
                                                                    <p class="form-control-static" data-display="card_cvc">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Expiration:</label>
                                                                <div class="col-md-4">
                                                                    <p class="form-control-static" data-display="card_expiry_date">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Payment Options:</label>
                                                                <div class="col-md-4">
                                                                    <p class="form-control-static" data-display="payment[]">
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-actions">
                                                    <div class="row">
                                                        <div class="col-md-offset-3 col-md-9">
                                                            <a href="javascript:;" class="btn default button-previous">
                                                                <i class="m-icon-swapleft"></i> Back </a>
                                                            <a href="javascript:;" class="btn blue button-next">
                                                                Continue <i class="m-icon-swapright m-icon-white"></i>
                                                            </a>
                                                            <a href="javascript:;" class="btn green button-submit">
                                                                Submit <i class="m-icon-swapright m-icon-white"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            if (!jQuery().bootstrapWizard) {
                return;
            }

            function format(state) {
                if (!state.id) return state.text; // optgroup
                return "<img class='flag' src='../../assets/global/img/flags/" + state.id.toLowerCase() + ".png'/>&nbsp;&nbsp;" + state.text;
            }

            $("#country_list").select2({
                placeholder: "Select",
                allowClear: true,
                formatResult: format,
                formatSelection: format,
                escapeMarkup: function (m) {
                    return m;
                }
            });

            var form = $('#submit_form');
            var error = $('.alert-danger', form);
            var success = $('.alert-success', form);

            form.validate({
                doNotHideMessage: true, //this option enables to show the error/success messages on tab switch.
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                rules: {
                    //account
                    username: {
                        minlength: 5,
                        required: true
                    },
                    password: {
                        minlength: 5,
                        required: true
                    },
                    rpassword: {
                        minlength: 5,
                        required: true,
                        equalTo: "#submit_form_password"
                    },
                    //profile
                    fullname: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    phone: {
                        required: true
                    },
                    gender: {
                        required: true
                    },
                    address: {
                        required: true
                    },
                    city: {
                        required: true
                    },
                    country: {
                        required: true
                    },
                    //payment
                    card_name: {
                        required: true
                    },
                    card_number: {
                        minlength: 16,
                        maxlength: 16,
                        required: true
                    },
                    card_cvc: {
                        digits: true,
                        required: true,
                        minlength: 3,
                        maxlength: 4
                    },
                    card_expiry_date: {
                        required: true
                    },
                    'payment[]': {
                        required: true,
                        minlength: 1
                    }
                },

                messages: { // custom messages for radio buttons and checkboxes
                    'payment[]': {
                        required: "Please select at least one option",
                        minlength: jQuery.validator.format("Please select at least one option")
                    }
                },

                errorPlacement: function (error, element) { // render error placement for each input type
                    if (element.attr("name") == "gender") { // for uniform radio buttons, insert the after the given container
                        error.insertAfter("#form_gender_error");
                    } else if (element.attr("name") == "payment[]") { // for uniform checkboxes, insert the after the given container
                        error.insertAfter("#form_payment_error");
                    } else {
                        error.insertAfter(element); // for other inputs, just perform default behavior
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

            var displayConfirm = function() {
                $('#tab4 .form-control-static', form).each(function(){
                    var input = $('[name="'+$(this).attr("data-display")+'"]', form);
                    if (input.is(":radio")) {
                        input = $('[name="'+$(this).attr("data-display")+'"]:checked', form);
                    }
                    if (input.is(":text") || input.is("textarea")) {
                        $(this).html(input.val());
                    } else if (input.is("select")) {
                        $(this).html(input.find('option:selected').text());
                    } else if (input.is(":radio") && input.is(":checked")) {
                        $(this).html(input.attr("data-title"));
                    } else if ($(this).attr("data-display") == 'payment[]') {
                        var payment = [];
                        $('[name="payment[]"]:checked', form).each(function(){
                            payment.push($(this).attr('data-title'));
                        });
                        $(this).html(payment.join("<br>"));
                    }
                });
            }

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

                if (current >= total) {
                    $('#form_wizard_1').find('.button-next').hide();
                    $('#form_wizard_1').find('.button-submit').show();
                    displayConfirm();
                } else {
                    $('#form_wizard_1').find('.button-next').show();
                    $('#form_wizard_1').find('.button-submit').hide();
                }
                Metronic.scrollTo($('.page-title'));
            }

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
                alert('Finished! Hope you like it :)');
            }).hide();

            //apply validation on select2 dropdown value change, this only needed for chosen dropdown integration.
            $('#country_list', form).change(function () {
                form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
