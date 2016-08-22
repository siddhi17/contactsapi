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

        $stmt = $dbConnection->prepare("select * from users where user_name=? and password=?");
    	$stmt->execute(array($this->username,$this->password));
   	 	$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    	if(count($rows) < 1) {

   			$response = array("status"=>-1,"message"=>"user doesnt exists");
   			return $response;
    	}
        else
        {
            $response = array("status"=>1,"message"=>"user authenticated successfully","user"=>$rows[0]);
            return $response;
        }


    }

}

?>