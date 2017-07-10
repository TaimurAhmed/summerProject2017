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

    public function insertNotification($post_id, $user_to, $type) {

        $userLoggedIn = $this->user_obj->getUsername();
        $userLoggedInName = $this->user_obj->getFirstAndLastName();

        $date_time = date("Y-m-d H:i:s");

        switch($type) {
            case 'comment':
                $message = $userLoggedInName . " commented on your post";
                break;
            case 'like':
                $message = $userLoggedInName . " liked your post";
                break;
            case 'profile_post':
                $message = $userLoggedInName . " posted on your profile";
                break;
            case 'comment_non_owner':
                $message = $userLoggedInName . " commented on a post you commented on";
                break;
            case 'profile_comment':
                $message = $userLoggedInName . " commented on your profile post";
                break;
        }

        $link = "post.php?id=" . $post_id;

        $insert_query = mysqli_query($this->con, "INSERT INTO notifications VALUES('', '$user_to', '$userLoggedIn', '$message', '$link', '$date_time', 'no', 'no')");
    }








}
?>

