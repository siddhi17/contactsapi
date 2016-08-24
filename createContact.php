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

$card_name = $json-> card_name;
$name = $json-> name;
$telephone_no = $json-> telephone_no;
$company_name = $json-> company_name;
$department = $json-> department;
$job_title = $json-> job_title;
$home_address = $json-> home_address;
$work_address = $json-> work_address;
$user_id = $json-> user_id;
$status = $json-> status;

$contact = new Contact("",$card_name,$name,$telephone_no,$company_name,$department,$job_title,$home_address,$work_address,$user_id,$status);
$response = $contact->createContact();

echo(json_encode($response));

?>