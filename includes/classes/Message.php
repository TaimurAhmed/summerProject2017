  <?php
class Message {
    private $user_obj;
    private $con;

    public function __construct($con,$user){
        $this->con = $con;
        $this->user_obj = new User($con,$user);
    }

    public function getMostRecentUser(){
    
        if(noMessages($userLoggedIn)){
            return false;
        }

        $user_to = "" ;
        $user_from = ""  ;

        $userLoggedIn = $this->user_obj->getUsername;
        $getmst_recent_user_query = "SELECT user_to,user_from FROM messages WHERE user_to = ? OR user_from = ? ORDER BY id DESC LIMIT 1";
        if($stmt = mysqli_prepare($this->con,$getmst_recent_user_query)){
            mysqli_stmt_bind_param($stmt, "ss",$userLoggedIn,$userLoggedIn);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$user_to,$user_from);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }

        if($user_to != $userLoggedIn){
            return $user_to
        }

        return $user_from;
    }

    private function noMessages($uName){
        $result = 0;
        $count_messages_query = "SELECT user_to,user_from FROM messages WHERE user_to = ? OR user_from = ?";
        if($stmt = mysqli_prepare($this->con,$getmst_recent_user_query)){
            mysqli_stmt_bind_param($stmt, "ss",$userLoggedIn,$userLoggedIn);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$result);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
        return $result === 0;
    }
 
 }

 ?>