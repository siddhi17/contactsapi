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
    private $fullName;

    function Register($userName,$password,$profileImage,$mobileNo,$deviceId,$emailId,$fullName)
    {
        $this->userName = $userName;
        $this->password = $password;
        $this->profileImage = $profileImage;
        $this->mobileNo = $mobileNo;
        $this->deviceId = $deviceId;
        $this->emailId = $emailId;
        $this->fullName = $fullName;
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

   		if(!empty($this->profileImage)) {

            $filenamePath = md5(time() . uniqid()) . ".png";
            $decoded = base64_decode($this->profileImage);
            file_put_contents("profile_images/" . $filenamePath, $decoded);
        }
        else{
            $filenamePath = "";
        }

        $job_title ="";
        $work_address="";
        $home_address="";
        $work_phone ="";
        $company ="";


        $stmt = $dbConnection->prepare("insert into Users(user_name,password,profile_image,mobile_no,device_id,email_id,full_name,job_title,work_address,home_address,work_phone,company) values(?,?,?,?,?,?,?,?,?,?,?,?)");
   		$stmt->execute(array($this->userName,$this->password,$filenamePath,$this->mobileNo,$this->deviceId,$this->emailId,$this->fullName,$job_title,$work_address,$home_address,$work_phone,$company));
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

   public function base64_to_jpeg($base64_string, $output_file) {
        $ifp = fopen($output_file, "wb");

        $data = explode(',', $base64_string);

        fwrite($ifp, base64_decode($data[1]));
        fclose($ifp);

        return $output_file;
    }

}

?>