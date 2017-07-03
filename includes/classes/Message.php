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

    private function getLatestMessages($userLoggedIn,$second_user){
        $body = "";
        $user_to = "";
        $details_array = array();
        $latest_msg_query = "SELECT body,user_to FROM messages WHERE ($user_to = ? AND user_from = ?) OR ($user_to = ? AND user_from = ?) ORDER BY  id DESC LIMIT 1";
        if($stmt = mysqli_prepare($this->con,$latest_msg_query)){
            mysqli_stmt_bind_param($stmt, "ssss",$userLoggedIn,$second_user,$userLoggedIn,$second_user);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$body,$user_to);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
        /*Who sent the latest message loggedinUser or the other guy? */
        $sent_by = ($user_to === $userLoggedIn) ? "They said " : "You said ";
                    /*Time*/
                    $date_time_now = date("Y-m-d H:i:s");
                    $start_date = new DateTime($date_time);/*Time of post*/
                    $end_date = new DateTime($date_time_now);/*Current Time*/
                    $interval = $start_date->diff($end_date);/*Diff b/w two dates*/ 
                    if($interval->y >= 1) {
                                if($interval == 1)
                                    $time_message = $interval->y . " year ago"; //1 year ago
                                else 
                                    $time_message = $interval->y . " years ago"; //1+ year ago
                    }
                    else if ($interval-> m >= 1) {
                        if($interval->d == 0) {
                            $days = " ago";
                        }
                        else if($interval->d == 1) {
                            $days = $interval->d . " day ago";
                        }
                        else {
                            $days = $interval->d . " days ago";
                        }


                        if($interval->m == 1) {
                            $time_message = $interval->m . " month". $days;
                        }
                        else {
                            $time_message = $interval->m . " months". $days;
                        }
                    }
                    else if($interval->d >= 1) {
                        if($interval->d == 1) {
                            $time_message = "Yesterday";
                        }
                        else {
                            $time_message = $interval->d . " days ago";
                        }
                    }
                    else if($interval->h >= 1) {
                        if($interval->h == 1) {
                            $time_message = $interval->h . " hour ago";
                        }
                        else {
                            $time_message = $interval->h . " hours ago";
                        }
                    }
                    else if($interval->i >= 1) {
                        if($interval->i == 1) {
                            $time_message = $interval->i . " minute ago";
                        }
                        else {
                            $time_message = $interval->i . " minutes ago";
                        }
                    }
                    else {
                        if($interval->s < 30) {
                            $time_message = "Just now";
                        }
                        else {
                            $time_message = $interval->s . " seconds ago";
                        }
                    }

        array_push($details_array,$sent_by);
        array_push($details_array,$body);
        array_push($details_array,$time_message ); 
        return $details_array;
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

        foreach ($convos as $username) {
            $user_found_obj = new User($this->con,$username);
            $latest_message_details = $this->getLatestMessages($userLoggedIn);
        

            /*If message is over 12 characters, chop off and append with dots*/
            $dots = (strlen($latest_message_details[1] >= 12) ? "...":"");
            $split = str_split($latest_message_details[1],12);
            $split = $split[0] . $dots;
        }
        
    }


 
 }

 ?>