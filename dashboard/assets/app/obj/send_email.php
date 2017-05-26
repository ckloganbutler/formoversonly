<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/4/2017
 * Time: 5:55 AM
 */
function send_mail($email, $subject, $email, $headers){
    mail($email, $subject, $email, $headers);
}