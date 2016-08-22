<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

require 'Login.php';

$jsonText = file_get_contents('php://input');


if(empty($jsonText))
{
    $response = array("status"=>-2,"message"=>"Empty request");
    die(json_encode($response));
}

$json = json_decode($jsonText);

$username = $json->user_name;
$password = $json->password;

$login = new Login($username,$password);
$response = $login->authenticate();

echo(json_encode($response));


?>