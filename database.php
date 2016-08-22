<?php
require 'contactsConstants.php';

class Database
{

    private $dbhost;
    private $dbuser;
    private $dbpass;
    private $dbname;


    function Database($dbhost,$dbuser,$dbpass,$dbname)
    {
        $this->dbhost = $dbhost;
        $this->dbuser = $dbuser;
        $this->dbpass = $dbpass;
        $this->dbname = $dbname;

    }
    function getDB()
    {

    	$mysql_conn_string = "mysql:host=$this->dbhost;dbname=$this->dbname;charset=utf8";

    	try{
    		$dbConnection = new PDO($mysql_conn_string, $this->dbuser, $this->dbpass);
    		$dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    	}
    	catch(PDOException $ex) {

    		echo($ex->getMessage());
    	}

    	return $dbConnection;
    }
}

?>