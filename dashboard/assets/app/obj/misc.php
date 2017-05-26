<?php
/**
 * Created by PhpStorm.
 * User: loganCk
 * Date: 3/4/2017
 * Time: 5:27 AM
 */
function generateRandomString($length) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function struuid($entropy){
    $s=uniqid("",$entropy);
    $num= hexdec(str_replace(".","",(string)$s));
    $index = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $base= strlen($index);
    $out = '';
    for($t = floor(log10($num) / log10($base)); $t >= 0; $t--) {
        $a = floor($num / pow($base,$t));
        $out = $out.substr($index,$a,1);
        $num = $num-($a*pow($base,$t));
    }
    return $out;
}
function compress_image($source_url, $destination_url, $quality) {
    $info = getimagesize($source_url);

    if ($info['mime'] == 'image/jpeg') $image = imagecreatefromjpeg($source_url);
    elseif ($info['mime'] == 'image/gif') $image = imagecreatefromgif($source_url);
    elseif ($info['mime'] == 'image/png') $image = imagecreatefrompng($source_url);

    //save it
    imagejpeg($image, $destination_url, $quality);

    //return destination file url
    return $destination_url;
}
function sentence_case($string) {
    $sentences = preg_split('/([.?!]+)/', $string, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
    $new_string = '';
    foreach ($sentences as $key => $sentence) {
        $new_string .= ($key & 1) == 0?
            ucwords(strtolower(trim($sentence))) :
            $sentence.' ';
    }
    return trim($new_string);
}
function cleanStr($string) {
    $string = str_replace('-', '', $string); // Replaces all spaces with hyphens.

    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}
function clean_phone($phone_number){
    $cleaned = preg_replace('/[^[:digit:]]/', '', $phone_number);
    preg_match('/(\d{3})(\d{3})(\d{4})/', $cleaned, $matches);
    return "({$matches[1]}) {$matches[2]}-{$matches[3]}";
}
function secret_mail($email)
{
    if(strlen($email) == 0){
        return "N/A";
    } else {
        $new_email = explode("@", $email);

        $mailname  = $new_email[0];
        $domain    = $new_email[1];

        $domain_l  = strlen($new_email[1]);
        for($k = 0; $k <= $domain_l; $k++){
            $domain_n.="x";
        }

        return $mailname."@".$domain_n;
    }
}