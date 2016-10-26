<?php
/**
 * Created by PhpStorm.
 * User: Siddhi
 * Date: 10/26/2016
 * Time: 11:39 AM
 */


header("Content-type: application/json");

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

require 'Feedback.php';

$jsonText = file_get_contents('php://input');
if(empty($jsonText))
{
    $response = array("status"=>-2,"message"=>"Empty request");
    die(json_encode($response));
}

$json = json_decode($jsonText);

$name = $json -> name;
$email_id = $json -> email_id;
$comment = $json -> comment;

$feedback = new Feedback($name,$email_id,$comment);
$response = $feedback->createFeedback();

echo(json_encode($response));

?>