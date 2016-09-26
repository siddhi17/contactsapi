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
    $invitations = $json->invitations;
   // $status = $json->status;

 //   $invitations = $json -> invitations;

 //   $invitation = new Invitation("","","","","");
 //   $response = $invitation->sendMultipleInvites();

$response = array();

foreach ($invitations as $j) {
  //  foreach($json as $j)
 //   {
        $sender_id = $j -> sender_id;
        $date= $j -> date;
        $invitee_no = $j -> invitee_no;
       $status = $j -> status;
        $user_name = $j -> user_name;
        $invitation = new Invitation($sender_id,$date,$invitee_no,$status,$user_name);
        $response[] = $invitation->sendMultipleInvites();

  //  }

}

$result = array("result"=> 1,"invitations" => $response);
echo(json_encode($result));
?>