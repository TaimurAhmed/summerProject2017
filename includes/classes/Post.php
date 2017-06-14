<?php
class Post{
    private $user_obj;
    private $con;

    public function __construct($con,$user){
        $this->con = $con;
        $this->user_obj = new User($con,$user);
    }

    public function  submitPost($body,$user_to) {
        $body = strip_tags($body); /*Remove HTML tags*/
        $body = mysqli_real_escape_string($this->con,$body);
        $check_empty = preg_replace('/\s+/', '', $body);/*Delete all spaces*/

        /*If string is not empty insert post*/
        if($check_empty != ""){
            /*Current date and time*/
            $date_added = date("Y-m-d H:i:s");
            /*Get userName*/
            $added_by = $this->user_obj->getusername();
            /*If user is not on own profile, user_to is set to 'none'*/
            if($user_to === $added_by){
                $user_to = "none";
            }
                /*Insert post into DB*/
                $create_post_query = "INSERT INTO posts VALUES('',?,?,?,?,'no','no','0')";
                if($stmt = mysqli_prepare($this->con,$create_post_query)){
                    /*Bind parameters for markers, type 's'/string */
                    mysqli_stmt_bind_param($stmt, "ssss",$body,$added_by,$user_to,$date_added);
                    /*Execute query*/
                    mysqli_stmt_execute($stmt);
                    /*The auto-generated surrogate key*/
                    $returned_id = $this->con->insert_id;
                    /*Close prepared stmt*/
                    mysqli_stmt_close($stmt);


                    /*Update Post Count for User*/
                    $num_posts = $this->user_obj->getNumPosts();
                    $num_posts++;
                    $update_user_numPosts_query = "UPDATE users SET num_posts = ? WHERE username = ? ";
                    if($stmt = mysqli_prepare($this->con,$update_user_numPosts_query)){
                        /*Bind parameters for markers, type 's'/string */
                        mysqli_stmt_bind_param($stmt, "ss",$num_posts,$added_by);
                        /*Execute query*/
                        mysqli_stmt_execute($stmt);
                        /*Close prepared stmt*/
                        mysqli_stmt_close($stmt);
                    }
                }
        }
    }

    public function loadPostFriends($data, $limit) {
        $page = $data["page"];
        $userLoggedIn = $this->user_obj->getusername();

        if($page === 1){
            $start =0;
        }else{
            $start = ($page-1) = $limit;
        }


        $str = ""; /*String to return*/
        $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");
        /*No user inpute; No prepared statement necessary + PHP API indicates permance is better*/
        while($row = mysqli_fetch_array($data_query)){
            $p_id = $row["id"];
            $body = $row["body"];
            $added_by = $row["added_by"];
            $date_time = $row["date_added"];

            /*Prepare user to string if posted to a user or otherwise*/
            if($row["user_to"] === "none"){
                $user_to = "";
            }else{
                $user_to_obj = new User($con,$row['user_to']);
                $user_to_name = $user_to_obj->getFirstandLastName();
                $user_to = "to  <a href='".$row["user_to"]."'>".$user_to_name."</a>";
            }
            /*Has the user closed there account?*/
            $added_by_obj = new User($this->con,$added_by);
            if($added_by_obj->isClosed()){
                continue;
            }

            $user_details_query = "SELECT first_name, last_name, profile_pic FROM users WHERE username = ?";
            $first_name = "firstname";
            $last_name = "lastname";
            $profile_pic = "";
            if($stmt = mysqli_prepare($this->con,$user_details_query)){
                mysqli_stmt_bind_param($stmt, "s",$added_by);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt,$first_name,$last_name,$profile_pic);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
            }



            
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
            $str = $str . "<div class='status_post'>
                                <div class='post_profile_pic'>
                                    <img src='$profile_pic' width='50'>
                                </div>

                                <div class='posted_by' style='color:#ACACAC;'>
                                    <a href='$added_by'> $first_name $last_name </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                                </div>
                                <div id='post_body'>
                                    $body
                                    <br>
                                </div>

                            </div>
                            <hr>";

        }
        echo $str;

    }
}
?>