<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 5/31/2017
 * Time: 2:45 AM
 */
session_start();
include '../init.php';

if(isset($_GET['type'])){
    if($_GET['type'] == 'documents'){
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);
        $findDocuments = mysql_query("SELECT document_id, document_link, document_type, document_desc, document_by_user_token FROM fmo_locations_assets_documents WHERE document_asset_id='".mysql_real_escape_string($_GET['id'])."'");
        $iTotalRecords = mysql_num_rows($findDocuments);

        $records = array();
        $records["data"] = array();

        while($doc = mysql_fetch_assoc($findDocuments)) {
            $records["data"][] = array(
                '<embed height="200px" width="100%" src="'.$doc['document_link'].'"/><br/><center>'.$doc['document_type'].'</center>',
                'File Type: <strong>'.$doc['document_type'].'</strong><br/> File Description: <strong>'.$doc['document_desc'].'</strong> <br/> <a target="_blank" href="'.$doc['document_link'].'"><strong>Click here to view document</strong></a>',
                ''.name($doc['document_by_user_token']).'',
            );
        }


        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }
    if($_GET['type'] == 'maintenance'){
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);
        $findDocuments = mysql_query("SELECT record_type, record_desc, record_by, record_cost, record_po_number, record_mileage, record_by_user_token, record_timestamp FROM fmo_locations_assets_records WHERE record_asset_id='".mysql_real_escape_string($_GET['id'])."'");
        $iTotalRecords = mysql_num_rows($findDocuments);

        $records = array();
        $records["data"] = array();

        while($doc = mysql_fetch_assoc($findDocuments)) {
            $records["data"][] = array(
                ''.date('m-d-Y', strtotime($doc['record_timestamp'])).'',
                '<strong>'.$doc['record_type'].'</strong> - '.$doc['record_desc'].' by '.$doc['record_by'],
                ''.$doc['record_cost'].'',
                ''.$doc['record_po_number'].'',
                ''.name($doc['record_by_user_token']).'',
            );
        }


        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }
    if($_GET['type'] == 'accidents'){
        $iDisplayLength = intval($_REQUEST['length']);
        $iDisplayStart = intval($_REQUEST['start']);
        $sEcho = intval($_REQUEST['draw']);
        $findDocuments = mysql_query("SELECT accident_asset, accident_timestamp, accident_address, accident_city, accident_state, accident_deaths, accident_nfi, accident_hazmat, accident_driver, accident_insurance_report FROM fmo_locations_accidents WHERE accident_location_token='".mysql_real_escape_string($_GET['luid'])."'");
        $iTotalRecords = mysql_num_rows($findDocuments);

        $records = array();
        $records["data"] = array();

        while($doc = mysql_fetch_assoc($findDocuments)) {
            $records["data"][] = array(
                ''.date('m-d-Y H:i:s A', strtotime($doc['accident_timestamp'])).'',
                ''.$doc['accident_asset'].'',
                ''.$doc['accident_address'].', '.$doc['accident_city'].', '.$doc['accident_state'],
                ''.$doc['accident_deaths'].'',
                ''.$doc['accident_nfi'].'',
                ''.$doc['accident_hazmat'].'',
                ''.$doc['accident_driver'].'',
                ''.$doc['accident_insurance_report'].'',
                '<a class="btn btn-xs red-stripe disabled">N/A</a>',
            );
        }


        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }
}