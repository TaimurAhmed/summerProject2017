<html class="like_entire">
    
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

            /*Get the id of post*/
            if(isset($_GET["post_id"])){
                $post_id = $_GET["post_id"];
            }
            $total_likes = 0;
            $user_liked="";


            /*Likes for a particular post*/
            $get_likes_query = "SELECT likes, added_by FROM posts WHERE id = ?";
            if($stmt = mysqli_prepare($con,$get_likes_query)){
                mysqli_stmt_bind_param($stmt, "s",$post_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt,$total_likes,$user_liked);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
            }


            /*Corresponding user data to liked data (needs a join so badly)*/
            $user_details_query = "SELECT num_likes FROM users WHERE username = ?";
            $total_user_likes = 0;
            if($stmt = mysqli_prepare($con,$user_details_query)){
                mysqli_stmt_bind_param($stmt, "s",$user_liked);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt,$total_user_likes);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
            }

            //Code for LIKE  button pressed       
            if(isset($_POST['like_button'])){
                $total_likes++;
                $make_like_query = "UPDATE posts SET likes = ? WHERE id = ?";
                if($stmt = mysqli_prepare($con,$make_like_query)){
                    mysqli_stmt_bind_param($stmt, "ss",$total_likes,$post_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                 }
                $total_user_likes++;

                $user_likes_query = "UPDATE users SET num_likes = ? WHERE username = ?";
                if($stmt = mysqli_prepare($con,$user_likes_query)){
                    mysqli_stmt_bind_param($stmt, "ss",$total_user_likes,$user_liked);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);

                }

                $insert_user_into_likes = "INSERT INTO likes VALUES('',?,?)";
                if($stmt = mysqli_prepare($con,$insert_user_into_likes)){
                    mysqli_stmt_bind_param($stmt, "ss",$userLoggedIn,$post_id );
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }

                /* Insert some kind of notification!!!!!!!!!!!!!!!!!!!!!!!!!*/

                /*Unlike button*/
                if(isset($_POST['unlike_button'])){
                $total_likes--;
                $make_like_query = "UPDATE posts SET likes = ? WHERE id = ?";
                if($stmt = mysqli_prepare($con,$make_like_query)){
                    mysqli_stmt_bind_param($stmt, "ss",$total_likes,$post_id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                 }
                $total_user_likes--;
                $user_likes_query = "UPDATE users SET num_likes = ? WHERE username = ?";
                if($stmt = mysqli_prepare($con,$user_likes_query)){
                    mysqli_stmt_bind_param($stmt, "ss",$total_user_likes,$user_liked);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
                $insert_user_into_likes = "DELETE FROM likes WHERE username=? AND post_id=?";
                if($stmt = mysqli_prepare($con,$insert_user_into_likes)){
                    mysqli_stmt_bind_param($stmt, "ss",$userLoggedIn,$post_id );
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }


            //Check for previous likes
            $check_for_prevLikes_query = "SELECT COUNT(id) FROM likes WHERE username=? AND post_id = ?";
            $num_rows = 0;
            if($stmt = mysqli_prepare($con,$check_for_prevLikes_query)){
                mysqli_stmt_bind_param($stmt, "ss",$userLoggedIn,$post_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt,$num_rows);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
            }


            
            //Check for previous likes
            $check_for_previous_likes_query = "SELECT * FROM likes WHERE username = ? AND post_id = ?";
            $num_row=0;
            /*Get number of rows of this query!!!*/

            if($num_rows > 0) {
                echo '<div class="like_page_form">
                        <form action="like.php?post_id=' . $post_id . '" method="POST">
                            <input type="submit" class="comment_like" name="unlike_button" value="Unlike">
                            <div class="like_value">
                                '. $total_likes .' Likes
                            </div>
                        </form>
                     </div>
                ';
            }
            else {
                echo '<div class="like_page_form">
                        <form action="like.php?post_id=' . $post_id . '" method="POST">
                            <input type="submit" class="comment_like" name="like_button" value="Like">
                            <div class="like_value">
                                '. $total_likes .' Likes
                            </div>
                        </form>
                    </div class="like_page_form">
                ';
            }


        ?>  
        
    </body>

</html>