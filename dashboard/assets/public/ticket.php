<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<?php
require '../app/init.php';
$event  = mysql_fetch_array(mysql_query("SELECT event_name, event_token, event_company_token, event_user_token, event_location_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));
$ticks  = mysql_query("SELECT ticket_id, ticket_token, ticket_user_token, ticket_event_token, ticket_company_token, ticket_location_token, ticket_department, ticket_priority, ticket_status, ticket_timestamp FROM fmo_locations_tickets WHERE ticket_event_token='".mysql_real_escape_string($event['event_token'])."'");
?>
<html lang="en">
<!--<![endif]-->
<head>
    <meta charset="utf-8"/>
    <title>Claim form | <?php echo $event['event_name']; ?></title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta content="For Movers Only UI Description" name="description">
    <meta content="For Movers Only Keywords" name="keywords">
    <meta content="loganck" name="author">
    <meta property="og:site_name" content="https://www.formoversonly.com">
    <meta property="og:title" content="For Movers Only">
    <meta property="og:description" content="Automated Fleet Software for Moving Companies">
    <meta property="og:type" content="website">
    <meta property="og:image" content="">
    <meta property="og:url" content="https://www.formoversonly.com">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css"/>
    <link href="../global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
    <link href="../global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css"/>
    <link href="../global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
    <link href="../global/plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css"/>
    <link href="../global/plugins/select2/select2.css" rel="stylesheet" type="text/css"/>
    <link href="../global/plugins/dropzone/css/dropzone.css" rel="stylesheet"/>
    <link href="../admin/pages/css/login3.css" rel="stylesheet" type="text/css"/>
    <link href="../global/css/components.css" id="style_components" rel="stylesheet" type="text/css"/>
    <link href="../global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="../admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
    <link href="../admin/layout/css/themes/default.css" rel="stylesheet" type="text/css" id="style_color"/>
    <link href="../admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="../../favicon.ico"/>
</head>
<body class="login">
<div class="logo" style="color: white; text-transform: uppercase; font-size: 23px; letter-spacing: .01em; word-spacing: 1px; width: auto; margin-top: 7px; font-weight: 300; margin-bottom: 0px;">
    <?php
    if(empty($_GET['cuid'])){
       $_GET['cuid'] = $event['event_company_token'];
    }
    $name = companyName($_GET['cuid']);
    if(!empty($name)){
        $cool = explode(" ", $name);
        $white = true; $red = false;
        foreach($cool as $word){
            if($white == true){
                echo "<span style='color: #FFFFFF'>".$word."</span>";
                $white = false;
                $red   = true;
            } elseif($red == true){
                echo "<span style='color: #cb5a5e'>".$word."</span>";
                $red   = false;
                $white = true;
            }
        }
    } else {

    }
    ?>
</div>
<div class="menu-toggler sidebar-toggler">
</div>
<div class="content">
    <?php
    if(isset($_GET['tk']) && mysql_num_rows($ticks) > 0){
        if(isset($_GET['tk'])){
            $ticket   = mysql_fetch_array(mysql_query("SELECT ticket_id, ticket_token, ticket_user_token, ticket_event_token, ticket_company_token, ticket_location_token, ticket_department, ticket_priority, ticket_status, ticket_timestamp FROM fmo_locations_tickets WHERE ticket_token='".mysql_real_escape_string($_GET['tk'])."'"));
        } else {
            $ticket = mysql_fetch_array($ticks);
        }
       ?>
        <div class="login-form">
            <h3 class="form-title"><strong>Support Ticket</strong> chatroom <span class="badge badge-danger"><?php echo $event['event_name']; ?></span></h3>
            <ul class="chats">
                <?php
                $messages = mysql_query("SELECT message_token, message_user_token, message_message, message_timestamp FROM fmo_locations_tickets_messages WHERE message_ticket_token='".mysql_real_escape_string($ticket['ticket_token'])."'");
                if(mysql_num_rows($messages) > 0){
                    while($message = mysql_fetch_assoc($messages)){
                        ?>
                        <li class="<?php if($_GET['uuid'] == $message['message_user_token']){ echo "out"; } else { echo "in"; } ?>">
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
                                            <img class="img-thumbnail" src="<?php echo $doc['document_link']; ?>" height="200" width="300"/>
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
        <?php
    } else {
        ?>
        <form id="new_ticket">
            <div class="login-form">
                <h3 class="form-title"><strong>Claim</strong> form <span class="badge badge-danger"><?php echo $event['event_name']; ?></span></h3>
                <div class="form-group">
                    <label class="control-label">Item that was damaged <span class="font-red">*</span></label>
                    <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Item that was damaged" name="item"/>
                </div>
                <div class="form-group">
                    <label class="control-label">Was the item padded?  <span class="font-red">*</span></label>
                    <select class="form-control placeholder-no-fix" name="padded">
                        <option disabled selected value="">Was the item padded?</option>
                        <option value="Yes">Yes</option>
                        <option value="No">No</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="control-label">Approximate Weight (LBs)  <span class="font-red">*</span></label>
                    <input class="form-control placeholder-no-fix" type="text" autocomplete="off" placeholder="Approximate Weight (Lbs)" name="weight"/>
                </div>
                <div class="form-group">
                    <label class="control-label">Message <span class="font-red">*</span></label>
                    <textarea class="form-control placeholder-no-fix" style="height: 150px;border-left: 2px solid #c23f44!important" placeholder="Detailed message for support ticket" name="message"></textarea>
                </div>
                <div class="form-group">
                    <label class="control-label">Image of Item  <span class="font-red">*</span></label>
                    <input class="form-control placeholder-no-fix" type="file" autocomplete="off" name="file"/>
                    <span class="help-block">Don't worry, you can upload more after this.</span>
                </div>
                <input type="hidden" name="department" value="Claims">
                <input type="hidden" name="priority"   value="High">
                <button type="submit" class="btn red pull-right btn-block submitter">
                    Submit <i class="m-icon-swapright m-icon-white"></i>
                </button>
                <br/><br/>
            </div>
        </form>
        <?php
    }
    ?>

</div>
<!-- END LOGIN -->
<!-- BEGIN COPYRIGHT -->
<div class="copyright">
    2017 &copy; For Movers Only | <strong>Powered by Logan</strong> <br/>  <a target="_blank" href="https://www.fmcsa.dot.gov/protect-your-move"><strong>Your rights & responsibilities.</strong></a>
</div>
<!--[if lt IE 9]>
<script src="../global/plugins/respond.min.js"></script>
<script src="../global/plugins/excanvas.min.js"></script>
<![endif]-->
<script src="../global/plugins/jquery.min.js" type="text/javascript"></script>
<script src="../global/plugins/jquery-migrate.min.js" type="text/javascript"></script>
<script src="../global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="../global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="../global/plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
<script src="../global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="../global/plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
<script src="../global/plugins/dropzone/dropzone.js"></script>
<script type="text/javascript" src="../global/plugins/select2/select2.min.js"></script>
<script src="../global/scripts/metronic.js" type="text/javascript"></script>
<script src="../admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="../admin/pages/scripts/login.js" type="text/javascript"></script>
<script>
    jQuery(document).ready(function() {
        Metronic.init();
        Layout.init();
        Login.init();
        <?php
        if(isset($_GET['tk']) && mysql_num_rows($ticks) > 0){
            ?>
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
                        url: '../app/add_setting.php?setting=ticket_reply&tk=<?php echo $ticket['ticket_token']; ?>&s=1&uuid=<?php echo $_GET['uuid']; ?>',
                        type: "POST",
                        data: new FormData($('#new_replies')[0]),
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        encode: true,
                        success: function (data) {
                            window.location.href += '&tk='+ data.tk + '&uuid=<?php echo $_GET['uuid']; ?>';
                            window.reload();
                        },
                        error: function () {
                            toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later. C");
                        }
                    });
                }
            });
            <?php
        } else {
            ?>
            $('#new_ticket').validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                    item: {
                        required: true
                    },
                    padded: {
                        required: true
                    },
                    weight: {
                        required: true
                    },
                    message: {
                        required: true
                    },
                    file: {
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
                    $('.submitter').prop('disabled', true);
                    $('.submitter').html("<i class='fa fa-spinner fa-spin'></i>");
                    $.ajax({
                        url: '../app/add_setting.php?setting=ticket&luid=<?php echo $event['event_location_token']; ?>&uuid=<?php echo $event['event_user_token']; ?>&ev=<?php echo $event['event_token']; ?>&cuid=<?php echo $event['event_company_token']; ?>',
                        type: "POST",
                        data: new FormData($('#new_ticket')[0]),
                        dataType: 'json',
                        processData: false,
                        contentType: false,
                        encode: true,
                        success: function (data) {
                            window.location.href += '&tk='+ data.tk;
                            window.reload();
                        },
                        error: function () {
                            toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later. C");
                        }
                    });
                }
            });
        <?php
        }
        ?>
    });
</script>
</body>
</html>