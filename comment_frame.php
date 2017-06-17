<html>
    <head>
        <title></title>
        <!--CSS-->
        <link rel="stylesheet" type="text/css" href="./assets/css/style.css">
        <link rel="stylesheet" type="text/css" href="./assets/css/comments.css">
    </head>
    <body>
        <?php 
            require './config/config.php';
            include("./includes/classes/User.php");
            include("./includes/classes/Post.php");

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

            if(isset($_POST['postComment' . $post_id])){
                $post_body = $_POST['post_body'];
                $post_body = mysqli_escape_string($con,$post_body);
                $date_time_now = date("Y-m-d H:i:s");
                $insert_comment_query = "INSERT INTO comments VALUES ('',?,?,?,?,'no',?)";
                if($stmt = mysqli_prepare($con,$insert_comment_query)){
                    mysqli_stmt_bind_param($stmt, "sssss",$post_body, $userLoggedIn, $posted_to, $date_time_now, $post_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
                echo "<p>Comment Posted ! <p>";
            }
        ?>
        <form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">
            <textarea name="post_body"></textarea>
            <input type="submit" name="postComment<?php echo $post_id; ?>" value="Post">
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

            if($count != 0){
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
                    $user_obj = new User($con, $posted_by);

                    ?>
                        <div class="comments_section">
                            <!-- target set to render parent_window NOT inside the iframe-->
                            <a href="<?php echo $posted_by;?>" target="_parent">
                                <img src='<?php echo $user_obj->getProfilePic();?>' title= "<?php echo $posted_by;?>" style ="float:left;" height = "30"/>

                            </a>
                            <a href="<?php echo $posted_by;?>" target="_parent">
                                 <b>
                                     <?php echo $user_obj->getFirstandLastName (); ?>
                                 </b>
                            </a>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <?php echo $time_message. "<br>" . $comment_post_body; ?>
                            <hr>
                        </div>
                     <?php


            }
            /*If there are comments to load*/
}


        ?>
            
        
    </body>
</html>