<?php
/**
 * Created by PhpStorm.
 * User: Siddhi
 * Date: 9/12/2016
 * Time: 5:38 PM
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

require 'Invitation.php';

$jsonText = file_get_contents('php://input');

if(empty($jsonText))
{
    $response = array("status"=>-2,"message"=>"Empty request");
    die(json_encode($response));
}

$json = json_decode($jsonText);

$sender_id = $json-> sender_id;
 $date = $json->date;
 $invitee_no = $json->invitee_no;
 $status = $json->status;
$user_name = $json -> user_name;

 $invitation = new Invitation($sender_id,$date,$invitee_no,$status,$user_name);
$response = $invitation->sendInvite();


echo(json_encode($response));
?>