<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

require 'database.php';
/**
 * Created by PhpStorm.
 * User: Siddhi
 * Date: 8/22/2016
 * Time: 2:55 PM
 */
class Contact
{

    private $unique_id,$card_name,$name,$telephone_no,$company_name,$department,$job_title,$home_address,$work_address,$user_id,$status;


    function Contact($unique_id,$card_name,$name,$telephone_no,$company_name,$department,$job_title,$home_address,$work_address,$user_id,$status)
    {

        $this->unique_id = $unique_id;
        $this->card_name = $card_name;
        $this->name = $name;
        $this->telephone_no = $telephone_no;
        $this->company_name = $company_name;
        $this->department = $department;
        $this->job_title = $job_title;
        $this->home_address = $home_address;
        $this->work_address = $work_address;
        $this->user_id = $user_id;
        $this->status = $status;
    }
    function createContact()
    {

        $database = new Database(ContactsConstants::DBHOST,ContactsConstants::DBUSER,ContactsConstants::DBPASS,ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();

        $stmt = $dbConnection->prepare("select * from contact where name=?");
        $stmt->execute(array($this->name));
        $rows = $stmt->rowCount();

        if($rows > 0)
        {
            $response = array("status"=>-3,"message"=>"contact exists.");
            return $response;
        }


        $stmt = $dbConnection->prepare("insert into contact(card_name,name,telephone_no,company_name,department,job_title,home_address,work_address,user_id,status) values(?,?,?,?,?,?,?,?,?,?)");
        $stmt->execute(array($this -> card_name,$this -> name,$this -> telephone_no,$this -> company_name,$this -> department,$this -> job_title, $this -> home_address,
            $this -> work_address,$this -> user_id,$this -> status));
        $rows = $stmt->rowCount();
        $Id = $dbConnection->lastInsertId();

        $stmt = $dbConnection->prepare("select * from contact where unique_id=?");
        $stmt->execute(array($Id));
        $contact = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if($rows < 1) {
            $response = array("status"=>-1,"message"=>"Failed to add contact., unknown reason");
            return $response;
        }
        else
        {
            $response = array("status"=>1,"message"=>"Contact created successfully.","contact"=>$contact);
            return $response;
        }

    }

    function getContacts()
    {
        $database = new Database(ContactsConstants::DBHOST,ContactsConstants::DBUSER,ContactsConstants::DBPASS,ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();

        $stmt = $dbConnection->prepare("SELECT contact.unique_id, contact.card_name, contact.name,contact.telephone_no,contact.company_name,contact.department,
                                        contact.job_title,contact.home_address,contact.work_address,contact.user_id,contact.status, Users.user_name, Users.user_id FROM contact INNER JOIN Users
                                        ON contact.user_id = Users.user_id WHERE contact.user_id = ?");
        $stmt->execute(array($this -> user_id));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $contacts = array();


            if (count($rows) > 0) {

                foreach($rows as $row)
                {
                    $contacts[] = $row;
                }

                $response = array("status" => 1, "message" => "Success", "contacts" => $contacts);
                return json_encode($response);
            }

        else {
            $response = array("status"=>-1,"message"=>"Contact list is empty");
            return json_encode($response);
        }
    }

    function updateContact()
    {
        $database = new Database(ContactsConstants::DBHOST,ContactsConstants::DBUSER,ContactsConstants::DBPASS,ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();

        $stmt = $dbConnection->prepare("UPDATE contact SET `card_name` = :card_name, `name` = :name, `telephone_no` = :telephone_no,`company_name` = :company_name, 
                                        `department` = :department, `job_title` = :job_title, `home_address` = :home_address, `work_address` = :work_address, `user_id` = :user_id, `status` = :status WHERE `unique_id` = :unique_id");

        $stmt->execute(array(':card_name' => $this -> card_name, ':name' => $this -> name,':telephone_no' => $this -> telephone_no,':company_name' => $this -> company_name,':department' => $this -> department,
            ':job_title' => $this -> job_title, ':home_address' => $this -> home_address,':work_address' => $this -> work_address,':user_id' => $this -> user_id, ':status' => $this -> status, ':unique_id' => $this -> unique_id));

        $count = $stmt->rowCount();

        if($count > 0) {
            $response = array("status"=>1,"message"=>"Contact Updated Successfully.","contact"=>$count);
            return $response;
        }
        else {
            $response = array("status"=>-1,"message"=>"Failed to update.");
            return $response;
        }
    }
    function deleteContact()
    {

        $database = new Database(ContactsConstants::DBHOST,ContactsConstants::DBUSER,ContactsConstants::DBPASS,ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();

        $stmt = $dbConnection->prepare("select * from contact where unique_id=?");
        $stmt->execute(array($this -> unique_id));
        $rows = $stmt->rowCount();

        if($rows == 0)
        {
            $response = array("status"=>-3,"message"=>"contact dose not exists.");
            return $response;
        }

        $stmt = $dbConnection->prepare("Delete from contact WHERE `unique_id` = :unique_id");

        $stmt->execute(array( ":unique_id" => $this -> unique_id ) );

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