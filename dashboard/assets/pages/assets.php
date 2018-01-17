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
    $location = mysql_fetch_array(mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    ?>
    <div class="page-content">
        <h3 class="page-title">
            <strong>Assets</strong>
        </h3>
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>"><?php echo $location['location_name']; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a class="load_page" data-href="assets/pages/assets.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Assets">Assets</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title tabbable-line">
                        <div class="caption caption-md">
                            <i class="icon-wallet theme-font bold"></i>
                            <span class="caption-subject font-red bold uppercase"><?php echo $location['location_name']; ?></span> <span class="font-red">|</span>  <small>Assets</small>
                        </div>
                        <ul class="nav nav-tabs">
                            <li class="active">
                                <a href="#assets_tab" data-toggle="tab">Assets</a>
                            </li>
                            <li class="">
                                <a href="#accidents" data-toggle="tab">Accidents <i class="fa fa-frown-o"></i></a>
                            </li>
                        </ul>
                    </div>
                    <div class="portlet-body">
                        <div class="tab-content">
                            <div class="tab-pane" id="accidents">
                                <div class="portlet">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-file-o"></i> Asset Accidents
                                        </div>
                                        <div class="actions">
                                            <a class="btn default red-stripe show_form" data-show="#add_accident">
                                                <i class="fa fa-plus"></i>
                                                <span class="hidden-480">Add new accident</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="table-container">
                                            <form role="form" id="add_accidents">
                                                <table class="table table-striped table-bordered table-hover datatable" data-src="assets/app/api/assets.php?type=accidents&luid=<?php echo $_GET['luid']; ?>">
                                                    <thead>
                                                    <tr role="row" class="filter" style="display: none;" id="add_accident">
                                                        <td>
                                                            <input type="text" class="form-control input-sm datepick" name="datetime">
                                                        </td>
                                                        <td>
                                                            <select name="asset" class="form-control input-sm">
                                                                <option disabled selected value="">Select one..</option>
                                                                <?php
                                                                $assets = mysql_query("SELECT asset_id, asset_desc FROM fmo_locations_assets WHERE asset_location_token='".$_GET['luid']."' ORDER BY asset_desc ASC");
                                                                if(mysql_num_rows($assets) > 0){
                                                                    while($asset = mysql_fetch_assoc($assets)){
                                                                        ?>
                                                                        <option value="<?php echo $asset['asset_desc']; ?>"><?php echo $asset['asset_desc']; ?></option>
                                                                        <?php
                                                                    }
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                        <td class="form-inline">
                                                            <input type="text" class="form-control input-sm" style="width: 33%!important;" name="address" placeholder="Street">
                                                            <input type="text" class="form-control input-sm" style="width: 33%!important;" name="city" placeholder="City">
                                                            <select class="form-control input-sm" style="width: 33%!important;" name="state">
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
                                                        </td>
                                                        <td><input type="text" class="form-control input-sm" name="deaths" placeholder="0"></td>
                                                        <td><input type="text" class="form-control input-sm" name="nfi" placeholder="0"></td>
                                                        <td>
                                                            <select name="hazmat" class="form-control input-sm">
                                                                <option disabled selected value="">Select one..</option>
                                                                <option value="Yes">Yes</option>
                                                                <option value="No">No</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" class="form-control input-sm" name="driver" placeholder="<?php echo name($_SESSION['uuid']); ?>"></td>
                                                        <td>
                                                            <select name="copies" class="form-control input-sm">
                                                                <option disabled selected value="">Select one..</option>
                                                                <option value="Yes">Yes</option>
                                                                <option value="No">No</option>
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm red margin-bottom add_accident"><i class="fa fa-download"></i> Save</button>
                                                        </td>
                                                    </tr>
                                                    <tr role="row" class="heading">
                                                        <th style="width: 20%">
                                                            Date & Time
                                                        </th>
                                                        <th style="width: 8%">
                                                            Asset
                                                        </th>
                                                        <th>
                                                            Address (Street, City, State)
                                                        </th>
                                                        <th style="width: 8%">
                                                            Deaths
                                                        </th>
                                                        <th style="width: 8%">
                                                            Non-Fatal Injuries
                                                        </th>
                                                        <th style="width: 8%;">
                                                            Hazmat
                                                        </th>
                                                        <th style="width: 8%">
                                                            Driver in Question
                                                        </th>
                                                        <th style="width: 8%">
                                                            State/Insurance Records
                                                        </th>
                                                        <th style="width: 8%">Actions</th>
                                                    </tr>

                                                    </thead>
                                                    <tbody>

                                                    </tbody>
                                                </table>
                                            </form>
                                        </div>
                                        <p><span class="text-danger">*</span> Upload images/documents to the asset's record.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane active" id="assets_tab">
                                <div class="portlet">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            Click an assets name to view/edit
                                        </div>
                                        <div class="actions">
                                            <a class="btn default red-stripe" data-toggle="modal" href="#add_asset">
                                                <i class="fa fa-plus"></i>
                                                <span class="hidden-480">Add new asset</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <?php
                                        $assets = mysql_query("SELECT asset_id, asset_type, asset_desc, asset_vin, asset_year, asset_make, asset_model, asset_color, asset_dop, asset_price, asset_tire_size, asset_agent, asset_plate, asset_by_user_token, asset_timestamp, asset_last_dot_inspec, asset_comments, asset_location_token FROM fmo_locations_assets WHERE asset_location_token='".$_GET['luid']."' ORDER BY asset_desc ASC");
                                        if(mysql_num_rows($assets) > 0){
                                            $pk = 0;
                                            while($asset = mysql_fetch_assoc($assets)){
                                                $pk++
                                                ?>
                                                <div id="asset_h_<?php echo $pk; ?>" class="panel-group">
                                                    <div class="panel panel-default">
                                                        <div class="panel-heading">
                                                            <div class="actions pull-right" style="margin-top: -6px; margin-right: -9px">
                                                                <a href="javascript:;" class="btn btn-default btn-sm edit" data-edit="as_<?php echo $asset['asset_id']; ?>" data-reload="">
                                                                    <i class="fa fa-pencil"></i> <span class="hidden-sm hidden-md hidden-xs">Edit</span> </a>
                                                            </div>
                                                            <div class="caption">
                                                                <h4 class="panel-title">
                                                                    <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#asset_h_<?php echo $pk; ?>" href="#asset_<?php echo $pk; ?>" aria-expanded="false"><?php echo $asset['asset_desc']; ?> - <strong><?php echo $asset['asset_year']." ".$asset['asset_make']." ".$asset['asset_model']; ?></strong> - <?php echo $asset['asset_vin']; ?></a>
                                                                </h4>
                                                            </div>
                                                        </div>
                                                        <div id="asset_<?php echo $pk; ?>" class="panel-collapse collapse" aria-expanded="true" style="height: 0px;">

                                                            <div class="panel-body">
                                                                <div class="scroller">
                                                                    <div class="row">
                                                                        <div class="col-md-4 col-sm-12 col-xs-12">
                                                                            <div class="well">
                                                                                <address>
                                                                                    Asset Type:
                                                                                    <strong>
                                                                                        <a class="as_<?php echo $asset['asset_id']; ?>" style="color:#333333" data-name="asset_type" data-pk="<?php echo $asset['asset_id']; ?>" data-type="select" data-source="[{value: 'Moving Truck', text: 'Moving Truck'},{value: 'Office Car', text: 'Office Car'},{value: 'Other', text: 'Other'}]" data-inputclass="form-control" data-placement="right" data-title="Select new type.." data-url="assets/app/update_settings.php?update=assets">
                                                                                            <?php echo $asset['asset_type']; ?>
                                                                                        </a><br/>
                                                                                    </strong>
                                                                                    Asset Unit Number:
                                                                                    <?php
                                                                                    if($_SESSION['group'] == 1){
                                                                                        ?>
                                                                                        <strong>
                                                                                            <a class="as_<?php echo $asset['asset_id']; ?>" style="color:#333333" data-name="asset_desc" data-pk="<?php echo $asset['asset_id']; ?>" data-type="text" data-placement="right" data-title="Enter new description.." data-url="assets/app/update_settings.php?update=assets">
                                                                                                <?php echo $asset['asset_desc']; ?>
                                                                                            </a><br/>
                                                                                        </strong>
                                                                                        <?php
                                                                                    } else {
                                                                                        ?>
                                                                                        <?php echo $asset['asset_desc']; ?><br/>
                                                                                        <?php
                                                                                    }
                                                                                    ?>

                                                                                    Asset VIN Number:
                                                                                    <strong>
                                                                                        <a class="as_<?php echo $asset['asset_id']; ?>" style="color:#333333" data-name="asset_vin" data-pk="<?php echo $asset['asset_id']; ?>" data-type="text" data-placement="right" data-title="Enter new vin.." data-url="assets/app/update_settings.php?update=assets">
                                                                                            <?php echo $asset['asset_vin']; ?>
                                                                                        </a><br/>
                                                                                    </strong>
                                                                                    <?php
                                                                                    if($_SESSION['group'] == 1){
                                                                                        ?>
                                                                                        Asset Location:
                                                                                        <strong>
                                                                                            <?php
                                                                                            $location      = mysql_fetch_array(mysql_query("SELECT location_name, location_state FROM fmo_locations WHERE location_token='".mysql_real_escape_string($asset['asset_location_token'])."'"));
                                                                                            $findLocations = mysql_query("SELECT location_name, location_token, location_state FROM fmo_locations WHERE location_owner_company_token='".$_SESSION['cuid']."' ORDER BY location_name ASC");
                                                                                            if(mysql_num_rows($findLocations) > 0){
                                                                                                $selectData = NULL;
                                                                                                while($loc = mysql_fetch_assoc($findLocations)){
                                                                                                    $selectData .= "{value: '".$loc['location_token']."', text: '".$loc['location_name']." (".$loc['location_state'].")'},";
                                                                                                }
                                                                                            }
                                                                                            ?>
                                                                                            <a class="as_<?php echo $asset['asset_id']; ?>" style="color:#333333" data-name="asset_location_token" data-pk="<?php echo $asset['asset_id']; ?>" data-type="select" data-source="[<?php echo $selectData; ?>]" data-inputclass="form-control" data-placement="right" data-title="Select new type.." data-url="assets/app/update_settings.php?update=assets">
                                                                                                <?php echo $location['location_name']." (".$location['location_state'].")"; ?>
                                                                                            </a><br/>
                                                                                        </strong>
                                                                                        <?php
                                                                                    }
                                                                                    ?>


                                                                                    <br/>

                                                                                    Year:
                                                                                    <strong>
                                                                                        <a class="as_<?php echo $asset['asset_id']; ?>" style="color:#333333" data-name="asset_year" data-pk="<?php echo $asset['asset_id']; ?>" data-type="number" data-placement="right" data-title="Enter new vehicle year.." data-url="assets/app/update_settings.php?update=assets">
                                                                                            <?php echo $asset['asset_year']; ?>
                                                                                        </a><br/>
                                                                                    </strong>
                                                                                    Make:
                                                                                    <strong>
                                                                                        <a class="as_<?php echo $asset['asset_id']; ?>" style="color:#333333" data-name="asset_make" data-pk="<?php echo $asset['asset_id']; ?>" data-type="text" data-placement="right" data-title="Enter new vehicle make.." data-url="assets/app/update_settings.php?update=assets">
                                                                                            <?php echo $asset['asset_make']; ?>
                                                                                        </a><br/>
                                                                                    </strong>
                                                                                    Model:
                                                                                    <strong>
                                                                                        <a class="as_<?php echo $asset['asset_id']; ?>" style="color:#333333" data-name="asset_model" data-pk="<?php echo $asset['asset_id']; ?>" data-type="text" data-placement="right" data-title="Enter new vehicle model.." data-url="assets/app/update_settings.php?update=assets">
                                                                                            <?php echo $asset['asset_model']; ?>
                                                                                        </a><br/>
                                                                                    </strong>
                                                                                    Color:
                                                                                    <strong>
                                                                                        <a class="as_<?php echo $asset['asset_id']; ?>" style="color:#333333" data-name="asset_color" data-pk="<?php echo $asset['asset_id']; ?>" data-type="text" data-placement="right" data-title="Enter new vehicle color.." data-url="assets/app/update_settings.php?update=assets">
                                                                                            <?php echo $asset['asset_color']; ?>
                                                                                        </a><br/>
                                                                                    </strong>

                                                                                    <br/>

                                                                                    Purchase Date:
                                                                                    <strong>
                                                                                        <a class="as_<?php echo $asset['asset_id']; ?>" style="color:#333333" data-name="asset_dop" data-pk="<?php echo $asset['asset_id']; ?>" data-type="date" data-format="mm/dd/yyyy" data-placement="right" data-title="Select new date of purchase.." data-url="assets/app/update_settings.php?update=assets">
                                                                                            <?php echo $asset['asset_dop']; ?>
                                                                                        </a><br/>
                                                                                    </strong>
                                                                                    Purchase Price:
                                                                                    <strong>
                                                                                        <a class="as_<?php echo $asset['asset_id']; ?>" style="color:#333333" data-name="asset_price" data-pk="<?php echo $asset['asset_id']; ?>" data-type="number" data-placement="right" data-title="Enter new price.." data-url="assets/app/update_settings.php?update=assets">
                                                                                            <?php echo $asset['asset_price']; ?>
                                                                                        </a><br/>
                                                                                    </strong>

                                                                                    <br/>

                                                                                    Tire Size:
                                                                                    <strong>
                                                                                        <a class="as_<?php echo $asset['asset_id']; ?>" style="color:#333333" data-name="asset_tire_size" data-pk="<?php echo $asset['asset_id']; ?>" data-type="text" data-placement="right" data-title="Enter new tire size.." data-url="assets/app/update_settings.php?update=assets">
                                                                                            <?php echo $asset['asset_tire_size']; ?>
                                                                                        </a><br/>
                                                                                    </strong>
                                                                                    Insurance Agent:
                                                                                    <strong>
                                                                                        <a class="as_<?php echo $asset['asset_id']; ?>" style="color:#333333" data-name="asset_agent" data-pk="<?php echo $asset['asset_id']; ?>" data-type="text" data-placement="right" data-title="Enter new agent.." data-url="assets/app/update_settings.php?update=assets">
                                                                                            <?php echo $asset['asset_agent']; ?>
                                                                                        </a><br/>
                                                                                    </strong>
                                                                                    Plate Number:
                                                                                    <strong>
                                                                                        <a class="as_<?php echo $asset['asset_id']; ?>" style="color:#333333" data-name="asset_plate" data-pk="<?php echo $asset['asset_id']; ?>" data-type="text" data-placement="right" data-title="Enter new plate number.." data-url="assets/app/update_settings.php?update=assets">
                                                                                            <?php echo $asset['asset_plate']; ?>
                                                                                        </a><br/>
                                                                                    </strong>
                                                                                    Last DOT Inspection:
                                                                                    <strong>
                                                                                        <a class="as_<?php echo $asset['asset_id']; ?>" style="color:#333333" data-name="asset_last_dot_inspec" data-pk="<?php echo $asset['asset_id']; ?>" data-type="date" data-placement="right" data-title="Select last DOT inspection date.." data-url="assets/app/update_settings.php?update=assets">
                                                                                            <?php echo $asset['asset_last_dot_inspec']; ?>
                                                                                        </a><br/>
                                                                                    </strong>

                                                                                    <br/>

                                                                                    Comments:
                                                                                    <strong>
                                                                                        <a class="as_<?php echo $asset['asset_id']; ?>" style="color:#333333" data-name="asset_comments" data-pk="<?php echo $asset['asset_id']; ?>" data-type="text" data-placement="right" data-title="Enter new comments.." data-url="assets/app/update_settings.php?update=assets">
                                                                                            <?php echo $asset['asset_comments']; ?>
                                                                                        </a><br/>
                                                                                    </strong>
                                                                                </address>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-8 col-sm-12 col-xs-12">
                                                                            <div class="tabbable-line">
                                                                                <ul class="nav nav-tabs ">
                                                                                    <li class="active">
                                                                                        <a href="#documents_<?php echo $asset['asset_id']; ?>" data-toggle="tab" aria-expanded="true">Images/Documents </a>
                                                                                    </li>
                                                                                    <li class="">
                                                                                        <a href="#maintenance_<?php echo $asset['asset_id']; ?>" data-toggle="tab" aria-expanded="false">Maintenance Records </a>
                                                                                    </li>
                                                                                    <li class="">
                                                                                        <a href="#location_<?php echo $asset['asset_id']; ?>" data-toggle="tab" aria-expanded="false">Location </a>
                                                                                    </li>
                                                                                </ul>
                                                                                <div class="tab-content">
                                                                                    <div class="tab-pane active" id="documents_<?php echo $asset['asset_id']; ?>">
                                                                                        <div class="portlet">
                                                                                            <div class="portlet-title">
                                                                                                <div class="caption">
                                                                                                    <i class="fa fa-file-o"></i> <small><span class="font-red">|</span> Missing files: <span class="font-red">Registration, Plate Copy</span>.</small>
                                                                                                </div>
                                                                                                <div class="actions">
                                                                                                    <a class="btn default red-stripe show_form" data-show="#add_document_<?php echo $asset['asset_id']; ?>">
                                                                                                        <i class="fa fa-plus"></i>
                                                                                                        <span class="hidden-480">Upload new document</span>
                                                                                                    </a>
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="portlet-body">
                                                                                                <div class="table-container">
                                                                                                    <form role="form" id="add_documents_<?php echo $asset['asset_id']; ?>">
                                                                                                        <table class="table table-striped table-bordered table-hover datatable" id="a_docs_<?php echo $asset['asset_id']; ?>" data-src="assets/app/api/assets.php?type=documents&id=<?php echo $asset['asset_id']; ?>">
                                                                                                            <thead>
                                                                                                            <tr role="row" class="filter" style="display: none;" id="add_document_<?php echo $asset['asset_id']; ?>">
                                                                                                                <td><input type="file" class="form-control input-sm" name="file"></td>
                                                                                                                <td>
                                                                                                                    <div class="form-group">
                                                                                                                        <div class="col-md-6">
                                                                                                                            <select class="form-control input-sm" name="file_type">
                                                                                                                                <option disabled selected value="">Select one..</option>
                                                                                                                                <option value="Registration">Registration</option>
                                                                                                                                <option value="Plate Copy">Plate Copy</option>
                                                                                                                                <option value="Other">Other</option>
                                                                                                                            </select>
                                                                                                                        </div>
                                                                                                                        <div class="col-md-6">
                                                                                                                            <input type="text" class="form-control form-filter input-sm" name="file_desc">
                                                                                                                        </div>
                                                                                                                    </div>
                                                                                                                </td>
                                                                                                                <td>
                                                                                                                    <button type="button" class="btn btn-sm red margin-bottom add_document" data-id="<?php echo $asset['asset_id']; ?>"><i class="fa fa-download"></i> Save</button>
                                                                                                                </td>
                                                                                                            </tr>
                                                                                                            <tr role="row" class="heading">
                                                                                                                <th width="40%">
                                                                                                                    File Thumbnail
                                                                                                                </th>
                                                                                                                <th>
                                                                                                                    File Type & Description
                                                                                                                </th>
                                                                                                                <th width="12%">
                                                                                                                    Submitted by
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
                                                                                    </div>
                                                                                    <div class="tab-pane" id="maintenance_<?php echo $asset['asset_id']; ?>">
                                                                                        <table class="table table-striped table-bordered table-hover datatable" data-src="assets/app/api/assets.php?type=maintenance&id=<?php echo $asset['asset_id']; ?>">
                                                                                            <thead>
                                                                                                <tr role="row" class="heading">
                                                                                                    <th>
                                                                                                        Date
                                                                                                    </th>
                                                                                                    <th>
                                                                                                        Record Type, Description, By
                                                                                                    </th>
                                                                                                    <th>
                                                                                                        Cost
                                                                                                    </th>
                                                                                                    <th>
                                                                                                        PO Number
                                                                                                    </th>
                                                                                                    <th>
                                                                                                        Record By
                                                                                                    </th>
                                                                                                </tr>
                                                                                            </thead>
                                                                                            <tbody>

                                                                                            </tbody>
                                                                                        </table>
                                                                                    </div>
                                                                                    <div class="tab-pane" id="location_<?php echo $asset['asset_id']; ?>">
                                                                                        <div id="asset_map" class="gmaps asset_map" style="height: 350px;">
                                                                                        </div>
                                                                                    </div>
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
                                                <strong>No assets added yet!</strong> Add new assets to see them appear here.
                                            </div>
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
        </div>
    </div>
    <form method="POST" action="" role="form" id="new_assets">
        <div class="modal fade bs-modal-lg" id="add_asset" tabindex="-1" role="basic" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content box red">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                        <h3 class="modal-title font-bold">Add new asset</h3>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Asset Type</label>
                                    <select class="form-control" name="type">
                                        <option disabled selected value="">Select one..</option>
                                        <option value="Moving Truck">Moving Truck</option>
                                        <option value="Office Car">Office Car</option>
                                        <option value="Trailer">Trailer</option>
                                        <option value="Other">Other</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Asset Unit Number</label>
                                    <input type="text" class="form-control" readonly value="This will be automatically generated when you are finished.">
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Vehicle VIN Number</label>
                                    <input type="text" class="form-control" name="vin" placeholder="P7192B38B3819N3D391">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Vehicle Year</label>
                                    <input type="number" class="form-control" name="year" placeholder="2007">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Vehicle Make</label>
                                    <input type="text" class="form-control" name="make" placeholder="FORD">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Vehicle Model</label>
                                    <input type="text" class="form-control" name="model" placeholder="CROWN VICTORIA">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Vehicle Color</label>
                                    <input type="text" class="form-control" name="color" placeholder="BLACK">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Date of Purchase</label>
                                    <input type="text" class="form-control date-picker" name="date_of_purchase" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Purchase Price</label>
                                    <input type="number" class="form-control" name="price" placeholder="">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Tire Size</label>
                                    <input type="text" class="form-control" name="tire_size" placeholder="">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Insurance Agent</label>
                                    <input type="text" class="form-control" name="agent">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Plate Number</label>
                                    <input type="text" class="form-control" name="plate">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>Asset Comments</label>
                                    <input type="text" class="form-control" name="comments" placeholder="Has awesome tires, runs like a race-horse.">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label>Last DOT Inspection</label>
                                    <input type="text" class="form-control date-picker" name="last_dot_inspec" placeholder="">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn red">Save new asset</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <script>
        $(document).ready(function() {
            $('.scroller').slimScroll({
                height: 300
            })
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
            $('.show_form').on('click', function() {
                var show = $(this).attr('data-show');

                $(show).show();
            });
            $('.date-picker').datepicker({
                orientation: "left",
                autoclose: true
            });
            $("#add_documents").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    file: {
                        required: true
                    },
                    file_type: {
                        required: true
                    },
                    file_desc: {
                        required: true
                    }
                }
            });
            $("#add_accidents").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'font-red', // default input error message class
                rules: {
                    datetime: {
                        required: true
                    },
                    asset: {
                        required: true
                    },
                    address: {
                        required: true
                    },
                    city: {
                        required: true
                    },
                    state: {
                        required: true
                    },
                    deaths: {
                        required: true
                    },
                    nfi: {
                        required: true
                    },
                    hazmat: {
                        required: true
                    },
                    driver: {
                        required: true
                    },
                    copies: {
                        required: true
                    }
                }
            });
            $('#new_assets').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                    type: {
                        required: true
                    },
                    desc: {
                        required: true
                    },
                    vin: {
                        required: true
                    },
                    year: {
                        required: true
                    },
                    make: {
                        required: true
                    },
                    model: {
                        required: true
                    },
                    color: {
                        required: true
                    },
                    date_of_purchase: {
                        required: true
                    },
                    price: {
                        required: true
                    }
                },

                invalidHandler: function(event, validator) { //display error alert on form submit

                },

                highlight: function(element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').addClass('has-error'); // set error class to the control group
                },

                success: function(label) {
                    label.closest('.form-group').removeClass('has-error');
                    label.remove();
                },


                submitHandler: function(form) {
                    $.ajax({
                        url: 'assets/app/add_setting.php?setting=asset&luid=<?php echo $_GET['luid']; ?>&cuid=<?php echo $_SESSION['cuid']; ?>',
                        type: "POST",
                        data: $('#new_assets').serialize(),
                        success: function(data) {
                            $('#new_asset').modal('hide')
                            $('#new_assets')[0].reset();
                            toastr.success("<strong>Logan says</strong>:<br/>That asset has been added to the companies record. I need to refresh the page to show you the new changes.");
                            $.ajax({
                                url: 'assets/pages/assets.php?luid=<?php echo $_GET['luid']; ?>',
                                success: function(data) {
                                    $('#page_content').html(data);
                                },
                                error: function() {
                                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                }
                            });
                        },
                        error: function() {
                            toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                        }
                    });
                }
            });
            $('.add_document').on('click', function(){
                var id = $(this).attr('data-id');
                if($("#add_documents_"+id).valid()){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=asset_doc&id="+id,
                        type: "POST",
                        data: new FormData($('#add_documents_'+id)[0]),
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        encode: true,
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>Document has been added to assets document table.');
                            $('#a_docs_'+id).DataTable().ajax.reload();
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                }
            });
            $('.add_accident').on('click', function(){
                if($("#add_accidents").valid()){
                    $.ajax({
                        url: "assets/app/add_setting.php?setting=accident&luid=<?php echo $_GET['luid']; ?>",
                        type: "POST",
                        data: new FormData($('#add_accidents')[0]),
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            toastr.info('<strong>Logan says</strong>:<br/>Accident has been added to this locations accident table.');
                            $('#add_accidents')[0].reset();
                            $('#add_accident').hide();
                        },
                        error: function() {
                            toastr.error('<strong>Logan says</strong>:<br/>That page didnt respond correctly. Try again, or create a support ticket for help.');
                        }
                    });
                }
            });
            $('.datepick').datetimepicker();
        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
