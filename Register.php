<?php

require 'database.php';

class Register
{
    private $userName;
    private $password;
    private $profileImage;
    private $mobileNo;
    private $deviceId;
    private $emailId;

    function Register($userName,$password,$profileImage,$mobileNo,$deviceId,$emailId)
    {
        $this->userName = $userName;
        $this->password = $password;
        $this->profileImage = $profileImage;
        $this->mobileNo = $mobileNo;
        $this->deviceId = $deviceId;
        $this->emailId = $emailId;
    }

    function RegisterUser()
    {

        $database = new Database(ContactsConstants::DBHOST,ContactsConstants::DBUSER,ContactsConstants::DBPASS,ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();

        $stmt = $dbConnection->prepare("select * from Users where user_name=?");
   		$stmt->execute(array($this->userName));
   		$rows = $stmt->rowCount();


   		if($rows > 0)
   		{
   			$response = array("status"=>-2,"message"=>"user exists");
   			return $response;
   		}


        $filenamePath = md5(time().uniqid()).".jpg";
    	$decoded=base64_decode($this->profileImage);
    	file_put_contents("profile_images/".$filenamePath,$decoded);

        $stmt = $dbConnection->prepare("insert into Users(user_name,password,profile_image,mobile_no,device_id,email_id) values(?,?,?,?,?,?)");
   		$stmt->execute(array($this->userName,$this->password,$this->profileImage,$this->mobileNo,$this->deviceId,$this->emailId));
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

}

?>