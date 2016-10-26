<?php

/**
 * Created by PhpStorm.
 * User: Siddhi
 * Date: 10/26/2016
 * Time: 11:29 AM
 */

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

require 'database.php';

class Feedback
{
    public $name,$email_id,$comment;

    function Feedback($name,$email_id,$comment)
    {
        $this->name = $name;
        $this->email_id = $email_id;
        $this->comment = $comment;
    }


    function createFeedback()
    {

        $database = new Database(ContactsConstants::DBHOST,ContactsConstants::DBUSER,ContactsConstants::DBPASS,ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();


        $stmt = $dbConnection->prepare("insert into feedback(name,email_id,comment) values(?,?,?)");
        $stmt->execute(array($this->name,$this->email_id,$this->comment));
        $rows = $stmt->rowCount();


        if($rows < 1) {
            $response = array("status"=>-1,"message"=>"unable to create feedback.");
            return $response;
        }
        else
        {
            $response = array("status"=>1,"message"=>"Feedback created.");
            return $response;
        }

    }

}