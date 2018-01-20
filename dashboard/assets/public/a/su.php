<?php
/**
 * Created by PhpStorm.
 * User: gameroom
 * Date: 1/15/2018
 * Time: 11:23 PM
 */
include '../../app/init.php';
?>
<div id="pg_content_sub">

</div>
<script type="text/javascript">
    $(document).ready(function() {
        $.ajax({
            url: 'a/sub/su_py.php?t=mgr&luid=<?php echo $_GET['luid']; ?>',
            type: 'POST',
            data: {
                uuid: '<?php echo $_POST['uuid']; ?>'
            },
            success: function(data){
                $('#pg_content_sub').html(data);
            },
            error: function(){
                toastr.error("<strong>Logan says:<br/><strong>An unexpected error occurred.");
            }
        });
    });
</script>