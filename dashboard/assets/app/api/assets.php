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
        $findDocuments = mysql_query("SELECT document_id, document_link, document_type, document_desc, document_by_user_token FROM fmo_locations_assets_documents WHERE document_id='".mysql_real_escape_string($_GET['id'])."'");
        $iTotalRecords = mysql_num_rows($findDocuments);

        $records = array();
        $records["data"] = array();

        while($doc = mysql_fetch_assoc($findDocuments)) {
            $records["data"][] = array(
                '<img class="img-responsive" src="'.$doc['document_link'].'"/><br/><center>'.$doc['document_type'].'</center>',
                'File Type: <strong>'.$doc['document_type'].'</strong><br/> File Description: <strong>'.$doc['document_desc'].'</strong>',
                ''.name($doc['document_by_user_token']).'',
            );
        }


        $records["draw"] = $sEcho;
        $records["recordsTotal"] = $iTotalRecords;
        $records["recordsFiltered"] = $iTotalRecords;

        echo json_encode($records);
    }
}