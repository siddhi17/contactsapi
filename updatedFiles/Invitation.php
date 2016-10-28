<?php

/**
 * Created by PhpStorm.
 * User: Siddhi
 * Date: 8/23/2016
 * Time: 8:16 PM
 */
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

require 'database.php';

class Invitation
{
    private $sender_id,$date,$invitee_no,$status,$invitations,$user_name;

    function Invitation($sender_id,$date,$invitee_no,$status,$user_name)
    {

       $this->sender_id = $sender_id;
        $this->date= $date;
        $this->invitee_no = $invitee_no;
        $this->status = $status;
        $this->user_name = $user_name;
       // $this -> invitations = $invitations;

    }

    function sendInvite()
    {

        $database = new Database(ContactsConstants::DBHOST, ContactsConstants::DBUSER, ContactsConstants::DBPASS, ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();

        $stmt = $dbConnection->prepare("select * from Invitation where invitee_no =?");
        $stmt->execute(array($this->invitee_no));
        $rows = $stmt->rowCount();


        if ($rows > 0) {
            $response = array("status" => -3, "message" => "Invitation exists.", "invitee_no" => $this->invitee_no);
            return $response;
        }

        $stmt = $dbConnection->prepare("insert into Invitation(sender_id,date,invitee_no,status,user_name) values(?,?,?,?,?)");

        $stmt->execute(array($this->sender_id,$this->date, $this->invitee_no, $this->status, $this -> user_name));

        $rows = $stmt->rowCount();
        $Id = $dbConnection->lastInsertId();

        $stmt = $dbConnection->prepare("select * from Invitation where invitation_id=?");
        $stmt->execute(array($Id));
        $invitation = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($rows < 1) {

            $response = array("status" => -1, "message" => "Failed to send Invitation., unknown reason");
            return $response;

        } else {
            $response = array("status" => 1, "message" => "Invitation sent.", "Invitation:" => $invitation);
            return $response;

        }

    }


    function sendMultipleInvites()
    {

        $database = new Database(ContactsConstants::DBHOST, ContactsConstants::DBUSER, ContactsConstants::DBPASS, ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();


      //  foreach($this->invitations as $invitation) {

         //   $date = $invitation->date;
          //  $invitee_no = $invitation->invitee_no;
         //   $status = $invitation->status;
        //    $sender_id = $invitation-> sender_id;


            $stmt = $dbConnection->prepare("select * from Invitation where invitee_no =?");
            $stmt->execute(array($this->invitee_no));
            $rows = $stmt->rowCount();


            if ($rows > 0) {
                $response = array("status" => -3, "message" => "Invitation exists.", "invitee_no" => $this->invitee_no,"invitation" => "");
                return $response;
            }

            $stmt = $dbConnection->prepare("insert into Invitation(sender_id,date,invitee_no,status,user_name) values(?,?,?,?,?)");

            $stmt->execute(array($this->sender_id, $this->date, $this->invitee_no, $this->status, $this->user_name));

            $rows = $stmt->rowCount();
            $Id = $dbConnection->lastInsertId();

            $stmt = $dbConnection->prepare("select * from Invitation where invitation_id=?");
            $stmt->execute(array($Id));
            $invitation = $stmt->fetch(PDO::FETCH_ASSOC);


            if ($rows < 1) {

                $response = array("status" => -1, "message" => "Failed to send Invitation., unknown reason");
                return $response;

            } else {
                $response = array("status" => 1, "invitation" => $invitation);
                return $response;

            }
       // }

    }

    function getInvitations()
    {
        $database = new Database(ContactsConstants::DBHOST,ContactsConstants::DBUSER,ContactsConstants::DBPASS,ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();

        $stmt = $dbConnection->prepare("SELECT Invitation.invitation_id, Invitation.sender_id, Invitation.date,Invitation.invitee_no,Invitation.status, Invitation.user_name, Users.user_name,Users.user_id, Users.profile_image FROM Invitation INNER JOIN Users ON Invitation.sender_id = Users.user_id WHERE Invitation.sender_id = ?");
        $stmt->execute(array($this -> sender_id));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $invitations = array();


        if (count($rows) > 0) {

            foreach($rows as $row)
            {
                $invitations[] = $row;
            }

            $response = array("status" => 1, "message" => "Success", "Invitations" => $invitations);
            return json_encode($response);
        }

        else {
            $response = array("status"=>-1,"message"=>"Invitations list is empty");
            return json_encode($response);
        }
    }

    function deleteInvitation($invitation_id)
    {
        $database = new Database(ContactsConstants::DBHOST,ContactsConstants::DBUSER,ContactsConstants::DBPASS,ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();

        $stmt = $dbConnection->prepare("select * from Invitation where `invitation_id` =?");
        $stmt->execute(array($invitation_id));
        $rows = $stmt->rowCount();

        if($rows == 0)
        {
            $response = array("status"=>-3,"message"=>"Invitation dose not exists.");
            return $response;
        }

        $stmt = $dbConnection->prepare("Delete from Invitation WHERE `invitation_id` = :invitation_id");

        $stmt->execute(array(":invitation_id"=>$invitation_id));

        $count = $stmt->rowCount();

        if($count > 0) {
            $response = array("status"=>1,"message"=>"Invitation Deleted Successfully.","Invitation"=>$count);
            return $response;
        }
        else {
            $response = array("status"=>-1,"message"=>"Failed to delete.");
            return $response;
        }
    }

    function updateInvitation($invitation_id,$status)
    {
        $database = new Database(ContactsConstants::DBHOST,ContactsConstants::DBUSER,ContactsConstants::DBPASS,ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();

        $stmt = $dbConnection->prepare("UPDATE Invitation SET `status` = :status
                                          WHERE `invitation_id` = :invitation_id");


        $stmt->execute(array(':status'=>$status,':invitation_id'=>$invitation_id));

        $count = $stmt->rowCount();

        if($count > 0) {
            $response = array("status"=>1,"message"=>"Invitation Updated Successfully.","Invitation"=>$count);
            return $response;
        }
        else {
            $response = array("status"=>-1,"message"=>"Failed to update.");
            return $response;
        }
    }

    // }
}