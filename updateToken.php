<?php
/**
 * Created by PhpStorm.
 * User: Siddhi
 * Date: 10/22/2016
 * Time: 12:40 PM
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

$user_id = $json -> user_id;
$token = $json -> token;

$user = new User("","","","","","","","","","","","");
$response = $user->updateToken($user_id,$token);

echo(json_encode($response));

?>