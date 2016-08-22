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
    private $unique_id,$card_name,$name,$telephone_no,$company_name,$department,$job_title,$home_address,$work_address;

    function Contact($card_name,$name,$telephone_no,$company_name,$department,$job_title,$home_address,$work_address)
    {

        $this->card_name = $card_name;
        $this->name = $name;
        $this->telephone_no = $telephone_no;
        $this->company_name = $company_name;
        $this->department = $department;
        $this->job_title = $job_title;
        $this->home_address = $home_address;
        $this->work_address = $work_address;
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
            $response = array("status"=>-2,"message"=>"contact exists.");
            return $response;
        }


        $stmt = $dbConnection->prepare("insert into contact(card_name,name,telephone_no,company_name,department,job_title,home_address,work_address) values(?,?,?,?,?,?,?,?)");
        $stmt->execute(array($this->card_name,$this->name,$this->telephone_no,$this->company_name,$this->department,$this->job_title,$this->home_address,$this->work_address));
        $rows = $stmt->rowCount();


        if($rows < 1) {
            $response = array("status"=>-1,"message"=>"unable to register, unknown reason");
            return $response;
        }
        else
        {
            $response = array("status"=>1,"message"=>"Success");
            return $response;
        }

    }

    function getContacts()
    {
        $database = new Database(ContactsConstants::DBHOST,ContactsConstants::DBUSER,ContactsConstants::DBPASS,ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();

        $stmt = $dbConnection->prepare("select * from contact");
        $stmt->execute();
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
            return $response;
        }
    }
}