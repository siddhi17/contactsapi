<?php

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

   // $date = $json->date;
   // $invitee_no = $json->invitee_no;
   // $status = $json->status;

   // $invitations = $json -> invitations;

   // $invitation = new Invitation($invitations);
   // $response = $invitation->sendInvite();

$response = array();

foreach ($json as $jsn) {
    foreach($jsn as $j)
    {
        $date= $j -> date;
        $invitee_no = $j -> invitee_no;
        $status = $j -> status;
        $user_id = $j -> user_id;
        $invitation = new Invitation("",$date,$invitee_no,$status,$user_id);
        $response[] = $invitation->sendInvite();

    }

}
echo(json_encode($response));
?>