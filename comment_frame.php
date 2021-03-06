<?php
/*Get the id of post*/
if(isset($_GET["post_id"])){
    $post_id = $_GET["post_id"];
    $p_id=$post_id; //Used later for Aria Labels
}
?>
<html>
    <head>
        <!--CSS-->
        <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
        <link rel="stylesheet" type="text/css" href="./assets/css/comments.css">
    </head>
    <body role='list' <?php echo "id='listOfComments$p_id' aria-labelledby='newsFeedPost$p_id listOfComments$p_id' aria-describedby='listOfComments'"?> >
        <?php 
            require './config/config.php';
            include("./includes/classes/User.php");
            include("./includes/classes/Post.php");
            include("./includes/classes/Notification.php");


            /*Redirect users who are not logged in*/
            if(isset($_SESSION["username"])){
                $userLoggedIn = $_SESSION["username"]; 

            }else{
                header("Location:register.php");
            }

            require './includes/header_handler.php'; 
        ?>

        <!-- JS -->
        <script>
            function toggle(){
                var element = document.getElementById("comment_section");
                if(element.style.display == "block"){
                    element.style.display = "none";
                }else{
                    element.style.display = "block";
                }
            } 
        </script>
        <?php
            /*Get the id of post*/
            if(isset($_GET["post_id"])){
                $post_id = $_GET["post_id"];
                $p_id=$post_id; //Used later for Aria Labels
            }
            $comment = array();
            $comment_query = "SELECT added_by,user_to FROM posts WHERE id = ?";
            if($stmt = mysqli_prepare($con,$comment_query)){
                mysqli_stmt_bind_param($stmt, "s",$post_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt,$comment["added_by"],$comment["user_to"]);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
            }
            $posted_to = $comment["added_by"];
            $user_to = $comment["user_to"];

            /*Post hanlder when submit button is pressed*/
            if(isset($_POST['postComment' . $post_id])){
                $post_body = $_POST['post_body'];
                $post_body = mysqli_escape_string($con,$post_body);
                $date_time_now = date("Y-m-d H:i:s");
                $insert_comment_query = "INSERT INTO comments VALUES ('',?,?,?,?,'no',?)";
                /*Abstract into function later*/
                $post_body = str_replace('\r\n','<br>',$post_body);
                $post_body = str_replace('\n','<br>',$post_body);
                $post_body = str_replace('\r','<br>',$post_body);
                /*If post is not empty insert it*/
                if($post_body !=""){
                    if($stmt = mysqli_prepare($con,$insert_comment_query)){
                        mysqli_stmt_bind_param($stmt, "sssss",$post_body, $userLoggedIn, $posted_to, $date_time_now, $post_id);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);
                        if($posted_to != $userLoggedIn){
                            $notification = new Notification($con,$userLoggedIn);
                            $notification->insertNotification($post_id,$posted_to,"comment");
                        }
                        if($user_to != 'none' && $user_to != $userLoggedIn){
                            $notification = new Notification($con,$userLoggedIn);
                            $notification->insertNotification($post_id,$user_to,"profile_comment");
                        }

                        $get_commenters_query = "SELECT posted_by FROM comments WHERE post_id = ?";
                        $notified_users = array();
                        $posted_by = array();
                        $i = 0;
                        if($stmt = mysqli_prepare($con,$get_commenters_query)){
                            mysqli_stmt_bind_param($stmt, "s",$post_id);
                            mysqli_stmt_execute($stmt);
                            while(mysqli_stmt_fetch($stmt)){
                                mysqli_stmt_bind_result($stmt,$posted_by[$i]);
                                $i++;
                            }
                            $i--;
                            mysqli_stmt_close($stmt);
                        }
                        for ($i;$i >= 0 ; $i--) {

                                if($posted_by[$i] != $posted_to 
                                     && $posted_by[$i] != $user_to 
                                     && $posted_by[$i] != $userLoggedIn 
                                     && !in_array($posted_by[$i],$notified_users)){
                                    $notification = new Notification($con,$userLoggedIn);
                                    $notification->insertNotification($post_id,$posted_by[$i],"comment_non_owner"); 
                                }
                                array_push($notified_users, $posted_by[$i]);

                        }

                    }

                    echo "<p>Comment Posted ! <p>";
                }
            }
        ?>
        <form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">
            <textarea placeholder='Type Comment Here !'  title='Type Comment Here' name="post_body"></textarea>
            <input  title='Comment on this post here!' type="submit" name="postComment<?php echo $post_id; ?>" value="Comment">
        </form>

        <!-- Load comments for post -->
        <?php
            $count_comment_query = "SELECT COUNT(id) FROM comments WHERE post_id = ?";
            $count = 0;
            
            if($stmt = mysqli_prepare($con,$count_comment_query)){
                mysqli_stmt_bind_param($stmt, "s",$post_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt,$count);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
            }

            if($count == 0){
                echo "<center><br><div role='alert' aria-relevant='all' >No comments to show ! </div><br></center>";
            }else{
            $get_comments_query = "SELECT post_body,posted_by,posted_to,date_added,removed FROM comments WHERE post_id = ? ORDER BY date_added DESC";
            $comments_array = array();
            $results_array = array();

            $row_num = 0;
            $col_num = 5;
            if($stmt = mysqli_prepare($con,$get_comments_query)){
                mysqli_stmt_bind_param($stmt, "s",$post_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt,$comments_array["post_body"],$comments_array["posted_by"],$comments_array["posted_to"],$comments_array["date_added"],$comments_array["removed"]);
                while(mysqli_stmt_fetch($stmt)){
                        $results_array[$row_num][0] = $comments_array["post_body"];
                        $results_array[$row_num][1] = $comments_array["posted_by"];
                        $results_array[$row_num][2] = $comments_array["posted_to"];
                        $results_array[$row_num][3] = $comments_array["date_added"];
                        $results_array[$row_num][4] = $comments_array["removed"]; 
                        $row_num++;
                }

                mysqli_stmt_close($stmt);
            }

            for($n = 0; $n < $row_num; $n++){
            $post_number_client = $n+1;
            /* Prevents errors. At worst will be set to null and nothing will appear*/
            $comment_post_body = $results_array[$n][0];
            $posted_by = $results_array[$n][1];
            $posted_to = $results_array[$n][2];
            $date_added = $results_array[$n][3];
            $removed = $results_array[$n][4];
            /*Time!!!!!!!!: Abstract this later*/
            $date_time_now = date("Y-m-d H:i:s");
            $start_date = new DateTime($date_added);/*Time of post*/ /*Refactor var here!!! i.e. var becomes arg*/
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
                            $time_message = $interval->m . " month ". $days;
                        }
                        else {
                            $time_message = $interval->m . " months ". $days;
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
                    $user_obj = new User($con, $posted_by);

                    ?>
                        <div role='listitem' class="comments_section" id=<?php echo "'comment$post_number_client' aria-labelledby='newsFeedPost$p_id comment$post_number_client' aria-describedby='comment$post_number_client"."of"."$count '"; ?>>
                            <!-- target set to render parent_window NOT inside the iframe-->
                            <a href="<?php echo $posted_by;?>" target="_parent" id="commentPoster<?php echo$post_number_client?>" aria-labelledby="newsFeedPost<?php echo $p_id?> commentPoster<?php echo$post_number_client?>" aria-describedby="posterOfCommentNumber<?php echo$post_number_client?>">
                                <img src='<?php echo $user_obj->getProfilePic();?>' title= "<?php echo $posted_by;?>" style ="float:left;" height = "30"/>
                            </a>
                            <a <?php echo 'id=linkToProfileOfCommentPoster'. $post_number_client ?> aria-labelledby='newsFeedPost<?php echo $p_id?> linkToProfileOfCommentPoster<?php echo $post_number_client?>' aria-describedby='linkToSenderOfComment<?php echo $post_number_client?>' title='Link to <?php echo $user_obj->getFirstandLastName ();?> profile' href="<?php echo $posted_by;?>" target="_parent">
                                 <b>
                                     <?php echo $user_obj->getFirstandLastName (); ?>
                                 </b>
                            </a>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <?php echo $time_message. "<br> <div id='bodyOfComment".$post_number_client."' aria-labelledby='newsFeedPost".$p_id." bodyOfComment".$post_number_client."' >". $comment_post_body . "</div>"; ?>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <?php echo "<div id='metaOfPost".$post_number_client."'aria-labelledby='newsFeedPost".$p_id." metaOfPost".$post_number_client."' aria-describedby='commentMetaData' class='newsFeedPostOptions'><br> Comment ". $post_number_client. " of ". $count." comments. </div>" ?>
                            <hr>
                        </div>
                     <?php


            }
            
}


        ?>
            
        
    </body>
</html>