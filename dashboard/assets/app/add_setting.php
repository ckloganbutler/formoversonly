<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/9/2017
 * Time: 2:46 AM
 */
session_start();
include 'init.php';

if(isset($_GET['setting'])){
    if($_GET['setting'] == 'claimImage'){
        $fileName  = struuid();
        $file_ext = substr($_FILES['file']['name'], strripos($_FILES['file']['name'], '.'));
        $uploaddir = '../upload/claims/';
        $uploadfile = $uploaddir . $fileName;

        move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile . $file_ext);
        $link = "//www.formoversonly.com/dashboard/assets/upload/claims/". $fileName . $file_ext;


        mysql_query("INSERT INTO fmo_locations_events_claims_images (
        image_event_token, 
        image_link
        ) VALUES (
        '" . mysql_real_escape_string($_GET['ev']) . "',
        '" . mysql_real_escape_string($link) . "')") or die(mysql_error());
    }
    if($_GET['setting'] == 'review'){
        $token          = $_GET['ev'];
        $rating         = $_POST['rating'];
        $comments       = $_POST['comments'];
        $name           = $_POST['name'];
        $anonymous      = $_POST['anonymous'];

        $event_user = mysql_fetch_array(mysql_query("SELECT event_user_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));

        mysql_query("INSERT INTO fmo_locations_events_reviews (review_event_token, review_rating, review_comments, review_name, review_anonymous) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($rating)."',
        '".mysql_real_escape_string($comments)."',
        '".mysql_real_escape_string($name)."',
        '".mysql_real_escape_string($anonymous)."')");
        timeline_event($token, $event_user['event_user_token'], "Review", "<strong>".name($event_user['event_user_token'])."</strong> submitted a new review.");
    }
    if($_GET['setting'] == 'claim'){
        $token          = $_GET['ev'];
        $item           = $_POST['item'];
        $padded         = $_POST['padded'];
        $weight         = $_POST['weight'];
        $comments       = $_POST['comments'];
        $remote_ip      = $_SERVER['REMOTE_ADDR'];

        $event_user = mysql_fetch_array(mysql_query("SELECT event_user_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));

        mysql_query("INSERT INTO fmo_locations_events_claims (claim_event_token, claim_item, claim_padded, claim_weight, claim_comments, claim_remote_addr) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($item)."',
        '".mysql_real_escape_string($padded)."',
        '".mysql_real_escape_string($weight)."',
        '".mysql_real_escape_string($comments)."',
        '".mysql_real_escape_string($remote_ip)."')");
        timeline_event($token, $event_user['event_user_token'], "Claim", "<strong>".name($event_user['event_user_token'])."</strong> submitted a new claim.");
    }
    if($_GET['setting'] == 'laborer'){
        $token          = $_GET['ev'];
        $role           = $_POST['role'];
        $laborer        = $_POST['laborer'];
        $wage           = mysql_fetch_array(mysql_query("SELECT user_employer_rate, user_phone FROM fmo_users WHERE user_token='".mysql_real_escape_string($laborer)."'"));
        $desc           = $_POST['desc'];
        $hp             = $_POST['hp'];
        $tip            = $_POST['tip'];

        $date           = $_POST['date'];
        $time           = explode("to", $_POST['time']);
        $location       = mysql_fetch_array(mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".$_GET['luid']."'"));

        mysql_query("INSERT INTO fmo_locations_events_laborers (laborer_event_token, laborer_user_token, laborer_rate, laborer_role, laborer_desc, laborer_hours_worked, laborer_tip, laborer_by_user_token) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($laborer)."',
        '".mysql_real_escape_string($wage['user_employer_rate'])."',
        '".mysql_real_escape_string($role)."',
        '".mysql_real_escape_string($desc)."',
        '".mysql_real_escape_string($hp)."',
        '".mysql_real_escape_string($tip)."',
        '".mysql_real_escape_string($_SESSION['uuid'])."')");
        _sendText($wage['user_phone'], "[Here To There]:\r\n".$location['location_name']." - ".$time[0]." on ".date("M d, Y", strtotime($date))."\r\nYou've been added to job, get to work!");
    }
    if($_GET['setting'] == 'usr_lic'){
        $token          = $_GET['uuid'];
        $type           = $_POST['type'];
        $state          = $_POST['state'];
        $prefix         = $_POST['prefix'];
        $number         = $_POST['number'];

        mysql_query("INSERT INTO fmo_users_licenses (license_user_token, license_type, license_state, license_prefix, license_number) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($type)."',
        '".mysql_real_escape_string($state)."',
        '".mysql_real_escape_string($prefix)."',
        '".mysql_real_escape_string($number)."')");
    }
    if($_GET['setting'] == 'marketer'){
        $location       = $_GET['luid'];
        $type           = $_POST['type'];
        $contact        = $_POST['fullname'];
        $phone          = $_POST['phone'];
        $email          = $_POST['email'];
        $address        = $_POST['address'];
        $address2       = $_POST['address2'];
        $city           = $_POST['city'];
        $state          = $_POST['state'];
        $apt            = $_POST['apt'];
        $zip            = $_POST['zip'];
        $company        = $_POST['company'];

        mysql_query("INSERT INTO fmo_locations_marketers (marketer_location_token, marketer_type, marketer_contact, marketer_phone, marketer_email, marketer_address, marketer_address2, marketer_city, marketer_state, marketer_apt, marketer_zip, marketer_company, marketer_by_user_token) VALUES (
        '".mysql_real_escape_string($location)."',
        '".mysql_real_escape_string($type)."',
        '".mysql_real_escape_string($contact)."',
        '".mysql_real_escape_string($phone)."',
        '".mysql_real_escape_string($email)."',
        '".mysql_real_escape_string($address)."',
        '".mysql_real_escape_string($address2)."',
        '".mysql_real_escape_string($city)."',
        '".mysql_real_escape_string($state)."',
        '".mysql_real_escape_string($apt)."',
        '".mysql_real_escape_string($zip)."',
        '".mysql_real_escape_string($company)."',
        '".mysql_real_escape_string($_SESSION['uuid'])."')") or die(mysql_error());
    }
    if($_GET['setting'] == 'asset'){
        $token          = $_GET['luid'];
        $type           = $_POST['type'];
        $vin            = $_POST['vin'];
        $year           = $_POST['year'];
        $make           = $_POST['make'];
        $model          = $_POST['model'];
        $color          = $_POST['color'];
        $dop            = $_POST['date_of_purchase'];
        $price          = $_POST['price'];
        $tire_size      = $_POST['tire_size'];
        $agent          = $_POST['agent'];
        $plate          = $_POST['plate'];
        $comments       = $_POST['comments'];
        $last_dot_inspec= $_POST['last_dot_inspec'];
        $by             = $_SESSION['uuid'];

        $type_query   = mysql_query("SELECT asset_id FROM fmo_locations_assets WHERE asset_location_token='".mysql_real_escape_string($token)."' AND asset_type='".mysql_real_escape_string($type)."'");
        $unit_id      = mysql_num_rows($type_query) + 1;
        if($type == 'Moving Truck'){
            $unit_name = 'MT';
        }elseif($type == 'Office Car'){
            $unit_name = 'OV';
        }elseif($type == 'Trailer'){
            $unit_name = 'T';
        }elseif($type == 'Other'){
            $unit_name = 'O';
        }
        $unit_number = $unit_name.$unit_id;

        mysql_query("INSERT INTO fmo_locations_assets (asset_location_token, asset_type, asset_desc, asset_vin, asset_year, asset_make, asset_model, asset_color, asset_dop, asset_price, asset_tire_size, asset_agent, asset_plate, asset_comments, asset_last_dot_inspec, asset_by_user_token) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($type)."',
        '".mysql_real_escape_string($unit_number)."',
        '".mysql_real_escape_string($vin)."',
        '".mysql_real_escape_string($year)."',
        '".mysql_real_escape_string($make)."',
        '".mysql_real_escape_string($model)."',
        '".mysql_real_escape_string($color)."',
        '".mysql_real_escape_string($dop)."',
        '".mysql_real_escape_string($price)."',
        '".mysql_real_escape_string($tire_size)."',
        '".mysql_real_escape_string($agent)."',
        '".mysql_real_escape_string($plate)."',
        '".mysql_real_escape_string($comments)."',
        '".mysql_real_escape_string($last_dot_inspec)."',
        '".mysql_real_escape_string($by)."')") or die(mysql_error());
    }
    if($_GET['setting'] == 'document'){
        $token = $_GET['uuid'];
        $type  = $_POST['file_type'];
        $desc  = $_POST['file_desc'];
        $by    = $_SESSION['uuid'];
        $fileName  = struuid();
        $file_ext = substr($_FILES['file']['name'], strripos($_FILES['file']['name'], '.'));
        $uploaddir = '../upload/documents/';
        $uploadfile = $uploaddir . $fileName;

        move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile . $file_ext);
        $link = "//www.formoversonly.com/dashboard/assets/upload/documents/". $fileName . $file_ext;

        $check   = mysql_query("SELECT document_id FROM fmo_users_employee_documents WHERE document_user_token='".mysql_real_escape_string($token)."' AND document_type='".mysql_real_escape_string($type)."'");
        $checked = mysql_num_rows($check);

        if($checked > 0){
            $id = mysql_fetch_array($check);
            mysql_query("UPDATE fmo_users_employee_documents SET document_link='".mysql_real_escape_string($link)."', document_desc='".mysql_real_escape_string($desc)."', document_by_user_token='".mysql_real_escape_string($by)."' WHERE document_id='".mysql_real_escape_string($id['document_id'])."'");
        } else {
            mysql_query("INSERT INTO fmo_users_employee_documents (document_user_token, document_type, document_desc, document_link, document_by_user_token) VALUES (
            '".mysql_real_escape_string($token)."',
            '".mysql_real_escape_string($type)."',
            '".mysql_real_escape_string($desc)."',
            '".mysql_real_escape_string($link)."',
            '".mysql_real_escape_string($by)."')") or die(mysql_error());
        }
    }
    if($_GET['setting'] == 'asset_doc'){
        $id    = $_GET['id'];
        $type  = $_POST['file_type'];
        $desc  = $_POST['file_desc'];
        $by    = $_SESSION['uuid'];
        $fileName  = struuid();
        $file_ext = substr($_FILES['file']['name'], strripos($_FILES['file']['name'], '.'));
        $uploaddir = '../upload/asset_docs/';
        $uploadfile = $uploaddir . $fileName;

        move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile . $file_ext);
        $link = "//www.formoversonly.com/dashboard/assets/upload/asset_docs/". $fileName . $file_ext;

        $check   = mysql_query("SELECT document_id FROM fmo_locations_assets_documents WHERE document_asset_id='".mysql_real_escape_string($id)."' AND document_type='".mysql_real_escape_string($type)."'");
        $checked = mysql_num_rows($check);

        if($checked > 0){
            $id = mysql_fetch_array($check);
            mysql_query("UPDATE fmo_locations_assets_documents SET document_link='".mysql_real_escape_string($link)."', document_desc='".mysql_real_escape_string($desc)."', document_by_user_token='".mysql_real_escape_string($by)."' WHERE document_id='".mysql_real_escape_string($id['document_id'])."'");
        } else {
            mysql_query("INSERT INTO fmo_locations_assets_documents (document_asset_id, document_type, document_desc, document_link, document_by_user_token) VALUES (
            '".mysql_real_escape_string($id)."',
            '".mysql_real_escape_string($type)."',
            '".mysql_real_escape_string($desc)."',
            '".mysql_real_escape_string($link)."',
            '".mysql_real_escape_string($by)."')") or die(mysql_error());
        }
    }
    if($_GET['setting'] == 'childsupport'){
        $token          = $_GET['uuid'];
        $case_name      = $_POST['case_name'];
        $case_number    = $_POST['case_number'];
        $amount         = $_POST['amount'];
        $address        = $_POST['address'];
        $address2       = $_POST['address2'];
        $city           = $_POST['city'];
        $state          = $_POST['state'];
        $zip            = $_POST['zip'];
        $pay_allowed    = $_POST['pay_allowed'];
        $pay_period     = $_POST['pay_period'];
        $comments       = $_POST['comments'];
        $by             = $_SESSION['uuid'];

        mysql_query("INSERT INTO fmo_users_employee_childsupports (childsupport_user_token, childsupport_case_name, childsupport_case_number, childsupport_amount, childsupport_address, childsupport_address2, childsupport_city, childsupport_state, childsupport_zip, childsupport_pay_allowed, childsupport_pay_period, childsupport_comments, childsupport_by_user_token) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($case_name)."',
        '".mysql_real_escape_string($case_number)."',
        '".mysql_real_escape_string($amount)."',
        '".mysql_real_escape_string($address)."',
        '".mysql_real_escape_string($address2)."',
        '".mysql_real_escape_string($city)."',
        '".mysql_real_escape_string($state)."',
        '".mysql_real_escape_string($zip)."',
        '".mysql_real_escape_string($pay_allowed)."',
        '".mysql_real_escape_string($pay_period)."',
        '".mysql_real_escape_string($comments)."',
        '".mysql_real_escape_string($by)."')") or die(mysql_error());
        timeline_log($token, $by, "Child Support Case", "Case <strong>$case_name</strong> created. Comments: <strong>$comments</strong>");
    }
    if($_GET['setting'] == 'usr_advance'){
        $token      = $_GET['uuid'];
        $advance    = $_POST['requested'];
        $available  = $_POST['available'];
        $reasoning  = $_POST['reasoning'];
        $by         = $_SESSION['uuid'];

        mysql_query("INSERT INTO fmo_users_employee_advances (advance_user_token, advance_requested, advance_available, advance_reason, advance_by_user_token) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($advance)."',
        '".mysql_real_escape_string($available)."',
        '".mysql_real_escape_string($reasoning)."',
        '".mysql_real_escape_string($by)."')");
        $id = mysql_insert_id();
        timeline_log($token, $by, "Advance", name($by)." authorized loan for: $<strong>".$advance."</strong>/$<strong>".$available."</strong>");
        $user_pay = mysql_fetch_array(mysql_query("SELECT user_employer_rate FROM fmo_users WHERE user_token='".mysql_real_escape_string($token)."'"));
        if($user_pay['user_employer_rate'] > 0){
            $refStart                = new DateTime('2017-01-02');
            $periodLength            = 14;
            $now                     = new DateTime();
            $cur                     = date('Y-m-d');
            $daysIntoCurrentPeriod   = (int)$now->diff($refStart)->format('%a') % $periodLength;
            $currentPeriodStart      = clone $now;
            $currentPeriodStart->sub(new DateInterval('P'.$daysIntoCurrentPeriod.'D'));
            $start                   = date("Y-m-d", strtotime($cur." -".$daysIntoCurrentPeriod." days"));
            $end                     = date('Y-m-d', strtotime($start." +13 days"));
            $hours = array();
            $prev  = mysql_query("
                            SELECT advance_requested FROM fmo_users_employee_advances
                            WHERE (advance_timestamp>='".mysql_real_escape_string($start)."' AND advance_timestamp<'".mysql_real_escape_string($end)."') AND advance_user_token='".mysql_real_escape_string($token)."'");
            $hours = mysql_query("
                            SELECT timeclock_user, timeclock_hours FROM fmo_users_employee_timeclock 
                            WHERE (timeclock_clockout>='".mysql_real_escape_string($start)."' AND timeclock_clockout<'".mysql_real_escape_string($end)."') AND timeclock_user='".mysql_real_escape_string($token)."'") or die(mysql_error());
            $misc_hours = mysql_query("SELECT laborer_hours_worked FROM fmo_locations_events_laborers WHERE (laborer_timestamp>='".mysql_real_escape_string($start)."' AND laborer_timestamp<'".mysql_real_escape_string($end)."') AND laborer_user_token='".mysql_real_escape_string($token)."'");
            $pay = array();
            if(mysql_num_rows($hours) > 0 || mysql_num_rows($misc_hours) > 0){
                while($work = mysql_fetch_assoc($hours)){
                    $pay['hours']+=$work['timeclock_hours'];
                } while ($misc_work = mysql_fetch_assoc($misc_hours)){
                    $pay['hours']+=$misc_work['laborer_hours_worked'];
                }
                if($pay['hours'] > 0){
                    $pay['rate']      = $user_pay['user_employer_rate'];
                    $pay['earned']    = $pay['hours'] * $user_pay['user_employer_rate'];
                    if(mysql_num_rows($prev) > 0){
                        while($loans = mysql_fetch_assoc($prev)){
                            $pay['loans'] += $loans['advance_requested'];
                        }
                    } else {$pay['loans'] = 0;}
                    $pay['available'] = number_format(($pay['earned'] * .25) - $pay['loans'], 2);
                } else {
                    $pay['available'] = 0;
                    $pay['hours']     = 0;
                    $pay['earned']    = 0;
                }
            } else {
                $pay['available'] = 0;
                $pay['hours']     = 0;
                $pay['earned']    = 0;
            }
        } else {
            $pay['available'] = 0;
            $pay['hours']     = 0;
            $pay['earned']    = 0;
        }
        $pay['id'] = $id;
        echo json_encode($pay);
    }
    if($_GET['setting'] == 'usr_writeup'){
        $token      = $_GET['uuid'];
        $writeup    = $_POST['reasoning'];
        $action     = $_POST['action'];
        $by         = $_SESSION['uuid'];

        mysql_query("INSERT INTO fmo_users_employee_writeups (writeup_user_token, writeup_reasoning, writeup_action, writeup_by_user_token) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($writeup)."',
        '".mysql_real_escape_string($action)."',
        '".mysql_real_escape_string($by)."')");
        timeline_log($token, $by, "Write-up", name($by)." wrote up ".name($token).": <strong>".$writeup."</strong> (Action taken: <strong>".$action."</strong>)");
    }
    if($_GET['setting'] == 'usr_cmt'){
        $token      = $_GET['uuid'];
        $comment    = $_POST['comment'];
        $by         = $_SESSION['uuid'];

        mysql_query("INSERT INTO fmo_users_employee_comments (comment_user_token, comment_comment, comment_by_user_token) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($comment)."',
        '".mysql_real_escape_string($by)."')");
        timeline_log($token, $by, "Comment", "<strong>".name($by)."</strong> commented: <strong>".$comment."</strong>");
    }
    if($_GET['setting'] == 'ev_cmt'){
        $token      = $_GET['ev'];
        $comment    = $_POST['comment'];
        $by         = $_SESSION['uuid'];

        mysql_query("INSERT INTO fmo_locations_events_comments (comment_event_token, comment_comment, comment_by_user_token) VALUES (
        '".mysql_real_escape_string($token)."',
        '".mysql_real_escape_string($comment)."',
        '".mysql_real_escape_string($by)."')");
        timeline_event($token, $by, "Comment", "<strong>".name($by)."</strong> commented: <strong>".$comment."</strong>");
    }
    if($_GET['setting'] == 'service_rates'){
        $location_token = $_GET['luid'];
        $item           = sentence_case($_POST['item']);
        $saleprice      = $_POST['saleprice'];
        $cost           = $_POST['cost'];
        $taxable        = $_POST['taxable'];
        $commissionable = $_POST['commissionable'];
        $type           = $_POST['type'];

        mysql_query("INSERT INTO fmo_services (services_location_token, services_item, services_saleprice, services_cost, services_taxable, services_commissionable, services_type) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($item)."',
        '".mysql_real_escape_string($saleprice)."',
        '".mysql_real_escape_string($cost)."',
        '".mysql_real_escape_string($taxable)."',
        '".mysql_real_escape_string($commissionable)."',
        '".mysql_real_escape_string($type)."')");
        echo $item;
    }
    if($_GET['setting'] == 'vendor'){
        $location_token = $_GET['luid'];
        $active         = $_POST['active'];
        $name           = sentence_case($_POST['name']);
        $type           = $_POST['type'];
        $phone          = $_POST['phone'];
        $contact        = $_POST['contact'];
        $account_ref    = $_POST['account_ref'];
        $extra_ref      = $_POST['extra_ref'];

        mysql_query("INSERT INTO fmo_locations_vendors (vendor_location_token, vendor_active, vendor_name, vendor_type, vendor_phone, vendor_contact, vendor_account_ref, vendor_extra_ref) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($active)."',
        '".mysql_real_escape_string($name)."',
        '".mysql_real_escape_string($type)."',
        '".mysql_real_escape_string($phone)."',
        '".mysql_real_escape_string($contact)."',
        '".mysql_real_escape_string($account_ref)."',
        '".mysql_real_escape_string($extra_ref)."')");
        echo $name;
    }
    if($_GET['setting'] == 'service_storage'){
        $location_token = $_GET['luid'];
        $available      = $_POST['available'];
        $unit           = $_POST['unit'];
        $priceperiod    = $_POST['priceperiod'];

        mysql_query("INSERT INTO fmo_locations_storages (storage_location_token, storage_available, storage_unit, storage_priceperiod) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($available)."',
        '".mysql_real_escape_string($unit)."',
        '".mysql_real_escape_string($priceperiod)."')");
        echo $unit;
    }
    if($_GET['setting'] == 'service_county'){
        $location_token = $_GET['luid'];
        $name           = sentence_case($_POST['county']);

        mysql_query("INSERT INTO fmo_locations_counties (county_location_token, county_name) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($name)."')");
        echo $name;
    }
    if($_GET['setting'] == 'times'){
        $location_token = $_GET['luid'];
        $start           = $_POST['starttime'];
        $end             = $_POST['endtime'];

        mysql_query("INSERT INTO fmo_locations_times (time_location_token, time_start, time_end) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($start)."',
        '".mysql_real_escape_string($end)."')");
        echo $start." to ".$end;
    }
    if($_GET['setting'] == 'eventtype'){
        $location_token = $_GET['luid'];
        $name           = sentence_case($_POST['eventtype']);

        mysql_query("INSERT INTO fmo_locations_eventtypes (eventtype_location_token, eventtype_name) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($name)."')");
        echo $name;
    }
    if($_GET['setting'] == 'subtype'){
        $location_token = $_GET['luid'];
        $name           = sentence_case($_POST['subtype']);

        mysql_query("INSERT INTO fmo_locations_subtypes (subtype_location_token, subtype_name) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($name)."')");
        echo $name;
    }
    if($_GET['setting'] == 'howhear'){
        $location_token = $_GET['luid'];
        $hear           = sentence_case($_POST['hear']);

        mysql_query("INSERT INTO fmo_locations_howhears (howhear_location_token, howhear_name) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($hear)."')");
        echo $hear;
    }
    if($_GET['setting'] == 'service_zipcode'){
        $location_token = $_GET['luid'];
        $code           = $_POST['code'];

        mysql_query("INSERT INTO fmo_locations_zipcodes (zipcode_location_token, zipcode_code) VALUES (
        '".mysql_real_escape_string($location_token)."',
        '".mysql_real_escape_string($code)."')");
        echo $code;
    }
}