<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/5/2017
 * Time: 5:23 PM
 */
session_start();
include 'init.php';

if(isset($_POST)){
    $location_token          = struuid();
    $location_owner_token    = $_SESSION['uuid'];
    $location_owner_company  = $_SESSION['cuid'];
    $location_name           = $_POST['name'];
    $location_address        = $_POST['address'];
    $location_city           = $_POST['city'];
    $location_state          = $_POST['state'];
    $location_zip            = $_POST['zip'];
    mysql_query("INSERT INTO fmo_locations (location_token, location_owner_token, location_owner_company_token, location_name, location_address, location_city, location_state, location_zip) VALUES (
    '".mysql_real_escape_string($location_token)."',
    '".mysql_real_escape_string($location_owner_token)."',
    '".mysql_real_escape_string($location_owner_company)."',
    '".mysql_real_escape_string($location_name)."',
    '".mysql_real_escape_string($location_address)."',
    '".mysql_real_escape_string($location_city)."',
    '".mysql_real_escape_string($location_state)."',
    '".mysql_real_escape_string($location_zip)."')");

    /**
     *  Create sample data *eyeroll*
     *
     * * Counties
     * * Zip Codes
     * * Referrals
     * * Event Times
     * * Event Types
     * * Event Subtypes
     *
     */
    /** Counties */
    mysql_query("INSERT INTO fmo_locations_counties (county_location_token, county_name, county_status) VALUES (
    '".mysql_real_escape_string($location_token)."',
    '".mysql_real_escape_string("Sample County - Please add / update county options in your location settings")."',
    '".mysql_real_escape_string("1")."')");
    /** Zip Codes */
    mysql_query("INSERT INTO fmo_locations_zipcodes (zipcode_location_token, zipcode_code, zipcode_status) VALUES (
    '".mysql_real_escape_string($location_token)."',
    '".mysql_real_escape_string(substr($location_zip, 0, 3))."',
    '".mysql_real_escape_string("1")."')");
    /** Referrals */
    mysql_query("INSERT INTO fmo_locations_howhears (howhear_location_token, howhear_name) VALUES (
    '".mysql_real_escape_string($location_token)."',
    '".mysql_real_escape_string("Sample Type - Please add / update marketing options in location settings")."',
    '".mysql_real_escape_string("1")."')");
    /** Event Times */
    mysql_query("INSERT INTO fmo_locations_times (time_location_token, time_start, time_status) VALUES (
    '".mysql_real_escape_string($location_token)."',
    '".mysql_real_escape_string("8:00 AM")."',
    '".mysql_real_escape_string("1")."')");
    /** Event Types */
    mysql_query("INSERT INTO fmo_locations_eventtypes (eventtype_location_token, eventtype_name, eventtype_status) VALUES (
    '".mysql_real_escape_string($location_token)."',
    '".mysql_real_escape_string("Local")."',
    '".mysql_real_escape_string("1")."')");
    mysql_query("INSERT INTO fmo_locations_eventtypes (eventtype_location_token, eventtype_name, eventtype_status) VALUES (
    '".mysql_real_escape_string($location_token)."',
    '".mysql_real_escape_string("Out Of State")."',
    '".mysql_real_escape_string("1")."')");
    /** Event Subtypes */
    mysql_query("INSERT INTO fmo_locations_subtypes (subtype_location_token, subtype_name, subtype_status) VALUES (
    '".mysql_real_escape_string($location_token)."',
    '".mysql_real_escape_string("Sample Subtype - Please add / update subtype options in location settings")."',
    '".mysql_real_escape_string("1")."')");

    echo $location_token;
} else {die();}