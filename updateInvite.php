<?php
/**
 * Created by PhpStorm.
 * User: Siddhi
 * Date: 10/5/2016
 * Time: 4:49 PM
 */

header('Content-Type: application/json');
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

$invitation_id = $json -> invitation_id;
$status = $json -> status;

$invitation = new Invitation("","","","","","");
$response = $invitation->updateInvitation($invitation_id,$status);

echo(json_encode($response));

?>