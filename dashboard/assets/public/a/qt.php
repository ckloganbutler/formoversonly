<?php

if(isset($_GET['ev'])){
    include '../../app/init.php';
    $event = mysql_fetch_array(mysql_query("SELECT event_location_token, event_user_token, event_name, event_status, event_truckfee, event_laborrate, event_countyfee, event_comments, event_date_start, event_date_end, event_company_token, event_token, event_laborrate_rate, event_weekend_upcharge_rate, event_by_user_token FROM fmo_locations_events WHERE event_token='".mysql_real_escape_string($_GET['ev'])."'"));
    $location = mysql_fetch_array(mysql_query("SELECT location_quote, location_quote_extra, location_quote_cancel, location_quote_overtime_time, location_quote_overtime_rate, location_quote_oversized_safe, location_quote_oversized_playset, location_quote_oversized_pooltable, location_quote_oversized_piano, location_quote_oversized_hottub, location_quote_packing_small, location_quote_packing_medium, location_quote_packing_large, location_quote_packing_dishpack,  location_quote_packing_wardrobe, location_quote_packing_paper, location_quote_packing_tape,  location_quote_packing_shrinkwrap FROM fmo_locations WHERE location_token='".mysql_real_escape_string($event['event_location_token'])."'"));

    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="portlet light">
                <div class="portlet-body rates-form">
                    <h5>Date: <strong class="text-danger pull-right">
                            <?php
                            if(date('M d, Y', strtotime($event['event_date_start'])) == date('M d, Y', strtotime($event['event_date_end']))) {
                                echo date('M d, Y', strtotime($event['event_date_start']));
                            } else {
                                echo date('M d, Y', strtotime($event['event_date_start'])); ?> - <?php echo date('M d, Y', strtotime($event['event_date_end']));
                            }
                            ?></strong></h5>
                    <h5>Truck Fee (<?php echo $event['event_truckfee']; ?> trucks): <strong class="text-danger font-bold pull-right">$<span id="TF"></span></strong></h5>
                    <h5>Hourly Rate (<?php echo $event['event_laborrate']; ?> men): <strong class="text-danger font-bold pull-right">$<span id="LR"></span></strong></h5>
                    <h5>Travel Fee (<?php echo $event['event_countyfee']; ?> counties): <strong class="text-danger font-bold pull-right">$<span id="CF"></span></strong></h5>
                    <hr/>
                    <?php
                    if(strpos($location['location_quote'], "view_quote_other") !== false){
                        ?>
                        <h4 class="form-title text-center" style="margin-top: 0px;"><strong>Other possible fees</strong> <br/> <span class="badge badge-danger">Optional</span></h4>
                        <h6>Booking Fee: <strong class="text-danger pull-right">$10.00</strong></h6>
                        <h6>Credit Card Processing Fee: <strong class="text-danger pull-right"><?php ?>3%</strong></h6>
                        <?php
                        if(strpos($location['location_quote'], "view_quote_other_extra") !== false){
                            ?>
                            <h6>Extra man/per hour: <strong class="text-danger font-bold pull-right">$<?php echo number_format($event['event_laborrate_rate'], 2); ?> (each)</strong></h6>
                            <?php
                        } if(strpos($location['location_quote'], "view_quote_other_cancel") !== false){
                            ?>
                            <h6>Cancel Charge (< 24hrs notice): <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_cancel'], 2); ?></strong></h6>
                            <?php
                        } if(strpos($location['location_quote'], "view_quote_other_overtime") !== false){
                            ?>
                            <h6>Overtime Rate (after <?php echo $location['location_quote_overtime_time']; ?>pm): <strong class="text-danger pull-right"><?php echo number_format($location['location_quote_overtime_rate'], 1); ?>x current rate</strong></h6>
                            <?php
                        }
                        ?>
                        <h6 class="text-muted text-center margin-top-10">Rates may change if your event date changes.</h6>
                        <hr/>
                        <?php
                    }
                    if(strpos($location['location_quote'], "view_quote_oversized") !== false){
                        ?>
                        <h4 class="form-title text-center" style="margin-top: 0px;"><strong>Oversized Items</strong> <br/> <span class="badge badge-danger">Call for details</span></h4>
                        <?php
                        if(strpos($location['location_quote'], "view_quote_oversized_safe") !== false){
                            ?>
                            <h6>Safe: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_oversized_safe'], 2); ?></strong></h6>
                            <?php
                        } if(strpos($location['location_quote'], "view_quote_oversized_playset") !== false){
                            ?>
                            <h6>Play Set: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_oversized_playset'], 2); ?></strong></h6>
                            <?php
                        } if(strpos($location['location_quote'], "view_quote_oversized_pooltable") !== false){
                            ?>
                            <h6>Pool Table: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_oversized_pooltable'], 2); ?></strong></h6>
                            <?php
                        } if(strpos($location['location_quote'], "view_quote_oversized_piano") !== false){
                            ?>
                            <h6>Piano: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_oversized_piano'], 2); ?></strong></h6>
                            <?php
                        } if(strpos($location['location_quote'], "view_quote_oversized_hottub") !== false){
                            ?>
                            <h6>Hot Tub: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_oversized_hottub'], 2); ?></strong></h6>
                            <?php
                        }
                        ?>
                        <h6 class="text-muted text-center margin-top-10">Rates only for oversized items you have.</h6>
                        <hr/>
                        <?php
                    }
                    ?>

                    <?php
                    if(strpos($location['location_quote'], "view_quote_oversized") !== false){
                        ?>
                        <h4 class="form-title text-center" style="margin-top: 0px;"><strong>Packing Materials</strong> <br/> <span class="badge badge-danger">Optional</span></h4>
                        <?php
                        if(strpos($location['location_quote'], "view_quote_packing_small") !== false){
                            ?>
                            <h6>Small Box: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_small'], 2); ?></strong></h6>
                            <?php
                        } if(strpos($location['location_quote'], "view_quote_packing_medium") !== false){
                            ?>
                            <h6>Medium Box: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_medium'], 2); ?></strong></h6>
                            <?php
                        } if(strpos($location['location_quote'], "view_quote_packing_large") !== false){
                            ?>
                            <h6>Large Box: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_large'], 2); ?></strong></h6>
                            <?php
                        } if(strpos($location['location_quote'], "view_quote_packing_dishpack") !== false){
                            ?>
                            <h6>Dishpack: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_dishpack'], 2); ?></strong></h6>
                            <?php
                        } if(strpos($location['location_quote'], "view_quote_packing_wardrobe") !== false){
                            ?>
                            <h6>Wardrobe: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_wardrobe'], 2); ?> </strong></h6>
                            <?php
                        } if(strpos($location['location_quote'], "view_quote_packing_paper") !== false){
                            ?>
                            <h6>Packing Paper: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_paper'], 2); ?></strong></h6>
                            <?php
                        } if(strpos($location['location_quote'], "view_quote_packing_tape") !== false){
                            ?>
                            <h6>Tape: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_tape'], 2); ?></strong></h6>
                            <?php
                        } if(strpos($location['location_quote'], "view_quote_packing_shrinkwrap") !== false){
                            ?>
                            <h6>Shrinkwrap: <strong class="text-danger pull-right">$<?php echo number_format($location['location_quote_packing_shrinkwrap'], 2); ?></strong></h6>
                            <?php
                        }
                        ?>
                        <hr/>
                        <?php
                    }
                    ?>
                    <h6>Comments: </h6>
                    <strong><?php echo $event['event_comments']; ?></strong>
                    <hr/>

                    <blockquote class="hero">
                        <p>
                            <em>Think you're ready to continue? It's easy! Book your move using our easy tool now. You can call back anytime and request me for further assistance</em>
                        </p>
                        <small><strong>Your CSR</strong>, <?php echo name($event['event_by_user_token']); ?></small>
                    </blockquote>
                    <br/> <br/>
                    <button type="button" class="btn red book-move btn-block" style="margin: auto !important;">
                        Book your move now! <i class="m-icon-swapright m-icon-white"></i>
                    </button>
                    <br/><br/>
                    <h5 class="text-muted text-center">
                        <?php echo companyName($event['event_company_token']); ?> <br/>
                        <?php echo companyAddress($event['event_company_token']); ?> <br/>
                        <?php echo clean_phone(locationPhone($event['event_location_token'])); ?> - <?php echo clean_phone(companyPhone3($event['event_company_token'])); ?> <br/>
                        <?php echo companyLicenses($event['event_company_token']); ?> <br/>
                    </h5>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            var a = <?php echo $event['event_truckfee']; ?>;
            var b = <?php echo $event['event_laborrate']; ?>;
            var c = <?php echo $event['event_countyfee']; ?>;
            $.ajax({
                url: '../app/api/event.php?type=math&ev=<?php echo $event['event_token']; ?>',
                type: 'POST',
                data: {
                    a: a,
                    b: b,
                    c: c
                },
                success: function(d){
                    var e = JSON.parse(d);
                    $("#TF").html(e.truck_fee);
                    $("#LR").html(e.total_labor_rate);
                    $("#CF").html(e.county_fee);
                },
                error: function(e){

                }
            });

            $('.book-move').click(function(){
                $.ajax({
                    url: '../app/api/event.php?type=book_now&ev=<?php echo $event['event_token']; ?>',
                    type: 'POST',
                    success: function(d){
                        $('.rates-form').html(d);
                    },
                    error: function(e){

                    }
                });
            });
        });
    </script>
    <?php
}

?>