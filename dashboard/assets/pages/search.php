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
    $location = mysql_fetch_array(mysql_query("SELECT location_name, location_owner_company_token FROM fmo_locations WHERE location_token='".mysql_real_escape_string($_GET['luid'])."'"));
    ?>
    <div class="page-content">
        <h3 class="page-title">
            <strong>Global Search</strong>
        </h3>
        <div class="page-bar">
            <ul class="page-breadcrumb">
                <li>
                    <i class="fa fa-home"></i>
                    <a class="load_page" data-href="assets/pages/dashboard.php?luid=<?php echo $_GET['luid']; ?>"><?php echo $location['location_name']; ?></a>
                    <i class="fa fa-angle-right"></i>
                </li>
                <li>
                    <a class="load_page" data-href="assets/pages/search.php?luid=<?php echo $_GET['luid']; ?>" data-page-title="Global Search">Global Search</a>
                </li>
            </ul>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="portlet light">
                    <div class="portlet-title">
                        <div class="caption caption-md">
                            <i class="icon-call-out theme-font bold"></i>
                            <span class="caption-subject font-red bold uppercase"><?php echo companyName($_SESSION['cuid']); ?></span> <span class="font-red">|</span> <small>Global Search</small>
                        </div>
                    </div>
                    <div class="portlet-body">
                        <div class="row search-form-default">
                            <div class="col-md-12">
                                <div class="input-group">
                                    <div class="input-cont">
                                        <input type="text" placeholder="Search globally..." class="form-control" id="search_deep" value="<?php echo $_POST['search']; ?>" autofocus>
                                    </div>
                                    <span class="input-group-btn">
                                        <button type="button" class="btn default red-stripe search-deep">
                                         Search &nbsp; <i class="m-icon-swapright"></i>
                                        </button>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <hr/>
                        <div class="row" id="results">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        jQuery(document).ready(function(){
            $.ajax({
                url: 'assets/app/api/search.php?e=ctv',
                type: 'POST',
                data: {
                    search: "<?php echo $_POST['search']; ?>"
                },
                success: function(data){
                    $('#results').html(data);
                },
                error: function(data){
                    toastr.error("<strong>Logan says:</strong><br/>I have encountered an error. Please try again later.");
                }
            });
            $('#search_deep').on('input', function() {
                var search = $(this).val();
                $.ajax({
                    url: 'assets/app/api/search.php?e=ctv',
                    type: 'POST',
                    data: {
                        search: search
                    },
                    success: function(data){
                        $('#results').html(data);
                    },
                    error: function(data){
                        toastr.error("<strong>Logan says:</strong><br/>I have encountered an error. Please try again later.");
                    }
                });
            });
            $('.search-deep').unbind().on('click', function(){
                var search = $('#search_deep').val();
                $.ajax({
                    url: 'assets/app/api/search.php?e=ctv',
                    type: 'POST',
                    data: {
                        search: search
                    },
                    success: function(data){
                        $('#results').html(data);
                    },
                    error: function(data){
                        toastr.error("<strong>Logan says:</strong><br/>I have encountered an error. Please try again later.");
                    }
                });
            });
        });
    </script>
    <?php
} else {
    header("Location: ../../../index.php?err=no_access");
}
?>
