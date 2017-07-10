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

    private function noNotification($userLoggedIn){
        $count_notifications = "SELECT COUNT(id) FROM notifications WHERE user_to= ?";
        $result = 0;
        if($stmt = mysqli_prepare($this->con,$count_notifications)){
            mysqli_stmt_bind_param($stmt, "s",$userLoggedIn);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$result);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }

        return $result <= 0;
    }

    private function getTimeStamp($date_time){
        $date_time_now = date("Y-m-d H:i:s");
        $start_date = new DateTime($date_time); //Time of post
        $end_date = new DateTime($date_time_now); //Current time
        $interval = $start_date->diff($end_date); //Difference between dates 
        if($interval->y >= 1) {
            if($interval == 1)
                $time_message = $interval->y . " year ago"; //1 year ago
            else 
                $time_message = $interval->y . " years ago"; //1+ year ago
        }
        else if ($interval->m >= 1) {
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

        return $time_message;
    }

    public function getNotifications($data,$limit){

        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();
        $return_string = "";

        if($page == 1)
            $start = 0;
        else 
            $start = ($page - 1) * $limit;

        $set_viewed_query = "UPDATE notifications SET viewed='yes' WHERE user_to=?";
        if($stmt = mysqli_prepare($this->con,$set_viewed_query)){
            mysqli_stmt_bind_param($stmt, "s",$userLoggedIn);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }



        if($this->noNotification($userLoggedIn)) {
            echo "You have no notifications!";
            return;
        }

        $num_iterations = 0; //Number of messages checked 
        $count = 1; //Number of messages posted
        $get_all_notifcation = "SELECT user_from,datetime,profile_pic,opened,link,message FROM notifications JOIN users ON user_from=username WHERE user_to=? ORDER BY notifications.id DESC";
        if($stmt = mysqli_prepare($this->con,$get_all_notifcation)){
            mysqli_stmt_bind_param($stmt, "s",$userLoggedIn);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$user_from,$datetime,$profile_pic,$opened,$link,$message);
            while(mysqli_stmt_fetch($stmt)){
                if($num_iterations++ < $start)
                    continue;
                if($count > $limit)
                    break;
                else 
                    $count++;
                /*Convert to formatted date string*/
                $datetime=$this->getTimeStamp($datetime);

                $style = ($opened == 'no') ? "background-color: white;" : "";

                $return_string .= "<a href='" . $link . "'> 
                                        <div class='resultDisplay resultDisplayNotification' style='" . $style . "'>
                                            <div class='notificationsProfilePic'>
                                                <img src='" . $profile_pic . "'>
                                            </div>
                                            <p class='timestamp_smaller' id='grey'>" . $datetime . "</p>" . $message . "
                                        </div>
                                    </a>";

            }
            mysqli_stmt_close($stmt);
        }

        //If posts were loaded
        if($count > $limit)
            $return_string .= "<input type='hidden' class='nextPageDropdownData' value='" . ($page + 1) . "'><input type='hidden' class='noMoreDropdownData' value='false'>";
        else 
            $return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'> <p style='text-align: center;'>No more notifications to load!</p>";

        return $return_string;
    }
}
?>

