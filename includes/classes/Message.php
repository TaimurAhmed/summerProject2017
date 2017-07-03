   <?php
class Message {
    private $user_obj;
    private $con;

    public function __construct($con,$user){
        $this->con = $con;
        $this->user_obj = new User($con,$user);
    }

    public function getMostRecentUser(){
    


        $user_to = "" ;
        $user_from = ""  ;
        $userLoggedIn = $this->user_obj->getUsername();

        if($this->noMessages($userLoggedIn)){
            return false;
        }

        $getmst_recent_user_query = "SELECT user_to,user_from FROM messages WHERE user_to = ? OR user_from = ? ORDER BY id DESC LIMIT 1";
        if($stmt = mysqli_prepare($this->con,$getmst_recent_user_query)){
            mysqli_stmt_bind_param($stmt, "ss",$userLoggedIn,$userLoggedIn);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$user_to,$user_from);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }

        if($user_to != $userLoggedIn){
            return $user_to;
        }

        return $user_from;
    }

    public function sendMessage($user_to,$body,$date){
        if($body!=""){
            $userLoggedIn = $this->user_obj->getUsername();
        }
        $create_message_query = "INSERT INTO messages VALUES('',?,?,?,?,'no','no','no')";
        if($stmt = mysqli_prepare($this->con,$create_message_query)){
            mysqli_stmt_bind_param($stmt, "ssss",$user_to,$userLoggedIn,$body,$date);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }

    }

    private function noMessages($uName){
        $result = 0;
        $count_messages_query = "SELECT COUNT(id) FROM messages WHERE user_to = ? OR user_from = ?";
        if($stmt = mysqli_prepare($this->con,$count_messages_query)){
            mysqli_stmt_bind_param($stmt, "ss",$uName,$uName);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$result);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
        return $result === 0;
    }

    public function getMessages($otherUser){
        $userLoggedIn = $this->user_obj->getUsername();
        $user_to;
        $user_from;
        $body;
        $data = "";

        $read_message_query = "UPDATE messages SET opened ='yes' WHERE user_to = ? AND user_from = ?";
         if($stmt = mysqli_prepare($this->con,$read_message_query)){
            mysqli_stmt_bind_param($stmt, "ss",$userLoggedIn,$otherUser);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }



        $get_msgs_query  = "SELECT user_to,user_from,body FROM messages WHERE (user_to = ? AND user_from = ?) OR (user_from = ? AND user_to = ?)";
        if($stmt = mysqli_prepare($this->con,$get_msgs_query)){
            mysqli_stmt_bind_param($stmt, "ssss",$userLoggedIn,$otherUser,$userLoggedIn,$otherUser);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$user_to,$user_from,$body);
            /*Select formatting based on who recepient/sender and then concatenate*/
            while(mysqli_stmt_fetch($stmt)){
                    $div_top = ($user_to == $userLoggedIn) ? "<div class='message' id='green'>" :  "<div class='message' id='blue'>";
                    $data = $data . $div_top. $body ."</div><br><br>";
            }
            
            mysqli_stmt_close($stmt);
        }

        return $data; 
    }

    private function getLatestMessages($userLoggedIn ){

    }

    public function getConvos(){
        $userLoggedIn = $this->user_obj->getUsername();
        $return_string = "";
        $convos = array();

        $get_convo_query = "SELECT user_to,user_from FROM messages WHERE user_to = ? OR user_from = ?";
        if($stmt = mysqli_prepare($this->con,$get_convo_query)){
            mysqli_stmt_bind_param($stmt, "ss",$userLoggedIn,$userLoggedIn);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$user_to,$user_from);
            /*Push the reciever of the msg from user persp to array*/
            while(mysqli_stmt_fetch($stmt)){
                $user_to_push = ($user_to != $userLoggedIn) ? $user_to : $user_from;
                 if(!in_array($user_to_push, $convos)){
                    array_push($convos,$user_to_push)
                 }
            }
            mysqli_stmt_close($stmt);
        }

        foreach ($co as $username) {
            $user_found_obj = new User($this->con,$username);
            $latest_message_details = $this->getLatestMessages($userLoggedIn);
        }
    }


 
 }

 ?>