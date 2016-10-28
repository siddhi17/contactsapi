<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);
ini_set('display_errors', '1');

require 'database.php';

class User
{
    public $userId,$userName,$pass,$profileImage,$mobileNo,$deviceId,$emailId,$fullName,$work_address,$home_address,$work_no,$job_title;

    function User($userId,$userName,$pass,$profileImage,$mobileNo,$deviceId,$emailId,$fullName,$work_address,$home_address,$work_phone,$jobTitle)
    {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->pass = $pass;
        $this->profileImage = $profileImage;
        $this->mobileNo = $mobileNo;
        $this->deviceId = $deviceId;
        $this->emailId = $emailId;
        $this->fullName = $fullName;
        $this->work_address = $work_address;
        $this->home_address = $home_address;
        $this->work_no = $work_phone;
        $this->job_title = $jobTitle;
    }

    function getUser()
    {
        $database = new Database(ContactsConstants::DBHOST,ContactsConstants::DBUSER,ContactsConstants::DBPASS,ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();

        $stmt = $dbConnection->prepare("select * from Users where user_id =?");
        $stmt->execute(array($this->userId));
        $rows = $stmt->fetch(PDO::FETCH_ASSOC);

        if(count($rows) > 0) {
            $response = array("status"=>1,"message"=>"Success","user"=>$rows);
            return $response;
        }
        else {
            $response = array("status"=>-1,"message"=>"user dose not exists");
            return $response;
        }
    }

    function updateToken($userId,$token)
    {
        $database = new Database(ContactsConstants::DBHOST,ContactsConstants::DBUSER,ContactsConstants::DBPASS,ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();


        $stmt = $dbConnection->prepare("UPDATE Users SET `device_id` = :device_id WHERE `user_id` = :user_id");

        $stmt->execute(array(':device_id' => $token, ':user_id' => $userId));
        $count = $stmt->rowCount();

        if($count > 0) {
            $response = array("status"=>1,"message"=>"Token Updated Successfully.");
            return $response;
        }
        else {
            $response = array("status"=>-1,"message"=>"Failed to update token.");
            return $response;
        }
    }

    function updateUser()
    {

        $database = new Database(ContactsConstants::DBHOST,ContactsConstants::DBUSER,ContactsConstants::DBPASS,ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();

        if(!empty($this->profileImage)) {

            $filenamePath = md5(time() . uniqid()) . ".png";
            $decoded = base64_decode($this->profileImage);
            file_put_contents("profile_images/" . $filenamePath, $decoded);
        }
        else{
              $filenamePath = "";
        }


        $stmt = $dbConnection->prepare("Select * from Users where user_name =?");
        $stmt->execute(array($this->userName));

        $result = $stmt->fetch(PDO::FETCH_ASSOC);


        $mobilNo = $result["mobile_no"];
        $emailId = $result["email_id"];
        $fullName = $result['full_name'];
        $workAddress = $result['work_address'];
        $homeAddress = $result['home_address'];
        $workPhone = $result['work_phone'];
        $job_title = $result['job_title'];


        $stmt = $dbConnection->prepare("SELECT * from linkage where `user_id` =?");
        $stmt->execute(array($this->userId));
        $linkedrows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $linkedContacts = array();

        if (count($linkedrows) > 0) {
            foreach ($linkedrows as $row) {
                $l_contact_id = $row['linked_contact_id'];

                $stmt = $dbConnection->prepare("SELECT * from Users where `user_id` =?");
                $stmt->execute(array($l_contact_id));

                $r = $stmt->fetch(PDO::FETCH_ASSOC);

                $linkedContacts[] = $r;

            }
        }

        if(empty($filenamePath))
        {

            $stmt = $dbConnection->prepare("UPDATE Users SET `user_name` = :user_name, `password` = :password, `device_id` = :device_id, 
                                        `email_id` = :email_id, `mobile_no` = :mobile_no, `full_name` = :fullName, `work_address` = :work_address, `home_address` = :home_address,
                                         `work_phone` = :work_phone, `job_title` = :job_title WHERE `user_id` = :user_id");

            $stmt->execute(array(':user_name' => $this -> userName, ':password' => $this -> pass, ':device_id' => $this -> deviceId,
                ':email_id' => $this -> emailId, ':mobile_no' => $this -> mobileNo, ':fullName' => $this-> fullName, ':work_address' => $this-> work_address,
                ':home_address' => $this->home_address, ':work_phone' => $this->work_no, ':job_title' => $this->job_title, ':user_id' => $this -> userId));

            $count = $stmt->rowCount();


        }
        else {

            $stmt = $dbConnection->prepare("UPDATE Users SET `user_name` = :user_name, `password` = :password, `profile_image` = :profile_image,`device_id` = :device_id, 
                                        `email_id` = :email_id, `mobile_no` = :mobile_no, `full_name` = :fullName, `work_address` = :work_address, `home_address` = :home_address,
                                         `work_phone` = :work_phone, `job_title` = :job_title WHERE `user_id` = :user_id");

            $stmt->execute(array(':user_name' => $this->userName, ':password' => $this->pass, ':profile_image' => $filenamePath, ':device_id' => $this->deviceId,
                ':email_id' => $this->emailId, ':mobile_no' => $this->mobileNo, ':fullName' => $this->fullName, ':work_address' => $this->work_address,
                ':home_address' => $this->home_address, ':work_phone' => $this->work_no, ':job_title' => $this->job_title, ':user_id' => $this->userId));

            $count = $stmt->rowCount();
        }

        $api_key = "AIzaSyA1tR83CDRLGeSXSLPKMfvCZYAGouO3n9w";

        if($count > 0) {
            $response = array("status"=>1,"message"=>"User Updated Successfully.","user"=>$count);


            foreach ($linkedContacts as $contact) {


                if (strcmp($this->mobileNo, $mobilNo) != 0) {

                    $text = $this->userName . " " . "has updated mobile number.";

                        $token = $contact['device_id'];

                    $this->sendPush($text, $token, $api_key, $this->userId);

                }
                if (strcmp($this->emailId, $emailId) != 0) {

                    $text = $this->userName . " " . "has updated email id.";

                        $token = $contact['device_id'];

                        $this->sendPush($text, $token, $api_key,$this->userId);

                }
                if (strcmp($this->home_address, $homeAddress)  != 0) {

                    $text = $this->userName . " " . "has updated home address.";

                        $token = $contact['device_id'];

                        $this->sendPush($text, $token, $api_key,$this->userId);

                }
                if (strcmp($this->work_address, $workAddress)  != 0) {

                    $text = $this->userName . " " . "has updated work address.";

                        $token = $contact['device_id'];

                        $this->sendPush($text, $token, $api_key,$this->userId);

                }
                if (strcmp($this->work_no, $workPhone)  != 0) {

                    $text = $this->userName . " " . "has updated work phone number.";

                        $token = $contact['device_id'];

                        $this->sendPush($text, $token, $api_key,$this->userId);


                }
                if (strcmp($this->job_title, $job_title)  != 0) {

                    $text = $this->userName . " " . "has updated job title.";

                        $token = $contact['device_id'];

                        $this->sendPush($text, $token, $api_key,$this->userId);

                }
                if (strcmp($this->fullName, $fullName) != 0) {

                    $text = $this->userName . " " . "has updated full name.";

                        $token = $contact['device_id'];

                        $this->sendPush($text, $token, $api_key,$this->userId);
                }
            }

            return $response;
        }
        else {
            $response = array("status"=>-1,"message"=>"Failed to update.");
            return $response;
        }
    }
    public function sendPush($text, $tokens, $apiKey,$user_id)
    {

        $notification = array(
            "title" => "User updated profile.",
            "text" => $text,
            'vibrate' => 3,
            "click_action" => "OPEN_ACTIVITY_2",
            'sound' => "default",
            'user_id' => $user_id,
            "icon"=>"contacts_icon"
        );

        $data = array("user_id" => $user_id , "updateNotification" => "true");

        $msg = array
        (
            'message' => $text,
            'title' => 'User updated profile.',
            'tickerText' => 'New Message',
        );
        $fields = array
        (
            'to' => $tokens,
            'data' => $data,
            'notification' => $notification
        );

        $headers = array
        (
            'Authorization: key=' . $apiKey,
            'Content-Type: application/json'
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
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
?>