<?php

/**
 * Created by PhpStorm.
 * User: Siddhi
 * Date: 8/23/2016
 * Time: 8:16 PM
 */
error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

require_once 'database.php';

class Invitation
{
    private $sender_id,$date,$invitee_no,$status,$username;


    function Invitation($sender_id,$date,$invitee_no,$status,$user_name)
    {

        $this->sender_id = $sender_id;
        $this->date= $date;
        $this->invitee_no = $invitee_no;
        $this->status = $status;
        $this->user_name = $user_name;

    }

    function sendInvite()
    {

        $database = new Database(ContactsConstants::DBHOST, ContactsConstants::DBUSER, ContactsConstants::DBPASS, ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();

        $stmt = $dbConnection->prepare("Select device_id,mobile_no from Users where user_name =?");
        $stmt->execute(array($this->user_name));

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $token = $result["device_id"];

        $this->invitee_no = $result["mobile_no"];

        $stmt = $dbConnection->prepare("select * from Invitation where invitee_no = ? && sender_id =?");
        $stmt->execute(array($this->invitee_no,$this->sender_id));
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $response = array("status" => -3, "message" => "Invitation exists.", "invitee_no" => $this->invitee_no);
            return $response;
        }

        $stmt = $dbConnection->prepare("insert into Invitation(sender_id,date,invitee_no,status) values(?,?,?,?)");

        $stmt->execute(array($this->sender_id, $this->date, $this->invitee_no, $this->status));

        $rows = $stmt->rowCount();
        $Id = $dbConnection->lastInsertId();


        $stmt = $dbConnection->prepare("select * from Invitation where invitation_id=?");
        $stmt->execute(array($Id));
        $invitation = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($rows < 1) {

            $response = array("status" => -1, "message" => "Failed to send Invitation., unknown reason");
            return $response;

        } else {

            $message =  'Hi,add me to your unique contact list and you never need to update any changes anymore!';

            $server_key = 'AIzaSyBGwwJaThyLm-PhvgcbdYurj-bYQQ7XmCc';

            $this->sendPush($message,$token,$server_key);

            $response = array("status" => 1, "message" => "Invitation sent.", "Invitation:" => $invitation);
            return $response;
        }
    }

    function sendMultipleInvites()
    {

        $database = new Database(ContactsConstants::DBHOST, ContactsConstants::DBUSER, ContactsConstants::DBPASS, ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();


        $stmt = $dbConnection->prepare("select * from Invitation where sender_id =? && invitee_no =?");
        $stmt->execute(array($this->sender_id,$this->invitee_no));
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $response = array("status" => -3, "message" => "Invitation exists.", "invitee_no" => $this->invitee_no);
            return $response;
        }

        $stmt = $dbConnection->prepare("insert into Invitation(sender_id,date,invitee_no,status) values(?,?,?,?)");

        $stmt->execute(array($this->sender_id, $this->date, $this->invitee_no, $this->status));

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

    }

    function getInvitations()
    {
        $database = new Database(ContactsConstants::DBHOST,ContactsConstants::DBUSER,ContactsConstants::DBPASS,ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();

        $stmt = $dbConnection->prepare("SELECT * FROM `Invitation` WHERE Invitation.invitee_no = ?");
        $stmt->execute(array($this-> invitee_no));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $invitations = array();

        if (count($rows) > 0) {

            foreach($rows as $row)
            {
                $stmt = $dbConnection->prepare("Select * from Users where user_id =?");
                $stmt->execute(array($row['sender_id']));

                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                $final_array = array_merge($row,(array)$result);
                $invitations[] = $final_array;
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


    public function sendPush($text, $tokens, $apiKey)
    {

        $notification = array(
            "title" => "You got an invitation.",
            "text" => $text,
            "click_action" => "OPEN_ACTIVITY_1"
        );

        $msg = array
        (
            'message' => $text,
            'title' => 'You got an invitation.',
        );
        $fields = array
        (
            'to' => $tokens,
            'data' => $msg,
            'notification' => $notification
        );

        $headers = array
        (
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result = curl_exec($ch);
        //  echo($result);
        //    return $result;
        curl_close($ch);
    }
}