<?php
/**
 * Created by PhpStorm.
 * User: Siddhi
 * Date: 10/13/2016
 * Time: 4:33 PM
 */

header("Content-type: application/json");

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

require_once 'notification.php';

$jsonText = file_get_contents('php://input');

if(empty($jsonText))
{
    $response = array("status"=>-2,"message"=>"Empty request");
    die(json_encode($response));
}

$json = json_decode($jsonText);

$user_name = $json-> user_name;
$text = $json -> text;

$notify = new notification();
$response = $notify->send($text,$user_name);

echo(json_encode($response));

?>