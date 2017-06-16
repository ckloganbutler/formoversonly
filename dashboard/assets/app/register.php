<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/4/2017
 * Time: 5:08 AM
 */
include 'init.php';
if(isset($_POST) && !isset($_GET['disabled'])){

    /*
     *  Let's get the user's input, then we'll generate the users tokens and such.
     */

    $name       = explode(" ", sentence_case($_POST['fullname']));
    $email      = $_POST['email'];
    $phone      = preg_replace('/[^A-Za-z0-9\-]/', '', $_POST['phone']);
    $company    = sentence_case($_POST['company']);
    $address    = sentence_case($_POST['address']);
    $address2   = sentence_case($_POST['address2']);
    $apt        = $_POST['apt'];
    $city       = sentence_case($_POST['city']);
    $zip        = $_POST['zip'];
    $state      = $_POST['state'];
    $pic        = '//www.formoversonly.com/dashboard/assets/admin/layout/img/default.png';

    /*
     *  Let's generate those 'tokens and such' now
     *
     *  Since this software mandates the user, we'll assume this registration as the master user.
     *  If other users are entered, the user_group variable may change based on the scripts heading.
     *
     *  Possible variables for group:
     *
     *  1.) Administrator
     *  2.) Manager
     *  3.) Customer
     *  4.) Customer Service Rep.
     *  5.) Employees {
     *          5.1- Drivers
     *          5.2- Helpers
     *          5.3- Crewman/Other
     *      }
     */
    //Lets generate their password
    if(!empty($_POST['password'])){
        $password   = md5($_POST['password']);
    } else {
        $pass       = struuid();
        $password   = md5($pass);
    }
    // Users name
    if(count($name) != 3){
        $fname = $name[0]; $lname = $name[1];
    } else { $fname = $name[0]; $mname = substr($name[1], 0)."."; $lname = $name[2]; }
    // Users token
    $uuid =  struuid(true);
    // Users company token (for referencing a company if they make one)
    $cuid =  struuid();
    // Users group
    if(isset($_GET['gr'])){
        $group      = $_GET['gr'];
        $creator    = $_GET['c'];
        $last_ext   = $_GET['luid'];
        if($group == 2 || $group >= 4){
            $employer          = $creator;
            $employer_location = $last_ext;
        } else {$employer = NULL; $employer_location = NULL;}
    } else { $group = 1; $creator = $uuid; $last_ext = NULL; $employer = NULL; $employer_location = NULL;}

    $checkTaken = mysql_num_rows(mysql_query("SELECT user_email, user_phone FROM as_users WHERE user_email='".mysql_real_escape_string($email)."' OR user_phone='".mysql_real_escape_string($phone)."'"));
    if($checkTaken == 0){
        mysql_query("INSERT INTO fmo_users (user_group, user_token, user_company_token, user_employer, user_employer_location,  user_fname, user_mname, user_lname, user_email, user_phone, user_pword, user_address, user_address2, user_apt, user_city, user_state, user_zip, user_company_name, user_creator, user_pic, user_last_ext_location) VALUES (
        '".mysql_real_escape_string($group)."',
        '".mysql_real_escape_string($uuid)."',
        '".mysql_real_escape_string($cuid)."',
        '".mysql_real_escape_string($employer)."',
        '".mysql_real_escape_string($employer_location)."',
        '".mysql_real_escape_string(sentence_case($fname))."',
        '".mysql_real_escape_string(sentence_case($mname))."',
        '".mysql_real_escape_string(sentence_case($lname))."',
        '".mysql_real_escape_string($email)."',
        '".mysql_real_escape_string($phone)."',
        '".mysql_real_escape_string($password)."',
        '".mysql_real_escape_string($address)."',
        '".mysql_real_escape_string($address2)."',
        '".mysql_real_escape_string($apt)."',
        '".mysql_real_escape_string($city)."',
        '".mysql_real_escape_string($state)."',
        '".mysql_real_escape_string($zip)."',
        '".mysql_real_escape_string($company)."',
        '".mysql_real_escape_string($creator)."',
        '".mysql_real_escape_string($pic)."',
        '".mysql_real_escape_string($last_ext)."')");

        $id = mysql_insert_id();

        if($group != 1){
            timeline_log($uuid, $creator, "User creation", $fname." was registered to the system.");
        }
        if($group == 2 || $group >= 4) {
            _sendText($phone, "Welcome to the company! https://www.formoversonly.com\r\nEmployee ID: ".$id."\r\nPassword: ".$pass."");
        }
        echo $uuid;
    }
}