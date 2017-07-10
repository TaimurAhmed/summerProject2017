  <?php
class Post{
    private $user_obj;
    private $con;

    public function __construct($con,$user){
        $this->con = $con;
        $this->user_obj = new User($con,$user);
    }

    public function countPosts($post_id){
        $count_posts_query = "SELECT COUNT(id) FROM comments WHERE post_id = ?";
        $result = "";
        if($stmt = mysqli_prepare($con,$count_posts_query)){
            $this->user = array();
            /*Bind parameters for markers, type 's'/string */
            mysqli_stmt_bind_param($stmt, "s",$post_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt,$result);
            mysqli_stmt_fetch($stmt);
            mysqli_stmt_close($stmt);
        }
        return $result;

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
                    /*The auto-generated surrogate key to b used later*/
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

                    //Insert notifications into DB for post like
                    if($user_to != "none"){

                        $notification = new Notification($this->con,$added_by);
                        $notification->insertNotification($returned_id,$user_to, "profile_post");
                    
                    }

                }



        }
    }
    /*For loading posts on newsfeed*/
    public function loadPostFriends($data, $limit) {
        
        $page = $data["page"];/*Name of variable in Ajax request i.e. 1*/
        $userLoggedIn = $this->user_obj->getUsername();

        /*If page is 1 start at 0 */
        if($page ==  1){
            $start = 0;
        }else{
            $start = ($page-1) * $limit;
        }


        $str = ""; /*String to return*/
        $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' ORDER BY id DESC");
        
        /*If rows greater than zero*/
        if(mysqli_num_rows($data_query) > 0) {

            /*Number of results checked but not necessarily posted*/
            $num_iterations =0;
            $count = 1;



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
                    $user_to_obj = new User($this->con,$row['user_to']);
                    $user_to_name = $user_to_obj->getFirstandLastName();
                    $user_to = "to  <a href='".$row["user_to"]."'>".$user_to_name."</a>";
                }


                /*Has the user closed there account?*/
                $added_by_obj = new User($this->con,$added_by);   
                if($added_by_obj->isClosed()){ continue;}

                $user_logged_obj = new  User($this->con,$userLoggedIn);
                if($user_logged_obj->isFriend($added_by)){


                    /* If numbers of iterations is less than starting posting, start next loop*/
                    if($num_iterations++ < $start){ continue; }

                    /* If number of posts requested i.e. limit , is breached break else increase count*/
                    if($count > $limit){
                        break;
                    }else{
                        $count++;
                    }

                    //Bootbox delete post button: Load if comment belongs to you
                    if($userLoggedIn == $added_by)
                        $delete_button = "<button class='delete_button btn-danger' id='post$p_id'>X</button>";
                    else
                        $delete_button ="";


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

                    ?>
                        <script>
                            /*Toggle1 Toggle2...Toggle100; So we know which comment to show  etc*/
                            function toggle<?php echo $p_id; ?>(){
                                var element = document.getElementById("toggleComment<?php echo $p_id;?>");
                                if(element.style.display == "block"){
                                    element.style.display = "none";
                                }else{
                                    element.style.display = "block";
                                }
                            } 
                        </script>

                    <?



                    
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

                        /*On click toggle1, toggle2 etc*/
                        $str .= "<div class='status_post' onClick='javascript:toggle$p_id()'>
                                    <div class='post_profile_pic'>
                                        <img src='$profile_pic' width='50'>
                                    </div>

                                    <div class='posted_by' style='color:#ACACAC;'>
                                        <a href='$added_by'> $first_name $last_name </a> $user_to &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                                        $delete_button
                                    </div>
                                    <div id='post_body'>
                                        $body
                                        <br>
                                        <br>
                                        <br>
                                    </div>
                                    <!--Buggy when likes iframe is places on top of comments iframe-->
                                    <div class='newsFeedPostOptions'>
                                        comments(Placeholder)&nbsp;&nbsp;&nbsp;
                                        <iframe src='like.php?post_id=$p_id' scrolling='no'></iframe>
                                    </div>
                                    <br>
                                    <div class='post_comment' id='toggleComment$p_id' style='display:none;'>
                                        <iframe src='comment_frame.php?post_id=$p_id' id ='comment_iframe' frameborder='0'></iframe>
                                    </div>
                                </div>
                                <hr>";
                    }
                    //Delete Post
                    ?>
        <script>
            
        $(document).ready(function() {

            $('#post<?php echo $p_id; ?>').on('click', function() {
                bootbox.confirm("Are you sure you want to delete this post?", function(result) {
                /*Call reload on sucessfull callback, otherwise an http race condition is created*/
                $.post("./includes/form_handlers/delete_post.php?post_id=<?php echo $p_id; ?>", {result:result})
                  .done( function(){
                    /*To prevent reload if cancelled delete post */
                    if(result){
                        window.location.reload(true);
                    }
                });



                });
            });


        });


        </script>
                    <?php
                

            } /*End ugly while loop*/

            if($count > $limit) 
                $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                            <input type='hidden' class='noMorePosts' value='false'>";
            else 
                $str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> No more posts to show! </p>";
        }

        echo $str;

    }
    /*For loading posts on profile*/
    public function loadProfilePosts($data, $limit) {
        
        $page = $data["page"];/*Name of variable in Ajax request i.e. 1*/
        $profileUser = $data['profileUsername'];
        $userLoggedIn = $this->user_obj->getUsername();

        /*If page is 1 start at 0 */
        if($page ==  1){
            $start = 0;
        }else{
            $start = ($page-1) * $limit;
        }


        $str = ""; /*String to return*/
        $data_query = mysqli_query($this->con, "SELECT * FROM posts WHERE deleted='no' AND ((added_by = '$profileUser' AND user_to = 'none')  OR user_to = '$profileUser') ORDER BY id DESC");
        
        /*If rows greater than zero*/
        if(mysqli_num_rows($data_query) > 0) {

            /*Number of results checked but not necessarily posted*/
            $num_iterations =0;
            $count = 1;



            /*No user inpute; No prepared statement necessary + PHP API indicates permance is better*/
            while($row = mysqli_fetch_array($data_query)){
                $p_id = $row["id"];
                $body = $row["body"];
                $added_by = $row["added_by"];
                $date_time = $row["date_added"]; 

                    /* If numbers of iterations is less than starting posting, start next loop*/
                    if($num_iterations++ < $start){ continue; }

                    /* If number of posts requested i.e. limit , is breached break else increase count*/
                    if($count > $limit){
                        break;
                    }else{
                        $count++;
                    }

                    //Bootbox delete post button: Load if comment belongs to you
                    if($userLoggedIn == $added_by)
                        $delete_button = "<button class='delete_button btn-danger' id='post$p_id'>X</button>";
                    else
                        $delete_button ="";


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

                    ?>
                        <script>
                            /*Toggle1 Toggle2...Toggle100; So we know which comment to show  etc*/
                            function toggle<?php echo $p_id; ?>(){
                                var element = document.getElementById("toggleComment<?php echo $p_id;?>");
                                if(element.style.display == "block"){
                                    element.style.display = "none";
                                }else{
                                    element.style.display = "block";
                                }
                            } 
                        </script>

                    <?



                    
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

                        /*On click toggle1, toggle2 etc*/
                        $str .= "<div class='status_post' onClick='javascript:toggle$p_id()'>
                                    <div class='post_profile_pic'>
                                        <img src='$profile_pic' width='50'>
                                    </div>

                                    <div class='posted_by' style='color:#ACACAC;'>
                                        <a href='$added_by'> $first_name $last_name </a> &nbsp;&nbsp;&nbsp;&nbsp;$time_message
                                        $delete_button
                                    </div>
                                    <div id='post_body'>
                                        $body
                                        <br>
                                        <br>
                                        <br>
                                    </div>
                                    <!--Buggy when likes iframe is places on top of comments iframe-->
                                    <div class='newsFeedPostOptions'>
                                        comments(Placeholder)&nbsp;&nbsp;&nbsp;
                                        <iframe src='like.php?post_id=$p_id' scrolling='no'></iframe>
                                    </div>
                                    <br>
                                    <div class='post_comment' id='toggleComment$p_id' style='display:none;'>
                                        <iframe src='comment_frame.php?post_id=$p_id' id ='comment_iframe' frameborder='0'></iframe>
                                    </div>
                                </div>
                                <hr>";
                    
                    //Delete Post
                    ?>
        <script>
            
        $(document).ready(function() {

            $('#post<?php echo $p_id; ?>').on('click', function() {
                bootbox.confirm("Are you sure you want to delete this post?", function(result) {
                /*Call reload on sucessfull callback, otherwise an http race condition is created*/
                $.post("./includes/form_handlers/delete_post.php?post_id=<?php echo $p_id; ?>", {result:result})
                  .done( function(){
                    /*To prevent reload if cancelled delete post */
                    if(result){
                        window.location.reload(true);
                    }
                });



                });
            });


        });


        </script>
                    <?php
                

            } /*End ugly while loop*/

            if($count > $limit) 
                $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
                            <input type='hidden' class='noMorePosts' value='false'>";
            else 
                $str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;'> No more posts to show! </p>";
        }

        echo $str;

    }
}
?>