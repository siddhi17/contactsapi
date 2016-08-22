<?php
/**
 * Created by PhpStorm.
 * User: Siddhi
 * Date: 8/22/2016
 * Time: 12:43 PM
 */

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
$userId = $json->userId;

$user = new User($userId);
$response = $user->getUser($userId);

echo(json_encode($response));

?>