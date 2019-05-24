<?php
$usergnum = $_GET["n"];
$ans_length = 4;
$a_count = 0;
$b_count = 0;
$answer;
$problem_id;
$problem_att_time;
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "linechatbot";
$conn = new mysqli($servername, $username, $password, $dbname);
$access_token ='token';


$json_string = file_get_contents('php://input');

$file = fopen("D:\\Line_log.txt", "a+");
fwrite($file, $json_string."\n"); 
$json_obj = json_decode($json_string);

$event = $json_obj->{"events"}[0];
$userID = $event->{"source"}->{"userId"};
$type  = $event->{"message"}->{"type"};
$message = $event->{"message"};
$reply_token = $event->{"replyToken"};

switch ($message->{"text"}) {
    case 'start':
        
        $sql1 = "SELECT idguessnum , answer , times FROM guessnum WHERE status='playing' and userID = '". $userID ."'";
        $result = mysqli_query($conn, $sql1);
        if (mysqli_num_rows($result) > 0) {
            // output data of each row
            while($row = mysqli_fetch_assoc($result)) {        
               $sq3 = "UPDATE guessnum SET status='unfinished' WHERE idguessnum=" . $row["idguessnum"]; 
                 $conn->query($sq3);
            }
        }
         $sql2 = "INSERT INTO guessnum (userID, status, answer)
                VALUES ('" .$userID . "', 'playing', '" . rand(1000, 9999) . "')";
            $conn->query($sql2);
            $return_text = [
                 "replyToken" => $reply_token,
                 "messages" => [
                [
                    "type" => "text",
                    "text" => "game start"
                ]
                ]
            ];
        break;
    
    default:
        $usergnum = $message->{"text"};
        $sql1 = "SELECT idguessnum , answer , times FROM guessnum WHERE status='playing' and userID = '". $userID ."'";
        $result = mysqli_query($conn, $sql1);
        if (mysqli_num_rows($result) > 0) {
            // output data of each row
            while($row = mysqli_fetch_assoc($result)) {        
                $answer = $row["answer"];
                $problem_id = $row["idguessnum"];
                $problem_att_time = $row["times"];
            }
            $ans_length = strlen($answer);
            $ans = array();
            for ($x = 0; $x < $ans_length; $x++) {
                  array_unshift($ans,array($answer%10, 0));    
                  $answer = floor( $answer/10);
                } 
                
              $arrayobj = array();
              for ($x = 0; $x < $ans_length; $x++) {
                  array_unshift($arrayobj,array($usergnum%10, 0));    
                  $usergnum = floor( $usergnum/10);
              }              
              for ($x = 0; $x < $ans_length; $x++) {
                  if($ans[$x][0] == $arrayobj[$x][0])
                  {
                     $arrayobj[$x][1] = 1;
                     $ans[$x][1] = 1;
                     $a_count += 1;
                  }    
              } 
              for ($x = 0; $x < $ans_length; $x++) {
                  if ( $arrayobj[$x][1] != 1)
                  {
                      for ($y = 0; $y < $ans_length; $y++) {
                          if ( $ans[$y][1] != 1)
                          {
                              if ( $ans[$y][0] == $arrayobj[$x][0])
                              {
                                  $arrayobj[$x][1] = 1;
                                  $ans[$y][1] = 1;
                                  $b_count += 1;
                                  break;
                              }
                          }
                      }

                  }   
              }
             if($a_count == $ans_length)
             {
                 $sq3 = "UPDATE guessnum SET status='win' WHERE idguessnum=" . $problem_id;   
                 $return_text = [
                 "replyToken" => $reply_token,
                 "messages" => [
                [
                    "type" => "text",
                    "text" => "You Win (you have tried ".$problem_att_time . " times)"
                ]
                ]
                ];
             }else{
                 $problem_att_time +=1;
                 $sq3 = "UPDATE guessnum SET times='" . $problem_att_time ."' WHERE idguessnum=" . $problem_id; 
             
                $return_text = [
                 "replyToken" => $reply_token,
                 "messages" => [
                [
                    "type" => "text",
                    "text" => $a_count . "A" . $b_count . "B"
                ]
                ]
                ];
             }
             $conn->query($sq3);
        }else{
            $return_text = [
                 "replyToken" => $reply_token,
                 "messages" => [
                [
                    "type" => "text",
                    "text" => "You have not yet start a game! To start a game just type 'start'"
                ]
                ]
            ];
            
        }
        break;
}



mysqli_close($conn);




$file1 = fopen("D:\\Line_log1.txt", "a+");
fwrite($file1, $userID."\n"); 
fclose($file1);
$post_data = [
  "replyToken" => $reply_token,
  "messages" => [
    [
      "type" => "text",
      "text" => $message->{"text"}
    ]
  ]
];
$post_data_unyetstart = [
  "replyToken" => $reply_token,
  "messages" => [
    [
      "type" => "text",
      "text" => "\u5c1a\u672a\u958b\u59cb\u904a\u6232\n\u82e5\u8981\u958b\u59cb\u8acb\u8f38\u5165 start"
    ]
  ]
]; 
   $ii= rand(1, 6);
 $post_data1 = [
  "replyToken" => $reply_token,
  "messages" => [
    [
      "type" => "image",
        "originalContentUrl" => "https://www.atosystem.org/linebot/img/". $ii .".jpg",
        "previewImageUrl"=> "https://www.atosystem.org/linebot/img/". $ii .".jpg"
    ]
  ]
]; 
  $post_data2 =  [
  "replyToken" => $reply_token,
  "messages" => [
    [
  "type" =>  "template",
  "altText" =>  "This is a buttons template",
  "template"=> [
      "type" =>  "buttons",
      "thumbnailImageUrl" =>  "https://www.atosystem.org/linebot/test.png",
      "imageAspectRatio" =>  "rectangle",
      "imageSize" =>  "cover",
      "imageBackgroundColor" =>  "#FFFFFF",
      "title" =>  "Menu",
      "text" =>  'ss',
      "actions"=> [
          [
            "type" =>  "postback",
            "label" =>  "Buy",
            "data" =>  "action=buy&itemid=123"
          ],
          [
            "type" =>  "postback",
            "label" =>  "Add to cart",
            "data" =>  "action=add&itemid=123"
          ],
          [
            "type" =>  "uri",
            "label" =>  "View detail",
            "uri" =>  "https://www.atosystem.org/linebot/test.png"
          ]
      ]
  ]
]
  ]
]; 
fwrite($file, json_encode($post_data)."\n");

$ch = curl_init("https://api.line.me/v2/bot/message/reply");
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($return_text));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer '.$access_token
    //'Authorization: Bearer '. TOKEN
));
$result = curl_exec($ch);
fwrite($file, $result."\n");  
fclose($file);
curl_close($ch); 
?>
