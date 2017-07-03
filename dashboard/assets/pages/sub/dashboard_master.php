<?php
/**
 * Created by PhpStorm.
 * User: gameroom
 * Date: 6/21/2017
 * Time: 8:57 AM
 */
session_start();
include '../../app/init.php';

if(isset($_SESSION['logged'])) {
    if ($_GET['t'] == 'dash_evs') {
        $range = explode(" - ", $_POST['range']);
        ?>
        <div class="row">
            <div class="col-md-6">
                <h3 style="margin-top: 0px;">Morning <small class="hidden-sm"><span class="text-danger">| <span id="morning"></span></span></small></h3>
                <div class="todo-tasklist">
                    <?php
                    $events = mysql_query("SELECT event_name, event_date_start, event_date_end, event_time, event_token, event_status, event_type, event_subtype, event_booking, event_truckfee, event_laborrate FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (event_date_start>='".mysql_real_escape_string($range[0])."' AND event_date_end<='".mysql_real_escape_string($range[1])."') AND NOT event_status=0");
                    if(mysql_num_rows($events) > 0){
                        $eventsCount = 0;
                        while($event = mysql_fetch_assoc($events)){
                            switch($event['event_status']){
                                case 1: $status = "New Booking"; $color = "blue"; $badge = "badge-info"; break;
                                case 2: $status = "Confirmed"; $color = "green"; $badge = "badge-success"; break;
                                case 3: $status = "Left Message"; $color = "red"; $badge = "badge-danger"; break;
                                case 4: $status = "On Hold"; $color = "red"; $badge = "badge-danger"; break;
                                case 5: $status = "Cancelled"; $color = "red"; $badge = "badge-danger"; break;
                                default: $status = "On Hold"; $color = "red"; $badge = "badge-danger"; break;
                            }
                            $times = explode("to", $event['event_time']);
                            if(strtotime($times[0]) >= strtotime("12:00PM")){
                                continue;
                            }
                            $eventsCount++;
                            $start = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=1"));
                            $end   = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=2"));
                            ?>
                            <div class="todo-tasklist-item todo-tasklist-item-border-<?php echo $color; ?> load_page col-md-12" data-href="assets/pages/event.php?ev=<?php echo $event['event_token']; ?>" data-page-title="<?php echo $event['event_name']; ?>">
                                <div class="todo-tasklist-item-title">
                                    <?php echo $event['event_name']; ?> <span class="font-<?php echo $color; ?>">|</span> <small><i class="fa fa-truck"></i> Trucks: <?php echo $event['event_truckfee']; ?> + <i class="fa fa-users"></i> Crew size: <?php echo $event['event_laborrate']; ?></small> <span class="font-<?php echo $color; ?>">|</span> <span class="badge badge-roundless <?php echo $badge; ?>"><?php echo $status; ?></span>
                                </div>
                                <div class="todo-tasklist-item-text">
                                    <?php
                                    if(!empty($start['address_address']) && !empty($end['address_address'])){
                                        ?>
                                        <strong>Start:</strong> <?php echo $start['address_address'].", ".$start['address_city']; ?> <i class="fa fa-map"></i> <strong>End:</strong> <?php echo $end['address_address'].", ".$end['address_city']; ?>
                                        <?php
                                    } elseif(!empty($event['event_comments'])) {
                                        ?>
                                        <strong>Comments:</strong> <?php echo $event['event_comments']; ?>
                                        <?php
                                    } else {
                                        ?>
                                        <strong>Click to view more details & manage</strong>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="todo-tasklist-controls pull-left">
                                    <span class="todo-tasklist-date"><i class="fa fa-calendar"></i> <?php echo date('d M Y', strtotime($event['event_date_start'])); ?> - <?php echo date('d M Y', strtotime($event['event_date_end'])); ?> @ <?php echo $event['event_time']; ?></span>
                                    <?php
                                    if(!empty($event['event_type'])){
                                        ?>
                                        <span class="todo-tasklist-badge badge badge-roundless badge-danger"><?php echo $event['event_type']; ?></span>
                                        <?php
                                    }
                                    if(!empty($event['event_subtype'])){
                                        ?>
                                        <span class="todo-tasklist-badge badge badge-roundless badge-info"><?php echo $event['event_subtype']; ?></span>
                                        <?php
                                    }
                                    if($event['event_booking'] == 1){
                                        ?>
                                        <span class="todo-tasklist-badge badge badge-roundless badge-success"><i class="fa fa-check"  style="margin-top: -6px"></i> Booking fee paid</span>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <span id="morningCount" class="hidden"><?php echo $eventsCount; ?></span>
                        <?php
                    } else {
                        ?>
                        <br/>
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            <strong>No events found</strong> for this morning at this location.
                            <span id="morningCount" class="hidden">0</span>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <div class="col-md-6">
                <h3 style="margin-top: 0px">Afternoon <small class="hidden-sm"><span class="text-danger">| <span id="afternoon"></span></span></small></h3>
                <div class="todo-tasklist">
                    <?php
                    $events = mysql_query("SELECT event_name, event_date_start, event_date_end, event_time, event_token, event_status, event_type, event_subtype, event_booking, event_truckfee, event_laborrate FROM fmo_locations_events WHERE event_location_token='".mysql_real_escape_string($_GET['luid'])."' AND (event_date_start>='".$range[0]."' AND event_date_end<='".$range[1]."') AND NOT event_status=0");
                    if(mysql_num_rows($events) > 0){
                        $eventsCount = 0;
                        while($event = mysql_fetch_assoc($events)){
                            switch($event['event_status']){
                                case 1: $status = "New Booking"; $color = "blue"; $badge = "badge-info"; break;
                                case 2: $status = "Confirmed"; $color = "green"; $badge = "badge-success"; break;
                                case 3: $status = "Left Message"; $color = "red"; $badge = "badge-danger"; break;
                                case 4: $status = "On Hold"; $color = "red"; $badge = "badge-danger"; break;
                                case 5: $status = "Cancelled"; $color = "red"; $badge = "badge-danger"; break;
                                default: $status = "On Hold"; $color = "red"; $badge = "badge-danger"; break;
                            }
                            $times = explode("to", $event['event_time']);
                            if(strtotime($times[0]) <= strtotime("12:00PM")){
                                continue;
                            }
                            $eventsCount++;
                            $start = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=1"));
                            $end   = mysql_fetch_array(mysql_query("SELECT address_address, address_city FROM fmo_locations_events_addresses WHERE address_event_token='".$event['event_token']."' AND address_type=2"));
                            ?>
                            <div class="todo-tasklist-item todo-tasklist-item-border-<?php echo $color; ?> load_page col-md-12" data-href="assets/pages/event.php?ev=<?php echo $event['event_token']; ?>" data-page-title="<?php echo $event['event_name']; ?>">
                                <div class="todo-tasklist-item-title">
                                    <?php echo $event['event_name']; ?> <span class="font-<?php echo $color; ?>">|</span> <small><i class="fa fa-truck"></i> Trucks: <?php echo $event['event_truckfee']; ?> + <i class="fa fa-users"></i> Crew size: <?php echo $event['event_laborrate']; ?></small> <span class="font-<?php echo $color; ?>">|</span> <span class="badge badge-roundless <?php echo $badge; ?>"><?php echo $status; ?></span>
                                </div>
                                <div class="todo-tasklist-item-text">
                                    <?php
                                    if(!empty($start['address_address']) && !empty($end['address_address'])){
                                        ?>
                                        <strong>Start:</strong> <?php echo $start['address_address'].", ".$start['address_city']; ?> <i class="fa fa-map"></i> <strong>End:</strong> <?php echo $end['address_address'].", ".$end['address_city']; ?>
                                        <?php
                                    } elseif(!empty($event['event_comments'])) {
                                        ?>
                                        <strong>Comments:</strong> <?php echo $event['event_comments']; ?>
                                        <?php
                                    } else {
                                        ?>
                                        <strong>Click to view more details & manage</strong>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="todo-tasklist-controls pull-left">
                                    <span class="todo-tasklist-date"><i class="fa fa-calendar"></i> <?php echo date('d M Y', strtotime($event['event_date_start'])); ?> - <?php echo date('d M Y', strtotime($event['event_date_end'])); ?> @ <?php echo $event['event_time']; ?></span>
                                    <?php
                                    if(!empty($event['event_type'])){
                                        ?>
                                        <span class="todo-tasklist-badge badge badge-roundless badge-danger"><?php echo $event['event_type']; ?></span>
                                        <?php
                                    }
                                    if(!empty($event['event_subtype'])){
                                        ?>
                                        <span class="todo-tasklist-badge badge badge-roundless badge-info"><?php echo $event['event_subtype']; ?></span>
                                        <?php
                                    }
                                    if($event['event_booking'] == 1){
                                        ?>
                                        <span class="todo-tasklist-badge badge badge-roundless badge-success"><i class="fa fa-check" style="margin-top: -6px"></i> Booking fee paid</span>
                                        <?php
                                    }
                                    ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>
                        <span id="afternoonCount" class="hidden"><?php echo $eventsCount; ?></span>
                        <?php
                    } else {
                        ?>
                        <br/>
                        <div class="alert alert-danger alert-dismissable">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true"></button>
                            <strong>No events found</strong> for this afternoon at this location.
                            <span id="afternoonCount" class="hidden">0</span>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function(){
                $('#afternoon').html(document.getElementById("afternoonCount").innerText);
                $('#morning').html(document.getElementById("morningCount").innerText);
            });
        </script>
        <?php
    } elseif($_GET['t'] == 'dash_mag'){
        ?>
        <div class="row">
            <div class="col-sm-12">
                <div id="mag" class="has-toolbar">
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function() {

                var date = new Date(Date.parse("<?php echo $_POST['month']; ?>"));
                var d = date.getDate();
                var m = date.getMonth();
                var y = date.getFullYear();

                console.log(m + ' ' + y + ' ' + d);

                var h = {};

                <?php
                $events = mysql_query("SELECT event_name, event_time, event_date_start, event_token FROM fmo_locations_events WHERE MONTH(event_date_start)=MONTH('".mysql_real_escape_string($_POST['month'])."') AND event_location_token='".mysql_real_escape_string($_GET['luid'])."'");
                ?>

                $('#mag').fullCalendar('destroy'); // destroy the calendar
                $('#mag').fullCalendar({ //re-initialize the calendar
                    header: {
                        left:   'title',
                        center: '',
                        right:  ''
                    },
                    defaultView: 'month', // change default view with available options from http://arshaw.com/fullcalendar/docs/views/Available_Views/
                    slotMinutes: 15,
                    events: [
                        <?php
                    if(mysql_num_rows($events)>0){
                        while($event = mysql_fetch_assoc($events)){
                         ?>
                        {
                            title: "<?php echo $event['event_name']; ?>",
                            ev: "<?php echo $event['event_token']; ?>",
                            start: new Date(Date.parse("<?php echo $event['event_date_start']; ?> 23:00:00")),
                            backgroundColor: Metronic.getBrandColor('red')
                        },
                        <?php
                        }
                    }
                    ?>
                    ],
                    eventClick: function(event) {
                        Pace.track(function(){
                            $.ajax({
                                url: "assets/pages/event.php?ev=" + event.ev,
                                success: function(data) {
                                    $('#page_content').html(data);
                                    document.title = event.title+" - For Movers Only";
                                },
                                error: function() {
                                    toastr.error("<strong>Logan says</strong>:<br/>An unexpected error has occured. Please try again later.");
                                }
                            });
                        });
                    }
                });


                $('#mag').fullCalendar('gotoDate', '<?php echo $_POST['month']; ?>');

                $(".fc-button-prev").addClass('fc-state-disabled');
                $(".fc-button-next").addClass('fc-state-disabled');
            });
        </script>
        <?php
    }
}