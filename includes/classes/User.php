 <?php
class User{
    private $user;
    private $con;
    private $test;

    public function __construct($con,$user){
         $this->con = $con;
         $user_details_query = "SELECT id,first_name,last_name,username,friend_array FROM users WHERE username = ?";
        if($stmt = mysqli_prepare($con,$user_details_query)){
            $this->user = array();
            /*Bind parameters for markers, type 's'/string */
            mysqli_stmt_bind_param($stmt, "s",$user);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$this->user["id"],$this->user["first_name"],$this->user["last_name"],$this->user["username"],$this->user["friend_array"]);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    public function getUsername(){
        return $this->user['username']; 
    }

    public function  getNumPosts(){
        $username = $this->user['username'];
        $getNumPosts_query = "SELECT num_posts FROM users WHERE id = ?";
        $row="";
        if($stmt = mysqli_prepare($this->con,$getNumPosts_query)){
            mysqli_stmt_bind_param($stmt, "s",$this->user["id"]);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$row);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
        return $row;
    }

    public function getFirstandLastName() {
        $username = $this->user['username'];
        $getFirstandLastName_query = "SELECT first_name,last_name FROM users WHERE id = ?";
        $row = array();
        if($stmt = mysqli_prepare($this->con,$getFirstandLastName_query)){
            mysqli_stmt_bind_param($stmt, "s",$this->user["id"]);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$row["first_name"],$row["last_name"]);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
        return $row["first_name"] . " " . $row["last_name"];
    }

    public function getProfilePic() {
        $username = $this->user['username'];
        $getFirstandLastName_query = "SELECT profile_pic FROM users WHERE id = ?";
        $row = array();
        if($stmt = mysqli_prepare($this->con,$getFirstandLastName_query)){
            mysqli_stmt_bind_param($stmt, "s",$this->user["id"]);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$row["profile_pic"]);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
        return $row["profile_pic"];
    }
    

    public function isClosed(){
        $username = $this->user['id'];
        $result = "";
        $isClosedQuery = "SELECT user_closed FROM users WHERE id = ?";
        if($stmt = mysqli_prepare($this->con,$isClosedQuery)){
            mysqli_stmt_bind_param($stmt, "s",$this->user["id"]);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$result);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
       }
       return $result === "yes";
    }

    public function isFriend($username_to_check){
        $usernameComma = "," . $username_to_check . ",";

        /*Check if string is inside another string*/
        if(strstr($this->user['friend_array'],$usernameComma) || $username_to_check == $this->user['username']){
            return true;
        }else{
            return false;
        }
    }

    public function didRecieveRequest($user_to){ 
        $user_from = $this->user['username'];
        $number;
        $check_request_query = "SELECT COUNT(id) FROM friend_requests WHERE user_to=? AND user_from = ?";
        if($stmt = mysqli_prepare($this->con,$check_request_query)){
            mysqli_stmt_bind_param($stmt, "ss",$user_to,$user_from);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$number);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
       }

       return $number > 0 ;
    }

    public function didSendRequest($user_from){ 
        $user_to  = $this->user['username'];
        $number;
        $check_request_query = "SELECT COUNT(id) FROM friend_requests WHERE user_to=? AND user_from = ?";
        if($stmt = mysqli_prepare($this->con,$check_request_query)){
            mysqli_stmt_bind_param($stmt, "ss",$user_to,$user_from);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$number);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }

       return $number > 0 ;
    }

    public function removeFriend($user_to_remove){
        $logged_in_user = $this->user['username'];
        $get_friends_query = "SELECT friend_array FROM users WHERE username = ?";
        if($stmt = mysqli_prepare($this->con,$get_friends_query)){
            mysqli_stmt_bind_param($stmt, "s",$user_to_remove);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$friend_array);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }

        /*To delete friend from users profile*/
        $friend_array = str_replace($user_to_remove . ",", "", $friend_array);
        deleteFriendFromArray($friend_array,$logged_in_user);
        /*To delete friend from other persons profile too*/
        $friend_array = str_replace($this->['username'] . ",","",$friend_array);
        deleteFriendFromArray($friend_array,$user_to_remove);

    }

    private function deleteFriendFromArray($friend_array,$user_to_remove){
        $update_friends_array = "UPDATE users SET friend_array = ?  WHERE username = ?";
        if($stmt = mysqli_prepare($this->con,$update_friends_array)){
            mysqli_stmt_bind_param($stmt, "ss",$friend_array,$user_to_remove);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    /*Send friend request*/
    public function sendRequest($user_to){
        $user_from = $this->user['username'];
        $query = "INSERT INTO friend_requests VALUES ('',?,?)";
        if($stmt = mysqli_prepare($this->con,$user_to){
            mysqli_stmt_bind_param($stmt, "ss",$user_to,$user_from);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

}


?>