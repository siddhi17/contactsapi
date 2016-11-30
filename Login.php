<?php

require 'database.php';

class Login
{
    private $username;
    private $password;

    function Login($user,$pwd)
    {
        $this->username = $user;
        $this->password = $pwd;
    }
    function authenticate()
    {
        $database = new Database(ContactsConstants::DBHOST,ContactsConstants::DBUSER,ContactsConstants::DBPASS,ContactsConstants::DBNAME);
        $dbConnection = $database->getDB();

        $stmt = $dbConnection->prepare("select * from Users where user_name=? and password=?");
    	$stmt->execute(array($this->username,$this->password));
   	 	$rows = $stmt->fetch(PDO::FETCH_ASSOC);

    	if(count($rows) < 1) {

   			$response = array("status"=>-1,"message"=>"user doesn't exists");
   			return $response;
    	}
        else
        {
            $username = $rows['user_name'];
            $password = $rows['password'];

            if($username == $this->username && $password == $this->password) {
                $response = array("status" => 1, "message" => "user authenticated successfully", "user" => $rows);
                return $response;
            }
            else
            {
                $response = array("status"=>-1,"message"=>"user doesn't exists");
                return $response;
            }
        }
    }

}

?>