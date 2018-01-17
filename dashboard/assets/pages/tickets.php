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
    if(isset($_GET['tk'])){
        mysql_query("UPDATE fmo_users SET user_last_location='".mysql_real_escape_string(basename(__FILE__, '.php')).".php?".$_SERVER['QUERY_STRING']."' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'");
        $ticket   = mysql_fetch_array(mysql_query("SELECT ticket_id, ticket_token, ticket_user_token, ticket_event_token, ticket_company_token, ticket_location_token, ticket_department, ticket_priority, ticket_status, ticket_timestamp FROM fmo_locations_tickets WHERE ticket_token='".mysql_real_escape_string($_GET['tk'])."'"));
        $location = mysql_fetch_array(mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".mysql_real_escape_string($ticket['ticket_location_token'])."'"));
        $uuidperm = mysql_fetch_array(mysql_query("SELECT user_esc_permissions, user_permissions FROM fmo_users WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'"));
        ?>
        <div class="page-content">
            <h3 class="page-title">
                <strong>Support Ticket</strong>
            </h3>
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li>
                        <i class="fa fa-home"></i>
                        <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>"><?php echo $location['location_name']; ?></a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <a class="load_page" data-href="assets/pages/tickets.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Support Tickets">Support Tickets</a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <a class="load_page" data-href="assets/pages/tickets.php?tk=<?php echo $ticket['ticket_token']; ?>" data-page-title="Ticket #<?php echo $ticket['ticket_id']; ?>">Ticket #<?php echo $ticket['ticket_id']; ?></a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <a class="popout" data-pop="profile.php?uuid=<?php echo $ticket['ticket_user_token']; ?>&luid=<?php echo $ticket['ticket_location_token']; ?>" data-page-title="<?php echo name($ticket['ticket_useR_token']); ?>"><?php echo name($ticket['ticket_user_token']); ?></a>
                    </li>
                    <?php
                    if(!empty($ticket['ticket_event_token'])){
                        ?>
                        <li>
                            <span class="text-muted">&nbsp;[+]&nbsp;</span>
                            <a class="popout" data-pop="event.php?ev=<?php echo $ticket['ticket_event_token']; ?>" data-page-title="<?php echo eventName($ticket['ticket_event_token']); ?>"><?php echo eventName($ticket['ticket_event_token']); ?></a>
                        </li>
                        <?php
                    }
                    ?>

                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="portlet-title tabbable-line">
                            <div class="caption caption-md">
                                <i class="icon-envelope-letter theme-font bold"></i>
                                <span class="caption-subject font-red bold uppercase">Support Ticket</span> <span class="font-red">|</span>  <small>#<?php echo $ticket['ticket_id']; ?></small>
                            </div>
                            <div class="actions btn-set">
                                <a class="btn default red-stripe close_ticket">
                                    <i class="fa fa-user-times"></i> Close Ticket (Solved)
                                </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <ul class="chats scrolerz">
                                <?php
                                $messages = mysql_query("SELECT message_token, message_user_token, message_message, message_timestamp FROM fmo_locations_tickets_messages WHERE message_ticket_token='".mysql_real_escape_string($ticket['ticket_token'])."'");
                                if(mysql_num_rows($messages) > 0){
                                    while($message = mysql_fetch_assoc($messages)){
                                        ?>
                                        <li class="<?php if($_SESSION['uuid'] == $message['message_user_token']){ echo "out"; } else { echo "in"; } ?>">
                                            <img class="avatar" alt="" src="<?php echo picture($message['message_user_token']); ?>"/>
                                            <div class="message">
                                                <span class="arrow"></span>
                                                <span class="datetime" style="font-size: 10px;"><?php echo ago($message['message_timestamp']); ?> </span>
                                                <span class="name">
                                                    <?php
                                                    if($ticket['ticket_id'] == '10070'){
                                                        ?>
                                                        <strong>Carolyn Nichols</strong>
                                                        <?php
                                                    } else {
                                                        ?>
                                                        <strong><?php echo name($message['message_user_token']); ?></strong>
                                                        <?php
                                                    }
                                                    ?>

                                                </span>
                                                <span class="body">
                                                    <strong><?php echo $message['message_message']; ?></strong>
                                                    <?php
                                                    $documents = mysql_query("SELECT document_link FROM fmo_locations_tickets_messages_documents WHERE document_message_token='".mysql_real_escape_string($message['message_token'])."'");
                                                    if(mysql_num_rows($documents) > 0){
                                                        $doc = mysql_fetch_array($documents);
                                                        ?><br/> <br/> Attached file: <br/>
                                                        <a href="<?php echo $doc['document_link']; ?>" target="_blank">
                                                            <embed class="img-thumbnail" src="<?php echo $doc['document_link']; ?>" height="200" width="300"></embed>
                                                        </a>
                                                        <?php
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                }
                                ?>
                            </ul>
                            <div class="chat-form">
                                <form method="POST" action="" role="form" id="new_replies">
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="control-label">Reply <span class="font-red">*</span></label>
                                            <textarea class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Detailed reply goes here.." name="message" style="height: 150px;"></textarea>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <label class="control-label">Reply attachment</label>
                                            <input class="form-control placeholder-no-fix" type="file" autocomplete="off" name="file"/>
                                            <br/><br/><br/>
                                            <span class="text-muted">Notifications will be sent to your phone/email when someone replies.</span>
                                            <br/>
                                            <button class="btn btn-block red submit-msg faa-parent animated-hover" style="margin-top:8px!important;">Send message <i class="fa fa-arrow-up faa-vertical"></i></button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {
                $('.scrolerz').slimScroll({
                    height: 600,
                    start: 'bottom'
                });
                $('.close_ticket').on('click', function(){
                    $.ajax({
                        url: 'assets/app/update_settings.php?update=ticket',
                        type: 'POST',
                        data: {
                            name: 'ticket_status',
                            value: 3,
                            pk: '<?php echo $ticket['ticket_token']; ?>'
                        },
                        success: function(s){
                            $.ajax({
                                url: 'assets/pages/tickets.php?luid=<?php echo $_GET['luid']; ?>',
                                success: function(data) {
                                    $('#page_content').html(data);
                                },
                                error: function() {
                                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                }
                            });
                            toastr.success("<strong>Logan says:</strong><br/>Support ticket closed. Thank you for your support.");
                        },
                        error: function(s){

                        }
                    });
                });
                $('#new_replies').validate({
                    errorElement: 'span', //default input error message container
                    errorClass: 'help-block', // default input error message class
                    focusInvalid: false, // do not focus the last invalid input
                    ignore: "",
                    rules: {
                        message: {
                            required: true
                        }
                    },

                    invalidHandler: function (event, validator) { //display error alert on form submit

                    },

                    highlight: function (element) { // hightlight error inputs
                        $(element)
                            .closest('.form-group').addClass('has-error'); // set error class to the control group
                    },

                    success: function (label) {
                        label.closest('.form-group').removeClass('has-error');
                        label.remove();
                    },


                    submitHandler: function (form) {
                        var group = $('#role').val();
                        $('.submit-msg').prop('disabled', true);
                        $('.submit-msg').html("<i class='fa fa-spinner fa-spin'></i>");
                        $.ajax({
                            url: 'assets/app/add_setting.php?setting=ticket_reply&tk=<?php echo $ticket['ticket_token']; ?>&s=2&uuid=<?php echo $_SESSION['uuid']; ?>',
                            type: "POST",
                            data: new FormData($('#new_replies')[0]),
                            dataType: 'json',
                            processData: false,
                            contentType: false,
                            encode: true,
                            success: function (data) {
                                $.ajax({
                                    url: 'assets/pages/tickets.php?tk='+ data.tk,
                                    type: "POST",
                                    success: function (data) {
                                        $('#page_content').html(data);
                                    },
                                    error: function () {
                                        toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later. B");
                                    }
                                });
                            },
                            error: function () {
                                toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later. C");
                            }
                        });
                    }
                });
            });
        </script>
        <?php
    } else {
        mysql_query("UPDATE fmo_users SET user_last_location='".mysql_real_escape_string(basename(__FILE__, '.php')).".php?".$_SERVER['QUERY_STRING']."' WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'");
        $location = mysql_fetch_array(mysql_query("SELECT location_name FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
        $uuidperm = mysql_fetch_array(mysql_query("SELECT user_esc_permissions, user_permissions FROM fmo_users WHERE user_token='".mysql_real_escape_string($_SESSION['uuid'])."'"));
        ?>
        <div class="page-content">
            <h3 class="page-title">
                <strong>Support Tickets</strong>
            </h3>
            <div class="page-bar">
                <ul class="page-breadcrumb">
                    <li>
                        <i class="fa fa-home"></i>
                        <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>"><?php echo $location['location_name']; ?></a>
                        <i class="fa fa-angle-right"></i>
                    </li>
                    <li>
                        <a class="load_page" data-href="assets/pages/tickets.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Support Tickets">Support Tickets</a>
                    </li>
                </ul>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="portlet light">
                        <div class="portlet-title tabbable-line">
                            <div class="caption caption-md">
                                <i class="icon-envelope-letter theme-font bold"></i>
                                <span class="caption-subject font-red bold uppercase"><?php echo $location['location_name']; ?></span> <span class="font-red">|</span>  <small>Support Tickets</small>
                            </div>
                            <div class="actions btn-set">
                                <a class="btn default red-stripe" data-toggle="modal" href="#add_ticket">
                                    <i class="fa fa-plus"></i> Add new ticket
                                </a>
                            </div>
                        </div>
                        <div class="portlet-body">
                            <div class="tab-content">
                                <div class="tab-pane active" id="employees_tab">
                                    <div class="table-container">
                                        <table class="table table-striped table-hover datatable">
                                            <thead>
                                            <tr role="row" class="heading">
                                                <th width="8%">
                                                    Ticket Number
                                                </th>
                                                <th>
                                                    Location / Department
                                                </th>
                                                <th>
                                                    Customer / Event
                                                </th>
                                                <th>
                                                    Priority
                                                </th>
                                                <th>
                                                    Status
                                                </th>
                                                <th width="10%">
                                                    Last Message By
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php
                                            $per   = array();
                                            $perms = explode(',', $uuidperm['user_permissions']);
                                            foreach($perms as $perm){ $per[] = ($perm); }
                                            $tickets = mysql_query("SELECT ticket_id, ticket_token, ticket_location_token, ticket_user_token, ticket_event_token, ticket_department, ticket_priority, ticket_status, ticket_last_contacted_by, ticket_timestamp FROM fmo_locations_tickets WHERE ticket_company_token='".mysql_real_escape_string($_SESSION['cuid'])."' AND NOT ticket_status=3 ORDER BY ticket_priority ASC");
                                            if(mysql_num_rows($tickets) > 0){
                                                while($ticket = mysql_fetch_assoc($tickets)){
                                                    if(in_array($ticket['ticket_location_token'], $per)){
                                                        /* Statuses
                                                          * 0 = Open - new
                                                          * 1 = Open - waiting for staff reply
                                                          * 2 = Open - waiting for user reply
                                                          * 3 = Closed - Solved
                                                          */
                                                        switch($ticket['ticket_status']) {
                                                            case  0: $badge = "<span class='badge badge-info badge-roundless'><strong>Open</strong> - New ticket</span>"; break;
                                                            case  1: $badge = "<span class='badge badge-warning badge-roundless'><strong>Open</strong> - waiting for staff reply</span>"; break;
                                                            case  2: $badge = "<span class='badge badge-purple badge-roundless'><strong>Open</strong> - waiting for user reply</span>"; break;
                                                            case  3: $badge = "<span class='badge badge-success badge-roundless'><strong>Closed</strong> - Solved</span>"; break;
                                                            default: $badge = "<span class='badge badge-danger badge-roundless'><strong>Open</strong> - waiting for action</span>"; break;
                                                        }
                                                        ?>
                                                        <tr style="cursor: pointer" class="load_page" data-href="assets/pages/tickets.php?tk=<?php echo $ticket['ticket_token']; ?>&luid=<?php echo $_GET['luid']; ?>" data-page-title="Support Ticket #<?php echo $ticket['ticket_id']; ?>">
                                                            <td><span class="badge badge-danger badge-roundless"><strong>#<?php echo $ticket['ticket_id']; ?></strong></span></td>
                                                            <td><strong><?php echo locationName($ticket['ticket_location_token']); ?> / <?php echo $ticket['ticket_department']; ?></strong></td>
                                                            <td>
                                                                <strong>USER:</strong> <?php echo name($ticket['ticket_user_token']); ?>
                                                                <?php
                                                                if(!empty($ticket['ticket_event_token'])){
                                                                    ?>
                                                                    / <strong>EVENT</strong>: <?php echo eventName($ticket['ticket_event_token']); ?>
                                                                    <?php
                                                                }
                                                                ?>
                                                            </td>
                                                            <td><span class="badge badge-danger badge-roundless"><strong><?php echo $ticket['ticket_priority']; ?></strong></span></td>
                                                            <td><?php echo $badge; ?></td>
                                                            <td><strong><?php echo name($ticket['ticket_last_contacted_by']); ?></strong></td>
                                                        </tr>
                                                        <?php
                                                    } else {
                                                        continue;
                                                    }
                                                }
                                            }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            $(document).ready(function() {
                $('.datatable').dataTable({
                    stateSave: true,
                    "order": [[ 4, "desc" ]],
                    "bFilter" : true,
                    "bLengthChange": true,
                    "bPaginate": true,
                    "info": true

                });
            });
        </script>
        <?php
    }

} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
