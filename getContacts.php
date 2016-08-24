<?php
/**
 * Created by PhpStorm.
 * User: Siddhi
 * Date: 8/22/2016
 * Time: 12:43 PM
 */
header("Content-type: application/json");

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

require 'Contact.php';

    $jsonText = file_get_contents('php://input');

    $json = json_decode($jsonText);
    $user_id = $json->user_id;

    $contact = new Contact("","","","","","","","","",$user_id,"");
    $response = $contact->getContacts();

    if ( $response == null ) {
        $response = json_encode(array("result" => -2, "message" => "Empty result"));
        echo $response;
    } else {
        echo $response;
    }


?>