<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 12/20/2017
 * Time: 6:31 AM
 */
?>

<div id="page-wrap">

    <h2>jQuery/PHP Chat</h2>

    <p id="name-area"></p>

    <div id="chat-wrap"><div id="chat-area"></div></div>

    <form id="send-message-area">
        <p>Your message: </p>
        <textarea id="sendie" maxlength = '100'></textarea>
    </form>

</div>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.4.4/jquery.min.js"></script>
<script src="chat.js"></script>
<script>

    // ask user for name with popup prompt
    var name = prompt("Enter your chat name:", "Guest");

    // default name is 'Guest'
    if (!name || name === ' ') {
        name = "Guest";
    }

    // strip tags
    name = name.replace(/(<([^>]+)>)/ig,"");

    // display name on page
    $("#name-area").html("You are: <span>" + name + "</span>");

    // kick off chat
    var chat =  new Chat();

    $(function() {

        chat.getState();

        // watch textarea for key presses
        $("#sendie").keydown(function(event) {

            var key = event.which;

            //all keys including return.
            if (key >= 33) {

                var maxLength = $(this).attr("maxlength");
                var length = this.value.length;

                // don't allow new content if length is maxed out
                if (length >= maxLength) {
                    event.preventDefault();
                }
            }
        });
        // watch textarea for release of key press
        $('#sendie').keyup(function(e) {

            if (e.keyCode == 13) {

                var text = $(this).val();
                var maxLength = $(this).attr("maxlength");
                var length = text.length;

                // send
                if (length <= maxLength + 1) {
                    chat.send(text, name);
                    $(this).val("");
                } else {
                    $(this).val(text.substring(0, maxLength));
                }
            }
        });
    });
</script>
<script type="text/javascript">
    function Chat () {
        this.update = updateChat;
        this.send = sendChat;
        this.getState = getStateOfChat;
    }
    function getStateOfChat() {
        if(!instanse){
            instanse = true;
            $.ajax({
                type: "POST",
                url: "process.php",
                data: {'function': 'getState', 'file': file},
                dataType: "json",
                success: function(data) {state = data.state;instanse = false;}
            });
        }
    }
    function updateChat() {
        if(!instanse){
            instanse = true;
            $.ajax({
                type: "POST",
                url: "process.php",
                data: {'function': 'update','state': state,'file': file},
                dataType: "json",
                success: function(data) {
                    if(data.text){
                        for (var i = 0; i < data.text.length; i++) {
                            $('#chat-area').append($("

                            "+ data.text[i] +"

                            "));
                        }
                    }
                    document.getElementById('chat-area').scrollTop = document.getElementById('chat-area').scrollHeight;
                    instanse = false;
                    state = data.state;
                }
            });
        }
        else {
            setTimeout(updateChat, 1500);
        }
    }
    function sendChat(message, nickname) {
        updateChat();
        $.ajax({
            type: "POST",
            url: "process.php",
            data: {'function': 'send','message': message,'nickname': nickname,'file': file},
            dataType: "json",
            success: function(data){
                updateChat();
            }
        });
    }
</script>