<?php

class guess_num {
public $servername = "localhost";
public $username = "root";
public $password = "";
public $dbname = "linechatbot";
    function __construct() {
        //echo 'Main controller<br/>';
        
    }
    function user_exist($u_id){
         $conn = new mysqli($servername, $username, $password, $dbname);
         $sql1 = "SELECT * FROM user_profile WHERE  line_id = '". $u_id ."'";
        $result = mysqli_query($conn, $sql1);
        if (mysqli_num_rows($result) > 0) {
            // output data of each row
            while($row = mysqli_fetch_assoc($result)) {        
               $sq3 = "UPDATE guessnum SET status='unfinished' WHERE idguessnum=" . $row["idguessnum"]; 
                 $conn->query($sq3);
            }
        }else{
             $sql2 = "INSERT INTO user_profile (line_id, line_displayName, line_pictureUrl,line_statusMessage)
                VALUES ('" .$u_id . "', 'playing', '" . rand(1000, 9999) . "')";
            $conn->query($sql2);
        }
        
        
            $return_text = [
                 "replyToken" => $reply_token,
                 "messages" => [
                [
                    "type" => "text",
                    "text" => "game start"
                ]
                ]
            ];
    }
    function createuser($u_id) {
        
    }
}