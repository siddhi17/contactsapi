<?php

/**
 * Created by PhpStorm.
 * User: Siddhi
 * Date: 9/4/2016
 * Time: 11:25 AM
 */
require 'database.php';


class Linkage
{

    private $id,$user_id,$linked_contact_id;

    function Linkage($id,$user_id,$linked_contact_id)
    {

        $this-> user_id = $user_id;
        $this->linked_contact_id = $linked_contact_id;
        $this->id = $id;
    }

    function createLinkageId(){

        $database = new Database(ContactsConstants::DBHOST,ContactsConstants::DBUSER,ContactsConstants::DBPASS,ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();


        $stmt = $dbConnection->prepare("SELECT * from linkage where `linked_contact_id` =? && `user_id` =?");
        $stmt->execute(array($this->linked_contact_id,$this->user_id));
        $rows = $stmt->rowCount();

        if($rows > 0) {
            $response = array("status"=>-3,"message"=>"Linked contact exists.");
            return $response;
        }

        else {

            $stmt = $dbConnection->prepare("insert into linkage(user_id,linked_contact_id) values(?,?)");
            $stmt->execute(array($this->linked_contact_id, $this->user_id));
            $rows = $stmt->rowCount();

            $stmt = $dbConnection->prepare("insert into linkage(user_id,linked_contact_id) values(?,?)");
            $stmt->execute(array($this->user_id, $this->linked_contact_id));
            $rows = $stmt->rowCount();


            if ($rows < 1) {
                $response = array("status" => -1, "message" => "unable to create linked contact, unknown reason");
                return $response;
            } else {
                $response = array("status" => 1, "message" => "Success, Contact is linked.");
                return $response;
            }
        }

    }

    function getLinkedContacts()
    {


        $database = new Database(ContactsConstants::DBHOST,ContactsConstants::DBUSER,ContactsConstants::DBPASS,ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();

        $stmt = $dbConnection->prepare("SELECT * from linkage where `user_id` =?");
       $stmt->execute(array($this->user_id));
       $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $linkedContacts = array();

        $count = $stmt->rowCount();

        if($count > 0)

        {
            foreach($rows as $row)
             {
               $l_contact_id = $row['linked_contact_id'];

               $stmt = $dbConnection->prepare("SELECT * from Users where `user_id` =?");
               $stmt->execute(array($l_contact_id));

                 $r = $stmt->fetch(PDO::FETCH_ASSOC);
                 $linkedContacts[] = $r;
            }

            $response = array("status" => 1, "message" => "Success", "contacts" => $linkedContacts);
           return json_encode($response);
        }

        else {
                $response = array("status"=>-1,"message"=>"Contact list is empty");
                return json_encode($response);
     }
   }

    function deleteContact()
    {

        $database = new Database(ContactsConstants::DBHOST,ContactsConstants::DBUSER,ContactsConstants::DBPASS,ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();

        $stmt = $dbConnection->prepare("SELECT * FROM linkage WHERE `user_id` IN (`user_id`,`linked_contact_id`) && `linked_contact_id` IN (`linked_contact_id`,`user_id`)");
        $stmt->execute(array($this->user_id,$this->linked_contact_id,$this->linked_contact_id,$this->user_id));
        $rows = $stmt->rowCount();

        if($rows == 0)
        {
            $response = array("status"=>-3,"message"=>"contact dose not exists.");
            return $response;
        }

        $stmt = $dbConnection->prepare("Delete from linkage WHERE `user_id` = :user_id && `linked_contact_id` = :linked_contact_id");

        $stmt->execute(array(":user_id"=>$this->user_id,":linked_contact_id" => $this->linked_contact_id));

        $count = $stmt->rowCount();

        $stmt = $dbConnection->prepare("Delete from linkage WHERE `user_id` = :user_id && `linked_contact_id` = :linked_contact_id");

        $stmt->execute(array(":user_id"=>$this->linked_contact_id,":linked_contact_id" => $this->user_id));

        $count = $stmt->rowCount();

        if($count > 0) {
            $response = array("status"=>1,"message"=>"Contact Deleted Successfully.","contact"=>$count);
            return $response;
        }
        else {
            $response = array("status"=>-1,"message"=>"Failed to delete.");
            return $response;
        }
    }

}