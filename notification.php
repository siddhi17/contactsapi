<?php
/**
 * Created by PhpStorm.
 * User: Siddhi
 * Date: 10/13/2016
 * Time: 4:14 PM
 */


require_once 'database.php';

class notification
{

    private $text,$user_name;
    public $dbConnection;

    public function __construct()
    {

    }
 //   public function setNotification($text, $username) {
  //      $this->text = $text;
  //      $this->user_name = $username;
 //   }
    public function send($text, $username)
    {

        $database = new Database(ContactsConstants::DBHOST, ContactsConstants::DBUSER, ContactsConstants::DBPASS, ContactsConstants::DBNAME);
        $this->dbConnection = $database->getDB();

        $stmt = $this->dbConnection->prepare("Select device_id from Users where user_name =?");
        $stmt->execute(array($username));

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $token = $result["device_id"];


        if(!empty($token)) {
            echo $token;
            $response = $this->sendPush($text, $token, "AIzaSyBGwwJaThyLm-PhvgcbdYurj-bYQQ7XmCc");
        }
    }

    public function sendPush($text, $tokens, $apiKey)
    {

        $notification = array(
            "title" => "You got an invitation.",
            "text" => $text,
            "icon" => "ic_chat_bubble_white_48dp",
            'vibrate' => 3,
            'sound' => "default"
        );

        $msg = array
        (
            'message' => $text,
            'title' => 'You got an invitation.',
            'tickerText' => 'New Message',
            'largeIcon' => 'large_icon',
            'smallIcon' => 'small_icon'
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
        echo($result);
        return $result;
        curl_close($ch);
    }
}
?>