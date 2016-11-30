<?php
/**
 * Created by PhpStorm.
 * User: Siddhi
 * Date: 11/30/2016
 * Time: 12:53 PM
 */


header("Content-type: application/json");

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
$emailId = $json->email_id;

$user = new User("","","","","","",$emailId,"","","","","","");
$response = $user->getPass();

echo(json_encode($response));

?>