<?php
/**
 * Created by PhpStorm.
 * User: Siddhi
 * Date: 9/12/2016
 * Time: 5:38 PM
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

require_once 'Invitation.php';

$jsonText = file_get_contents('php://input');

if(empty($jsonText))
{
    $response = array("status"=>-2,"message"=>"Empty request");
    die(json_encode($response));
}

$j = json_decode($jsonText);

$sender_id = $j -> sender_id;
$date = $j -> date;
$status = $j -> status;
$username = $j -> user_name;

$invitation = new Invitation($sender_id,$date,"",$status,$username);
$response = $invitation->sendInvite();


//$message =  'Hi,add me to your unique contact list and you never need to update any changes anymore!';

//$invitation->setNotification($message,$user_name);


echo(json_encode($response));
?>