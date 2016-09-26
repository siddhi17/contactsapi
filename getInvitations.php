<?php

header("Content-type: application/json");

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

require 'Invitation.php';

    $jsonText = file_get_contents('php://input');

    $json = json_decode($jsonText);

    $user_name = $json-> user_name;

    $invitation = new Invitation("","","","",$user_name);
    $response = $invitation -> getInvitations();

    if ( $response == null ) {
        $response = json_encode(array("result" => -2, "message" => "Empty result"));
        echo $response;
    } else {
        echo $response;
    }

?>