<?php

header("Content-type: application/json");

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

require 'Linkage.php';

$jsonText = file_get_contents('php://input');
if(empty($jsonText))
{
    $response = array("status"=>-2,"message"=>"Empty request");
    die(json_encode($response));
}

$json = json_decode($jsonText);

$user_id = $json -> user_id;
$linked_contact_id = $json -> linked_contact_id;

$linkage = new Linkage("",$user_id,$linked_contact_id);
$response = $linkage->deleteContact();

echo(json_encode($response));

?>