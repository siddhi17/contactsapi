<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

require 'Register.php';

$jsonText = file_get_contents('php://input');

if(empty($jsonText))
{
    $response = array("status"=>-2,"message"=>"Empty request");
    die(json_encode($response));
}

$json = json_decode($jsonText);

$userName = $json->user_name;
$password = $json->password;
$profileImage = $json->profile_image;
$mobileNo = $json->mobile_no;
$deviceId = $json->device_id;
$emailId = $json->email_id;
$fullName = $json->fullName;

$Register = new Register($userName,$password,$profileImage,$mobileNo,$deviceId,$emailId,$fullName);
$response = $Register->RegisterUser();

echo(json_encode($response));

?>