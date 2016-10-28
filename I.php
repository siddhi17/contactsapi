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
    private $sender_id,$date,$invitee_no,$status,$invitations,$user_name,$contact_id,$contact_name;

    function Invitation($sender_id,$date,$invitee_no,$status,$user_name,$contact_id,$contact_name)
    {

        $this->sender_id = $sender_id;
        $this->date= $date;
        $this->invitee_no = $invitee_no;
        $this->status = $status;
        $this->user_name = $user_name;
        $this->contact_id = $contact_id;
        $this->contact_name = $contact_name;
        // $this -> invitations = $invitations;

    }

    function sendInvite()
    {

        $database = new Database(ContactsConstants::DBHOST, ContactsConstants::DBUSER, ContactsConstants::DBPASS, ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();

        $stmt = $dbConnection->prepare("select * from Invitation where user_name =? and sender_id = ?");
        $stmt->execute(array($this->user_name,$this->sender_id));
        $rows = $stmt->rowCount();


        if ($rows > 0) {
            $response = array("status" => -3, "message" => "Invitation exists.", "user_name" => $this->user_name);
            return $response;
        }

        $this->date = "";
        $this->invitee_no = "";
        $this->status = "0";
        $this->contact_id = 0;
        $this->contact_name = "";


        $stmt = $dbConnection->prepare("insert into Invitation(sender_id,date,invitee_no,status,user_name,contact_id,contact_name) values(?,?,?,?,?,?,?)");

        $stmt->execute(array($this->sender_id, $this->date, $this->invitee_no, $this->status, $this->user_name,$this->contact_id,$this->contact_name));

        $rows = $stmt->rowCount();
        $Id = $dbConnection->lastInsertId();

        $stmt = $dbConnection->prepare("Select device_id from Users where user_name =?");
        $stmt->execute(array($this->user_name));

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $token = $result["device_id"];


        // $token = $row['device_id'];

        //  echo $token;
        $message =  'Hi,add me to your unique contact list and you never need to update" +
                        " any changes anymore!';
        //   $data = array('post_id'=>'12345','post_title'=>'\'Hi,add me to your unique contact list and you never need to update" +
        //                 " any changes anymore!');
        // if(!empty($token))
        //   {
        /*  $url = 'https://fcm.googleapis.com/fcm/send';
//api_key available in Firebase Console -> Project Settings -> CLOUD MESSAGING -> Server key
          $server_key = 'AIzaSyBGwwJaThyLm-PhvgcbdYurj-bYQQ7XmCc';

          $fields = array();
          $fields['data'] = $data;
          if(is_array($token)){
              $fields['registration_ids'] = $token;
          }else{
              $fields['to'] = $token;
          }
//header with content_type api key
          $headers = array(
              'Content-Type:application/json',
              'Authorization:key='.$server_key
          );

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_POST, true);
          curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
          curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
          curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
          curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
          $result = curl_exec($ch);
          if ($result === FALSE) {
              die('FCM Send Error: ' . curl_error($ch));
          }
          curl_close($ch);
          echo $result;*/

        //   $token = "d2bAfWa0aIM:APA91bGEO-8yuHbRAVSQc9tYDkEzOgRwUbH7V8QmaEcvUibO4wH7GOKgRjYnXK5TP941_3zporIdICfhI976WsmejDlM4zWqVjapf8h67xSeXj_Zmj2H-TjZxGmeRozuSUkUBnw_ofJe";

        $message = 'Hi, add me to your unique contact list and you never need to update any changes anymore!';

        $post = array("message" => "Hi add me to your unique contact list and you never need to update any changes anymore","title" => "You got an Invitation");

        $data = array('message'=>'\"Hi,add me to your unique contact list and you never need to update any changes anymore!\"','title'=>'\"A Blog post\"');
        //  $data = array('message'=>'Hi,add me to your unique contact list and you never need to update any changes anymore!');

        if (!empty($token)) {
            $url = 'https://fcm.googleapis.com/fcm/send';

            //    $fields = array();
            //   $fields['data'] =  array('message' => $message);
            //   $fields['notification'] = array('title' => 'You got an Invitation', 'body' => 'Hi,add me to your unique contact list and you never need to update any changes anymore!');
            //  $fields['to'] = $token;
            $notification = array (
                "title" =>  "You got an invitation",
                "text" => 'Hi add me to your unique contact list and you never need to update any changes anymore',
                'vibrate'	=> 1,
                'sound'		=> "default"
            );

            $msg = array
            (
                'message' 	=> 'Hi add me to your unique contact list and you never need to update any changes anymore',
                'title'		=> 'You got an invitation'
            );
            $fields = array
            (
                'to' 	=> $token,
                'data'			=> $msg,
                "notification"=> $notification
            );

            //    $fields = array();
            //    $fields['data'] = $data;
            //  $fields['to'] = $token;

            //   $fields = $fields;

            $headers = array(
                'Authorization: key=' . "AIzaSyBGwwJaThyLm-PhvgcbdYurj-bYQQ7XmCc",
                'Content-Type: application/json'
            );

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt( $ch,CURLOPT_POST, true );
            curl_setopt( $ch,CURLOPT_HTTPHEADER, $headers );
            curl_setopt( $ch,CURLOPT_RETURNTRANSFER, true );
            curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );
            curl_setopt( $ch,CURLOPT_POSTFIELDS, json_encode( $fields ) );

            $result = curl_exec($ch );
            echo($result);

            curl_close($ch);
        }

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


        $stmt = $dbConnection->prepare("select * from Invitation where sender_id =? && contact_id =?");
        $stmt->execute(array($this->sender_id,$this->contact_id));
        $rows = $stmt->rowCount();

        if ($rows > 0) {
            $response = array("status" => -3, "message" => "Invitation exists.", "invitee_no" => $this->invitee_no,"invitation" => "");
            return $response;
        }

        $this->user_name ="";

        $stmt = $dbConnection->prepare("insert into Invitation(sender_id,date,invitee_no,status,user_name,contact_id,contact_name) values(?,?,?,?,?,?,?)");

        $stmt->execute(array($this->sender_id, $this->date, $this->invitee_no, $this->status, $this->user_name,$this->contact_id,$this->contact_name));

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

        $stmt = $dbConnection->prepare("SELECT Invitation.invitation_id, Invitation.sender_id, Invitation.date, Invitation.invitee_no, Invitation.status, Invitation.contact_name, Invitation.user_name, Invitation.contact_id, Users.user_id, Users.profile_image FROM Invitation INNER JOIN Users ON Invitation.sender_id = Users.user_id where Invitation.invitee_no = ? AND Invitation.status = 0");
        $stmt->execute(array($this-> invitee_no));
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