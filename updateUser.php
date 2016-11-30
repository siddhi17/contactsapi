<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

require 'User.php';

$jsonText = file_get_contents('php://input');
if(empty($jsonText))
{
    $response = array("status"=>-2,"message"=>"Empty request");
    die(json_encode($response));
}

$json = json_decode($jsonText);

$userId = $json-> user_id;
$userName = $json-> user_name;
$pass = $json-> password;
$profileImage = $json-> profile_image;
$mobileNo = $json-> mobile_no;
$deviceId = $json-> device_id;
$emailId = $json-> email_id;
$fullName = $json-> full_name;
$jobTitle = $json-> job_title;
$work_address = $json-> work_address;
$home_address = $json-> home_address;
$work_phone = $json-> work_phone;
$company = $json-> company;


$user = new User($userId,$userName,$pass,$profileImage,$mobileNo,$deviceId,$emailId,$fullName,$work_address,$home_address,$work_phone,$jobTitle,$company);
$response = $user->updateUser();

echo(json_encode($response));

?>