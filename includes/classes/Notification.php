<?php
class Notification {
    private $user_obj;
    private $con;

    public function __construct($con,$user){
        $this->con = $con;
        $this->user_obj = new User($con,$user);
    }

    public function getUnreadNumber() {
        $userLoggedIn = $this->user_obj->getUsername();
        $result = 0;
        $query = "SELECT COUNT(tab.n) FROM (SELECT user_from AS n FROM notifications WHERE viewed = 'no' AND user_to = ? GROUP BY user_from) AS tab";
        if($stmt = mysqli_prepare($this->con,$query)){
            mysqli_stmt_bind_param($stmt, "s",$userLoggedIn);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$result);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
        return $result;
    }


 ?>