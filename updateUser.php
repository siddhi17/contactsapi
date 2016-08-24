<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

require 'User';

$jsonText = file_get_contents('php://input');
if(empty($jsonText))
{
    $response = array("status"=>-2,"message"=>"Empty request");
    die(json_encode($response));
}

$json = json_decode($jsonText);

$userId = $json->user_id;
$userName = $json->user_name;
$pass  = $json->password;
$profileImage = $json->profile_image;
$mobileNo = $json->mobile_no;
$deviceId = $json->device_id;
$emailId = $json->email_id;
$user_id = $json-> user_id;
$status = $json-> status;

$user = new User($userId,$userName,$pass,$profileImage,$mobileNo,$deviceId,$emailId,$user_id,$status);
$response = $user->updateUser();

echo(json_encode($response));

?>