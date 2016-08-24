<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

require 'Contact.php';

$jsonText = file_get_contents('php://input');
if(empty($jsonText))
{
    $response = array("status"=>-2,"message"=>"Empty request");
    die(json_encode($response));
}

$json = json_decode($jsonText);

$unique_id = $json -> unique_id;

$contact = new Contact($unique_id,"","","","","","","","");
$response = $contact->deleteContact();

echo(json_encode($response));

?>